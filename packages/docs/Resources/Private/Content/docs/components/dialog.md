# Dialog

**A popup that opens on top of the entire page.**

{% component: "ui:referenceButtons", arguments: { "name": "Dialog" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Dialog.examples.simple" } %}

## Features

- Focus is trapped within the dialog and restored when closed
- Supports modal and non-modal modes
- Scrolling is blocked when dialog is open (in modal mode)
- Pressing `Escape` closes the dialog
- Supports controlled and uncontrolled open state

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

## Examples

### Default Open

Set the dialog to be open by default.

```html
<ui:dialog.root defaultOpen="{true}">
    <ui:dialog.trigger asChild="{true}">
        <ui:button>Open Dialog</ui:button>
    </ui:dialog.trigger>
    <ui:dialog.content>
        <ui:dialog.header>
            <ui:dialog.title>Already Open</ui:dialog.title>
        </ui:dialog.header>
        <p>This dialog opens automatically.</p>
    </ui:dialog.content>
</ui:dialog.root>
```

### Prevent Close on Outside Click

Keep the dialog open when clicking outside.

```html
<ui:dialog.root closeOnInteractOutside="{false}">
    <ui:dialog.trigger asChild="{true}">
        <ui:button>Open Dialog</ui:button>
    </ui:dialog.trigger>
    <ui:dialog.content>
        <ui:dialog.header>
            <ui:dialog.title>Persistent Dialog</ui:dialog.title>
        </ui:dialog.header>
        <p>Click outside won't close this dialog.</p>
        <ui:dialog.footer>
            <ui:dialog.close asChild="{true}">
                <ui:button>Close</ui:button>
            </ui:dialog.close>
        </ui:dialog.footer>
    </ui:dialog.content>
</ui:dialog.root>
```

### Prevent Close on Escape

Disable closing the dialog with the Escape key.

```html
<ui:dialog.root closeOnEscape="{false}">
    <ui:dialog.trigger asChild="{true}">
        <ui:button>Open Dialog</ui:button>
    </ui:dialog.trigger>
    <ui:dialog.content>
        <ui:dialog.header>
            <ui:dialog.title>No Escape</ui:dialog.title>
        </ui:dialog.header>
        <p>Pressing Escape won't close this dialog.</p>
        <ui:dialog.footer>
            <ui:dialog.close asChild="{true}">
                <ui:button>Close</ui:button>
            </ui:dialog.close>
        </ui:dialog.footer>
    </ui:dialog.content>
</ui:dialog.root>
```

### Confirmation Dialog

A common pattern for confirming destructive actions.

```html
<ui:dialog.root>
    <ui:dialog.trigger asChild="{true}">
        <ui:button variant="destructive">Delete Item</ui:button>
    </ui:dialog.trigger>
    <ui:dialog.content>
        <ui:dialog.header>
            <ui:dialog.title>Are you sure?</ui:dialog.title>
            <ui:dialog.description> This action cannot be undone. This will permanently delete your item. </ui:dialog.description>
        </ui:dialog.header>
        <ui:dialog.footer>
            <ui:dialog.close asChild="{true}">
                <ui:button variant="outline">Cancel</ui:button>
            </ui:dialog.close>
            <ui:button variant="destructive">Delete</ui:button>
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
