# Primitives

Fluid Primitives exposes headless (unstyled) primitives that you can use to build your own components. These primitives are built on top of [Zag.js](https://zagjs.com/) state machines and provide the necessary behavior and accessibility features.

They are exposed in the `primitives` namespace.

## Primitive Anatomy

For example the `Tooltip` anatomy looks like this:

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

You could use them directly in your templates and add your classes and styles. But they are mainly meant to be used as building blocks for your own components.

## Building Components with Primitives

Its recommended to create components for each primitive part, so you can easily customize the styling and behavior of your design system in one place. To help you with that we built a simple registry (inspired by [shadcn/ui](https://ui.shadcn.com/)) and a `typo3 ui:add <component>` command that generates a component for you based on the primitive anatomy.

See the [File Structure](/docs/core-concepts/file-structure) documentation for more details.

In case of the Tooltip we could simplify the primtive parts little bit and wrap the `positioner`, `content` and `arrow` parts into a single `content` part. Inside there you can then also use the [ui:portal](/docs/viewhelpers/portal) ViewHelper if you want.

## Component Anatomy

The anatomy of our `ui:tooltip` component would then look like this:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger />
    <ui:tooltip.content />
</ui:tooltip.root>
```

Inside the component files you can then use the [ui:useProps](/docs/viewhelpers/useProps) ViewHelper to import all props from the primitive and pass them to the primitive with `spreadProps="{true}"`.

```html
<ui:useProps name="primitives:tooltip.root" />

<primitives:tooltip.root spreadProps="{true}">
    <f:slot />
</primitives:tooltip.root>

<vite:asset entry="EXT:docs/Resources/Private/Components/ui/Tooltip/source/Tooltip.entry.ts" />
```

Note that we also load the component's JavaScript entry file in our `Root.html` with the [Vite Asset Collector](https://extensions.typo3.org/extension/vite_asset_collector) ViewHelper here instead of in our main JavaScript file so its only included if we use the component.

Now we can simply use our `ui:tooltip` component like this:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger>Hover me</ui:tooltip.trigger>
    <ui:tooltip.content>This is the tooltip content.</ui:tooltip.content>
</ui:tooltip.root>
```

## Available Primitives

You can find an overview of all available primitives in the [Components](/docs/components) section. Or you can use the `typo3 ui:list` command to get a list of all available components.

## Registry Limitations

The registry is only meant to be used for scuffolding your components. Its not a fully fledged design system foundation/solution like shadcn/ui.

The Registry Components come with Tailwind CSS classes that only provide a basic styling. You will need to customize the styling and behavior of the components to fit your design system needs.

We also use the [Vite Asset Collector](https://extensions.typo3.org/extension/vite_asset_collector) ViewHelper by default to load the JavaScript entry files of each component individually. You may need to adjust this to fit your build process and setup.
