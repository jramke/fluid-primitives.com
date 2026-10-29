# Toc

**A table of contents that highlights the headings you are reading.**

{% component: "ui:referenceButtons", arguments: { "name": "Toc" } %}

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.simple", "withEntryFile": true } %}

## Features

- Highlights the links of the headings that are visible, tracked with an `IntersectionObserver`
- Highlights several links at once when several headings are visible
- Scrolls its own list so the first active link stays in view
- Follows the headings of a scroll container as well as of the page
- Sets `aria-current="location"` on the active links
- Exposes the position of the active links for an animated indicator

## Anatomy

```html
<primitives:toc.root>
    <primitives:toc.title />
    <primitives:toc.list>
        <primitives:toc.indicator />
        <primitives:toc.item>
            <primitives:toc.link />
        </primitives:toc.item>
    </primitives:toc.list>
</primitives:toc.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Toc" } %}

## Examples

### Indent nested headings

Give an item the `depth` of its heading: `2` for an `h2`, `3` for an `h3`. The item exposes it as the `--depth` CSS variable, which the `ui:toc.item` of the registry turns into an indent.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.nested" } %}

### Follow the active link with an indicator

Place a `ui:toc.indicator` in the list. It follows the active links through the `--top`, `--height`, `--left` and `--width` CSS variables of the root, and the list is its positioning context.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.indicator" } %}

### Draw a rail

Each link draws a segment of the rail beside it, and the segment of an active link turns dark. A heading one level deeper steps the rail in, with a curve where the level changes.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.rail" } %}

### Collapse the list into a summary

Put the list into a `ui:collapsible`. The trigger shows the active heading and how far you are, so the list can stay closed. The script reads the `data-active` attribute of the items, so it needs no handle on the instance.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.collapsible", "withEntryFile": true } %}

### Track headings inside a scroll container

Set `scrollEl` to a CSS selector when the headings scroll inside an element instead of the page. The examples above all do. A click on a link then scrolls that element, with the behavior of `scrollBehavior`.

```html
<ui:toc.root scrollEl="#article">...</ui:toc.root>
```

### Choose which part of the viewport counts

A heading is active while it is inside the `rootMargin` of the scroll container. The default `-20px 0% -40% 0%` activates headings in the upper 60 percent. `threshold` sets how much of a heading has to be inside the margin.

```html
<ui:toc.root rootMargin="0px 0px -80% 0px" threshold="0.5">...</ui:toc.root>
```

### React to the active headings

Create the instance yourself to pass `onActiveChange`. It receives the ids and the items of the active headings.

```typescript
import { mountAll } from 'fluid-primitives';
import { Toc } from 'fluid-primitives/toc';

mountAll('ui:toc', ({ props }) => {
    return new Toc({
        ...props,
        onActiveChange: ({ activeIds }) => console.log(activeIds),
    }).init();
});
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toc",
        "parts": [
            ["root", "Wraps the table of contents and provides its state. Renders a `<nav>` element."],
            ["title", "Labels the table of contents. Renders an `<h2>` element."],
            ["list", "Wraps the items and is the positioning context of the indicator. Renders a `<ul>` element."],
            ["item", "One heading of the page, with its `value` and `depth`. Renders an `<li>` element."],
            ["link", "Links to the heading of its item. Renders an `<a>` element."],
            ["indicator", "Follows the active items. Renders a `<div>` element."]
        ]
    }
%}
