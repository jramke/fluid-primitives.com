# The Pitch

Consider the most common request in any TYPO3 project: "we need an accordion." Or a dialog. Or a select. Something interactive, accessible, and not ugly.

The traditional answer is to build it yourself, every project, from scratch. Wire up some JavaScript, remember `aria-expanded`, forget `aria-controls`, copy-paste last year's version from a different client, hope it still works. Or use Bootstrap and fight the styles and markup.

Even on a normal marketing site, the moment you need a [dialog](/docs/components/dialog), [select](/docs/components/select), [tabs](/docs/components/tabs), [navigation menu](/docs/components/navigation-menu), [popover](/docs/components/popover), or better [form](/docs/components/form) UX, you also need keyboard support, focus management, semantics, labeling, and all the edge cases that come with it.

That part is annoying. And it's already solved for every modern web framework, so why not for TYPO3?

## Why use Fluid Primitives?

Because opening and closing a thing is easy. Making it work properly is the hard part.

Take a dialog. You do not just need a box on the screen. You need focus trap, restore focus on close, Escape handling, outside interaction rules, scroll locking, proper labeling, and semantics that actually work for keyboard and assistive technology users.

```html
<ui:dialog.root>
    <ui:dialog.trigger>Open dialog</ui:dialog.trigger>
    <ui:dialog.backdrop />
    <ui:dialog.positioner>
        <ui:dialog.content>
            <ui:dialog.title>Delete item?</ui:dialog.title>
            ...
            <ui:dialog.closeTrigger>Cancel</ui:dialog.closeTrigger>
        </ui:dialog.content>
    </ui:dialog.positioner>
</ui:dialog.root>
```

Same story with a select, navigation menu, or combobox. The hard part is not the styling. It is the behavior, and the edge cases you only notice after shipping. That is exactly the part Fluid Primitives handles for you.

This is also where traditional UI frameworks start to get in the way. The moment your project wants something more custom, you are fighting pre-made classes, pre-made wrappers, pre-made DOM assumptions, and JavaScript behavior that was never designed around your component API. Then you end up overriding markup, patching styles, and rebuilding behavior anyway.

Fluid Primitives goes the other way around: keep the behavior solid, keep the styling open, and let the component adapt to the project.

## Built for how we actually work in TYPO3

TYPO3 Fluid finally has first-class component support. So we can build actual components instead of stuffing everything into partials or ViewHelpers.

Traditional components tend to accumulate props until they become a mess:

```html
<ui:card rootClass="some-class" image="path/to/image" imageAlt="Alt text" title="Hello World" titleLevel="3" text="Lorem ipsum" cta="1" ctaVariant="secondary" ctaText="Learn more" ctaLink="/some-page" secondaryCta="1" secondaryCtaText="More" />
```

Need two buttons? More props. Different layout? More conditionals. Soon the template is full of `f:if` branches and nobody wants to touch it.

Fluid's native `f:slot` and named slots via `f:fragment` are already a good step. But they still push you toward large content areas with fixed placement decided by the component author.

[Composition](/docs/core-concepts/composition) goes one step further: instead of passing big chunks into a component, you assemble the component from explicit parts.

Fluid Primitives enables that composable approach:

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

That scales better, reads better, and is much easier to extend.

And if a component needs more than markup, Fluid Primitives adds [Context classes](/docs/core-concepts/context). That gives your components a proper place for logic and computed state without pushing it back into the template or some ViewHelper you only use once.

## Headless on purpose

Every project already has enough opinions.

You might use Tailwind. Or Bootstrap utilities. Or custom CSS. Or a design system that grew over five client projects and one panic relaunch. Whatever. The component library should not fight that.

Fluid Primitives is headless by design. You get behavior and structure, not a visual identity you have to undo first. If you want more on that, the [styling docs](/docs/core-concepts/styling) go into the approach in more detail.

## Start fast, keep ownership

If you want to move quickly, scaffold components with `typo3 ui:add`. You get a solid starting point with basic styles, similar in spirit to [shadcn/ui](https://ui.shadcn.com/).

But the important part is this: you own the code.

You are not stuck overriding some black box. You can copy it, shape it, rename parts, simplify it, or whatever you want while keeping the foundation.

That is the whole point.

Fluid Primitives is my answer to a question I kept asking myself for years: if every modern stack gets good headless component primitives, why not TYPO3 Fluid too?

I think this should be a basic part of the stack. Not a weird extra. Not something every team rebuilds in slightly broken ways. Just a solid foundation you can use, adapt, and trust.

If that sounds like the way you want to build TYPO3 projects, this package is for you.

&nbsp;&nbsp;&nbsp;&nbsp;–&nbsp;&nbsp;Joost
