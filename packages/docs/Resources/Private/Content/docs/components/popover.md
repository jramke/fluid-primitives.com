# Popover

**An accessible popup anchored to a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Popover" } %}
{% component: "ui:popover.examples.simple" %}

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
