# Typesafe client-side hydration props

## Context

`ComponentHydrationData.props` (`packages/fluid-primitives/Resources/Private/Client/src/types.ts:34-41`) is typed as `{ id: string; ids: {...}; [key: string]: unknown }`. Every prop a `ui:prop ... client="{true}"` declaration sends beyond `id`/`ids` collapses to `unknown` on the client, and `mountAll`/`mount` hand this straight into `new SomeComponent(props)` where the component expects a real, specific `Props` type (`select.Props` from `@zag-js/select`, or a hand-rolled `FieldArrayProps`, etc.).

This "works" today almost by accident: most Zag `Props` interfaces make everything but `id` optional, so an `unknown`-keyed bag satisfies them structurally with zero real checking. The only place this surfaces a real signal is when a primitive has a *required* non-`id` prop (Select's `collection`, and the hand-rolled Field/FieldArray's `name`) — currently patched ad hoc with `// @ts-expect-error` at each `*.entry.ts` call site. One such file (`packages/benchmarks/.../Select.entry.ts`) is missing that suppression and is a live, uncaught `tsc` error today — a small proof that the current setup catches nothing reliably.

Separately, `ui:prop` already permits arbitrary PHP types, including objects/DTOs (`ListCollection`, which works because it implements `JsonSerializable`). Nothing stops a user from marking a non-`JsonSerializable` object (e.g. Extbase's `FileReference`) `client="{true}"`. Traced through `HydrationScriptBuilder::toJson()` (`JSON_THROW_ON_ERROR`, one combined `json_encode()` for the whole page's hydration registry), this either silently serializes to `{}` (protected properties) or throws an uncaught `JsonException` that breaks hydration for *every* component on the page, not just the offending one — a correctness/robustness bug independent of typing, but one this typing work runs straight into.

The goal: make `props` genuinely typed at the `mountAll`/`Component` boundary, for both built-in primitives and user-authored custom components in third-party TYPO3 extensions, and close the object/model prop hole with an explicit, extensible conversion hook instead of a silent/uncontrolled failure.

Decisions already made with the user: ship the typed-boundary generator and the object-converter hook together (they share the same PHP collection code path), and commit generated files to the repo rather than generating them transiently at build time (matches this repo's existing `GenerateZagDocsCommand` generate-then-commit convention; no PHP dependency for pure-npm consumers).

## Key existing building blocks (reuse, don't reinvent)

- **`AbstractComponentCollection::getComponentDefinition($viewHelperName)`** (`packages/fluid-primitives/Classes/Component/AbstractComponentCollection.php`) — parses (not renders) a component's `.fluid.html` and returns `ArgumentDefinition[]`: name, PHP type-string, required/optional, default, and the `ClientArgumentAnnotation`/`ContextArgumentAnnotation`/`RequiredAtRuntimeArgumentAnnotation` markers. Already consumed, docs-only, by `packages/docs/Classes/Components/Contexts/ComponentPropsTableContext.php`. This is the PHP-side source of truth — no persisted manifest needed.
- **Every primitive's own `<Name>.ts`** already declares/imports the real, authoritative value-type for its props: `export class Select extends Component<select.Props, select.Api>` (imported from `@zag-js/select`), or for the 6 hand-rolled non-Zag primitives (`Field`, `FieldArray`, `CheckboxGroup`, `Form`, `Input`, `Textarea`) a local `<Name>Props` interface in `./src/<name>.types.ts`. Confirmed uniform convention, no exceptions found. This is the TS-side source of truth for value types — codegen should `Pick` from it wherever the wire shape and the machine shape actually match (see "Wire type vs. machine type" below for where they don't).
- **`ComponentNameUtility::camelCaseToLowerCaseDashed()`** — already used to map a primitive's PascalCase folder name to its kebab-case `@zag-js/*` package name (via `ZagDocsMetadata::forPrimitive()`); reuse for the same mapping in codegen.
- **`ClientPropsContextExtractor`** (`Classes/Utility/ClientPropsContextExtractor.php`) — reflects `#[ExposeToClient]` context methods; a second, separate source of client props, not backed by any existing TS type (context-computed values like translations), so these need direct PHP-type→TS mapping, not `Pick`.
- **`ComponentHydrationCollector::collectForRootComponent()`** (`Classes/Service/Component/ComponentHydrationCollector.php`) — the single choke point where prop values are resolved into the JSON payload; this is where both the object-converter hook and the required-prop null-safety fix plug in.
- Registration precedent for new PHP services: this repo autowires everything under `Classes/*`; a new interface + `Registry/` collector class follows the existing `HydrationRegistry`/`PortalRegistry` pattern.

