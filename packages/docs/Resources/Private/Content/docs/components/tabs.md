# Tabs

**A component for toggling between related panels on the same page.**

{% component: "ui:referenceButtons", arguments: { "name": "Tabs" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Tabs.examples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support with arrow keys
- Supports horizontal and vertical orientations
- Supports automatic and manual tab activation modes
- Content is lazy mounted by default
- Supports disabled tabs

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tabs" } %}

## Examples

### Vertical Orientation

Display tabs in a vertical layout.

{% component: "ui:componentExample", arguments: { "componentName": "Tabs.examples.vertical" } %}

### Disabled Tabs

Disable specific tabs to prevent interaction.

{% component: "ui:componentExample", arguments: { "componentName": "Tabs.examples.disabledTabs" } %}

### Manual Activation

Require pressing Enter or Space to activate tabs instead of activating on focus.

{% component: "ui:componentExample", arguments: { "componentName": "Tabs.examples.manualActivation" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Tabs",
        "parts": [
            ["root", "Provides shared tabs state and wraps the tab list and panels. Renders a `<div>` element."],
            ["list", "Groups the tab triggers. Renders a `<div>` element."],
            ["trigger", "Activates a tab and focuses its panel. Renders a `<button>` element."],
            ["indicator", "Displays the active tab indicator. Renders a `<div>` element."],
            ["content", "Displays the panel content for a tab. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:tabs.root>
    <primitives:tabs.list>
        <primitives:tabs.trigger />
        <primitives:tabs.indicator />
    </primitives:tabs.list>
    <primitives:tabs.content />
</primitives:tabs.root>
```
