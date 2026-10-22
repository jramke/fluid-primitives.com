# Getting Started

Install Fluid Primitives, register your component collection and build your first component.

## Prerequisites

- TYPO3 14+
- PHP 8.3+
- Familiarity with [Fluid Components](https://docs.typo3.org/other/typo3fluid/fluid/main/en-us/Usage/Components.html)
- Recommended: a JavaScript build setup, for example Vite with [praetorius/vite-asset-collector](https://packagist.org/packages/praetorius/vite-asset-collector)

## Installation

Install both the PHP and JavaScript packages:

```bash
composer require jramke/fluid-primitives:"^1.0@beta"
npm install fluid-primitives@beta
```

{% component: "ui:alert", arguments: {"title": "Version Match", "text": "Keep the Composer and npm package versions in sync to avoid compatibility issues.", "variant": "warning"} %}

Your sitepackage should require `jramke/fluid-primitives` in its own `composer.json`. Otherwise overrides, like the [global context](/docs/core-concepts/context#content-global-context), could load before the library and lose.

### TypeScript

Using TypeScript? Add the optional [jramke/fluid-types](https://packagist.org/packages/jramke/fluid-types) package to generate typed client props for `mountAll`/`mount`. Read more about [Generating TypeScript Types](/docs/core-concepts/arguments#content-generating-typescript-types).

```bash
composer require --dev jramke/fluid-types
```

### Canary builds

Need a fix before the next release? Use the canary builds:

```bash
composer require jramke/fluid-primitives:"dev-main"
npm install fluid-primitives@next
```

## Setup

### 1. Create a component collection

Create a `ComponentCollection` class in your sitepackage to register your component paths and contexts:

```php
<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Components;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('my_extension', 'Resources/Private/Components/ui'),
            ExtensionManagementUtility::extPath('my_extension', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }

    public function getContextNamespaces(): array
    {
        return [
            'MyVendor\\MyExtension\\Components\\Contexts',
        ];
    }
}
```

Registering `ui/` as its own root path makes `<ui:accordion.root>` resolve to `ui/Accordion/Root.html`. [File Structure](/docs/core-concepts/file-structure) shows the layout.

The collection extends `AbstractComponentCollection` of Fluid Primitives. A collection built on the Fluid core class, or registered in `Configuration/Fluid/ComponentCollections.php`, cannot resolve `ui:prop` and the other `ui:*` ViewHelpers.

### 2. Register the namespace

Add the `ui` namespace to your `ext_localconf.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] =
    'MyVendor\\MyExtension\\Components\\ComponentCollection';
```

{% component: "ui:alert", arguments: {"title": "Namespace Array", "text": "Fluid Primitives registers its own ViewHelpers under `ui` too. Append your collection with `[] =` to keep both.", "variant": "warning"} %}

### 3. Include the Site Set (or TypoScript manually)

Add the library's site set to the dependencies of your own set:

```yaml
name: my-vendor/my-extension
label: 'My Extension'
dependencies:
    - jramke/fluid-primitives
```

### 4. Optional: component settings

Expose custom settings to component templates:

```typoscript
plugin.tx_fluidprimitives {
    settings {
        privacyPageUid = 12
    }
}
```

These merge with `lib.contentElement.settings` and are available as `{settings}` in component templates.

## Your first component

A tooltip that shows a short text for a trigger.

```html
<ui:useProps name="primitives:tooltip.root" as="rootProps" />
<ui:prop name="content" type="string" />

<primitives:tooltip.root spreadProps="{rootProps}">
    <primitives:tooltip.trigger class="{ui:cn(value: 'tooltip-trigger {class}')}">
        <f:slot />
    </primitives:tooltip.trigger>
    <ui:portal>
        <primitives:tooltip.positioner class="tooltip-positioner">
            <primitives:tooltip.content class="tooltip">
                <primitives:tooltip.arrow class="tooltip-arrow" />
                {content}
            </primitives:tooltip.content>
        </primitives:tooltip.positioner>
    </ui:portal>
</primitives:tooltip.root>

<vite:asset entry="EXT:my_extension/Resources/Private/Components/ui/Tooltip/Tooltip.entry.ts" />
```

Use it anywhere in your templates. The slot is the label of the trigger, `content` is the text of the tooltip:

```html
<ui:tooltip content="Orders ship within 2 working days.">Delivery time</ui:tooltip>
```

Every prop of the primitive works on your component too, like `openDelay`, `disabled` or `positioning`:

```html
<ui:tooltip content="Free returns for 30 days" openDelay="200" positioning="{placement: 'bottom'}">
    Returns
</ui:tooltip>
```

The template uses:

- [ui:useProps](/docs/viewhelpers/useProps) - takes over the props of the tooltip primitive, so your component accepts them without declaring each one
- `spreadProps` - hands the taken-over props to the primitive
- [ui:prop](/docs/viewhelpers/prop) - defines your own `content` prop
- `primitives:tooltip.*` - the parts of the [tooltip primitive](/docs/components/tooltip), which bring the delays, keyboard support and ARIA attributes
- `<f:slot />` - renders the child content of the tag inside the trigger
- [ui:portal](/docs/viewhelpers/portal) - renders the tooltip at the end of `<body>`
- `<vite:asset>` - loads the entry file when the component is used

The entry file hydrates every tooltip on the page in the browser:

```typescript
import { mountAll } from 'fluid-primitives';
import { Tooltip } from 'fluid-primitives/tooltip';

mountAll('ui:tooltip', ({ props }) => new Tooltip(props).init());
```

[Hydration](/docs/core-concepts/hydration) covers the client-side setup in full, [Styling](/docs/core-concepts/styling) the data attributes you can target.

## Next steps

- [Core Concepts](/docs/core-concepts) - Composition, context and hydration
- [Components](/docs/components) - Browse available primitives
- [ViewHelpers](/docs/viewhelpers) - Reference for `ui:prop`, `ui:ref`, and more
