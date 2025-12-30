# Collapsible

**A collapsible panel controlled by a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Collapsible" } %}

{% component: "ui:collapsible.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Collapsible" } %}

## Usage

```html
<ui:collapsible.root>
    <ui:collapsible.trigger>
        <ui:collapsible.triggerText openText="Show content" closeText="Hide content" />
    </ui:collapsible.trigger>
    <ui:collapsible.content> Your collapsible content here. </ui:collapsible.content>
</ui:collapsible.root>
```

## Anatomy

```html
<primitives:collapsible.root>
    <primitives:collapsible.trigger>
        <primitives:collapsible.triggerText />
    </primitives:collapsible.trigger>
    <primitives:collapsible.content />
</primitives:collapsible.root>
```
