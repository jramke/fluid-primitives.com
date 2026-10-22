# Menu

**An accessible dropdown and context menu that displays a list of actions or options.**

{% component: "ui:referenceButtons", arguments: { "name": "Menu" } %}

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.simple", "withEntryFile": true } %}

## Features

- Items, labels and groups of items
- Checkbox and radio items
- Submenus
- Context menu and several triggers for one menu
- Focus managed with `aria-activedescendant`
- Typeahead to focus items by typing
- Keyboard navigation with arrow keys, <kbd>Home</kbd>, <kbd>End</kbd> and submenus

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

<f:comment><!-- A submenu: linked to its parent by id --></f:comment>
<primitives:menu.root rootId="submenu" parentId="parent-rootId">
    <primitives:menu.positioner>
        <primitives:menu.content>
            <primitives:menu.item />
        </primitives:menu.content>
    </primitives:menu.positioner>
</primitives:menu.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Menu" } %}

## Examples

### Group related items

Wrap related items in a `menu.itemGroup` and name it with a `menu.itemGroupLabel`. A `menu.separator` divides the groups.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.grouping" } %}

### Link to pages

Set `asChild="{true}"` on a `menu.item` to put its attributes on your `<a href>`. The item navigates like a regular link, also from the keyboard.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.withLinks" } %}

### Toggle options

Use `menu.checkboxItem` for options that switch on and off independently. The root sets `closeOnSelect="{false}"`, so the menu stays open while you check several.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.withCheckboxes" } %}

### Choose one option

Radio items that share a `name` are mutually exclusive: choosing one unchecks the others.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.withRadios" } %}

### Add a submenu

Link a submenu to its parent by id: give its `menu.root` a `rootId` and a `parentId` that is the parent's `rootId`, then add a `menu.triggerItem` with a matching `childId` to the parent's content. Open Share or Export below.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.nested" } %}

### Open on right-click

Wrap an area in a `menu.contextTrigger` to open the menu on right-click. Add a regular `menu.trigger` as well to open the same menu from a button.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.contextMenu" } %}

### Share one menu between triggers

Give each `menu.trigger` its own `value` and they open the same menu.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.multipleTriggers" } %}

### Use a menu inside a dialog

Menus portal to the end of the `<body>`. Set `portalled="{false}"` on `menu.content` inside a dialog, or another focus-trapped element, to keep the menu in its DOM subtree.

{% component: "ui:componentExample", arguments: { "componentName": "MenuExamples.insideDialog" } %}

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
