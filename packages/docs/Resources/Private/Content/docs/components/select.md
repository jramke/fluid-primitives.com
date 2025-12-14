# Select

**A common form component for choosing a predefined value in a dropdown menu.**

{% component: "ui:referenceButtons", arguments: { "name": "Select" } %}

{% component: "ui:select.examples.simple" %}

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
