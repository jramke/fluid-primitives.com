# Collapsible

**A collapsible panel controlled by a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Collapsible" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Collapsible.examples.simple" } %}

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

## Anatomy

```html
<primitives:collapsible.root>
    <primitives:collapsible.trigger>
        <primitives:collapsible.triggerText />
    </primitives:collapsible.trigger>
    <primitives:collapsible.content />
</primitives:collapsible.root>
```