## Design

### 1. Object/model props: `ClientTypeAwareInterface` (own the class) + `ClientPropConverterInterface`/registry (don't own the class)

Two related but distinct mechanisms, both answering "what happens if a `ui:prop` is a PHP object/model and marked `client`?":

**`ClientTypeAwareInterface`** — a small interface a fluid-primitives (or user) DTO can implement directly on itself:
```php
interface ClientTypeAwareInterface
{
    public function getTsType(): string;      // e.g. 'ListCollectionData' or an inline literal '{ items: unknown[] }'
    public function getTsImport(): ?string;    // e.g. "import type { ListCollectionData } from 'fluid-primitives/client';" — null for inline literals
}
```
Use this when you control the source class. Example — `ListCollection` (`Classes/Domain/Dto/ListCollection.php`) already implements `JsonSerializable`; it would additionally implement `ClientTypeAwareInterface`, pointing at a `ListCollectionData` type defined once in `Resources/Private/Client/src/types.ts`. Both `Select.hydration.ts` and `Combobox.hydration.ts` (both use `collection`) then import that one canonical type instead of the generator inventing a duplicated inline literal in each file — that's the entire reason `getTsImport()` exists: it's optional, and only matters when a shape is reused across multiple generated files; a one-off converter can just return an inline literal and leave it `null`.

**`ClientPropConverterInterface` + `Registry/ClientPropConverterRegistry`** — for types you *don't* control (Extbase `FileReference`, a vendor model, anything you can't add an interface to):
```php
interface ClientPropConverterInterface
{
    public function supports(mixed $value, ArgumentDefinition $definition): bool;
    public function convert(mixed $value): mixed;   // must return JSON-encodable scalar/array
    public function getTsType(): string;
    public function getTsImport(): ?string;
}
```
`ClientPropConverterRegistry` collects all tagged services implementing this interface (autoconfigured, `public: true`, same reasoning as this repo's existing `Factory/` classes), exposing `findFor(mixed $value, ArgumentDefinition $definition): ?ClientPropConverterInterface`.

Both mechanisms are consulted from the same place: `ComponentHydrationCollector::collectForRootComponent()`, at the point `$propsMarkedForClientValues` is built (~L62-70). For each client-marked object value: if it implements `ClientTypeAwareInterface`, use it directly; else check the converter registry; if a converter's `supports()` matches, use `convert()` for the runtime value. If the value is a non-`JsonSerializable` object matched by neither, throw a scoped exception immediately (component + prop name + PHP class) — instead of letting it reach the page-wide `HydrationScriptBuilder::toJson()` call, where today a failure corrupts hydration for the *entire* page, not just the offending component.

### Wire type vs. machine type — a correctness gap the naive "just Pick from the Props type" idea misses

`select.Props.collection` (the Zag `.d.ts`) is typed as a real `ListCollection<T>` class instance with methods — but `Select.ts`'s `transformProps()` only constructs that instance *after* hydration, from the raw JSON (`ListCollection::jsonSerialize()`'s output: `items`, etc.) that's actually sitting in `window.FluidPrimitives.hydrationData.select[id].props.collection`. So a naive `Pick<select.Props, 'collection'>` would generate the *wrong* type for that key — it describes the post-transform shape, not what's actually on the wire.

This generalizes: **any prop a component's own `transformProps()` touches needs its wire-time shape declared explicitly (via `ClientTypeAwareInterface`/converter), never `Pick`ed from the machine's final `Props` type.** Concretely, codegen's value-type resolution must run in this order per prop:
1. Does the prop's PHP type implement `ClientTypeAwareInterface`, or match a registered converter? → use its declared `getTsType()`/`getTsImport()`. Always wins, even if a same-named key exists on the primitive's own Props type.
2. Else, does a same-named key exist on the primitive's own `<Name>Props` type? → `Pick` from it.
3. Else → map the PHP scalar/enum/array type string directly.

