# Getting Started

Get up and running with Fluid Primitives in just a few steps. This guide covers installation, basic setup, and creating your first component.

## Prerequisites

- TYPO3 13+
- PHP 8.2+
- Basic familiarity with [Fluid Components](https://docs.typo3.org/other/typo3fluid/fluid/main/en-us/Usage/Components.html)
- A modern JavaScript build setup like Vite, Webpack, etc. (Recommended)

## Installation

Install both the PHP and JavaScript packages:

```bash
composer require jramke/fluid-primitives
npm install fluid-primitives
```

{% component: "ui:alert", arguments: {"title": "Version Match", "text": "Keep the Composer and npm package versions in sync to avoid compatibility issues.", "variant": "warning"} %}

## Setup

### 1. Create a Component Collection

Create a `ComponentCollection` class in your sitepackage to register your component paths and contexts:

```php
<?php

declare(strict_types=1);

namespace MyVendor\MySitepackage\Components;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('my_sitepackage', 'Resources/Private/Components/ui'),
            ExtensionManagementUtility::extPath('my_sitepackage', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }

    public function getContextNamespaces(): array
    {
        return [
            'MyVendor\\MySitepackage\\Components\\Contexts',
        ];
    }
}
```

**Important:** Use `AbstractComponentCollection` from Fluid Primitives, not Fluid core.

Why two template paths? This lets you use `<ui:button>` instead of `<ui:ui.button>`. See [File Structure](/docs/core-concepts/file-structure) for details.

### 2. Register the Namespace

Add the `ui` namespace to your `ext_localconf.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] =
    'MyVendor\\MySitepackage\\Components\\ComponentCollection';
```

{% component: "ui:alert", arguments: {"title": "Namespace Array", "text": "Fluid Primitives already registers ViewHelpers under `ui`. Append to the array instead of overwriting it.", "variant": "warning"} %}

### 3. Optional: Component Settings

Expose custom settings to component templates:

```typoscript
plugin.tx_fluidprimitives {
    settings {
        # Your custom settings here
    }
}
```

These merge with `lib.contentElement.settings` and are available as `{settings}` in templates.

## Your First Component

Create a simple button at `Resources/Private/Components/ui/Button/Button.html`:

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />
<ui:prop name="type" type="string" optional="{true}" default="button" />

<button type="{type}" class="{ui:cn(value: 'btn btn--{variant} {class}')}" {ui:attributes()}>
    <f:slot />
</button>
```

Use it anywhere in your templates:

```html
<ui:button variant="secondary" data-analytics="cta"> Get Started </ui:button>
```

**What's happening:**

- [ui:prop](/docs/viewhelpers/ui-prop) defines the components arguments (props) with types and defaults
- [ui:attributes](/docs/viewhelpers/ui-attributes) forwards any extra attributes (like `data-*`)
- [ui:cn](/docs/viewhelpers/ui-cn) is a helper for conditional class names
- `{class}` is automatically available for additional classes
- `<f:slot />` renders child content

## Adding Interactive Components

For components with client-side behavior (accordion, dialog, tabs, etc.), you'll also need to initialize them in JavaScript.

Example with a collapsible:

```html
<ui:collapsible.root>
    <ui:collapsible.trigger>Toggle content</ui:collapsible.trigger>
    <ui:collapsible.content> Hidden content that expands/collapses </ui:collapsible.content>
</ui:collapsible.root>
```

```typescript
import { mount } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

mount('collapsible', ({ props }) => {
    const collapsible = new Collapsible(props);
    collapsible.init();
    return collapsible;
});
```

See [Hydration](/docs/core-concepts/hydration) for the full picture on client-side setup.

## Next Steps

- [Core Concepts](/docs/core-concepts) - Understand composition, context, and hydration
- [Components](/docs/components) - Browse available primitives
- [ViewHelpers](/docs/viewhelpers) - Reference for `ui:prop`, `ui:ref`, and more
