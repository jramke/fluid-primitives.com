# Harden HydrationScriptBuilder's JSON against `<script>` tag breakout

**Status:** Not started

## Goal

`HydrationRegistry` writes hydration data to the page as an inline `<script>` block, built by `HydrationScriptBuilder::build()`/`toJson()`. Both `json_encode()` calls explicitly pass `JSON_UNESCAPED_SLASHES`, and neither passes `JSON_HEX_TAG`/`JSON_HEX_AMP`/`JSON_HEX_APOS`/`JSON_HEX_QUOT`. If any string reaching this payload contains `</script>`, it breaks out of the inline script block and lets an attacker inject arbitrary HTML/JS into the page — a stored/reflected XSS, depending on where the string originated.

Fix: encode the JSON in a way that's safe to embed inside an HTML `<script>` element, without changing the payload's actual JSON *value* (parseability/structure/whitespace behavior in dev vs prod must stay identical).

## Where this lives

- [Classes/Registry/HydrationScriptBuilder.php:52-61](../../packages/fluid-primitives/Classes/Registry/HydrationScriptBuilder.php#L52) — `toJson()`, called twice from `build()` (once for `globals`, once for `hydrationData`).
- [Classes/Registry/HydrationRegistry.php](../../packages/fluid-primitives/Classes/Registry/HydrationRegistry.php) — `updateAssetCollector()` passes the built string straight to `AssetCollector::addInlineJavaScript()`, which writes it into the page verbatim inside a `<script>` tag; no escaping happens at that layer, so `HydrationScriptBuilder` is the only place this can be fixed.

## Why this is reachable, not just theoretical

The data flowing into `toJson()` comes from two sources, both of which can plausibly carry editor/content-controlled text:

- `ComponentHydrationCollector`'s `$propsMarkedForClientValues` — the raw values of any `<ui:prop client="{true}">` a component template author chooses to expose (see [Classes/Service/Component/ComponentHydrationCollector.php](../../packages/fluid-primitives/Classes/Service/Component/ComponentHydrationCollector.php)). Nothing stops a `client`-marked prop from being bound to a CMS field, a translation, or other less-trusted content at the call site — the library itself has no way to know or constrain that.
- `ClientPropsContextExtractor::extract()` — any `#[ExposeToClient]`-attributed Context method's return value.

## Root cause detail

`JSON_UNESCAPED_SLASHES` is the specific thing making this worse than a default `json_encode()` call: without it, PHP already escapes `/` to `\/` by default, which incidentally breaks `</script>` into `<\/script>` and neutralizes the breakout — this codebase has explicitly opted out of that incidental protection. The fix should not just remove `JSON_UNESCAPED_SLASHES` (that's an incidental side effect of an unrelated flag, easy to reintroduce by accident later) — add the purpose-built `JSON_HEX_*` flags so the intent is explicit and doesn't depend on flag ordering/interaction.

## Decision & implementation

In `toJson()` (`Classes/Registry/HydrationScriptBuilder.php:52`), add `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT` to both `json_encode()` calls (development and production branches), keeping the existing `JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT` flags as they are — this only adds encoding, it doesn't change whitespace/pretty-printing behavior:

```php
private function toJson(array $data, bool $development): string
{
    // HEX flags prevent a string containing "</script>" from breaking out of the inline
    // <script> tag this gets embedded into (see HydrationRegistry::updateAssetCollector()).
    $flags = JSON_THROW_ON_ERROR
        | JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT;

    if ($development) {
        return json_encode($data, $flags | JSON_PRETTY_PRINT);
    }
    return json_encode($data, $flags);
}
```

`JSON_HEX_TAG` turns `<`/`>` into `<`/`>` (so `</script>` can never appear literally), `JSON_HEX_AMP` turns `&` into `&`, and `JSON_HEX_APOS`/`JSON_HEX_QUOT` turn `'`/`"` into their unicode escapes — standard, well-known flags for exactly this "JSON embedded in an inline `<script>` tag" scenario. None of them change how `json_decode()`/`JSON.parse()` on the client parses the value back — only the *serialized textual form* changes, not the resulting JS object, so hydration data itself is unaffected.

## Verification

- Add a test to `tests/Unit/HydrationScriptBuilderTest.php` asserting a value containing `</script>` in the registry data does **not** appear literally in the built output (e.g. `assertStringNotContainsString('</script>', $js)` for a payload like `['accordion' => ['«f1»' => ['label' => '</script><script>alert(1)</script>']]]`), for both `development: true` and `development: false`.
- Confirm the two existing tests in that file still pass unchanged — they assert on `"locale": "en_US"` / `"locale":"en_US"` substrings, which are unaffected by the new flags (no special characters in `"locale"`/`"en_US"`).
- `ddev exec php vendor/bin/phpunit -c packages/fluid-primitives/phpunit.xml` — full suite must stay green.
- `ddev exec vendor/bin/mago lint packages/fluid-primitives` / `ddev exec vendor/bin/mago analyze packages/fluid-primitives` — confirm no new issues (this is a pure flag addition, shouldn't affect either).
- Manually sanity-check in a dev-mode page render that hydration still works (`window.FluidPrimitives.hydrationData`/`.globals` still parse correctly and components still hydrate) — the browser's JS engine handles `\uXXXX` escapes transparently, but worth a visual confirmation given this touches every page's inline hydration script.
