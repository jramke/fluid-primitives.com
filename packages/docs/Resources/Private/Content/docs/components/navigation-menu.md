# Navigation Menu

**A collection of links and menus for website navigation.**

{% component: "ui:referenceButtons", arguments: { "name": "NavigationMenu" } %}

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenuExamples.withViewport", "withEntryFile": true } %}

## Features

- Supports simple inline dropdowns and shared viewport layouts
- Keyboard navigation with arrow keys, `Home`, and `End`
- Supports trigger-based menus and direct navigation links in the same list
- Animated indicator and shared viewport support
- Supports horizontal and vertical orientation

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "NavigationMenu" } %}

## Examples

### Add dropdowns to items

Give an item a `trigger` and a `content` to open a dropdown right below it. Open Products or Company below.

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenuExamples.simple" } %}

### Use plain links

An item can hold a plain `link`. Set `current="{true}"` on the link of the page the visitor is on to render `aria-current="page"`.

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenuExamples.withLinks" } %}

### Build a mega menu

Set `withViewport="{true}"` on the root and place the `content` parts in one shared `viewport`. The panels share one animated surface and an indicator marks the open item. Open Products, then Company.

{% component: "ui:componentExample", arguments: { "componentName": "NavigationMenuExamples.withViewport" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "NavigationMenu",
        "parts": [
            ["root", "Provides shared navigation menu state and semantics. Renders a `<nav>` element."],
            ["list", "Groups the top-level navigation items. Renders a `<ul>` element."],
            ["item", "Wraps a single navigation item and its related parts. Renders an `<li>` element."],
            ["trigger", "Opens and closes the related navigation content. Renders a `<button>` element."],
            ["triggerProxy", "Provides a hidden focus proxy for shared viewport behavior. Renders a `<span>` element."],
            ["link", "Renders a navigational link item. Renders an `<a>` element."],
            ["indicatorTrack", "Provides a track for the active-item indicator. Renders a `<div>` element."],
            ["indicator", "Displays the shared active-item indicator. Renders a `<div>` element."],
            ["itemIndicator", "Displays an indicator for an open item. Renders a `<div>` element."],
            ["content", "Contains the popup content for a navigation item. Renders a `<div>` element."],
            ["viewportPositioner", "Positions the shared viewport element. Renders a `<div>` element."],
            ["viewport", "Displays the shared viewport that can host item content. Renders a `<div>` element."],
            ["viewportProxy", "Provides a proxy element used to size and align the viewport. Renders a `<span>` element."],
            ["arrow", "Displays a decorative arrow for the indicator or content. Renders a `<div>` element."]
        ]
    }
%}
