# Dialog

**A popup that opens on top of the entire page.**

{% component: "ui:referenceButtons", arguments: { "name": "Dialog" } %}

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.simple", "withEntryFile": true } %}

## Features

- Focus is trapped within the dialog and restored when closed
- Supports modal and non-modal modes
- Page scrolling is locked while a modal dialog is open
- Pressing `Escape` closes the dialog
- Opens from a trigger, `defaultOpen` or your own code

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Dialog" } %}

## Examples

### Confirm a destructive action

Set `role="alertdialog"` and `closeOnInteractOutside="{false}"` with `closeOnEscape="{false}"` for critical confirmations like deleting an item. The Cancel button receives focus when the dialog opens, so the safest action comes first.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.alert" } %}

### Open a dialog from a dialog

Put a `dialog.root` inside the content of another dialog. Each level opens on top of the previous one.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.nested" } %}

### Add a popover to a dialog

Use a [popover](/docs/components/popover) for small anchored extras like share options. The dialog keeps its focus management.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.withPopover" } %}

### Scroll the whole dialog

When the content is taller than the viewport, the dialog scrolls as one piece, header and footer included. The `dialog.positioner` is the scroll container.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.outsideScroll" } %}

### Scroll the dialog body

Keep the header and footer in view while the body scrolls: set a `max-height` on the dialog content and `overflow-y: auto` on the body.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.insideScroll" } %}

### Keep it open on outside click

Set `closeOnInteractOutside="{false}"` for a dialog the visitor should answer, like a cookie settings dialog. A click on the backdrop leaves it open.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.preventCloseOutside" } %}

### Keep it open on Escape

Set `closeOnEscape="{false}"` to ignore the <kbd>Escape</kbd> key.

{% component: "ui:componentExample", arguments: { "componentName": "DialogExamples.preventCloseEscape" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Dialog",
        "parts": [
            ["root", "Provides dialog state and context for the composed parts. Renders no wrapper element."],
            ["trigger", "Opens the dialog. Renders a `<button>` element."],
            ["backdrop", "Displays the overlay behind the dialog content. Renders a `<div>` element."],
            ["positioner", "Positions the dialog content within the viewport. Renders a `<div>` element."],
            ["content", "Contains the dialog surface and interactive content. Renders a `<div>` element."],
            ["title", "Provides the accessible title for the dialog. Renders a `<div>` element."],
            ["description", "Provides supporting descriptive text for the dialog. Renders a `<div>` element."],
            ["closeTrigger", "Closes the dialog when activated. Renders a `<button>` element."]
        ]
    }
%}
