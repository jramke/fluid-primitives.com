---
preview: true
---

# Table of Contents

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

### Build the items from your content

`items` takes `TocItem` objects or arrays with a `value` (the id of the heading element) and a `depth`. A `TocItem` also carries the `label` for your template, and only its `value` and `depth` reach the client.

```php
use Jramke\FluidPrimitives\Domain\Dto\TocItem;

$items = array_map(
    static fn(array $header) => new TocItem(value: 'c' . $header['uid'], depth: 2, label: $header['header']),
    $contentElements,
);
```

```html
<ui:toc.root items="{items}">
    <ui:toc.list>
        <f:for each="{items}" as="item">
            <ui:toc.item value="{item.value}"><ui:toc.link>{item.label}</ui:toc.link></ui:toc.item>
        </f:for>
    </ui:toc.list>
</ui:toc.root>
```

### Indent nested headings

List the `depth` of each heading in `items`: `2` for an `h2`, `3` for an `h3`. An item exposes it as the `--depth` CSS variable, which the `ui:toc.item` of the registry turns into an indent.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.nested" } %}

### Follow the active link with an indicator

Place a `ui:toc.indicator` in the list. It follows the active links through the `--top`, `--height`, `--left` and `--width` CSS variables of the root, and the list is its positioning context.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.indicator" } %}

### Draw a rail

Each link draws a segment of the rail beside it, and the segment of an active link turns dark. A heading one level deeper steps the rail in, with a curve where the level changes.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.rail" } %}

### Collapse the list into a summary

Put the list into a `ui:collapsible`. The trigger shows the active heading and how far you are, so the list can stay closed. The script mounts the `Toc` itself and updates the summary from `onActiveChange`: the `--progress` of the ring, the number and the label of the active heading.

{% component: "ui:componentExample", arguments: { "componentName": "TocExamples.collapsible", "additionalFiles": {"Collapsible.entry.ts": "EXT:docs/Resources/Private/Components/ui/TocExamples/Collapsible.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toc",
        "parts": [
            ["root", "Wraps the table of contents, provides its state and takes the `items`. Renders a `<nav>` element."],
            ["title", "Labels the table of contents. Renders an `<h2>` element, set `level` for another heading level."],
            ["list", "Wraps the items and is the positioning context of the indicator. Renders a `<ul>` element."],
            ["item", "One heading of the page, identified by its `value`. Renders an `<li>` element."],
            ["link", "Links to the heading of its item. Renders an `<a>` element."],
            ["indicator", "Follows the active items. Renders a `<div>` element."]
        ]
    }
%}
