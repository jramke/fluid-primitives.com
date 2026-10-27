# Animation

Every part of a component exposes its state as a data attribute, and parts are plain DOM elements. That leaves the animation up to you: a CSS transition, a keyframe animation or a JavaScript library all work, and the patterns below show how a few common cases fit together.

## CSS transitions

A part that opens carries `data-state="open"` or `data-state="closed"`. Transition between the two and most enter and exit animations need no JavaScript:

```css
[data-popover-content] {
    transform-origin: var(--transform-origin);
    transition:
        opacity 250ms,
        scale 250ms,
        display 250ms allow-discrete;

    &[data-state='closed'] {
        opacity: 0;
    }

    @starting-style {
        &[data-state='open'] {
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: no-preference) {
        &[data-state='closed'] {
            scale: 0.95;
        }

        @starting-style {
            &[data-state='open'] {
                scale: 0.95;
            }
        }
    }
}
```

Two CSS features make this work for parts that are hidden while closed:

- [`@starting-style`](https://developer.mozilla.org/en-US/docs/Web/CSS/@starting-style) gives a part the style to transition from on the frame it appears.
- [`transition-behavior: allow-discrete`](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-behavior) lets `display` take part in the transition. Fluid Primitives sets the `hidden` attribute as soon as a part closes, and `allow-discrete` postpones it until the exit has played.

{% component: "ui:alert", arguments: {"title": "Quick Tip", "text": "Use transitions over animation keyframes because they are interruptible.", "variant": "info"} %}

### Scale from the trigger

Popover, Menu, Select, Combobox, Tooltip and DatePicker set `--transform-origin` on their positioner to the point closest to the trigger. Pick a side and open the popover.

{% component: "ui:componentExample", arguments: { "componentName": "AnimationExamples.popoverPlacement", "additionalFiles": {"PopoverPlacement.entry.ts": "EXT:docs/Resources/Private/Components/ui/AnimationExamples/PopoverPlacement.entry.ts"} } %}

### Swap an indicator

A pressed Toggle carries `data-pressed`. Two icons in one grid cell and a `group-data-[pressed]:` variant crossfade the icons, here with a short blur and scale.

{% component: "ui:componentExample", arguments: { "componentName": "AnimationExamples.toggleIcon", "additionalFiles": {"ToggleIcon.entry.ts": "EXT:docs/Resources/Private/Components/ui/AnimationExamples/ToggleIcon.entry.ts"} } %}

Collapsible and Button indicators switch with the `hidden` attribute, so they use the same two features as a part that opens: `starting:` styles the indicator that appears and a `[&[hidden]]:` rule styles the one that leaves.

{% component: "ui:componentExample", arguments: { "componentName": "AnimationExamples.collapsibleIndicator" } %}

## JavaScript

CSS cannot animate everything. A list that changes its columns, an element that moves to another parent or a button that changes its width have no CSS transition to reach for. For these, hand the work to an animation library. The examples below use [Motion](https://motion.dev), and the same approach works with any library that animates DOM elements: the callbacks of a component (`onOpenChange`, `onValueChange`, ...) tell you when to start.

```bash
npm install motion
```

### Animate a layout change

Motion's `animateLayout` measures elements before and after a DOM change and animates the difference. Mark the elements with `data-layout` and make the change inside the callback:

```ts
import { animateLayout } from 'motion';

animateLayout(list, () => (list.dataset.view = 'list'), {
    type: 'spring',
    duration: 0.4,
    bounce: 0,
});
```

{% component: "ui:componentExample", arguments: { "componentName": "AnimationExamples.layoutView", "additionalFiles": {"LayoutView.entry.ts": "EXT:docs/Resources/Private/Components/ui/AnimationExamples/LayoutView.entry.ts"} } %}

- `data-layout` on a child keeps its proportions while the parent resizes. `data-layout="position"` moves a child without resizing it, which suits text.
- The scale of a box stretches its border and corners while it animates. The product images are square in both views and the rows have no surface, so nothing looks stretched.

{% component: "ui:alert", arguments: {"title": "Transitions on transform", "text": "Motion writes `transform` on every frame. A CSS transition that includes it, like Tailwind's `transition-all`, works against it, so list the properties you transition.", "variant": "info"} %}

### Move an element into a dialog

An element that moves to another parent animates from its old box to the new one. Here the whole card moves into the dialog and grows, while the image, title and price settle into their new places and the description fades in. The dialog panel stays still and the card brings its own background, so Motion measures the card where it lands.

On its way back the card stays in the layer of the dialog and flies to a fixed position over its button, so it never passes below the backdrop. Once it has landed, it moves back into the list.

{% component: "ui:componentExample", arguments: { "componentName": "AnimationExamples.productCards", "additionalFiles": {"ProductCards.entry.ts": "EXT:docs/Resources/Private/Components/ui/AnimationExamples/ProductCards.entry.ts"} } %}

### Animate a button's width and label

A width cannot transition between two `auto` values, so a button whose label changes length jumps. The three labels are in the markup and `data-status` decides which one is displayed. They enter and leave with the `@starting-style` and `allow-discrete` pair from above, and `justify-center` keeps them in the middle of the button while it resizes. Motion only animates the width: the script reads it before and after the status change and springs between the two.

{% component: "ui:componentExample", arguments: { "componentName": "AnimationExamples.loginLinkButton", "additionalFiles": {"LoginLinkButton.entry.ts": "EXT:docs/Resources/Private/Components/ui/AnimationExamples/LoginLinkButton.entry.ts"} } %}
