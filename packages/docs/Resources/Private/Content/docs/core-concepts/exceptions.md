# Exceptions

Everything Fluid Primitives throws implements `Jramke\FluidPrimitives\Exception\ExceptionInterface`. One class stands for one kind of mistake, so you can tell them apart without reading the message.

## Catch what the library throws

Catch the interface to handle every exception of the library at once:

```php
use Jramke\FluidPrimitives\Exception\ExceptionInterface;

try {
    $html = $view->render();
} catch (ExceptionInterface $exception) {
    // a part outside its root, a missing prop, an unknown option ...
}
```

When the failing ViewHelper sits in a template file, Fluid wraps the exception in its own `ViewHelper\Exception` that names the file. The exception of the library is then the `getPrevious()` of the one you catch. An exception thrown while a string template renders has no file to name and arrives as it is.

## The exceptions

| Class                            | Thrown when                                                                                          |
| -------------------------------- | ---------------------------------------------------------------------------------------------------- |
| `MissingContextException`        | A part or ViewHelper is used outside the component it belongs to.                                    |
| `MissingRequestException`        | A render has no request, or not an Extbase one, where one is needed. A `MissingContextException`.    |
| `MissingPropException`           | A required prop or ViewHelper argument is not provided.                                              |
| `InvalidPropValueException`      | A prop or argument has a value that is not allowed, such as an unknown option or a bad CSS length.   |
| `InvalidSlotContentException`    | The slot content has the wrong shape, such as `asChild` without exactly one child element.           |
| `HydrationException`             | A component cannot hydrate: no or a duplicate `rootId`, or a prop value that cannot be sent as JSON. |
| `UnresolvableComponentException` | A component is used but its template file does not exist.                                            |
| `TemplateErrorException`         | A template stops the render on purpose with `ui:error`.                                              |
| `InvalidArgumentException`       | A value passed to a DTO or registry, such as `TagAttributes`, is not valid.                          |
| `ConfigurationException`         | The project setup does not fit, such as no package to put a component in with `ui:add`.              |

The first eight extend the matching exception of Fluid's `Core\ViewHelper` namespace, which is what makes Fluid name the template. `InvalidArgumentException` and `ConfigurationException` extend the SPL classes of the same name.

## Throw them from your own components

Throw one of these classes in a context or ViewHelper of your own, so a template author sees which template failed and you can catch your code together with the library's.
