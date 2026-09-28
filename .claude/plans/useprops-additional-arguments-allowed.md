# Make `additionalArgumentsAllowed` detection fully structural via `ui:useProps`'s new `as=` binding, using real argument annotations

## Context

Two earlier drafts were rejected: a merged `ui:delegate` ViewHelper (traded away `ui:useProps`'s JSX-`{...props}`-like flexibility), then a `{ui:spreadProps()}` marker ViewHelper (fixed robustness, but forced writing "spreadProps" twice and added a whole new class just to stand in for `{true}`).

The fix that stuck: give `ui:useProps` an `as="someName"` argument. When given, it binds the imported, forwardable prop names under that chosen name instead of the fixed, opaque `spreadProps` variable name used today:

```html
<ui:useProps name="primitives:tooltip.root" as="triggerProps" />

<primitives:tooltip.root spreadProps="{triggerProps}">
    <f:slot />
</primitives:tooltip.root>
```

`as=` has **no default value, deliberately** - that's what makes it a genuine signal rather than decoration. Its *presence* on `ui:useProps` (a real, parse-time-visible fact on a ViewHelper already whitelisted in the structural pre-parse) is the "I genuinely intend to delegate rendering, not just reuse prop declarations" fact the old `str_contains($templateString, 'spreadProps')` scan was trying to guess at from raw text. Giving it a generic default (e.g. always defaulting to some fixed name) would erase exactly this distinction - there'd be no way to tell `Dialog/Header.fluid.html`/`Footer.fluid.html` (which import `primitives:dialog.content`'s prop shape but never render it) apart from a genuine delegator, which is the one thing this whole redesign needs to get right. So `as=` stays required for anyone who wants the delegation-side behavior; omitted, `ui:useProps` behaves exactly as it does today (pure prop-declaration reuse, no `additionalArgumentsAllowed` implication).

**On the internal-marker mechanism itself**: the prior draft proposed a new `#__`-prefixed `Constants::ADDITIONAL_ARGUMENTS_ALLOWED_KEY`, smuggled into `$argumentDefinitions` as a fake entry and stripped before caching - the same shape as the already-dead `PROPS_MARKED_FOR_CLIENT_KEY`/`PROPS_MARKED_FOR_CONTEXT_KEY`. That pattern is exactly what the argument-annotation system (`ArgumentAnnotationInterface`, `ClientArgumentAnnotation`/`ContextArgumentAnnotation`/`RequiredAtRuntimeArgumentAnnotation` in `Classes/Annotations/`, already read by `ComponentArgumentResolver::resolve()` via `$argDef->getAnnotations()`) was built to replace - real, typed metadata attached to a real `ArgumentDefinition`, not a fake argument key masquerading as one. This plan uses that mechanism instead, and removes the two dead constants as a first, separate step so nothing new gets built next to a pattern that's being explicitly retired.

## Step 0: remove the dead markers first (own commit, before any new design work)

Delete `Constants::PROPS_MARKED_FOR_CLIENT_KEY`/`PROPS_MARKED_FOR_CONTEXT_KEY` and `PropsUtility::createPropsMarkedForClientArgumentDefinition()`/`createPropsMarkedForContextArgumentDefinition()`. Confirmed zero call sites anywhere outside their own definitions; `tests/Unit/PropsUtilityTest.php`'s one test only covers `cleanupNonForwardableProps()`, unaffected. This is its own commit specifically so the new annotation-based mechanism never sits next to the old smuggled-key pattern, even temporarily.

## Design

**1. Two new argument annotations, `Classes/Annotations/` - same shape as `ClientArgumentAnnotation`** (`implements ArgumentAnnotationInterface`, a `compile(): string` returning `'new ' . static::class . '()'`, no properties):

- `AdditionalArgumentsAllowedAnnotation` - attached to whichever real `ArgumentDefinition` is the reason a component allows undeclared arguments. Its mere presence on *any* argument definition in a component's set is the signal, read by scanning (mirrors exactly how `ComponentArgumentResolver::resolve()` already scans for `ClientArgumentAnnotation`/`ContextArgumentAnnotation`).
- `InternalBindingAnnotation` - marks an argument definition as `ui:useProps`'s own internal delegation wiring, not a real prop of the imported component. Needed specifically because the `as=`-bound argument's *name* is chosen per-template (unlike `rootId`/`context`/`component`/`settings`, which are fixed, enumerable names already covered by `Constants::NON_FORWARDABLE_PROPS`) - a dynamically-named entry can't be excluded from forwarding by a fixed list, so it needs to carry its own "don't forward me" fact instead.

**2. `Classes/ViewHelpers/UsePropsViewHelper.php`** - add an optional `as` argument (`string`, no default - see Context). In `nodeInitializedEvent()`:
- Keep the *whole* imported `ComponentDefinition` (not just `->getArgumentDefinitions()` as today).
- When `as` is given: register a new `ArgumentDefinition` under that name, default value `array_keys($forwardableArgumentDefinitions)` - replaces today's hardcoded `$mergedArgumentDefinitions['spreadProps'] = PropsUtility::createSpreadPropsArgumentDefinition(array_keys(...))` line. Always carries `InternalBindingAnnotation`. Also carries `AdditionalArgumentsAllowedAnnotation` when (and only when) `$externalComponentDefinition->additionalArgumentsAllowed()` is true. Deliberately not gated on `$isPrimitivesComponent` - generalizes to userland-to-userland imports, same fix every draft has included.
- When `as` is *not* given: none of the above - identical to today's behavior (`props:`/`defaults:` filtering, argument-definition merge only).

**3. `Classes/Utility/PropsUtility.php`** - extend `cleanupNonForwardableProps()` to also filter out any `ArgumentDefinition` carrying `InternalBindingAnnotation`, alongside the existing fixed-name check against `Constants::NON_FORWARDABLE_PROPS` (needs `ARRAY_FILTER_USE_BOTH` instead of `ARRAY_FILTER_USE_KEY` now that the filter inspects the value, not just the key). This is what stops a wrapper-of-a-wrapper from incorrectly inheriting `additionalArgumentsAllowed` purely because an *inner* wrapper's own `as=`-bound argument leaked through as if it were a real, forwardable prop.

**4. `Classes/ViewHelpers/AttributesViewHelper.php`** - add `nodeInitializedEvent()` (implementing `ViewHelperNodeInitializedEventInterface`), adds the `'attributes'` `ArgumentDefinition` carrying `AdditionalArgumentsAllowedAnnotation` unconditionally (no `InternalBindingAnnotation` - `attributes` is a real, public, forwardable prop, same as `class`/`asChild`). No early-return guard - `'attributes'` isn't reserved, so it can legitimately pre-exist from an unrelated import; gating on "not already present" would wrongly skip re-adding the annotation. Add `'attributes'` to `TemplateStructureViewHelperResolver::STRUCTURE_VIEWHELPERS` (`'useProps'` is already on the list).

**5. `Classes/Component/AbstractComponentCollection.php`** - `additionalArgumentsAllowed` becomes a scan, not a key lookup and strip:

```php
$additionalArgumentsAllowed = false;
foreach ($argumentDefinitions as $argDef) {
    foreach ($argDef->getAnnotations() as $annotation) {
        if ($annotation instanceof AdditionalArgumentsAllowedAnnotation) {
            $additionalArgumentsAllowed = true;
            break 2;
        }
    }
}
```

No `unset()` needed anywhere - nothing fake was ever added to `$argumentDefinitions`; the annotation lives on a real, legitimately-present argument. Both the `ui:attributes(` `str_contains` block (line 168-180) and the `spreadProps` + `<ui:useProps name="primitives:` combo (lines 184-189) are deleted outright. The unconditional `spreadProps` argument-definition fallback (lines 191-195) is also deleted - every component still gets a `spreadProps` argument, registered directly by `PropsUtility::createSpreadPropsArgumentDefinition()` the same way `class`/`rootId` etc. are, not conditionally patched in here. The `class` regex (line 157) is untouched - still the one genuinely irreducible case (a bare Fluid variable reference, no ViewHelper call site to hook into).

**6. `Classes/Service/Component/ComponentIdentityResolver.php:44-47`** - `spreadProps`'s value is now typically an array (the forwardable prop names), not literal `true`. Broaden:

```php
if (!empty($arguments['spreadProps'])) {
    $isRenderedAsRoot = false;
}
```

Small, single-line, same file/spot/purpose - just a truthy check instead of `=== true`.

**7. `Classes/Service/Component/ComponentArgumentResolver.php`** - `resolveSpreadProps()` reads `$propsToUse = Typed::arrayOrNull($arguments['spreadProps']) ?? [];` directly, removing the separate `$parentRenderingContext->getVariableProvider()->get('spreadProps')` lookup entirely (that lookup only existed because the array had nowhere else to travel from `ui:useProps` to the delegate call - now it arrives as the argument's own value, one lookup instead of two). `isExplicitlyProvidedAtCallSite()` untouched.

## Migration (packages/docs)

Two-token, mechanical change per call site: add `as="someName"` to `<ui:useProps>`, change the paired delegate tag's `spreadProps="{true}"` to `spreadProps="{someName}"`. No template restructuring - `class` overrides, other explicit arguments on the delegate tag, `props:`/`defaults:` filtering all stay exactly where they are. `Dialog/Header.fluid.html`/`Footer.fluid.html` are **not** touched - no `as=` needed since they never delegate.

Naming convention for `as=`: short, traceable to the part (`as="rootProps"`, `as="triggerProps"`), not the full primitive path. Update `.claude/skills/add-zag-primitive/SKILL.md` Step 9 with the new pattern.

## Tests (packages/fluid-primitives/tests/)

No existing test asserts on `additionalArgumentsAllowed()` directly - add direct coverage:

- A component using `{ui:attributes()}` directly → `additionalArgumentsAllowed()` true.
- A `ui:useProps ... as="x"` + `spreadProps="{x}"` wrapper around a component that allows additional arguments → true, and an unknown argument passed to the wrapper actually reaches the target.
- **The regression test for the case an earlier draft's naive fix broke**: a `Dialog/Header`/`Footer`-shaped fixture - `ui:useProps` (no `as=`) from a component that allows additional arguments, never delegates - still throws `InvalidArgumentValueException` for an unknown argument.
- A chained userland-to-userland `ui:useProps ... as=` import (A imports B, B imports C, C allows additional arguments, A delegates) → true on A, proving the generalization beyond `primitives:`-only imports.
- **The `InternalBindingAnnotation` regression test**: a wrapper-of-a-wrapper - W2 does `ui:useProps name="userland:w1"` (no `as=`, pure declaration reuse) where W1 itself used `as=` to delegate to something that allows additional arguments - W2 must **not** inherit `additionalArgumentsAllowed`, proving W1's internal `as=`-bound argument didn't leak through as a forwardable prop.
- Explicit per-call override on the delegate tag (e.g. `class="..."`) still wins over the forwarded value.
- The root-suppression check still correctly treats an array-valued `spreadProps` as "suppress root" - no spurious second hydration entry for a wrapper using the new `as=` pattern.
- Neither annotation-carrying argument definition (`attributes`, or an `as=`-bound one without its own further `as=`-import) ends up mis-exposed anywhere a real prop shouldn't be (docs/types generation, if convenient to check).

Rewrite `tests/Fixtures/UsePropsForwarding/**` fixtures to the new `as=` syntax - `UsePropsViewHelperTest`'s two existing regression tests should keep asserting the same rendered-HTML outcomes.

Run `ddev composer test` (unit + functional) from repo root.

## Verification

1. `ddev composer format && ddev composer run lint` in `packages/fluid-primitives`.
2. `ddev composer test` from repo root - full suite green, plus the new tests above.
3. `ddev npm run docs:build` from repo root - confirms the mechanical migration across all wrapper templates didn't break anything structurally.
4. Manually smoke-check one interactive component in the browser (e.g. Tooltip or Dialog via `ddev npm run docs:dev`) - confirms client-side hydration still finds the right elements post-migration, since the `ComponentIdentityResolver` truthy-check broadening is exactly the kind of thing that could silently misfire.
