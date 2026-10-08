// Derives the API docs data from the fluid-primitives sources with the TypeScript compiler API:
//   generated/client-api.json       every export of Client/index.ts, keyed by export name
//   generated/machines/<name>.json  per self-made machine (Primitives/<Name>/src/*.machine.ts), in the shape of
//                                   the @zag-js/docs data so ComponentPropsTable renders it unchanged
// `--check` regenerates in memory and exits non-zero on any difference or warning instead of writing.
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import ts from 'typescript';

const docsRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const pkgRoot = path.resolve(docsRoot, '../fluid-primitives');
const generatedRoot = path.join(docsRoot, 'Resources/Private/Content/generated');
const clientIndex = path.join(pkgRoot, 'Resources/Private/Client/index.ts');
const primitivesRoot = path.join(pkgRoot, 'Resources/Private/Primitives');
const checkOnly = process.argv.includes('--check');

const errors = [];
const warnings = [];

const configPath = path.join(pkgRoot, 'tsconfig.json');
const config = ts.getParsedCommandLineOfConfigFile(
    configPath,
    {},
    {
        ...ts.sys,
        onUnRecoverableConfigFileDiagnostic: d => {
            throw new Error(ts.flattenDiagnosticMessageText(d.messageText, '\n'));
        },
    }
);
const program = ts.createProgram(
    config.fileNames.filter(file => !file.includes('/tests/')),
    config.options
);
const checker = program.getTypeChecker();

// ---------------------------------------------------------------------------------------------
// helpers

function where(node) {
    const sourceFile = node.getSourceFile();
    const { line } = sourceFile.getLineAndCharacterOfPosition(node.getStart());
    return `${path.relative(pkgRoot, sourceFile.fileName)}:${line + 1}`;
}

const squash = text =>
    text
        .replace(/\s+/g, ' ')
        .replace(/^\| /, '')
        .replace(/\(\s+/g, '(')
        .replace(/\s+\)/g, ')')
        .replace(/,\s*([})\]])/g, (_, bracket) => (bracket === '}' ? ` ${bracket}` : bracket))
        .trim();
const textOf = node => squash(node.getText());
const kebab = name => name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
const pascal = name => name.charAt(0).toUpperCase() + name.slice(1);
const isExternal = node => node.getSourceFile().fileName.includes('/node_modules/');

const KNOWN_TAGS = new Set([
    'param',
    'returns',
    'return',
    'default',
    'example',
    'deprecated',
    'internal',
]);

function unwrap(text) {
    // JSDoc hard-wraps prose: join the soft line breaks, but leave code fences and lists alone.
    if (/```|(?:^|\n\n)\s*(?:[-*]|\d+\.)\s/.test(text)) return text;
    return text
        .split(/\n{2,}/)
        .map(paragraph => paragraph.replace(/\s*\n\s*/g, ' '))
        .join('\n\n');
}

function dedent(text) {
    const lines = text.split('\n');
    const indents = lines.filter(line => line.trim()).map(line => line.match(/^ */)[0].length);
    const indent = indents.length ? Math.min(...indents) : 0;
    return lines.map(line => line.slice(indent)).join('\n');
}

function linksToCode(text) {
    return text.replace(
        /\{@(?:link|linkcode|linkplain|see)\s+([^}|\s]+)(?:[|\s]+([^}]+))?\}/g,
        (_, target, label) => `\`${label ?? target}\``
    );
}

function commentText(comment) {
    return comment ? linksToCode(ts.getTextOfJSDocComment(comment) ?? '').trim() : '';
}

function jsDocNode(node) {
    let host = node;
    if (ts.isArrowFunction(node) || ts.isFunctionExpression(node)) {
        // the JSDoc of `const f = () => {}` and of `{ f: () => {} }` sits on the declaration around it
        host = ts.isVariableDeclaration(node.parent)
            ? node.parent
            : ts.isPropertyAssignment(node.parent)
              ? node.parent
              : node;
    }
    if (ts.isVariableDeclaration(host)) host = host.parent.parent;
    const docs = host.jsDoc;
    return docs?.[docs.length - 1];
}

