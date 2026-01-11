# Menu

**An accessible dropdown and context menu that displays a list of actions or options.**

{% component: "ui:referenceButtons", arguments: { "name": "Menu" } %}

{% component: "ui:menu.examples.simple" %}

## Features

- Support for items, labels, groups of items
- Focus is fully managed using `aria-activedescendant` pattern
- Typeahead to allow focusing items by typing text
- Keyboard navigation support including arrow keys, home/end, page up/down
- Support for nested/submenu patterns
- Support for checkbox and radio menu items

## Installation

{% component: "ui:installationSection", arguments: { "name": "Menu" } %}

## Usage

```html
<ui:menu.root>
    <ui:menu.trigger>Open Menu</ui:menu.trigger>
    <ui:menu.content>
        <ui:menu.item value="new-file">New File</ui:menu.item>
        <ui:menu.item value="new-window">New Window</ui:menu.item>
        <ui:menu.separator />
        <ui:menu.item value="settings">Settings</ui:menu.item>
    </ui:menu.content>
</ui:menu.root>
```

## Examples

### Context Menu

Create a context menu that appears on right-click.

```html
<ui:menu.root>
    <ui:menu.contextTrigger>
        Right-click this area
    </ui:menu.contextTrigger>
    <ui:menu.content>
        <ui:menu.item value="cut">Cut</ui:menu.item>
        <ui:menu.item value="copy">Copy</ui:menu.item>
        <ui:menu.item value="paste">Paste</ui:menu.item>
    </ui:menu.content>
</ui:menu.root>
```

### With Item Groups

Organize items into logical groups.

```html
<ui:menu.root>
    <ui:menu.trigger>File</ui:menu.trigger>
    <ui:menu.content>
        <ui:menu.itemGroup id="file-group">
            <ui:menu.itemGroupLabel htmlFor="file-group">File Operations</ui:menu.itemGroupLabel>
            <ui:menu.item value="new">New</ui:menu.item>
            <ui:menu.item value="open">Open</ui:menu.item>
            <ui:menu.item value="save">Save</ui:menu.item>
        </ui:menu.itemGroup>
        <ui:menu.separator />
        <ui:menu.itemGroup id="edit-group">
            <ui:menu.itemGroupLabel htmlFor="edit-group">Edit Operations</ui:menu.itemGroupLabel>
            <ui:menu.item value="undo">Undo</ui:menu.item>
            <ui:menu.item value="redo">Redo</ui:menu.item>
        </ui:menu.itemGroup>
    </ui:menu.content>
</ui:menu.root>
```

### With Disabled Items

Disable specific menu items to prevent interaction.

```html
<ui:menu.root>
    <ui:menu.trigger>Actions</ui:menu.trigger>
    <ui:menu.content>
        <ui:menu.item value="edit">Edit</ui:menu.item>
        <ui:menu.item value="duplicate">Duplicate</ui:menu.item>
        <ui:menu.item value="delete" disabled="{true}">Delete (Disabled)</ui:menu.item>
    </ui:menu.content>
</ui:menu.root>
```

### With Checkbox Items

Create menu items that can be toggled on and off.

```html
<ui:menu.root>
    <ui:menu.trigger>View</ui:menu.trigger>
    <ui:menu.content>
        <ui:menu.optionItem name="show-toolbar" type="checkbox" value="toolbar">
            Show Toolbar
        </ui:menu.optionItem>
        <ui:menu.optionItem name="show-sidebar" type="checkbox" value="sidebar" checked="{true}">
            Show Sidebar
        </ui:menu.optionItem>
        <ui:menu.optionItem name="show-statusbar" type="checkbox" value="statusbar">
            Show Status Bar
        </ui:menu.optionItem>
    </ui:menu.content>
</ui:menu.root>
```

### With Radio Items

Create mutually exclusive menu options.

```html
<ui:menu.root>
    <ui:menu.trigger>Sort By</ui:menu.trigger>
    <ui:menu.content>
        <ui:menu.optionItem name="sort" type="radio" value="name" checked="{true}">
            Name
        </ui:menu.optionItem>
        <ui:menu.optionItem name="sort" type="radio" value="date">
            Date Modified
        </ui:menu.optionItem>
        <ui:menu.optionItem name="sort" type="radio" value="size">
            Size
        </ui:menu.optionItem>
    </ui:menu.content>
</ui:menu.root>
```

### Nested Menus (Submenus)

Create hierarchical menus with submenus. Use `rootId` on the parent and `parentMenu` on the child to link them.

```html
<f:comment><!-- Parent Menu --></f:comment>
<ui:menu.root rootId="file-menu">
    <ui:menu.trigger>File</ui:menu.trigger>
    <ui:menu.content>
        <ui:menu.item value="new-tab">New Tab</ui:menu.item>
        <ui:menu.item value="new-window">New Window</ui:menu.item>
        <ui:menu.separator />
        <ui:menu.triggerItem value="share-menu">Share</ui:menu.triggerItem>
        <ui:menu.separator />
        <ui:menu.item value="print">Print...</ui:menu.item>
    </ui:menu.content>
</ui:menu.root>

<f:comment><!-- Child/Nested Menu --></f:comment>
<ui:menu.root rootId="share-menu" parentMenu="file-menu">
    <ui:menu.content>
        <ui:menu.item value="email">Email</ui:menu.item>
        <ui:menu.item value="messages">Messages</ui:menu.item>
        <ui:menu.item value="airdrop">AirDrop</ui:menu.item>
    </ui:menu.content>
</ui:menu.root>
```

## Anatomy

```html
<primitives:menu.root>
    <primitives:menu.trigger />
    <primitives:menu.contextTrigger />
    <primitives:menu.positioner>
        <primitives:menu.arrow />
        <primitives:menu.content>
            <primitives:menu.item>
                <primitives:menu.itemText />
                <primitives:menu.itemIndicator />
            </primitives:menu.item>
            <primitives:menu.optionItem />
            <primitives:menu.triggerItem />
            <primitives:menu.separator />
            <primitives:menu.itemGroup>
                <primitives:menu.itemGroupLabel />
            </primitives:menu.itemGroup>
        </primitives:menu.content>
    </primitives:menu.positioner>
</primitives:menu.root>
```
