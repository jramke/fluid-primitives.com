# Dialog

**A popup that opens on top of the entire page.**

{% component: "ui:referenceButtons", arguments: { "name": "Dialog" } %}

{% component: "ui:dialog.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Dialog" } %}

## Usage

```html
<ui:dialog.root>
    <ui:dialog.trigger asChild="{true}">
        <ui:button>Open Dialog</ui:button>
    </ui:dialog.trigger>
    <ui:dialog.content>
        <ui:dialog.header>
            <ui:dialog.title>Dialog Title</ui:dialog.title>
            <ui:dialog.description>This is a dialog description.</ui:dialog.description>
        </ui:dialog.header>
        <p>Dialog content goes here.</p>
        <ui:dialog.footer>
            <ui:dialog.close asChild="{true}">
                <ui:button>Close</ui:button>
            </ui:dialog.close>
        </ui:dialog.footer>
    </ui:dialog.content>
</ui:dialog.root>
```

## Anatomy

```html
<primitives:dialog.root>
    <primitives:dialog.trigger />
    <primitives:dialog.backdrop />
    <primitives:dialog.positioner>
        <primitives:dialog.content>
            <primitives:dialog.title />
            <primitives:dialog.description />
        </primitives:dialog.content>
    </primitives:dialog.positioner>
</primitives:dialog.root>
```
