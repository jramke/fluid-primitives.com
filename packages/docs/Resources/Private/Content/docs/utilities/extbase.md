# Extbase

**A small client-side helper for POSTing to an Extbase controller action from your own code - e.g. an async search's `load` callback.**

## `extbase.post()`

```typescript
import { extbase } from 'fluid-primitives';

const response = await extbase.post(searchUrl, { q: filterText }, { signal });
```

Bracket-prefixes `data` under the URL's own Extbase argument namespace - the same `tx_{extension}_{plugin}[...]` convention `f:uri.action` embeds in the URL it builds - and sends it as a real `multipart/form-data` POST body, the same shape Extbase's argument mapper expects from an ordinary HTML form. `null`/`undefined` values in `data` are skipped. `init` is spread first, so it can extend the request (e.g. `{ signal }`, to make it abortable) without overriding the method/body `post()` sets.

For a URL that doesn't carry an `[action]`/`[controller]` pair in its query string, `data` is sent unprefixed - `post()` works as a plain "POST as FormData" helper for non-Extbase endpoints too.

On the PHP side, a plain typed action argument receives the value the same way regardless of whether it arrives via GET or POST:

```php
public function searchAction(string $q = ''): ResponseInterface
{
    // ...
}
```

### Why a POST body

A frontend action URL built with `f:uri.action`/`f:uri.link` carries a `cHash` computed from the arguments known at build time. Appending a query parameter to that URL afterward changes its parameter set without changing the hash, and TYPO3 rejects the mismatch with a 404. `cHash` only governs the GET query string, so a POST body is unaffected by it.

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

The lower-level piece `post()` is built on, for reading the prefix without sending a request:

```typescript
extbase.getArgumentPrefix(
    'https://example.com/?tx_docs_docs[action]=search&tx_docs_docs[controller]=CitySearch'
);
// -> 'tx_docs_docs'
```

Returns `null` for a URL with no `[action]`/`[controller]` pair in its query string.
