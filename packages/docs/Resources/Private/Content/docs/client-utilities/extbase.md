# Extbase

**Sends GET and POST requests to an Extbase controller action from your own code, for example from the `load` callback of an async search.**

## `extbase.post()`

```typescript
import { extbase } from 'fluid-primitives';

const response = await extbase.post(searchUrl, { q: filterText }, { signal });
```

`post()` nests `data` under the Extbase argument namespace of the URL, the `tx_{extension}_{plugin}[...]` that `f:uri.action` builds into it, and sends a `multipart/form-data` body, the shape Extbase's argument mapper reads from an HTML form. The third argument takes `fetch` options such as `{ signal }`.

Nested objects and arrays are flattened into bracket notation, so an action that takes an object works as it is. `null` and `undefined` are skipped, a `File` or `Blob` is sent as it is:

```typescript
await extbase.post(url, { demand: { city: 'Berlin' }, ids: [1, 2] });
// -> tx_myextension_citysearch[demand][city]=Berlin, tx_myextension_citysearch[ids][0]=1, tx_myextension_citysearch[ids][1]=2
```

### Set the namespace yourself

A URL without an `[action]` or `[controller]` key in its query string, such as a pretty URL built by a route enhancer, has no namespace to read, so `data` is sent unprefixed. Pass the namespace as `prefix`, `get()` takes it too. An empty `prefix` sends `data` unprefixed, which makes `post()` a plain FormData POST for endpoints outside Extbase:

```typescript
await extbase.post(
    '/events/register',
    { email: 'anna@example.com' },
    { prefix: 'tx_myextension_eventregistration' }
);
// -> tx_myextension_eventregistration[email]=anna@example.com
```

### Post to an action that takes an object

`objectName` nests the data one level deeper, between the prefix and the keys:

```typescript
await extbase.post(
    url,
    { title: 'Summer meetup', address: { city: 'Berlin' } },
    { objectName: 'event' }
);
// -> tx_myextension_eventregistration[event][title]=Summer meetup
// -> tx_myextension_eventregistration[event][address][city]=Berlin
```

### Send the values of a form

The `values.toObject()` of a [`Form`](/docs/core-concepts/forms) carries Extbase's hidden fields (`__trustedProperties`, `__identity`) under a key named like the prefix. `post()` keeps that key below the prefix alone, so this sends the form the way the browser would:

```typescript
await extbase.post(api.getAction(), values.toObject(), {
    prefix: api.getFieldNamePrefix(),
    objectName: api.getObjectName(),
});
```

## `extbase.get()`

```typescript
const response = await extbase.get(searchUrl, { q: filterText }, { signal });
```

`get()` appends `data` as query parameters under the Extbase argument namespace of the URL, flattened like in `post()`. The URL can be relative, as `f:uri.action` returns it, or absolute.

### Avoid a cHash mismatch

A URL from `f:uri.action` carries a `cHash` computed from the arguments known when it was built. `get()` keeps the query of the URL, `cHash` included, so a parameter it adds fails the check and TYPO3 answers with a 404. Recommended: send the data with `post()`, since the `cHash` covers only the query string. To keep `get()`, exclude the parameter in your `settings.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_myextension_citysearch[q]';
```

The entry is an exact match. Prefix it with `^` to cover nested keys like `tx_myextension_citysearch[demand][city]`.

## `extbase.getArgumentPrefix()`

Reads the namespace that `get()` and `post()` use, without sending a request. It returns `null` for a URL without an `[action]` or `[controller]` key in its query string:

```typescript
extbase.getArgumentPrefix(
    'https://example.com/?tx_myextension_citysearch[action]=search&tx_myextension_citysearch[controller]=CitySearch'
);
// -> 'tx_myextension_citysearch'
```

## Handle errors

`get()` and `post()` return the native `Response` and reject on a network failure or an aborted `signal`. A non-2xx response resolves normally, so check `response.ok`.

A `422` response with a JSON object as body, which is what [`JsonValidationErrorsTrait`](/docs/core-concepts/forms) answers for failed validation, throws an `ExtbaseValidationError`. Its `errors` hold the messages by property path and its `response` is the unread `422` response:

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

A `Form` catches that error itself when it is thrown from `onSubmit`.

## Post to a domain model

An action argument that is a plain PHP class takes the data of `extbase.post()` without further setup. For a persisted entity (a class extending `AbstractEntity`, `AbstractDomainObject` or `AbstractValueObject`), Extbase only sets the properties that the controller allows:

```php
protected function initializeCreateAction(): void
{
    $this->arguments->getArgument('registration')
        ->getPropertyMappingConfiguration()
        ->allowProperties('name', 'email')
        ->setTypeConverterOption(
            PersistentObjectConverter::class,
            PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
            true,
        );
}
```

List the properties by name. `allowAllProperties()` would let a visitor set every property of the object, also those no field shows.

## Reference

{% component: "ui:apiReference", arguments: { "symbol": "extbase" } %}
