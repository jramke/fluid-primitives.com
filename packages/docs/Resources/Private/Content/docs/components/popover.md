# Popover

**An accessible popup anchored to a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Popover" } %}
{% component: "ui:popover.examples.simple" %}

## Features

- Focus is managed and can be trapped within the popover
- Supports modal and non-modal modes
- Supports custom positioning with placement options
- Pressing `Escape` closes the popover
- Automatically adjusts position to stay in viewport

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

## Examples

### With Close Button

Show a close button inside the popover.

```html
<ui:popover.root>
    <ui:popover.trigger asChild="{true}">
        <ui:button>Open Popover</ui:button>
    </ui:popover.trigger>
    <ui:popover.content showCloseButton="{true}">
        <ui:popover.title>Closeable</ui:popover.title>
        <ui:popover.description>Click the X button to close.</ui:popover.description>
    </ui:popover.content>
</ui:popover.root>
```

### Custom Positioning

Control where the popover appears relative to the trigger.

```html
<ui:popover.root positioning="{placement: 'right'}">
    <ui:popover.trigger asChild="{true}">
        <ui:button>Open Right</ui:button>
    </ui:popover.trigger>
    <ui:popover.content>
        <ui:popover.title>Right Position</ui:popover.title>
        <ui:popover.description>This popover opens to the right.</ui:popover.description>
    </ui:popover.content>
</ui:popover.root>
```

### Modal Mode

Make the popover modal to trap focus and block interaction with the rest of the page.

```html
<ui:popover.root modal="{true}">
    <ui:popover.trigger asChild="{true}">
        <ui:button>Open Modal Popover</ui:button>
    </ui:popover.trigger>
    <ui:popover.content>
        <ui:popover.title>Modal Popover</ui:popover.title>
        <ui:popover.description>Focus is trapped within this popover.</ui:popover.description>
    </ui:popover.content>
</ui:popover.root>
```

### Default Open

Set the popover to be open by default.

```html
<ui:popover.root defaultOpen="{true}">
    <ui:popover.trigger asChild="{true}">
        <ui:button>Already Open</ui:button>
    </ui:popover.trigger>
    <ui:popover.content>
        <ui:popover.title>Default Open</ui:popover.title>
        <ui:popover.description>This popover is open by default.</ui:popover.description>
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
