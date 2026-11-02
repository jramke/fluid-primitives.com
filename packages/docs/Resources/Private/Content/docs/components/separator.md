# Separator

**A separator element between groups of content, read by screen readers.**

{% component: "ui:referenceButtons", arguments: { "name": "Separator" } %}

{% component: "ui:componentExample", arguments: { "componentName": "SeparatorExamples.simple" } %}

## Features

- `role="separator"` with `aria-orientation`
- Horizontal or vertical
- Server-rendered only, it needs no client script
- Works with `asChild`, e.g. on an `<li>` in a list

## Anatomy

The separator is a single element, so the file named after its folder is the root (`<ui:separator>`). See [Root or the Folder Name](/docs/core-concepts/file-structure#content-root-or-the-folder-name).

```html
<primitives:separator />
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Separator" } %}

## Examples

### Separate stacked content

A separator is horizontal by default. Style it through `data-orientation="horizontal"`.

{% component: "ui:componentExample", arguments: { "componentName": "SeparatorExamples.horizontal" } %}

### Separate items in a row

Set `orientation` to `vertical` for a separator between items side by side, as in the links at the top of this page. It renders `aria-orientation="vertical"` and `data-orientation="vertical"`.

```html
<ui:separator
    orientation="{f:constant(name: 'Jramke\FluidPrimitives\Enum\Orientation::Vertical')}"
/>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Separator",
        "parts": [
            ["", "The separator. Renders a `<div>` element with `data-orientation` set to `horizontal` or `vertical`."]
        ]
    }
%}