function readDoc(node) {
    const doc = {
        summary: '',
        params: {},
        returns: '',
        default: null,
        examples: [],
        deprecated: null,
        internal: false,
    };
    const jsDoc = jsDocNode(node);
    if (!jsDoc) return doc;

    doc.summary = unwrap(commentText(jsDoc.comment));
    for (const tag of jsDoc.tags ?? []) {
        const name = tag.tagName.text;
        if (!KNOWN_TAGS.has(name)) {
            errors.push(
                `${where(node)}: unknown JSDoc tag @${name}. Wrap a \`@scope/package\` name in backticks, or remove the tag.`
            );
            continue;
        }
        const text = commentText(tag.comment);
        if (name === 'param') {
            doc.params[tag.name.getText()] = unwrap(text.replace(/^-\s*/, ''));
        } else if (name === 'returns' || name === 'return') {
            doc.returns = unwrap(text);
        } else if (name === 'default') {
            doc.default = text;
        } else if (name === 'example') {
            doc.examples.push(parseExample(dedent(text.replace(/^\s*\n/, '')).trimEnd()));
        } else if (name === 'deprecated') {
            doc.deprecated = unwrap(text) || 'Deprecated.';
        } else if (name === 'internal') {
            doc.internal = true;
        }
    }
    return doc;
}

function parseExample(text) {
    const fenced = text.match(/^```(\w*)\n([\s\S]*?)\n?```$/);
    return fenced
        ? { language: fenced[1] || 'typescript', code: fenced[2] }
        : { language: 'typescript', code: text };
}

function isHidden(member) {
    const flags = ts.getCombinedModifierFlags(member);
    return (
        !!(flags & (ts.ModifierFlags.Private | ts.ModifierFlags.Protected)) ||
        (member.name && ts.isPrivateIdentifier(member.name))
    );
}

function typeText(typeNode, fallbackNode) {
    return typeNode
        ? textOf(typeNode)
        : checker.typeToString(checker.getTypeAtLocation(fallbackNode));
}

function describeParam(param, doc) {
    const name = squash(param.name.getText());
    return {
        name: param.dotDotDotToken ? `...${name}` : name,
        type: typeText(param.type, param),
        optional: !!param.questionToken || !!param.initializer,
        default: param.initializer ? textOf(param.initializer) : null,
        description: doc.params[name] ?? '',
    };
}

function returnTypeText(decl) {
    if (decl.type) return textOf(decl.type);
    const signature = checker.getSignatureFromDeclaration(decl);
    return signature ? checker.typeToString(checker.getReturnTypeOfSignature(signature)) : 'void';
}

function paramListText(decl) {
    return decl.parameters
        .map(param => {
            const { name, type, optional } = describeParam(param, { params: {} });
            return `${name}${optional ? '?' : ''}: ${type}`;
        })
        .join(', ');
}

const typeParamsText = decl =>
    decl.typeParameters?.length ? `<${decl.typeParameters.map(textOf).join(', ')}>` : '';

// `(a: A) => R`, the way @zag-js/docs writes a function-typed member
const arrowTypeText = decl =>
    `${typeParamsText(decl)}(${paramListText(decl)}) => ${returnTypeText(decl)}`;

function describeSignature(name, decl, doc) {
    return {
        signature: `${name}${typeParamsText(decl)}(${paramListText(decl)}): ${returnTypeText(decl)}`,
        params: decl.parameters.map(param => describeParam(param, doc)),
        returns: { type: returnTypeText(decl), description: doc.returns },
    };
}

// `decls` are the overloads (or the single declaration) of one callable.
function describeCallable(name, decls) {
    const documented = decls.find(decl => jsDocNode(decl)) ?? decls[0];
    const doc = readDoc(documented);
    if (!doc.summary && !doc.internal)
        warnings.push(`${where(documented)}: ${name}() has no summary.`);
    return {
        name,
        summary: doc.summary,
        deprecated: doc.deprecated,
        examples: doc.examples,
        signatures: decls.map(decl => describeSignature(name, decl, doc)),
        internal: doc.internal,
    };
}

// A property of an interface, a type literal or a props type (Zag's inherited ones included).
function describeProperty(symbol) {
    const decl = symbol.declarations?.[0];
    if (!decl) return null;
    const doc = readDoc(decl);
    if (doc.internal) return null;

    let type;
    let optional = !!(symbol.flags & ts.SymbolFlags.Optional);
    if (ts.isMethodSignature(decl) || ts.isMethodDeclaration(decl)) {
        type = arrowTypeText(decl);
    } else if (ts.isPropertySignature(decl) || ts.isPropertyDeclaration(decl)) {
        type = typeText(decl.type, decl);
    } else if (ts.isGetAccessor(decl)) {
        type = typeText(decl.type, decl);
    } else {
        return null;
    }

    if (!doc.summary) warnings.push(`${where(decl)}: \`${symbol.getName()}\` has no summary.`);
    return {
        name: symbol.getName(),
        type,
        optional,
        readonly: !!(ts.getCombinedModifierFlags(decl) & ts.ModifierFlags.Readonly),
        default: doc.default,
        description: doc.summary,
        deprecated: doc.deprecated,
    };
}

