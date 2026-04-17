# Navigation Menu

**A collection of links and menus for website navigation.**

{% component: "ui:referenceButtons", arguments: { "name": "NavigationMenu" } %}

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenu.examples.withViewport", "withEntryFile": true } %}

## Features

- Supports simple inline dropdowns and shared viewport layouts
- Keyboard navigation with arrow keys, `Home`, and `End`
- Supports trigger-based menus and direct navigation links in the same list
- Animated indicator and shared viewport support
- Supports horizontal and vertical orientation

## Installation

{% component: "ui:installationSection", arguments: { "name": "NavigationMenu" } %}

## Examples

### Simple Dropdown

Render navigation items with inline dropdown content.

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenu.examples.simple" } %}

### Links Only

Use the navigation menu as a simple list of links, including a current page link.

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenu.examples.withLinks" } %}

### With Viewport

Use `withViewport="{true}"` to render dropdown content inside a shared viewport. This pattern works well for richer header navigation with animated transitions and an indicator.

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenu.examples.withViewport" } %}

## Anatomy

```html
<primitives:navigationMenu.root>
    <primitives:navigationMenu.list>
        <primitives:navigationMenu.item>
            <primitives:navigationMenu.trigger />
            <primitives:navigationMenu.content>
                <primitives:navigationMenu.link />
            </primitives:navigationMenu.content>
        </primitives:navigationMenu.item>

        <primitives:navigationMenu.item>
            <primitives:navigationMenu.link />
        </primitives:navigationMenu.item>
    </primitives:navigationMenu.list>
</primitives:navigationMenu.root>
```

When using the shared viewport pattern, add the optional viewport-related parts:

```html
<primitives:navigationMenu.root>
    <primitives:navigationMenu.list>
        <primitives:navigationMenu.item>
            <primitives:navigationMenu.trigger />
            <primitives:navigationMenu.triggerProxy />
            <primitives:navigationMenu.viewportProxy />
        </primitives:navigationMenu.item>
    </primitives:navigationMenu.list>

    <primitives:navigationMenu.indicator>
        <primitives:navigationMenu.arrow />
    </primitives:navigationMenu.indicator>

    <primitives:navigationMenu.viewportPositioner>
        <primitives:navigationMenu.viewport>
            <primitives:navigationMenu.content>
                <primitives:navigationMenu.link />
            </primitives:navigationMenu.content>
        </primitives:navigationMenu.viewport>
    </primitives:navigationMenu.viewportPositioner>
</primitives:navigationMenu.root>
```
