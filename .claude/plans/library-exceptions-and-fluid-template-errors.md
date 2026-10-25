# Own exception hierarchy, and Fluid's exceptions for errors in templates

Replaces `fluid-exceptions-for-template-errors.md`.

## Problem

Two things, fixed together because they touch the same 84 `throw` sites.

1. **A template author gets no hint which template failed.** The library throws `\RuntimeException` and
   `\InvalidArgumentException` when a template is used the wrong way (a part outside its root, a missing
   prop, an unknown option). Fluid's `ViewHelperInvoker` only catches `Core\ViewHelper\Exception`, and its
   error handler then wraps it as `<class> in <template path>: <message>`. A plain PHP exception skips
   that. TYPO3 14.2 made this the intended way
   ([Feature-109167](https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Feature-109167-ImprovedExceptionsInFluidTemplates.html),
   Fluid 5.2, backported to 4.6). The installed Fluid is 5.3.1, the library requires `typo3fluid/fluid` `^5`.
2. **The library has no exceptions of its own.** Everything is a generic SPL exception, so nobody can
   `catch` "anything Fluid Primitives threw", and two different failures share one class and are told
   apart by message text only.

## The pattern

A **package exception hierarchy**: a marker interface the whole package implements, plus concrete
exceptions per case that extend the closest standard or framework exception. Symfony, Doctrine and Guzzle
do this (`Exception\ExceptionInterface`, `Exception\InvalidArgumentException extends \InvalidArgumentException
implements ExceptionInterface`); TYPO3 documents the base-class variant
(`\MyVendor\MyExtension\Exception extends \TYPO3\CMS\Core\Exception`).

The base-class variant does not fit literally here. An exception only gets the template path when it extends
Fluid's `Core\ViewHelper\Exception`, and a CLI or DTO exception should not depend on Fluid. PHP has single
inheritance, so a **marker interface** is the shared root and each class picks its own parent.

Proposed shape, to be settled once the inventory (step 1) is done. Namespace
`Jramke\FluidPrimitives\Exception` (singular, like `Enum`):

```
ExceptionInterface                    marker: catch (ExceptionInterface $e) takes everything the library throws

Raised while a template renders - extend Fluid's, so Fluid adds the template path:
  InvalidPropValueException           extends Fluid InvalidArgumentValueException   a prop value is wrong, an unknown option
  MissingPropException                extends Fluid MissingArgumentException        a required prop is missing
  MissingContextException             extends Fluid Core\ViewHelper\Exception       a part outside its root, no request
  HydrationException                  extends Fluid Core\ViewHelper\Exception       unserializable or null client prop, no rootId, duplicate rootId
  ... one class per distinct case the inventory finds, not one per site

No Fluid dependency (Domain/Dto, Registry, CLI, services):
  InvalidArgumentException            extends \InvalidArgumentException             what ListCollection and Positioning throw
  ConfigurationException              extends \RuntimeException                     ui:add, RegistryService, ComponentCollectionService
```

Every class implements `ExceptionInterface`. A DTO throws the pure `InvalidArgumentException`; the code that
calls it in a render translates it and adds what the DTO cannot know (the argument name).
`ConvertibleArgumentUtility` is the first such place: today it lets the DTO's `\InvalidArgumentException`
through (see "Where it stands").

Invariants (`$this->renderingContext ?? throw ...`, "expected `getAll()` to return an array") stay
`\LogicException`: nothing the author can do. Parse time (`nodeInitializedEvent`) keeps
`Core\Parser\Exception`. `AbstractComponentCollection::resolveViewHelperClassName()` already throws Fluid's
`UnresolvableViewHelperException`.

Keep the error code of every migrated site. New codes follow the `1_788_xxx_xxx` series already in use, and
a code is never reused (two sites already share `1_788_200_001` and `1_788_200_003`, fix that on the way).

## Where it stands

`ConvertibleArgumentUtility` (array|string to object, 2026-10-25) was first written with the Fluid
exception, then **rolled back on purpose** so that feature needs no `typo3fluid/fluid` bump: the DTOs
(`ListCollection`, `Positioning`) throw `\InvalidArgumentException` and the utility does not wrap it. This
plan brings the wrapping back, as `InvalidPropValueException` with the argument name
(`The argument "positioning" is invalid: Unknown positioning option "placment"`), together with the bump.

## Steps

1. **Take the inventory again** - do not trust the snapshot below, the code has moved on. At least:
    - `grep -rn "throw new" packages/*/Classes`, plus `?? throw`, `throw $`, and exceptions imported without a
      backslash (`new RuntimeException`, `new Exception`, `new UnresolvableViewHelperException`).
    - `grep -rn "catch (" packages/*/Classes`: a `catch (\RuntimeException)` stops catching once its throw
      moves. Two exist today and change with the throw they guard: `AbstractComponentContext::getGlobals()` and
      `FormContext`, both around `getRequest()` / `ExtbaseRequestResolver`.
    - `grep -rn "expectException(" packages/*/tests` (about 16 assert `\RuntimeException` or
      `\InvalidArgumentException`), `@throws` in docblocks, and `RuntimeException` / `InvalidArgumentException`
      in `packages/docs/Resources/Private/Content` and `skills/`.
    - Anything under `Resources/` that builds an exception (Fluid templates call ViewHelpers, so no).
      Classify each hit: raised in a render by what the author did, invariant, non-render, DTO, already Fluid.