function describeMembers(type) {
    return checker.getPropertiesOfType(type).map(describeProperty).filter(Boolean);
}

// ---------------------------------------------------------------------------------------------
// client API

function describeClass(name, decl) {
    const doc = readDoc(decl);
    if (doc.internal) return null;
    const baseClass = decl.heritageClauses?.find(
        clause => clause.token === ts.SyntaxKind.ExtendsKeyword
    )?.types[0];
    const entry = {
        kind: 'class',
        name,
        summary: doc.summary,
        deprecated: doc.deprecated,
        examples: doc.examples,
        declaration: [
            ts.getCombinedModifierFlags(decl) & ts.ModifierFlags.Abstract ? 'abstract ' : '',
            `class ${name}${typeParamsText(decl)}`,
            baseClass ? ` extends ${textOf(baseClass)}` : '',
        ].join(''),
        constructorParams: [],
        properties: [],
        methods: [],
    };
    if (!doc.summary) warnings.push(`${where(decl)}: class ${name} has no summary.`);

    const methods = new Map();
    for (const member of decl.members) {
        if (isHidden(member)) continue;
        const flags = ts.getCombinedModifierFlags(member);
        const isStatic = !!(flags & ts.ModifierFlags.Static);

        if (ts.isConstructorDeclaration(member)) {
            const constructorDoc = readDoc(member);
            entry.constructorParams = member.parameters.map(param =>
                describeParam(param, constructorDoc)
            );
            for (const param of member.parameters) {
                const paramFlags = ts.getCombinedModifierFlags(param);
                if (
                    !(paramFlags & ts.ModifierFlags.Readonly) &&
                    !(paramFlags & ts.ModifierFlags.Public)
                ) {
                    continue;
                }
                if (paramFlags & (ts.ModifierFlags.Private | ts.ModifierFlags.Protected)) continue;
                entry.properties.push(describeParam(param, constructorDoc));
            }
        } else if (ts.isPropertyDeclaration(member) || ts.isGetAccessorDeclaration(member)) {
            const memberDoc = readDoc(member);
            if (memberDoc.internal) continue;
            if (!memberDoc.summary)
                warnings.push(`${where(member)}: \`${member.name.getText()}\` has no summary.`);
            entry.properties.push({
                name: `${isStatic ? 'static ' : ''}${member.name.getText()}`,
                type: typeText(member.type, member),
                optional: !!member.questionToken,
                readonly:
                    ts.isGetAccessorDeclaration(member) || !!(flags & ts.ModifierFlags.Readonly),
                default: memberDoc.default,
                description: memberDoc.summary,
                deprecated: memberDoc.deprecated,
            });
        } else if (ts.isMethodDeclaration(member) || ts.isMethodSignature(member)) {
            const methodName = member.name.getText();
            const key = `${isStatic ? 'static ' : ''}${methodName}`;
            methods.set(key, [...(methods.get(key) ?? []), member]);
        }
    }

    for (const [key, decls] of methods) {
        const { internal, ...callable } = describeCallable(decls[0].name.getText(), decls);
        if (!internal) entry.methods.push({ ...callable, title: `${key}()` });
    }
    return entry;
}

function describeFunction(name, decls) {
    const { internal, ...callable } = describeCallable(name, decls);
    return internal ? null : { kind: 'function', ...callable };
}

function describeObject(name, variableDecl) {
    const doc = readDoc(variableDecl);
    if (doc.internal) return null;
    if (!doc.summary) warnings.push(`${where(variableDecl)}: ${name} has no summary.`);

    const methods = [];
    for (const property of variableDecl.initializer.properties) {
        const propertyName = property.name.getText();
        let decls;
        if (ts.isMethodDeclaration(property)) {
            decls = [property];
        } else if (ts.isShorthandPropertyAssignment(property)) {
            const target = checker.getShorthandAssignmentValueSymbol(property);
            decls = (target?.declarations ?? []).filter(ts.isFunctionDeclaration);
        } else if (
            ts.isPropertyAssignment(property) &&
            (ts.isArrowFunction(property.initializer) ||
                ts.isFunctionExpression(property.initializer))
        ) {
            decls = [property.initializer];
        }
        if (!decls?.length) continue;

        const { internal, ...callable } = describeCallable(propertyName, decls);
        if (!internal) methods.push({ ...callable, title: `${propertyName}()` });
    }
    return {
        kind: 'object',
        name,
        summary: doc.summary,
        deprecated: doc.deprecated,
        examples: doc.examples,
        methods,
    };
}

