# Collapsible

**A panel that a button expands and collapses.**

{% component: "ui:referenceButtons", arguments: { "name": "Collapsible" } %}

{% component: "ui:componentExample", arguments: { "componentName": "CollapsibleExamples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support
- Supports animation via CSS transitions or animations
- Supports custom open and collapsed heights/widths

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Collapsible" } %}

## Examples

### Start open

Set `defaultOpen` to render the panel expanded.

{% component: "ui:componentExample", arguments: { "componentName": "CollapsibleExamples.defaultOpen" } %}

### Disable the collapsible

Set `disabled` to keep the panel in its current state. The panel below stays closed.

{% component: "ui:componentExample", arguments: { "componentName": "CollapsibleExamples.disabled" } %}

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
