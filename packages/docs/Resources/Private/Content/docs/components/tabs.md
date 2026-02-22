# Tabs

**A component for toggling between related panels on the same page.**

{% component: "ui:referenceButtons", arguments: { "name": "Tabs" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Tabs.examples.simple" } %}

## Features

- Full keyboard navigation support with arrow keys
- Supports horizontal and vertical orientations
- Supports automatic and manual tab activation modes
- Content is lazy mounted by default
- Supports disabled tabs

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tabs" } %}

## Usage

```html
<ui:tabs.root defaultValue="tab1">
    <ui:tabs.list>
        <ui:tabs.trigger value="tab1">Tab 1</ui:tabs.trigger>
        <ui:tabs.trigger value="tab2">Tab 2</ui:tabs.trigger>
        <ui:tabs.trigger value="tab3">Tab 3</ui:tabs.trigger>
    </ui:tabs.list>
    <ui:tabs.content value="tab1">Content for tab 1.</ui:tabs.content>
    <ui:tabs.content value="tab2">Content for tab 2.</ui:tabs.content>
    <ui:tabs.content value="tab3">Content for tab 3.</ui:tabs.content>
</ui:tabs.root>
```

## Examples

### Vertical Orientation

Display tabs in a vertical layout.

```html
<ui:tabs.root defaultValue="tab1" orientation="vertical" class="flex gap-4">
    <ui:tabs.list class="flex-col">
        <ui:tabs.trigger value="tab1">Profile</ui:tabs.trigger>
        <ui:tabs.trigger value="tab2">Settings</ui:tabs.trigger>
        <ui:tabs.trigger value="tab3">Notifications</ui:tabs.trigger>
    </ui:tabs.list>
    <div class="flex-1">
        <ui:tabs.content value="tab1">Profile settings content.</ui:tabs.content>
        <ui:tabs.content value="tab2">General settings content.</ui:tabs.content>
        <ui:tabs.content value="tab3">Notification preferences.</ui:tabs.content>
    </div>
</ui:tabs.root>
```

### Disabled Tabs

Disable specific tabs to prevent interaction.

```html
<ui:tabs.root defaultValue="tab1">
    <ui:tabs.list>
        <ui:tabs.trigger value="tab1">Enabled</ui:tabs.trigger>
        <ui:tabs.trigger value="tab2" disabled="{true}">Disabled</ui:tabs.trigger>
        <ui:tabs.trigger value="tab3">Enabled</ui:tabs.trigger>
    </ui:tabs.list>
    <ui:tabs.content value="tab1">First tab content.</ui:tabs.content>
    <ui:tabs.content value="tab2">This tab is disabled.</ui:tabs.content>
    <ui:tabs.content value="tab3">Third tab content.</ui:tabs.content>
</ui:tabs.root>
```

### Manual Activation

Require pressing Enter or Space to activate tabs instead of activating on focus.

```html
<ui:tabs.root defaultValue="tab1" activationMode="manual">
    <ui:tabs.list>
        <ui:tabs.trigger value="tab1">Tab 1</ui:tabs.trigger>
        <ui:tabs.trigger value="tab2">Tab 2</ui:tabs.trigger>
        <ui:tabs.trigger value="tab3">Tab 3</ui:tabs.trigger>
    </ui:tabs.list>
    <ui:tabs.content value="tab1">Press Enter to switch tabs.</ui:tabs.content>
    <ui:tabs.content value="tab2">Content for tab 2.</ui:tabs.content>
    <ui:tabs.content value="tab3">Content for tab 3.</ui:tabs.content>
</ui:tabs.root>
```

### Loop Focus

Enable looping from the last tab to the first tab with keyboard navigation.

```html
<ui:tabs.root defaultValue="tab1" loopFocus="{true}">
    <ui:tabs.list>
        <ui:tabs.trigger value="tab1">First</ui:tabs.trigger>
        <ui:tabs.trigger value="tab2">Second</ui:tabs.trigger>
        <ui:tabs.trigger value="tab3">Third (loops to first)</ui:tabs.trigger>
    </ui:tabs.list>
    <ui:tabs.content value="tab1">First tab content.</ui:tabs.content>
    <ui:tabs.content value="tab2">Second tab content.</ui:tabs.content>
    <ui:tabs.content value="tab3">Third tab content.</ui:tabs.content>
</ui:tabs.root>
```

## Anatomy

```html
<primitives:tabs.root>
    <primitives:tabs.list>
        <primitives:tabs.trigger />
        <primitives:tabs.indicator />
    </primitives:tabs.list>
    <primitives:tabs.content />
</primitives:tabs.root>
```
