# Menu

**An accessible dropdown and context menu that displays a list of actions or options.**

{% component: "ui:referenceButtons", arguments: { "name": "Menu" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.simple", "withEntryFile": true } %}

## Features

- Support for items, labels, and groups of items
- Support for checkbox and radio menu items
- Support for nested/submenu patterns
- Support for context menus (triggered by right-click) and multiple independent triggers sharing one menu
- Focus is fully managed using the `aria-activedescendant` pattern
- Typeahead to allow focusing items by typing text
- Full keyboard navigation support, including arrow keys, home/end, and submenu navigation

## Installation

{% component: "ui:installationSection", arguments: { "name": "Menu" } %}

## Examples

### Grouping

Organize items into logical groups with their own labels.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.grouping" } %}

### With Links

Pass `asChild="{true}"` on `menu.item` to render the item's attributes and behavior directly onto a
single child element, like a plain `<a href>`, so it behaves like real navigation, including
keyboard activation.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.withLinks" } %}

### With Checkboxes

Use `menu.checkboxItem` for items that toggle independently of each other.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.withCheckboxes" } %}

### With Radios

Use `menu.radioItem` with a shared `name` for mutually exclusive options.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.withRadios" } %}

### Nested Menu

A submenu is just another `menu.root`, linked to its parent by two explicit ids: give the submenu's
`menu.root` a `rootId` and point its `parentId` back at the parent menu's own `rootId`, then render a
`menu.triggerItem` inside the _parent's_ own content with a `childId` matching the submenu's `rootId`.
The submenu itself doesn't need to live anywhere near the parent's markup - it's linked entirely by id.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.nested" } %}

### Context Menu

Use `menu.contextTrigger` to open the menu on right-click instead of (or in addition to) a regular trigger.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.contextMenu" } %}

### Multiple Triggers

Give several `menu.trigger` elements different `value`s to share a single menu instance between them.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.multipleTriggers" } %}

### Inside a Dialog

By default `menu.content` portals to the end of the document body, same as Select. Pass
`portalled="{false}"` when nesting a menu inside another portalled/focus-trapped element, like a
dialog, so it stays within that element's DOM subtree instead.

{% component: "ui:componentExample", arguments: { "componentName": "Menu.examples.insideDialog" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Menu",
        "parts": [
            ["root", "Provides shared menu state to the rest of the parts. Renders no element of its own."],
            ["trigger", "Opens and closes the menu. Renders a `<button>` element."],
            ["contextTrigger", "Opens the menu at the cursor position on right-click. Renders a `<div>` element."],
            ["content", "Contains the menu items. Renders a `<div>` element."],
            ["item", "A selectable menu action. Renders a `<div>` element."],
            ["checkboxItem", "A menu item that toggles independently of other items. Renders a `<div>` element."],
            ["radioItem", "A menu item that's mutually exclusive with other radio items sharing the same `name`. Renders a `<div>` element."],
            ["itemGroup", "Groups related items together. Renders a `<div>` element."],
            ["itemGroupLabel", "Labels an `itemGroup`. Renders a `<div>` element."],
            ["separator", "A visual divider between items. Renders a `<div>` element."],
            ["triggerItem", "Opens a nested submenu from within another menu's content. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:menu.root>
    <primitives:menu.trigger>
        <primitives:menu.indicator />
    </primitives:menu.trigger>
    <primitives:menu.contextTrigger />
    <primitives:menu.positioner>
        <primitives:menu.arrow />
        <primitives:menu.content>
            <primitives:menu.item>
                <primitives:menu.itemText />
            </primitives:menu.item>
            <primitives:menu.checkboxItem>
                <primitives:menu.itemIndicator />
                <primitives:menu.itemText />
            </primitives:menu.checkboxItem>
            <primitives:menu.radioItem>
                <primitives:menu.itemIndicator />
                <primitives:menu.itemText />
            </primitives:menu.radioItem>
            <primitives:menu.separator />
            <primitives:menu.itemGroup>
                <primitives:menu.itemGroupLabel />
            </primitives:menu.itemGroup>

            <f:comment><!-- Opens the submenu below, matched by rootId/childId --></f:comment>
            <primitives:menu.triggerItem childId="submenu" />
        </primitives:menu.content>
    </primitives:menu.positioner>
</primitives:menu.root>

<f:comment><!-- A submenu: linked to its parent by id, not by nesting --></f:comment>
<primitives:menu.root rootId="submenu" parentId="parent-rootId">
    <primitives:menu.positioner>
        <primitives:menu.content>
            <primitives:menu.item />
        </primitives:menu.content>
    </primitives:menu.positioner>
</primitives:menu.root>
```
