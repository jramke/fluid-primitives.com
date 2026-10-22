# Popover

**An accessible popup anchored to a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Popover" } %}

{% component: "ui:componentExample", arguments: { "componentName": "PopoverExamples.simple", "withEntryFile": true } %}

## Features

- Focus is managed and can be trapped within the popover
- Supports modal and non-modal modes
- Supports custom positioning with placement options
- Pressing `Escape` closes the popover
- Automatically adjusts position to stay in viewport

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Popover" } %}

## Examples

### Add a close button

Set `showCloseButton` on `popover.content` to add a close button in the corner.

{% component: "ui:componentExample", arguments: { "componentName": "PopoverExamples.withCloseButton" } %}

### Change the placement

Pass `placement` through the `positioning` prop. The popover below opens to the right of its trigger.

{% component: "ui:componentExample", arguments: { "componentName": "PopoverExamples.customPositioning" } %}

### Make it modal

Set `modal` to trap focus and block interaction with the rest of the page while the popover is open.

{% component: "ui:componentExample", arguments: { "componentName": "PopoverExamples.modal" } %}

### Translate the close button

The label of the close button comes from the library's language file and follows the site language. Override it per template with the `translations` prop:

```html
<ui:popover.root
    translations="{
        closeTriggerLabel: f:translate(key: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:popover.close')
    }"
>
    ...
</ui:popover.root>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Popover",
        "parts": [
            ["root", "Provides popover state and context for the composed parts. Renders no wrapper element."],
            ["trigger", "Opens and closes the popover. Renders a `<button>` element."],
            ["positioner", "Positions the popover content relative to the trigger. Renders a `<div>` element."],
            ["content", "Contains the popover panel content. Renders a `<div>` element."],
            ["arrow", "Displays a decorative arrow for the popover. Renders a `<div>` element."],
            ["indicator", "Displays a decorative state indicator. Renders a `<div>` element."],
            ["title", "Provides the accessible title for the popover. Renders a `<div>` element."],
            ["description", "Provides supporting descriptive text for the popover. Renders a `<div>` element."],
            ["closeTrigger", "Closes the popover when activated. Renders a `<button>` element."]
        ]
    }
%}