function describeInterfaceLike(name, symbol, decl) {
    const doc = readDoc(decl);
    if (doc.internal) return null;
    if (!doc.summary) warnings.push(`${where(decl)}: ${name} has no summary.`);

    const entry = {
        kind: ts.isInterfaceDeclaration(decl) ? 'interface' : 'type',
        name,
        summary: doc.summary,
        deprecated: doc.deprecated,
        examples: doc.examples,
        declaration: null,
        members: [],
    };

    const declared = checker.getDeclaredTypeOfSymbol(symbol);
    const isObject = !!(declared.flags & ts.TypeFlags.Object) && !declared.isUnion();
    const properties = isObject ? checker.getPropertiesOfType(declared) : [];
    // A mapped or conditional alias has synthetic members without a declaration: only the `declaration` is useful.
    if (properties.length && properties.every(property => property.declarations?.length)) {
        entry.members = properties.map(describeProperty).filter(Boolean);
    } else if (ts.isTypeAliasDeclaration(decl)) {
        entry.declaration = `type ${name}${typeParamsText(decl)} = ${textOf(decl.type)}`;
    }
    return entry;
}

// The package a re-export names (`export { mergeProps } from '@zag-js/vanilla'`), not the file it ends up in.
function reexportedFrom(exported) {
    for (let symbol = exported; symbol.flags & ts.SymbolFlags.Alias;) {
        const decl = symbol.declarations?.[0];
        const specifier =
            decl && ts.isExportSpecifier(decl) ? decl.parent.parent.moduleSpecifier?.text : null;
        if (specifier && !specifier.startsWith('.')) return specifier;
        symbol = checker.getImmediateAliasedSymbol(symbol);
    }
    return null;
}

function describeExport(name, exported) {
    const symbol =
        exported.flags & ts.SymbolFlags.Alias ? checker.getAliasedSymbol(exported) : exported;
    const decls = symbol.declarations ?? [];
    const first = decls[0];
    if (!first) return null;

    if (isExternal(first)) return { kind: 'external', name, package: reexportedFrom(exported) };

    if (symbol.flags & ts.SymbolFlags.Class) return describeClass(name, first);
    if (symbol.flags & ts.SymbolFlags.Function) {
        return describeFunction(
            name,
            decls.filter(
                decl => (ts.isFunctionDeclaration(decl) && !decl.body) || decls.length === 1
            )
        );
    }
    if (symbol.flags & ts.SymbolFlags.Interface) {
        return describeInterfaceLike(name, symbol, decls.find(ts.isInterfaceDeclaration));
    }
    if (symbol.flags & ts.SymbolFlags.TypeAlias) return describeInterfaceLike(name, symbol, first);
    if (
        symbol.flags & ts.SymbolFlags.Variable &&
        ts.isVariableDeclaration(first) &&
        first.initializer
    ) {
        if (ts.isObjectLiteralExpression(first.initializer)) return describeObject(name, first);
        if (ts.isArrowFunction(first.initializer) || ts.isFunctionExpression(first.initializer)) {
            return describeFunction(name, [first.initializer]);
        }
    }

    errors.push(`${where(first)}: don't know how to document the export \`${name}\`.`);
    return null;
}

function buildClientApi() {
    const moduleSymbol = checker.getSymbolAtLocation(program.getSourceFile(clientIndex));
    const api = {};
    for (const exported of checker
        .getExportsOfModule(moduleSymbol)
        .sort((a, b) => a.name.localeCompare(b.name))) {
        const entry = describeExport(exported.name, exported);
        if (entry) api[exported.name] = entry;
    }
    return api;
}

// ---------------------------------------------------------------------------------------------
// self-made machines

// What every part of a stateful primitive renders the same way, so it needs no JSDoc of its own.
const STANDARD_DATA_ATTRIBUTES = {
    'data-disabled': 'Present when disabled',
    'data-invalid': 'Present when invalid',
    'data-valid': 'Present when valid',
    'data-required': 'Present when required',
    'data-readonly': 'Present when read-only',
    'data-touched': 'Present when touched',
    'data-dirty': 'Present when dirty',
    'data-filled': 'Present when filled',
    'data-focus': 'Present when focused',
};

