# Extbase

**A small client-side helper for sending GET and POST requests to an Extbase controller action from your own code - e.g. an async search's `load` callback.**

## `extbase.post()`

```typescript
import { extbase } from 'fluid-primitives';

const response = await extbase.post(searchUrl, { q: filterText }, { signal });
```

Bracket-prefixes `data` under the URL's own Extbase argument namespace - the same `tx_{extension}_{plugin}[...]` convention `f:uri.action` embeds in the URL it builds - and sends it as a real `multipart/form-data` POST body, the same shape Extbase's argument mapper expects from an ordinary HTML form. Nested objects and arrays are flattened into bracket notation, so an action taking an object works directly:

```typescript
await extbase.post(url, { demand: { city: 'Berlin' }, ids: [1, 2] });
// -> tx_docs_docs[demand][city]=Berlin, tx_docs_docs[ids][0]=1, tx_docs_docs[ids][1]=2
```

`null`/`undefined` values in `data` are skipped, and a `File`/`Blob` is sent as-is. `init` is spread first, so it can extend the request (e.g. `{ signal }`, to make it abortable) without overriding the method/body `post()` sets.

For a URL that doesn't carry an `[action]`/`[controller]` pair in its query string, `data` is sent unprefixed - `post()` works as a plain "POST as FormData" helper for non-Extbase endpoints too.

On the PHP side, a plain typed action argument receives the value the same way regardless of whether it arrives via GET or POST:

```php
public function searchAction(string $q = ''): ResponseInterface
{
    // ...
}
```

### Sending JSON instead

The body type follows the `Content-Type` header you pass, the same way TYPO3 core's `AjaxRequest` does it. Set a JSON one and `post()` sends `JSON.stringify(data)`, nested under the URL's Extbase prefix if it has one and unprefixed otherwise:

```typescript
await extbase.post(url, { q: filterText }, { headers: { 'Content-Type': 'application/json' } });
// -> {"tx_docs_docs":{"q":"..."}}
```

TYPO3 core doesn't decode JSON request bodies, so Extbase only sees such a payload if your project adds a middleware that fills the parsed body from it. Without one, use it for endpoints that read the raw body themselves (an eID, a middleware, an API route).

### Why a POST body

A frontend action URL built with `f:uri.action`/`f:uri.link` carries a `cHash` computed from the arguments known at build time. Appending a query parameter to that URL afterward changes its parameter set without changing the hash, and TYPO3 rejects the mismatch with a 404. `cHash` only governs the GET query string, so a POST body is unaffected by it.

## `extbase.get()`

```typescript
const response = await extbase.get(searchUrl, { q: filterText }, { signal });
```

Appends `data` as query parameters under the URL's Extbase argument namespace, using the same flattening as `post()`. The URL's existing query is kept untouched, `cHash` included - so a parameter added this way fails the `cHash` check (see [Why a POST body](#why-a-post-body)) unless TYPO3 is told to ignore it, either by excluding it in your `settings.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_docs_docs[q]';
```

(an exact match - prefix the entry with `^` to cover nested keys like `tx_docs_docs[demand][city]`), or by using `post()` instead.

## Errors

A non-2xx response rejects with an `ExtbaseHttpError` instead of resolving, so a forgotten `response.ok` check can't turn a failed request into a bogus success. The error carries the native `response` with its body still unread, plus a `status` shortcut:

```typescript
import { extbase, ExtbaseHttpError } from 'fluid-primitives';

try {
    await extbase.post(url, { email });
} catch (error) {
    if (error instanceof ExtbaseHttpError && error.status === 422) {
        const errors = await error.response.json();
        // ...
    } else {
        throw error;
    }
}
```

Anything `fetch` itself rejects with - a network failure, or the `AbortError` from an aborted `signal` - passes through unchanged.

## Posting domain models

Extbase's mass-assignment protection (`__trustedProperties`) applies to `PersistentObjectConverter`, the type converter used for persisted entities and value objects (classes extending `AbstractEntity`/`AbstractDomainObject`/`AbstractValueObject`). A plain PHP class that isn't one of those is mapped with the more permissive `ObjectConverter`, which has no such gate - `extbase.post()` maps onto an argument shaped like that without any extra configuration.

For an argument that is a persisted entity, the properties Extbase is allowed to set are configured directly on the controller, independently of any client-submitted token:

```php
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

protected function initializeCreateAction(): void
{
    $this->arguments->getArgument('demand')
        ->getPropertyMappingConfiguration()
        ->allowProperties('q', 'page') // or ->allowAllProperties()
        ->setTypeConverterOption(
            PersistentObjectConverter::class,
            PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
            true,
        );
}
```

The service that generates a real `__trustedProperties` token (`MvcPropertyMappingConfigurationService`) is marked internal and isn't part of TYPO3's public API, so it isn't something `extbase.post()` generates or relies on.

## `extbase.getArgumentPrefix()`

The lower-level piece `get()` and `post()` are built on, for reading the prefix without sending a request:

```typescript
extbase.getArgumentPrefix(
    'https://example.com/?tx_docs_docs[action]=search&tx_docs_docs[controller]=CitySearch'
);
// -> 'tx_docs_docs'
```

Returns `null` for a URL with no `[action]`/`[controller]` pair in its query string.
