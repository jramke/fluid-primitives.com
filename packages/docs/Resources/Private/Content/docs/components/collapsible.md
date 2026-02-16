# Collapsible

**A collapsible panel controlled by a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Collapsible" } %}

{% component: "ui:collapsible.examples.simple" %}

## Features

- Full keyboard navigation support
- Supports animation via CSS transitions or animations
- Supports custom open and collapsed heights/widths

## Installation

{% component: "ui:installationSection", arguments: { "name": "Collapsible" } %}

## Usage

```html
<ui:collapsible.root>
    <ui:collapsible.trigger>
        <ui:collapsible.triggerText openText="Show content" closeText="Hide content" />
    </ui:collapsible.trigger>
    <ui:collapsible.content>Your collapsible content here.</ui:collapsible.content>
</ui:collapsible.root>
```

## Examples

### Default Open

Set the collapsible to be open by default.

```html
<ui:collapsible.root defaultOpen="{true}">
    <ui:collapsible.trigger>
        <ui:collapsible.triggerText openText="Show details" closeText="Hide details" />
    </ui:collapsible.trigger>
    <ui:collapsible.content>This content is visible by default.</ui:collapsible.content>
</ui:collapsible.root>
```

### Disabled

Prevent the collapsible from being toggled.

```html
<ui:collapsible.root disabled="{true}">
    <ui:collapsible.trigger>
        <ui:collapsible.triggerText openText="Show" closeText="Hide" />
    </ui:collapsible.trigger>
    <ui:collapsible.content>This collapsible cannot be toggled.</ui:collapsible.content>
</ui:collapsible.root>
```

### With Collapsed Height

Show a preview of the content when collapsed by setting a minimum collapsed height.

```html
<ui:collapsible.root collapsedHeight="60px">
    <ui:collapsible.trigger>
        <ui:collapsible.triggerText openText="Read more" closeText="Show less" />
    </ui:collapsible.trigger>
    <ui:collapsible.content>
        <p>This is a longer piece of content that shows a preview when collapsed.</p>
        <p>Click the button above to see the full content.</p>
        <p>There's more content here that will be revealed when expanded.</p>
    </ui:collapsible.content>
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