### 2. Required-prop null-safety fix

`ComponentHydrationCollector` currently does `$arguments[$name] ?? $definition->getDefaultValue() ?? null`, then `array_filter` drops nulls — this treats an *explicitly-passed* `null` for a required (no-default, non-optional) prop the same as "absent," silently dropping it from the JSON rather than erroring. This undermines the "required" guarantee the generated TS types will assert. Fix: when a prop has no default and is not `optional`, but resolves to `null`, throw (same class of scoped exception as above) rather than silently omitting it.

Note the distinct rule for `#[ExposeToClient]` props: they bypass `array_filter` — by default a null-returning method sends `key: null` (nullable in TS), and `excludeIfNull: true` drops the key (optional in TS). These are two different presence mechanisms and the generator must model both correctly:
- `ui:prop`, no default, `optional="{false}"` → required key, non-null value type, **and now runtime-enforced**.
- `ui:prop` with a default → required key (present unless the default itself is null — treat as optional in that edge case).
- `ui:prop`, `optional="{true}"`, no default → optional key (`?`) in TS.
- `#[ExposeToClient]`, default (`excludeIfNull: false`) → always-present key, `T | null` value type.
- `#[ExposeToClient(excludeIfNull: true)]` → optional key (`?`), no `null` in the union.

### 3. Component enumeration utility

No existing API lists "every component name a collection knows about" — `getComponentDefinition()` requires the name up front. Add a small utility (inverse of the existing template-name resolution, following the filesystem-walk precedent already used by `packages/docs/Classes/Services/NavigationBuilder.php`) that walks `getTemplatePaths()->getTemplateRootPaths()` for root `.fluid.html` files and yields viewHelperNames. Works identically for third-party collections since `getTemplatePaths()` is a public interface method on `ComponentCollectionInterface` — nothing about component-collection registration is closed off (`ComponentPrimitivesCollection` itself registers via a plain `ext_localconf.php` Fluid namespace entry, not a proprietary service tag), so a user's own `AbstractComponentCollection` subclass in their own extension is discoverable the same way.

### 4. PHP CLI command: `ui:generate-hydration-types`

New Symfony `#[AsCommand]` console command, sibling to `packages/docs/Classes/Command/GenerateZagDocsCommand.php`. Confirmed safe to bootstrap outside a request — `getComponentDefinition()` only needs a bare `RenderingContext` with no HTTP/TSFE dependency.

Accepts a `--collection` option (fully-qualified class name of a `ComponentCollectionInterface` implementation, defaulting to `ComponentPrimitivesCollection`) so third-party extensions can point the same command at their own components, and an `--output` directory option (built-ins default to writing alongside each primitive; third-party use has no npm/tsdown pipeline to plug into, so output location must be explicit for that case).

