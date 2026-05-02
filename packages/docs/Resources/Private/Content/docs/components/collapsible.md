# Collapsible

**A collapsible panel controlled by a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Collapsible" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Collapsible.examples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support
- Supports animation via CSS transitions or animations
- Supports custom open and collapsed heights/widths

## Installation

{% component: "ui:installationSection", arguments: { "name": "Collapsible" } %}

## Examples

### Default Open

Set the collapsible to be open by default.

{% component: "ui:componentExample", arguments: { "componentName": "Collapsible.examples.defaultOpen" } %}

### Disabled

Prevent the collapsible from being toggled.

{% component: "ui:componentExample", arguments: { "componentName": "Collapsible.examples.disabled" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Collapsible",
        "parts": [
            ["root", "Provides the collapsible state and wraps the trigger and content. Renders a `<div>` element."],
            ["trigger", "Toggles the collapsible open and closed. Renders a `<button>` element."],
            ["indicator", "Displays state-specific content inside the trigger. Use `state=\"open\"` and `state=\"closed\"`. Renders a `<span>` element."],
            ["content", "Contains the collapsible content region. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:collapsible.root>
    <primitives:collapsible.trigger>
        <primitives:collapsible.indicator state="closed" />
        <primitives:collapsible.indicator state="open" />
    </primitives:collapsible.trigger>
    <primitives:collapsible.content />
</primitives:collapsible.root>
```
