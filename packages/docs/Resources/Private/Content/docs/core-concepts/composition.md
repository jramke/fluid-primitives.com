# Composition

Use a component's behavior on your own element and compose multiple components on a single element.

## The `asChild` prop

Pass `asChild="{true}"` to merge the component's attributes into its first child element. The child takes the place of the element the part would render:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger asChild="{true}">
        <a href="/shipping">Delivery information</a>
    </ui:tooltip.trigger>
    <ui:tooltip.content>Delivered in 2 to 3 working days</ui:tooltip.content>
</ui:tooltip.root>
```

The `<a>` tag receives all necessary attributes (`aria-describedby`, event handlers, etc.) while keeping its link behavior.

### Adding `asChild` to your own component

Your own components offer `asChild` when you add `{ui:asChild()}` inside the opening bracket of the tag that should receive the merged attributes, next to `{ui:attributes()}`:

```html
<button
    {f:if(condition: class, then: 'class="{class}"' )}
    {ui:attributes()}
    {ui:asChild()}>
    <f:slot />
</button>
```

`{ui:asChild()}` registers the prop and marks the target tag, so no `ui:prop` is needed.

### Limitations

- **Opt-in per part.** `asChild` works on parts that add `{ui:asChild()}` to their own wrapper element. Root parts that render only their slot content (e.g. Dialog, Popover, Tooltip's `root`) have no element to merge attributes onto, so they do not offer it. The Arguments table in a component's docs page shows which parts do.
- **Single child element.** The slot holds exactly one element. Text nodes or several elements do not work.
- **Child attributes take precedence.** An attribute the child already has keeps its value.
- **No context access inside asChild.** The slot content cannot access the component's context. See [TYPO3/Fluid#1132](https://github.com/TYPO3/Fluid/issues/1132).

### Accessibility note

Your element has to stay accessible. A `<div>` in place of a `<button>` needs a `role`, a `tabindex` and the keyboard handlers the original element provided natively.

## Sharing IDs between components

A collapsible trigger that also has a tooltip is one element that both components write an `id` onto. Pass both the same ID, so the ARIA attributes (`aria-controls`, `aria-describedby`) keep pointing at that element:

```html
<f:variable name="sharedTriggerId" value="{ui:id()}" />

<ui:collapsible.root ids="{trigger: sharedTriggerId}">
    <ui:tooltip.root ids="{trigger: sharedTriggerId}">
        <ui:collapsible.trigger asChild="{true}">
            <ui:tooltip.trigger>Shipping</ui:tooltip.trigger>
        </ui:collapsible.trigger>
        <ui:tooltip.content>Click to read more</ui:tooltip.content>
    </ui:tooltip.root>
    <ui:collapsible.content>
        <p>Orders ship within 2 working days.</p>
    </ui:collapsible.content>
</ui:collapsible.root>
```

The `ids` prop accepts an object where keys are part names (`trigger`, `content`, etc.) and values are the IDs to use.
