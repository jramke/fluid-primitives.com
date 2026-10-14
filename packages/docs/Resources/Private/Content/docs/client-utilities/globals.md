# Globals

**Reads the request-wide values your global context exposes to the client.**

Values that every component needs, or constants for your own entry files like icons, come from the [global context](/docs/core-concepts/context#content-global-context) on the server. Getters marked `#[ExposeToClient]` are written once into `window.FluidPrimitives.globals`, and `getGlobal()` reads them.

## Usage

```php
// GlobalContext.php
#[ExposeToClient(name: 'lightbox.icons')]
public function getLightboxIcons(): array
{
    return ['close' => '<svg>...</svg>'];
}
```

```typescript
import { getGlobal } from 'fluid-primitives';

const icons = getGlobal<Record<string, string>>('lightbox.icons', {});
const locale = getGlobal<string>('locale');
```

## getGlobal

```typescript
function getGlobal<T = unknown>(path: string): T | undefined;
function getGlobal<T>(path: string, fallback: T): T;
```

The path is a dot path. A key that literally contains a dot wins, otherwise the path walks objects and arrays segment by segment (`getGlobal('items.0.label')`). A missing segment returns `undefined`, or the fallback when you pass one, and never throws.

`locale` and `debug` are always available. Everything else is whatever your global context exposes.

## getGlobals

```typescript
function getGlobals(): FluidPrimitivesGlobals | null;
```

Returns the whole `globals` object, or `null` when the page has no `window.FluidPrimitives`. That is the case on a page where no component hydrates and no template reads a global, see [Hydration](/docs/core-concepts/hydration#content-globals). Custom entry code that can run on such a page should use `getGlobal()` with a fallback.

## devWarn

```typescript
import { devWarn, isDebug } from 'fluid-primitives';

devWarn('Missing data-id on the lightbox trigger', element);
```

`devWarn()` prints a `console.warn` prefixed with `[fluid-primitives]`, but only when the `debug` global is `true`, which is the case in the Development application context. `isDebug()` returns that flag. Use them in your own entry files for checks that should be silent in production. The library itself uses `devWarn()` for its own development hints, for example when `mount()` finds no hydration data for a root id.
