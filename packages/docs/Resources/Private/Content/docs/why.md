# Why Fluid Primitives?

TYPO3 Fluid is great for templating, but building interactive UI components often becomes painful. Fluid Primitives fixes that.

## The Problem with Traditional Components

Traditional Fluid components tend to accumulate props until they become unwieldy:

```html
<ui:card rootClass="some-class" image="path/to/image" imageAlt="Alt text" title="Hello World" titleLevel="3" text="Lorem ipsum" cta="1" ctaVariant="secondary" ctaText="Learn more" ctaLink="/some-page" secondaryCta="1" secondaryCtaText="..." />
```

Need two buttons? Add more props. Different layout? More conditional logic. Soon you're managing a tangled mess of `f:if` conditions and documentation becomes impossible.

## The Composable Alternative

Fluid Primitives uses the composition pattern popularized by [Radix](https://www.radix-ui.com/primitives), [Base UI](https://base-ui.com/), and others:

```html
<ui:card.root class="some-class">
    <ui:card.image src="path/to/image" alt="Alt text" />
    <ui:card.title level="3">Hello World</ui:card.title>
    <ui:card.content>
        <p>Lorem ipsum</p>
        <ui:button variant="secondary" link="/some-page">Learn more</ui:button>
        <ui:button variant="ghost" link="/other-page">Secondary action</ui:button>
    </ui:card.content>
</ui:card.root>
```

**What's different:**

- **Flexible structure.** Add, remove, or reorder parts without changing any component API
- **No prop explosion.** Each part handles its own concerns
- **Clear relationships.** The template structure shows how things fit together
- **Easy customization.** Override just the parts you need

## Accessibility Without the Work

Interactive components are hard to build correctly. Keyboard navigation, focus management, screen reader support, ARIA states - it's easy to miss something.

Fluid Primitives handles all of this automatically. Every interactive component is built on [Zag.js](https://zagjs.com/) state machines that have been battle-tested across frameworks.

```html
<ui:accordion.root>
    <ui:accordion.item value="item-1">
        <ui:accordion.trigger>Section 1</ui:accordion.trigger>
        <ui:accordion.content>Content here...</ui:accordion.content>
    </ui:accordion.item>
</ui:accordion.root>
```

This accordion supports:

- Arrow key navigation between items
- Enter/Space to toggle
- Proper `aria-expanded`, `aria-controls`, `aria-labelledby`
- Focus management
- And much more you didn't think about

All without writing a single line of JavaScript or worrying about edge cases.

## Unstyled by Design

Components ship with zero visual styles. You bring your own design system:

```html
<ui:accordion.item class="border-b border-gray-200 data-[state=open]:bg-gray-50"></ui:accordion.item>
```

Use Tailwind utilities, BEM classes, or vanilla CSS. Target states via data attributes like `data-state="open"` or `data-disabled`.

Check the [Styling Guide](/docs/core-concepts/styling) for patterns and examples.

## Server-First Architecture

Unlike client-side component libraries, Fluid Primitives renders on the server first. The HTML arrives complete, then JavaScript hydrates interactive behavior.

This means:

- No content layout shift
- Works without JavaScript (graceful degradation)
- SEO-friendly
- Fast initial paint