For each root component name (via #3):
1. `getComponentDefinition()` → `ArgumentDefinition[]`.
2. Filter to `client`-annotated args + auto-injected `id`/`ids`, plus `#[ExposeToClient]` methods on the associated Context class.
3. Classify each prop's presence per the rules in #2.
4. Determine value type per the precedence order in #1 ("Wire type vs. machine type"): `ClientTypeAwareInterface`/converter first, then `Pick` from the primitive's own Props type (built-ins: parse the `extends (FieldAware)?Component<XProps, ...>` generic argument and its import — a simple, convention-reliable string/regex extraction given this codebase's consistent authoring pattern, no full TS AST tooling required), then direct PHP-type-string mapping (scalars 1:1, PHP backed-enum → union of case values, reusing the existing precedent in `packages/docs/Classes/Utility/DocsUtility.php::getCasesStringFromType()`). Fail generation with a clear message if an object-typed prop has neither a `ClientTypeAwareInterface` implementation nor a registered converter — surfacing the same gap from #1 at authoring/generation time instead of first render.
5. Emit one generated file per component, e.g. `Resources/Private/Primitives/Select/Select.hydration.ts`:
   ```ts
   // AUTO-GENERATED by `ddev composer ui:generate-hydration-types` — do not edit by hand.
   import type { Props as SelectProps } from '@zag-js/select';
   import type { ListCollectionData } from 'fluid-primitives/client';

   export type SelectHydrationProps =
       { id: string; ids: Record<string, string> } &
       { collection: ListCollectionData } &                          // from ListCollection's own ClientTypeAwareInterface, not Picked
       Partial<Pick<SelectProps, 'defaultValue' | 'disabled' | 'multiple'>>; // Picked directly — untouched by transformProps

   declare module 'fluid-primitives/client' {
       interface HydrationPropsRegistry {
           select: SelectHydrationProps;
       }
   }
   ```
   re-exported from `Select.ts` itself (required for built-ins: `tsdown.config.ts` builds one flat entry per primitive and consumers import via the npm subpath `fluid-primitives/select`, so a standalone un-exported file wouldn't reach `dist/select.d.ts`).
6. `--check` flag: regenerate into memory/temp and diff against committed output, exit non-zero on drift (mirrors this repo's existing `format` vs `format:check` split) — wire into `ddev composer run lint` and add a step to the `add-zag-primitive` skill's verification checklist.

### 5. Client-side: infer `props` type from the component-name string, no explicit generic needed

Rather than requiring `mountAll<SelectHydrationProps>('select', ...)` at every call site, each generated file registers its type into one merged interface via TS module augmentation, and `mountAll`/`mount` key off it with a conditional type:

```ts
// types.ts
export interface HydrationPropsRegistry {}  // empty; extended per-primitive via declare module

export function mountAll<K extends string>(
    componentName: K,
    callback: (data: {
        controlled: boolean;
        props: K extends keyof HydrationPropsRegistry ? HydrationPropsRegistry[K] : ComponentHydrationData['props'];
        createHydrator: () => ComponentHydrator;
    }) => Component<any, any> | void
): void
```
(same change to `mount`). Entry files then need no type annotation at all:
```ts
mountAll('select', ({ props }) => {
    const select = new Select(props); // props inferred as SelectHydrationProps — no more @ts-expect-error
    select.init();
    return select;
});
```
Components without generated types (not yet run through codegen) fall through to today's `unknown`-bag behavior automatically — graceful degradation, not a hard requirement to migrate everything at once.

**Risk to validate early:** `tsdown.config.ts` builds `select`, `accordion`, etc. as separate rolled-up dist entries, while `HydrationPropsRegistry` and `mountAll` live in the shared `client` entry. Module augmentation (`declare module 'fluid-primitives/client' { interface HydrationPropsRegistry {...} }`) needs to merge correctly across that multi-entry build in a consumer's own `tsc` run — this is a well-established TS pattern in general, but hasn't been verified against *this* specific build setup. Recommend a small spike (generate one primitive's augmentation, build with `tsdown`, consume from a scratch project, confirm `tsc` merges it) before committing to this as the primary mechanism. If it proves unreliable, fall back to the explicit-generic form (`mountAll<SelectHydrationProps>(...)`) as a documented pattern instead — strictly worse ergonomics, same safety.

This removes the existing `@ts-expect-error` suppressions at Select/Field/FieldArray entry files (both `packages/docs/.../Registry/*` and their `Components/ui/*` duplicates) and would have caught the currently-broken `packages/benchmarks/.../Select.entry.ts` at type-check time. `Component<Props, Api>`'s own generic (used inside `initMachine`, `transformProps`, etc.) is untouched — this change only affects the hydration boundary, not the component class's internal contract.

## Problems / open trade-offs worth flagging explicitly

- **Regex-based Props-type extraction is a convention bet, not a guarantee.** It relies on every primitive consistently writing `extends Component<XProps, XApi>` (or `FieldAwareComponent<...>`). Confirmed true for all current primitives, but a future hand-authored primitive that deviates could silently fall through to the type-string mapping path and produce a mismatched type without an obvious error. Worth adding a codegen sanity check: after generating `<Name>HydrationProps`, verify it's structurally assignable to the real `Props` type (a compile-time `satisfies`/assertion emitted into the generated file itself) so drift shows up as a `tsc` failure, not silently-wrong output.
- **Module-augmentation-based inference (§5) needs a build spike before being trusted as the primary mechanism** — see the risk note above.
- **Fail-fast is a breaking behavior change.** Today, a bad object prop or an explicit-null required prop fails silently or with an opaque page-wide crash; after this change it throws a clear, scoped error earlier. That's strictly better for anyone hitting it, but a site that's currently "working" only because the bad data path was never exercised at runtime could start throwing where it didn't before. Should ship with a changelog callout, not silently as a patch-level fix.
- **`#[ExposeToClient]` props have no natural TS source of truth.** These always need either the direct type-string mapping path or a `tsType` hint parameter on the `#[ExposeToClient]` attribute itself, since they're context-computed values with no corresponding Props-type key to Pick from at all.
- **Converters must stay conservative about what they accept.** `supports()` is called for every client-marked object value; a converter that matches too broadly could silently transform something it shouldn't. Recommend converters match on exact class/interface, not duck-typing.
- **Distribution differs for third-party use.** Built-in primitives' generated types flow through the existing npm/tsdown pipeline automatically once re-exported from `<Name>.ts`. Third-party extensions have no equivalent pipeline — generated files are just plain relative-importable `.ts` the user's own bundler picks up from wherever `--output` points, and the `declare module 'fluid-primitives/client'` augmentation trick still applies (augmenting a published package's module from consumer code is standard), but should be documented explicitly as a distinctly different (simpler, more manual) workflow.

## Critical files

- `packages/fluid-primitives/Classes/Service/Component/ComponentHydrationCollector.php` — converter/type-aware hook + null-safety fix land here.
- `packages/fluid-primitives/Classes/Component/AbstractComponentCollection.php` — source of `getComponentDefinition()`; add the enumeration utility near here or as a sibling collaborator.
- `packages/fluid-primitives/Classes/Utility/ClientPropsContextExtractor.php` — reference for how `#[ExposeToClient]` is currently read; extend/reuse for codegen's second prop source.
- `packages/fluid-primitives/Classes/Domain/Dto/ListCollection.php` — first real consumer of `ClientTypeAwareInterface`.
- New: `Classes/Contracts/ClientTypeAwareInterface.php`, `Classes/Contracts/ClientPropConverterInterface.php`, `Classes/Registry/ClientPropConverterRegistry.php`.
- New: `Classes/Command/GenerateHydrationTypesCommand.php` (mirror `packages/docs/Classes/Command/GenerateZagDocsCommand.php`).
- `packages/fluid-primitives/Resources/Private/Client/src/lib/hydration.ts` — `mountAll`/`mount` generics + `HydrationPropsRegistry`.
- `packages/fluid-primitives/Resources/Private/Client/src/types.ts` — `ComponentHydrationData` stays as the general/fallback shape (still used by the generic `ui:hydrationData` ViewHelper path); add `HydrationPropsRegistry` and `ListCollectionData`.
- Every `*.entry.ts` under `packages/docs/Resources/Private/{Registry,Components/ui}/*` and `packages/benchmarks/Resources/Private/Components/*` — drop `@ts-expect-error` once inference is in place.
- `.claude/skills/add-zag-primitive/SKILL.md` — add a "run the hydration-types generator" step to scaffolding + verification.
- `CLAUDE.md` — add the new composer/npm commands to the Build/Development Commands section once implemented.

## Verification

- `ddev composer test:functional` — cover the new fail-fast paths (required-null prop, unconvertible object prop) with functional tests under `tests/Functional/Components/` or a new `tests/Functional/Hydration/` group, per this repo's existing test-quality guidelines (test the behavior — "throws when X" — not trivial getters).
- Small standalone spike (before full build-out) confirming module augmentation merges correctly across `tsdown`'s multi-entry output, per the risk noted in §5.
- `ddev npm run types` at the root, plus explicitly type-checking `packages/fluid-primitives` itself (currently only implicitly checked via `tsdown`'s dts emission) — confirm the generated types remove all `@ts-expect-error` usages without introducing new errors, and that the previously-broken `packages/benchmarks/.../Select.entry.ts` now either passes or fails loudly and correctly.
- Run `ddev composer run ui:generate-hydration-types -- --check` in CI/lint to catch drift between `ui:prop` declarations and committed generated types.
- Manual smoke test: hydrate a page with Select in a browser (dev server) to confirm runtime behavior is unchanged — this work only tightens compile-time types and PHP-side error handling, not runtime prop values for the happy path.
