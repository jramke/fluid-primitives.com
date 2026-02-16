# Tooltip

**A popup that appears when an element is hovered or focused, showing a hint for sighted users.**

{% component: "ui:referenceButtons", arguments: { "name": "Tooltip" } %}

{% component: "ui:tooltip.examples.simple" %}

## Features

- Opens on hover and focus
- Closes on pointer down or Escape key press
- Supports custom open and close delays
- Supports custom positioning with placement options
- Automatically adjusts position to stay in viewport

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tooltip" } %}

## Usage

```html
<ui:tooltip.root>
    <ui:tooltip.trigger asChild="{true}">
        <ui:button>Hover me</ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content>This is the tooltip content.</ui:tooltip.content>
</ui:tooltip.root>
```

## Examples

### Custom Positioning

Control where the tooltip appears relative to the trigger.

```html
<ui:tooltip.root positioning="{placement: 'right'}">
    <ui:tooltip.trigger asChild="{true}">
        <ui:button>Hover for right tooltip</ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content>I appear on the right!</ui:tooltip.content>
</ui:tooltip.root>
```

### Open Delay

Set a custom delay before the tooltip opens.

```html
<ui:tooltip.root openDelay="500">
    <ui:tooltip.trigger asChild="{true}">
        <ui:button>Slow tooltip (500ms)</ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content>I took 500ms to appear.</ui:tooltip.content>
</ui:tooltip.root>
```

### Close Delay

Set a custom delay before the tooltip closes after the pointer leaves.

```html
<ui:tooltip.root closeDelay="300">
    <ui:tooltip.trigger asChild="{true}">
        <ui:button>Lingers for 300ms</ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content>I stay visible for 300ms after you leave.</ui:tooltip.content>
</ui:tooltip.root>
```

### Disabled Interaction

Prevent the tooltip from opening on hover.

```html
<ui:tooltip.root disabled="{true}">
    <ui:tooltip.trigger asChild="{true}">
        <ui:button>No tooltip here</ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content>You won't see this.</ui:tooltip.content>
</ui:tooltip.root>
```

### On Icon Buttons

Common pattern for icon-only buttons that need accessible labels.

```html
<ui:tooltip.root>
    <ui:tooltip.trigger asChild="{true}">
        <ui:button variant="ghost" size="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <span class="sr-only">Settings</span>
        </ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content>Settings</ui:tooltip.content>
</ui:tooltip.root>
```

## Anatomy

```html
<primitives:tooltip.root>
    <primitives:tooltip.trigger />
    <primitives:tooltip.positioner>
        <primitives:tooltip.content>
            <primitives:tooltip.arrow />
        </primitives:tooltip.content>
    </primitives:tooltip.positioner>
</primitives:tooltip.root>
```
