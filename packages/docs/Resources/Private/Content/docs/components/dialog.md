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

## Examples

### Alert Dialog

For critical confirmations or destructive actions, use `role="alertdialog"`. Alert dialogs differ from regular dialogs in important ways:

- **Automatic focus:** The close/cancel button receives focus when opened, prioritizing the safest action
- **Requires explicit dismissal:** Cannot be closed by clicking outside, only via button clicks or Escape key

{% component: "ui:componentExample", arguments: { "componentName": "Dialog.examples.alert" } %}

### Prevent Close on Outside Click

Keep the dialog open when clicking outside.

{% component: "ui:componentExample", arguments: { "componentName": "Dialog.examples.preventCloseOutside" } %}

### Prevent Close on Escape

Disable closing the dialog with the Escape key.

{% component: "ui:componentExample", arguments: { "componentName": "Dialog.examples.preventCloseEscape" } %}

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
