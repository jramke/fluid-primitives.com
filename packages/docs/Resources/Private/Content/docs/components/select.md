# Select

**A common form component for choosing a predefined value in a dropdown menu.**

{% component: "ui:referenceButtons", arguments: { "name": "Select" } %}

{% component: "ui:select.examples.simple" %}

## Features

- Support for single and multiple selection
- Typeahead to allow focusing items by typing text
- Keyboard navigation support including arrow keys, home/end
- Supports disabled items and groups
- Works with Field component for form integration
- Supports custom positioning

## Installation

{% component: "ui:installationSection", arguments: { "name": "Select" } %}

## Usage

```html
<ui:listCollection
    items="{
        0: {value: 'apple', label: 'Apple'},
        1: {value: 'banana', label: 'Banana'},
        2: {value: 'cherry', label: 'Cherry'}
    }"
    as="collection"
/>

<ui:select.root collection="{collection}">
    <ui:select.label>Select a fruit</ui:select.label>
    <ui:select.control>
        <ui:select.trigger placeholder="Choose a fruit" />
    </ui:select.control>
    <ui:select.content>
        <f:for each="{collection.items}" as="item">
            <ui:select.item item="{item}">
                <ui:select.itemText>{item.label}</ui:select.itemText>
                <ui:select.itemIndicator />
            </ui:select.item>
        </f:for>
    </ui:select.content>
</ui:select.root>
```

## Examples

### Default Value

Set an initial selected value.

```html
<ui:listCollection
    items="{
        0: {value: 'apple', label: 'Apple'},
        1: {value: 'banana', label: 'Banana'},
        2: {value: 'cherry', label: 'Cherry'}
    }"
    as="collection"
/>

<ui:select.root collection="{collection}" defaultValue="{0: 'banana'}">
    <ui:select.label>Select a fruit</ui:select.label>
    <ui:select.control>
        <ui:select.trigger placeholder="Choose a fruit" />
    </ui:select.control>
    <ui:select.content>
        <f:for each="{collection.items}" as="item">
            <ui:select.item item="{item}">
                <ui:select.itemText>{item.label}</ui:select.itemText>
                <ui:select.itemIndicator />
            </ui:select.item>
        </f:for>
    </ui:select.content>
</ui:select.root>
```

### Multiple Selection

Allow selecting multiple items.

```html
<ui:listCollection
    items="{
        0: {value: 'react', label: 'React'},
        1: {value: 'vue', label: 'Vue'},
        2: {value: 'angular', label: 'Angular'},
        3: {value: 'svelte', label: 'Svelte'}
    }"
    as="collection"
/>

<ui:select.root collection="{collection}" multiple="{true}">
    <ui:select.label>Select frameworks</ui:select.label>
    <ui:select.control>
        <ui:select.trigger placeholder="Choose frameworks" />
    </ui:select.control>
    <ui:select.content>
        <f:for each="{collection.items}" as="item">
            <ui:select.item item="{item}">
                <ui:select.itemText>{item.label}</ui:select.itemText>
                <ui:select.itemIndicator />
            </ui:select.item>
        </f:for>
    </ui:select.content>
</ui:select.root>
```

### Disabled Items

Disable specific items in the list.

```html
<ui:listCollection
    items="{
        0: {value: 'available', label: 'Available Option'},
        1: {value: 'unavailable', label: 'Unavailable Option', disabled: true},
        2: {value: 'another', label: 'Another Option'}
    }"
    as="collection"
/>

<ui:select.root collection="{collection}">
    <ui:select.label>Select an option</ui:select.label>
    <ui:select.control>
        <ui:select.trigger placeholder="Choose an option" />
    </ui:select.control>
    <ui:select.content>
        <f:for each="{collection.items}" as="item">
            <ui:select.item item="{item}">
                <ui:select.itemText>{item.label}</ui:select.itemText>
                <ui:select.itemIndicator />
            </ui:select.item>
        </f:for>
    </ui:select.content>
</ui:select.root>
```

### With Item Groups

Organize items into logical groups.

```html
<ui:listCollection
    items="{
        0: {value: 'apple', label: 'Apple', group: 'fruits'},
        1: {value: 'banana', label: 'Banana', group: 'fruits'},
        2: {value: 'carrot', label: 'Carrot', group: 'vegetables'},
        3: {value: 'broccoli', label: 'Broccoli', group: 'vegetables'}
    }"
    as="collection"
/>

<ui:select.root collection="{collection}">
    <ui:select.label>Select produce</ui:select.label>
    <ui:select.control>
        <ui:select.trigger placeholder="Choose produce" />
    </ui:select.control>
    <ui:select.content>
        <ui:select.itemGroup>
            <ui:select.itemGroupLabel>Fruits</ui:select.itemGroupLabel>
            <f:for each="{collection.items}" as="item">
                <f:if condition="{item.group} == 'fruits'">
                    <ui:select.item item="{item}">
                        <ui:select.itemText>{item.label}</ui:select.itemText>
                        <ui:select.itemIndicator />
                    </ui:select.item>
                </f:if>
            </f:for>
        </ui:select.itemGroup>
        <ui:select.itemGroup>
            <ui:select.itemGroupLabel>Vegetables</ui:select.itemGroupLabel>
            <f:for each="{collection.items}" as="item">
                <f:if condition="{item.group} == 'vegetables'">
                    <ui:select.item item="{item}">
                        <ui:select.itemText>{item.label}</ui:select.itemText>
                        <ui:select.itemIndicator />
                    </ui:select.item>
                </f:if>
            </f:for>
        </ui:select.itemGroup>
    </ui:select.content>
</ui:select.root>
```

### With Form Field

Use with the Field component for form validation.

```html
<ui:listCollection
    items="{
        0: {value: 'us', label: 'United States'},
        1: {value: 'uk', label: 'United Kingdom'},
        2: {value: 'de', label: 'Germany'}
    }"
    as="collection"
/>

<ui:field.root name="country" required="{true}">
    <ui:select.root collection="{collection}">
        <ui:field.label>
            <ui:select.label>Country</ui:select.label>
        </ui:field.label>
        <ui:select.control>
            <ui:select.trigger placeholder="Select your country" />
        </ui:select.control>
        <ui:select.content>
            <f:for each="{collection.items}" as="item">
                <ui:select.item item="{item}">
                    <ui:select.itemText>{item.label}</ui:select.itemText>
                    <ui:select.itemIndicator />
                </ui:select.item>
            </f:for>
        </ui:select.content>
    </ui:select.root>
    <ui:field.error />
</ui:field.root>
```

## Anatomy

```html
<primitives:select.root>
    <primitives:select.label />
    <primitives:select.control>
        <primitives:select.trigger>
            <primitives:select.valueText />
            <primitives:select.indicator />
        </primitives:select.trigger>
        <primitives:select.clearTrigger />
    </primitives:select.control>
    <primitives:select.positioner>
        <primitives:select.content>
            <primitives:select.item>
                <primitives:select.itemText />
                <primitives:select.itemIndicator />
            </primitives:select.item>
            <primitives:select.itemGroup>
                <primitives:select.itemGroupLabel />
            </primitives:select.itemGroup>
        </primitives:select.content>
    </primitives:select.positioner>
    <primitives:select.hiddenSelect />
</primitives:select.root>
```
