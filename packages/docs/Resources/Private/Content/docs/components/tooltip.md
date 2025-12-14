# Tooltip

**A popup that appears when an element is hovered or focused, showing a hint for sighted users.**

{% component: "ui:referenceButtons", arguments: { "name": "Tooltip" } %}

{% component: "ui:tooltip.examples.simple" %}

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