2. **Create `Classes/Exception/`** with the interface and the classes the inventory needs. Decide the names
   here; one class per distinct case.
3. **Migrate by group**, one commit each: ViewHelpers; the component pipeline (`Service/Component`,
   `ContextService`, `ComponentRootContextFactory`); contexts and utilities; DTOs plus
   `ConvertibleArgumentUtility`. Move the `catch` sites with their throws.
4. **Raise `typo3fluid/fluid` to `^5.2`** in `packages/fluid-primitives/composer.json` (the granular classes
   come with 5.2). Check the CI matrix and the root `composer.lock`.
5. **Tests.**
    - Update the ones that assert the old classes to the new class (or its Fluid parent). The message
      assertions stay true, Fluid's wrapper contains the original message.
    - One test per group that proves the path: render a template **file** (a string template has no path) and
      assert the message contains it. Pattern:
        ```php
        $file = (string)tempnam(sys_get_temp_dir(), 'call-');
        file_put_contents($file, '<primitives:select.root collection="{items}" positioning="{placment: \'top\'}" />');
        try {
            $view = $this->getView();
            $view->assign('items', []);
            $view->getRenderingContext()->getTemplatePaths()->setTemplatePathAndFilename($file);
            $view->render();
            $this->fail('Not rejected.');
        } catch (ViewHelperException $exception) {
            $this->assertStringContainsString($file, $exception->getMessage());
        } finally {
            unlink($file);
        }
        ```
    - A unit test that every class in `Classes/Exception/` implements `ExceptionInterface`, and one that keeps
      the rule: no bare `throw new \RuntimeException` / `\InvalidArgumentException` left in `Classes/` outside an
      allowlist of the invariants (a token scan is enough, mago has no rule for it).
6. **`CLAUDE.md`**: add an "Exceptions" section under PHP Guidelines (draft below).
7. **Docs and changelog.** `ExceptionInterface` is public API (users `catch` it), say so on a docs page and in
   the changelog under Features. The changed classes and the Fluid bump go under Breaking changes: someone
   catching `\RuntimeException` around a render has to adapt, and `Core\ViewHelper\Exception` extends
   `\Exception`, not `\RuntimeException`.

## Draft for `CLAUDE.md`

> ### Exceptions
>
> Throw the library's own exceptions from `Jramke\FluidPrimitives\Exception\`, never a bare `\RuntimeException`
> or `\InvalidArgumentException`. Each implements `ExceptionInterface`, so a user can catch all of them, and
> one class stands for one case, so a user can tell cases apart without reading the message.
>
> Raised while a template renders by what the author did (a part outside its root, a wrong or missing prop,
> an unknown option): extend the matching `TYPO3Fluid\Fluid\Core\ViewHelper\` exception. Fluid's
> `ViewHelperInvoker` only catches those, and its error handler then adds the template path.
> Code without a Fluid dependency (`Domain/Dto`, registries, CLI) extends the SPL class. The code that calls a
> DTO in a render translates its exception and adds what the DTO cannot know, as `ConvertibleArgumentUtility`
> adds the argument name. Invariants stay `\LogicException`. Keep the error code when you move a throw.

## Snapshot of the sites (2026-10-25, 84 `throw new` in `packages/*/Classes`)

About 30 are invariants or non-render. The rest, by file:

- `ViewHelpers/`: `AsChild` (46), `Attributes` (108, 139), `Call` (47, 63, 66), `Context` (50, 55, 59), `Error` (36),
  `ExposeToClient` (37, 44), `FileUploadDeleteCheckbox` (74, 82, 94), `ListCollection` (82, 93), `Prop` (80, 87, 94,
  101), `Ref` (190, 245), `Template` (193), `UseProps` (91, 119, 186).
- Component pipeline: `Service/ContextService` (56), `Service/Component/ComponentIdentityResolver` (53),
  `ComponentHydrationCollector` (47, 125), `ClientPropValueResolver` (34, 70), `AsChildAttributeSpreader` (40),
  `Registry/HydrationRegistry` (81, duplicate `rootId`).
- Contexts and utilities: `Contexts/FormContext` (133), `Contexts/AbstractComponentContext` (73, no request),
  `Utility/ExtbaseRequestResolver` (20, 25), `Utility/PropsUtility` (94), `Utility/ClientPropsContextExtractor`
  (45, development only).
- DTOs: `Domain/Dto/ListCollection`, `Domain/Dto/Positioning`.
- Non-render, probably `ConfigurationException`: `Command/*`, `Service/RegistryService`,
  `Service/ComponentCollectionService`, and the type generator in `fluid-types`.

Not part of this plan: the exceptions of the client TypeScript code.

## Done when

- The inventory commands of step 1 find only the invariants and the allowlisted places.
- `catch (ExceptionInterface $e)` around a render takes every exception the library throws.
- `CLAUDE.md` carries the rule, the docs name `ExceptionInterface`, the changelog lists the breaking change.
