# Popover

**An accessible popup anchored to a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Popover" } %}
{% component: "ui:popover.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Popover" } %}

## Usage

```html
<ui:popover.root>
    <ui:popover.trigger asChild="{true}">
        <ui:button>Open Popover</ui:button>
    </ui:popover.trigger>
    <ui:popover.content>
        <ui:popover.title>Popover Title</ui:popover.title>
        <ui:popover.description>This is the popover description.</ui:popover.description>
    </ui:popover.content>
</ui:popover.root>
```

## Anatomy

```html
<primitives:popover.root>
    <primitives:popover.trigger />
    <primitives:popover.positioner>
        <primitives:popover.content>
            <primitives:popover.arrow />
            <primitives:popover.closeTrigger />
            <primitives:popover.title />
            <primitives:popover.description />
        </primitives:popover.content>
    </primitives:popover.positioner>
</primitives:popover.root>
```