function findComponentClass(file) {
    const sourceFile = program.getSourceFile(file);
    if (!sourceFile) return null;
    return sourceFile.statements.find(
        statement =>
            ts.isClassDeclaration(statement) &&
            statement.heritageClauses?.some(clause =>
                clause.types.some(type => type.typeArguments?.length === 2)
            )
    );
}

function enclosingMemberName(node) {
    for (let current = node.parent; current; current = current.parent) {
        const isNamed =
            ts.isMethodDeclaration(current) ||
            ts.isPropertyAssignment(current) ||
            ts.isFunctionDeclaration(current);
        if (isNamed && current.name) return current.name.getText();
    }
    return null;
}

function collectParts(root, parts, seen = new Set()) {
    const visit = node => {
        if (
            ts.isPropertyAccessExpression(node) &&
            ts.isIdentifier(node.expression) &&
            node.expression.text === 'parts'
        ) {
            parts.add(node.name.text);
        } else if (
            ts.isElementAccessExpression(node) &&
            ts.isIdentifier(node.expression) &&
            node.expression.text === 'parts' &&
            ts.isStringLiteralLike(node.argumentExpression)
        ) {
            parts.add(node.argumentExpression.text);
        } else if (ts.isCallExpression(node) && ts.isIdentifier(node.expression)) {
            // follow a local helper (`...getControlBaseProps()`) to the parts it spreads
            const target = checker.getSymbolAtLocation(node.expression)?.declarations?.[0];
            const body = target && (ts.isFunctionDeclaration(target) ? target : target.initializer);
            if (body && !seen.has(body) && !isExternal(body)) {
                seen.add(body);
                visit(body);
            }
        }
        ts.forEachChild(node, visit);
    };
    visit(root);
}

function describeDataAttribute(name, symbol, argument, owner) {
    const decl = symbol.declarations?.[0];
    const doc = decl ? readDoc(decl) : null;
    let description = doc?.summary || STANDARD_DATA_ATTRIBUTES[name];
    if (!description) {
        errors.push(
            `${where(argument)}: ${owner} renders \`${name}\` without a description. Document the key with JSDoc.`
        );
        return '';
    }

    const type = checker.getTypeOfSymbolAtLocation(symbol, argument);
    const members = type.isUnion() ? type.types : [type];
    const values = members.filter(member => !(member.flags & ts.TypeFlags.Undefined));
    if (
        values.length > 1 &&
        values.every(member => member.isStringLiteral() && member.value !== '')
    ) {
        description += ` One of: ${values.map(member => `"${member.value}"`).join(', ')}.`;
    }
    return description;
}

