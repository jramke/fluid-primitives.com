# File Structure

By default components live in `Resources/Private/Components/` of your extension. This is the folder structure we and Fluid itself recommend and the one `ui:add` writes.

Its content structure differs from the structure of [native Fluid components](https://docs.typo3.org/other/typo3fluid/fluid/main/en-us/Usage/Components.html), which give every segment of the tag name its own folder and a template named after the component.

Fluid Primitives keeps the parts of one component together in a single folder. The last segment of the tag name is the file, the one before it the folder. `AbstractComponentCollection` of Fluid Primitives resolves names this way, and a collection cannot change the mapping.

## Basic structure

### Single-part components

A component that is a single element goes in a folder named like the component:

```
Components/
└── ui/
    └── Button/
        └── Button.html
```

### Multi-part components

A composable component has a `Root.html` plus one file per part:

```
Components/
└── ui/
    └── Accordion/
        ├── Root.html      # Main wrapper
        ├── Item.html      # Individual accordion item
        ├── Trigger.html   # Clickable header
        └── Content.html   # Expandable content
```

### Root or the folder name

A folder has one root template, and there are two ways to name it:

- **Root** is the default name for multi-part components (`<ui:accordion.root>`). The `.root` suffix makes the entry point obvious next to its parts.
- **A file named like the folder** is for a component that is a single element first and has parts as optional extras. `Button/Button.html` with an optional `Button/Indicator.html` gives `<ui:button>` and `<ui:button.indicator>`, no `.root` needed.

Use one of them per folder. Every other file in the folder is a part of the root and has access to its context, so `<ui:button.indicator>` can read the context of its button. With a `Root.html`, a file named like the folder is a part like any other (`Input/Input.html` is `<ui:input.input>`).

### Independent components in one folder

A folder with neither a `Root.html` nor a file named like the folder holds independent components. Each file is its own root, which suits a set of icons: `Icon/Menu.html` is `<ui:icon.menu>` and `Icon/Copy.html` is `<ui:icon.copy>`.

A wrapper folder around the parts of another component needs that component's root as well, even as a thin `ui:useProps` passthrough in `Root.html` or the file named like the folder. Without it, every wrapped part counts as an independent component.

### Nested folders

Nest folders to group components. `ui/molecules/CheckboxGroup/Root.html` is `<ui:molecules.checkboxGroup.root>`. The folder path is part of the name, so `molecules.checkboxGroup` and a plain `checkboxGroup` are two components, each with its own [context class](/docs/core-concepts/context#content-custom-context-classes).

### One component per folder

Keep a component's own folder for its own parts. A component nested in it, like `ui/Dialog/CookieSettings/CookieSettings.html`, stores its client data inside the dialog's, so `mountAll('ui:dialog', ...)` takes it for a dialog instance. Give it a folder next to the others: `ui/CookieSettings/CookieSettings.html`.

## Recommended structure

Recommended: keep primitives in `ui/` and your compositions next to it.

```
Components/
├── ui/                         # Primitives (small, unopinionated)
│   ├── Button/
│   ├── Accordion/
│   ├── Dialog/
│   ├── Tooltip/
│   └── Alert/
│       ├── Root.html
│       ├── Icon.html
│       ├── Title.html
│       └── Content.html
│
├── Alert/                      # Composed component (opinionated)
│   └── Alert.html
│
└── RegisterDialog/             # Other composed components
    └── RegisterDialog.html
```

`<ui:alert.root>` gives you full control over the layout, `<ui:alert>` is the ready-made composition. Your [component collection](/docs/getting-started#content-setup) registers both `ui/` and `Components/` as template root paths.

## Composed components

A composed component wraps primitive parts to save repetitive markup. This `Alert` takes a title and an optional text:

**`Components/Alert/Alert.html`**:

```html
<ui:prop name="title" type="string" />
<ui:prop name="text" type="string" optional="{true}" />
<ui:useProps name="ui:alert.root" props="{0: 'variant'}" />

<ui:alert.root class="{class}" variant="{variant}">
    <ui:alert.icon />
    <ui:alert.title>
        <h3>{title}</h3>
    </ui:alert.title>
    <f:if condition="{text}">
        <ui:alert.content>
            <p>{text}</p>
        </ui:alert.content>
    </f:if>
</ui:alert.root>
```

**Usage**:

```html
<!-- Simple API for common cases -->
<ui:alert
    title="Delivery delayed"
    text="Your order ships within 3 working days."
    variant="warning"
/>

<!-- Or use the parts directly for custom layouts -->
<ui:alert.root variant="error">
    <ui:alert.icon />
    <ui:alert.content>
        <strong>Payment failed.</strong> Check your card details and try again.
    </ui:alert.content>
</ui:alert.root>
```
