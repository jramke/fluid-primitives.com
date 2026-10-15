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

For a URL that doesn't carry an `[action]` or `[controller]` key in its query string - a pretty URL built by a route enhancer, for example - there is no namespace to read, so `data` is sent unprefixed. Pass the namespace as the `prefix` option then, `get()` takes it too. An empty `prefix` sends `data` unprefixed, which makes `post()` a plain "POST as FormData" helper for non-Extbase endpoints:

```typescript
await extbase.post('/events/register', { q: 'x' }, { prefix: 'tx_docs_docs' });
// -> tx_docs_docs[q]=x
```

For an action that takes an object, `objectName` nests the data one level deeper, between the prefix and the keys:

```typescript
await extbase.post(url, { title: 'Hello', address: { city: 'Berlin' } }, { objectName: 'event' });
// -> tx_docs_docs[event][title]=Hello, tx_docs_docs[event][address][city]=Berlin
```

A top-level key that equals the prefix holds Extbase's own fields (`__trustedProperties`, `__identity`) and stays below the prefix alone. That is how the `values.toObject()` of a [`Form`](/docs/core-concepts/forms) arrives, its hidden fields included, so `extbase.post(api.getAction(), values.toObject(), { prefix: api.getFieldNamePrefix(), objectName: api.getObjectName() })` sends a form the way the browser would.

On the PHP side, a plain typed action argument receives the value the same way regardless of whether it arrives via GET or POST:

```php
public function searchAction(string $q = ''): ResponseInterface
{
    // ...
}
```

### Why a POST body

A frontend action URL built with `f:uri.action`/`f:uri.link` carries a `cHash` computed from the arguments known at build time. Appending a query parameter to that URL afterward changes its parameter set without changing the hash, and TYPO3 rejects the mismatch with a 404. `cHash` only governs the GET query string, so a POST body is unaffected by it.

## `extbase.get()`

```typescript
const response = await extbase.get(searchUrl, { q: filterText }, { signal });
```

Appends `data` as query parameters under the URL's Extbase argument namespace, using the same flattening as `post()`. The URL can be relative, as `f:uri.action` returns it, or absolute. Its existing query is kept untouched, `cHash` included - so a parameter added this way fails the `cHash` check (see [Why a POST body](#why-a-post-body)) unless TYPO3 is told to ignore it, either by excluding it in your `settings.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_docs_docs[q]';
```

(an exact match - prefix the entry with `^` to cover nested keys like `tx_docs_docs[demand][city]`), or by using `post()` instead.

## Errors

`get()` and `post()` return the native `Response` and, like `fetch`, reject on a network failure or an aborted `signal`. A non-2xx response resolves normally, so check `response.ok` yourself.

The exception is a `422` with a JSON object as body, what [`JsonValidationErrorsTrait`](/docs/core-concepts/forms) answers for failed validation. It is thrown as an `ExtbaseValidationError` whose `errors` hold the messages by property path and whose `response` is the unread `422` response:

```typescript
import { extbase, ExtbaseValidationError } from 'fluid-primitives';

try {
    await extbase.post(url, { person: { email } });
} catch (error) {
    if (error instanceof ExtbaseValidationError) {
        console.log(error.errors); // { 'person.email': ['Invalid email'] }
    }
}
```

A `Form` handles that error itself when it is thrown from `onSubmit`, so you do not have to catch it there.

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

Returns `null` for a URL with no `[action]` or `[controller]` key in its query string.

## Reference

{% component: "ui:apiReference", arguments: { "symbol": "extbase" } %}
