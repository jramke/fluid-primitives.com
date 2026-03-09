# Composition

Build complex interfaces by combining simple parts. This is the core pattern in Fluid Primitives.

## The `asChild` Prop

Sometimes you need a component's behavior on your own element. Pass `asChild="{true}"` to merge the component's attributes into its first child instead of rendering the default element.

```html
<ui:tooltip.root>
    <ui:tooltip.trigger asChild="{true}">
        <a href="https://example.com">Link with tooltip</a>
    </ui:tooltip.trigger>
    <ui:tooltip.content>Tooltip for this link</ui:tooltip.content>
</ui:tooltip.root>
```

The `<a>` tag receives all necessary attributes (`aria-describedby`, event handlers, etc.) while keeping its link behavior.

This pattern comes from [Radix UI's asChild API](https://www.radix-ui.com/primitives/docs/guides/composition).

### Limitations

- **Single child element required.** Text nodes or multiple elements won't work.
- **Child attributes take precedence.** If the child already has an attribute, it won't be overwritten.
- **No context access inside asChild.** The slot content can't access the component's context. See [TYPO3/Fluid#1132](https://github.com/TYPO3/Fluid/issues/1132).

### Accessibility Note

When using `asChild`, ensure your element remains accessible. A `<div>` replacing a `<button>` needs proper `role`, `tabindex`, and keyboard handlers that the original element provided natively.

## Sharing IDs Between Components

When multiple components need to interact (like a collapsible trigger that also has a tooltip), share IDs to maintain proper accessibility bindings.

```html
<f:variable name="sharedTriggerId" value="{ui:id()}" />

<ui:collapsible.root ids="{trigger: sharedTriggerId}">
    <ui:tooltip.root ids="{trigger: sharedTriggerId}">
        <ui:collapsible.trigger asChild="{true}">
            <ui:tooltip.trigger>Toggle with tooltip</ui:tooltip.trigger>
        </ui:collapsible.trigger>
        <ui:tooltip.content>Click to expand</ui:tooltip.content>
    </ui:tooltip.root>
    <ui:collapsible.content>
        <p>The expanded content.</p>
    </ui:collapsible.content>
</ui:collapsible.root>
```

Both the collapsible and tooltip now reference the same trigger element, keeping `aria-controls`, `aria-describedby`, and other attributes in sync.

The `ids` prop accepts an object where keys are part names (`trigger`, `content`, etc.) and values are the IDs to use.