function buildMachine(dir) {
    const name = path.basename(dir);
    const src = path.join(dir, 'src');
    const component = findComponentClass(path.join(dir, `${name}.ts`));
    if (!component) {
        errors.push(
            `${name}: no component class with <Props, Api> type arguments found in ${name}.ts.`
        );
        return null;
    }
    const heritage = component.heritageClauses
        .flatMap(clause => clause.types)
        .find(type => type.typeArguments?.length === 2);
    const [propsType, apiType] = heritage.typeArguments.map(node =>
        checker.getTypeFromTypeNode(node)
    );

    const connectFile = program.getSourceFile(path.join(src, `${kebab(name)}.connect.ts`));
    const anatomyFile = program.getSourceFile(path.join(src, `${kebab(name)}.anatomy.ts`));
    const anatomyParts = new Set(
        checker
            .getPropertiesOfType(
                checker.getTypeOfSymbol(
                    checker
                        .getExportsOfModule(checker.getSymbolAtLocation(anatomyFile))
                        .find(s => s.name === 'parts')
                )
            )
            .map(property => property.name)
    );

    const partGetters = new Set();
    const dataAttributes = {};
    const visit = node => {
        if (
            ts.isCallExpression(node) &&
            ts.isPropertyAccessExpression(node.expression) &&
            ts.isIdentifier(node.expression.expression) &&
            node.expression.expression.text === 'normalize'
        ) {
            const getter = enclosingMemberName(node);
            if (getter) partGetters.add(getter);

            const argument = node.arguments[0];
            const parts = new Set();
            collectParts(argument, parts);
            for (const part of parts) {
                if (!anatomyParts.has(part)) {
                    errors.push(`${where(node)}: \`parts.${part}\` is not in the ${name} anatomy.`);
                }
            }

            const attributes = Object.fromEntries(
                checker
                    .getPropertiesOfType(checker.getTypeAtLocation(argument))
                    .filter(property => property.name.startsWith('data-'))
                    .map(property => [
                        property.name,
                        describeDataAttribute(
                            property.name,
                            property,
                            argument,
                            `${name}.${[...parts].join('/')}`
                        ),
                    ])
            );
            for (const part of parts) {
                if (Object.keys(attributes).length) {
                    dataAttributes[pascal(part)] = {
                        ...dataAttributes[pascal(part)],
                        ...attributes,
                    };
                }
            }
        }
        ts.forEachChild(node, visit);
    };
    visit(connectFile);

    const context = {};
    for (const symbol of checker.getPropertiesOfType(propsType)) {
        const member = describeProperty(symbol);
        if (!member) continue;
        let type = member.type;
        if (member.optional && !/\bundefined\b/.test(type)) {
            type = `${/^(?:<.*?>)?\(.*=>/.test(type) ? `(${type})` : type} | undefined`;
        }
        context[member.name] = {
            type,
            description: member.description,
            ...(member.default ? { defaultValue: member.default } : {}),
        };
    }

    const api = {};
    for (const symbol of checker.getPropertiesOfType(apiType)) {
        if (partGetters.has(symbol.getName())) continue;
        const member = describeProperty(symbol);
        if (member) api[member.name] = { type: member.type, description: member.description };
    }

    return {
        file: `machines/${kebab(name)}.json`,
        data: { api: { api, context }, dataAttributes },
    };
}

function buildMachines() {
    const outputs = {};
    for (const entry of fs.readdirSync(primitivesRoot, { withFileTypes: true })) {
        const dir = path.join(primitivesRoot, entry.name);
        if (!entry.isDirectory() || !fs.existsSync(path.join(dir, 'src'))) continue;
        if (!fs.readdirSync(path.join(dir, 'src')).some(file => file.endsWith('.machine.ts')))
            continue;
        const machine = buildMachine(dir);
        if (machine) outputs[machine.file] = machine.data;
    }
    return outputs;
}

// ---------------------------------------------------------------------------------------------
// output

// Keeps the JSON small: a missing key reads as null, false, empty or no list in the templates.
const prune = value => {
    if (Array.isArray(value)) return value.map(prune);
    if (value && typeof value === 'object') {
        return Object.fromEntries(
            Object.entries(value)
                .filter(
                    ([, item]) =>
                        item !== null &&
                        item !== false &&
                        item !== '' &&
                        !(Array.isArray(item) && !item.length)
                )
                .map(([key, item]) => [key, prune(item)])
        );
    }
    return value;
};

const outputs = { 'client-api.json': prune(buildClientApi()), ...buildMachines() };
const serialized = Object.fromEntries(
    Object.entries(outputs).map(([file, data]) => [file, JSON.stringify(data, null, 4) + '\n'])
);

for (const warning of warnings) console.warn(`warning: ${warning}`);
for (const error of new Set(errors)) console.error(`error: ${error}`);
if (errors.length) process.exit(1);

const machinesDir = path.join(generatedRoot, 'machines');
const stale = fs.existsSync(machinesDir)
    ? fs
          .readdirSync(machinesDir)
          .map(file => `machines/${file}`)
          .filter(file => !(file in serialized))
    : [];

if (checkOnly) {
    const outdated = [
        ...Object.keys(serialized).filter(file => {
            const target = path.join(generatedRoot, file);
            return !fs.existsSync(target) || fs.readFileSync(target, 'utf8') !== serialized[file];
        }),
        ...stale,
    ];
    for (const file of outdated) console.error(`outdated: ${file}`);
    if (outdated.length || warnings.length) {
        if (outdated.length) console.error('Run `npm run docs:generate` and commit the result.');
        process.exit(1);
    }
    console.log('API docs are up to date.');
} else {
    for (const [file, content] of Object.entries(serialized)) {
        const target = path.join(generatedRoot, file);
        fs.mkdirSync(path.dirname(target), { recursive: true });
        fs.writeFileSync(target, content);
    }
    for (const file of stale) fs.rmSync(path.join(generatedRoot, file));
    console.log(
        `Generated ${Object.keys(serialized).length} files in ${path.relative(process.cwd(), generatedRoot)}.`
    );
}
