# Accordion

**A set of collapsible panels with headings.**

{% component: "ui:referenceButtons", arguments: { "name": "accordion" } %}

{% component: "ui:accordion.examples.simple" %}

## Anatomy

```html
<primitives:accordion.root>
    <primitives:accordion.item>
        <primitives:accordion.itemHeader>
            <primitives:accordion.itemTrigger>
                <primitives:accordion.itemIndicator />
            </primitives:accordion.itemTrigger>
        </primitives:accordion.itemHeader>
        <primitives:accordion.itemContent />
    </primitives:accordion.item>
</primitives:accordion.root>
```
