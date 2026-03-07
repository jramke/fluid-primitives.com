# Getting Started

Before you start, please take a look at the core [documentation of Fluid Components](https://docs.typo3.org/other/typo3fluid/fluid/main/en-us/Usage/Components.html) first.

## Installation

Install Fluid Primitives via Composer:

```bash
composer require jramke/fluid-primitives
```

Then you need to add the client-side files. Just add the `fluid-primitives` package to your `package.json`.

```bash
npm install fluid-primitives
```

{% component: "ui:alert", arguments: {"title": "Make sure you use the same versions for the npm and composer packages.", "variant": "warning"} %}

## Setup Component Collection

Create a `ComponentCollection` class in your sitepackage:

```php
<?php

declare(strict_types=1);

namespace MyVendor\MyExt\Components;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            $templatePaths->setTemplateRootPaths([
                ExtensionManagementUtility::extPath('ext', 'Resources/Private/Components/ui'),
                ExtensionManagementUtility::extPath('ext', 'Resources/Private/Components'),
            ]);
        ]);
        return $templatePaths;
    }

    public function getContextNamespaces(): array
    {
        return [
            'MyVendor\MyExt\Components\Contexts',
        ];
    }
}
```

Make sure we use the `AbstractComponentCollection` class from Fluid Primitives and not from Fluid's core.

See [File Structure Guide](./core-concepts/file-structure) for more information about why we used two template root paths.

See [Context Guide](./core-concepts/context) for more information about component contexts and the new `getContextNamespaces` method.

## Register Global Namespace

Register the `ui` namespace for easier component usage:

```php
// ext_localconf.php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = 'MyVendor\\MyExt\\Components\\ComponentCollection';
```

{% component: "ui:alert", arguments: {"title": "Fluid Primitives uses the `ui` namespace for its ViewHelpers. Add your path to the array rather than overwriting it.", "variant": "warning"} %}

## Optional: Component Settings

Provide custom settings that are exposed as `{settings}` inside the component templates (merged with `lib.contentElement.settings`). So by default you get the normal content element settings.

```typoscript
plugin.tx_fluidprimitives {
    settings {}
}
```

## First Component

Create a button component at `Resources/Private/Components/ui/Button/Button.html`:

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />

<ui:cn value="button button--{variant} {class}" as="class" />

<button class="{class}" {ui:attributes()}>
    <f:slot />
</button>
```

And use it in your templates like this:

```html
<ui:button variant="secondary" class="another-class" data-test="abc"> Hello World </ui:button>
```
