// Keeps the `<vite:asset entry="%componentPath%/<Name>.entry.ts" />` line in the registry in sync.
// `ui:add` replaces `%componentPath%` with the EXT: path the component is written to.
//
//   node registry-vite-asset.mjs           append the line to every registry root file that lacks it
//   node registry-vite-asset.mjs --check   exit non-zero unless every registry component has exactly one
//                                          such line, naming an entry file that is listed and exists
//   --registry <dir>                       registry folder, defaults to the library's
//
// The line comes from the docs-site copy of the component (Components/ui/<Name>/): whatever that file has
// after the last line of the registry file, with its `EXT:docs/...` path swapped for the token. This keeps
// wrappers such as Button's `<f:if>` around the tag. The two copies differ in classes on purpose.
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const docsRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const registryArg = process.argv.indexOf('--registry');
const registryRoot = path.resolve(
    registryArg > -1
        ? process.argv[registryArg + 1]
        : path.join(docsRoot, '../fluid-primitives/Resources/Private/Registry')
);
const uiRoot = path.join(docsRoot, 'Resources/Private/Components/ui');
const checkOnly = process.argv.includes('--check');
const token = '%componentPath%';

// registry.yaml is flat: `key:` / `name:` / `description:` / `files:` + `- file` lines.
function readRegistry() {
    const components = [];
    for (const line of fs
        .readFileSync(path.join(registryRoot, 'registry.yaml'), 'utf8')
        .split('\n')) {
        if (/^\S[^:]*:\s*$/.test(line)) components.push({ key: line.split(':')[0], files: [] });
        else if (/^\s+name:/.test(line)) components.at(-1).name = line.split(':')[1].trim();
        else if (/^\s+-\s/.test(line))
            components.at(-1).files.push(line.replace(/^\s+-\s+/, '').trim());
    }
    return components;
}

const errors = [];
let changed = 0;

for (const { key, name, files } of readRegistry()) {
    const dir = path.join(registryRoot, name);
    const entry = `${name}.entry.ts`;

    if (!checkOnly) {
        const docsDir = path.join(uiRoot, name);
        const rootFile = fs
            .readdirSync(docsDir)
            .find(
                f =>
                    f.endsWith('.fluid.html') &&
                    fs.readFileSync(path.join(docsDir, f), 'utf8').includes('<vite:asset')
            );
        if (!rootFile) {
            errors.push(`${key}: no docs copy in ${docsDir} contains a <vite:asset> line`);
            continue;
        }
        const file = path.join(dir, rootFile);
        const current = fs.readFileSync(file, 'utf8');
        if (!current.includes(token)) {
            const docsCopy = fs.readFileSync(path.join(docsDir, rootFile), 'utf8');
            const lastLine = current.trimEnd().split('\n').at(-1);
            const lastLineEnd = docsCopy.lastIndexOf(lastLine) + lastLine.length;
            const tail = docsCopy
                .slice(lastLineEnd)
                .replaceAll(`EXT:docs/Resources/Private/Components/ui/${name}`, token);
            if (lastLineEnd < lastLine.length || !tail.includes(token)) {
                errors.push(
                    `${key}: cannot find the vite:asset block after "${lastLine}" in the docs copy of ${rootFile}`
                );
                continue;
            }
            fs.writeFileSync(file, current + tail);
            changed++;
        }
    }

    const lines = files.flatMap(f => {
        const text = fs.existsSync(path.join(dir, f))
            ? fs.readFileSync(path.join(dir, f), 'utf8')
            : '';
        return text.split('\n').filter(l => l.includes('<vite:asset'));
    });
    const expected = `entry="${token}/${entry}"`;
    if (lines.length !== 1 || !lines[0].includes(expected)) {
        errors.push(
            `${key}: expected exactly one <vite:asset ${expected} />, found ${lines.length}: ${lines.join(' | ')}`
        );
    }
    if (!files.includes(entry) || !fs.existsSync(path.join(dir, entry))) {
        errors.push(`${key}: ${entry} is not listed in registry.yaml or missing on disk`);
    }
}

if (errors.length) {
    console.error(errors.join('\n'));
    process.exit(1);
}
console.log(
    checkOnly
        ? 'Registry vite:asset lines are in place.'
        : `Updated ${changed} registry root file(s).`
);
