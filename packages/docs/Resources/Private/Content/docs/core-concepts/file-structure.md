# File Structure

Organize your components for maintainability as your design system grows.

## Basic Structure

Components live in `Resources/Private/Components/` within your sitepackage.

### Single-Part Components

Simple components like buttons go in a folder matching their name:

```
Components/
└── ui/
    └── Button/
        └── Button.html
```

### Multi-Part Components

Composable components have a `Root.html` plus additional parts:

```
Components/
└── ui/
    └── Accordion/
        ├── Root.html      # Main wrapper
        ├── Item.html      # Individual accordion item
        ├── Trigger.html   # Clickable header
        └── Content.html   # Expandable content
```

## How Root Components Are Identified

A component becomes "root" - gets its own `rootId`, PHP context class, and client-side hydration -
based on where its file sits on disk, not on registering it anywhere. A file is root if:

- it's a **single-part component**: its name matches its own containing folder
  (`Button/Button.html`), or
- it's literally named `Root.html` (`Accordion/Root.html`), or
- **its own folder has neither of the above** - no file there claims to be the folder's designated
  `Root`, so every file in it is treated as its own independent root component. This covers a
  nested folder of demo/example templates as much as a flat folder of otherwise-unrelated small
  components (a set of icons, each its own file, none named like the folder).

This means folders can nest arbitrarily deep with no extra template path registration for
_detecting_ a file as root at any depth. Its own identity (the key its context and client-side
hydration data are stored under) mirrors that same folder path, one nested level per segment,
minus only a trailing `.root` marker - a real `Root.html` names the same folder its own siblings
already resolve to, so it adds nothing of its own:

- An atomic-design tier is kept as a real, distinct identity level, not collapsed away -
  `<ui:molecules.checkboxGroup.root>` becomes `molecules.checkboxGroup`, `<ui:molecules.tooltip.root>`
  becomes `molecules.tooltip` - siblings under the same `molecules` namespace, never merged with
  each other or with an unrelated, untiered `checkboxGroup`/`tooltip`.
- A component's own folder is _itself_ the leaf identity holding its real instance data
  (`hydrationData.ui.checkboxGroup.<rootId>`) - never a namespace something else can nest under. An
  independently-root leaf (folder-shape default) keeps its own last segment for exactly this
  reason: it's genuinely distinguishing identity, not a marker to drop.

```
Components/
└── ui/
    ├── molecules/
    │   └── CheckboxGroup/
    │       ├── Root.html            # identity "molecules.checkboxGroup"
    │       └── Label.html           # identity "molecules.checkboxGroup" - shares its root's identity
    ├── CheckboxGroup/
    │   └── Root.html                # identity "checkboxGroup" - a leaf holding real instance data
    └── CheckboxGroupExamples/
        └── SelectAll.html           # <ui:checkboxGroupExamples.selectAll> - identity "checkboxGroupExamples.selectAll", own root, own context class, own hydration bucket
```

**Disallowed: nesting an unrelated root inside a real component's own folder.** A component's own
folder is already a leaf identity (it holds that component's real instance records) - it can never
also be a namespace another, unrelated root nests under. This is why a demo/example that needs its
own context class or hydration bucket lives in its own sibling `<Component>Examples/` folder
(`ui/CheckboxGroupExamples/`, next to `ui/CheckboxGroup/`), never nested inside the real
component's own folder (`ui/CheckboxGroup/Examples/`) - the latter would make `checkboxGroup` both
a leaf record and a namespace at once, and `mountAll('ui:checkboxGroup', ...)` would mistake the
nested example for a real `CheckboxGroup` instance. The same rule applies one level up too: don't
place another component's own root inside a folder that already belongs to a different declared
root (`MyComponent/MyComponent.html` plus `MyComponent/AnotherComponent/AnotherComponent.html`) -
give `AnotherComponent` its own top-level folder instead.

**Caveat**: this only looks at which files exist in a folder, not at what a file's own body does.
If you build a thin wrapper folder around another component's parts and don't (yet) wrap that
component's own `Root.html` there too, every part you _did_ wrap gets treated as its own root -
from that folder's point of view, no `Root.html` exists in it. Wrap the part's actual `Root.html`
as well (even as a thin `ui:useProps` passthrough) to avoid this.

## Recommended Structure

As your component library grows, separate primitive building blocks from composed components:

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

**Why this split?**

- `ui/` contains flexible primitives with minimal opinions
- Root `Components/` contains opinionated, ready-to-use compositions
- You can use `<ui:alert.root>` for flexibility or `<ui:alert>` for convenience

### Template Path Configuration

Register both paths so `ui/` components don't need the extra prefix:

```php
$templatePaths->setTemplateRootPaths([
    // First: ui/ subfolder (so <ui:button> works, not <ui:ui.button>)
    ExtensionManagementUtility::extPath('my_sitepackage', 'Resources/Private/Components/ui'),
    // Second: root Components folder
    ExtensionManagementUtility::extPath('my_sitepackage', 'Resources/Private/Components'),
]);
```

## Composed Components

Creating wrapper components reduces repetitive markup. Here's an `Alert` that composes the primitive parts:

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
<ui:alert title="Heads up" text="Something important happened" variant="warning" />

<!-- Or use primitives directly for custom layouts -->
<ui:alert.root variant="error">
    <ui:alert.icon />
    <ui:alert.content>
        <strong>Error:</strong> Custom layout with multiple paragraphs...
    </ui:alert.content>
</ui:alert.root>
```

This pattern gives you both convenience and flexibility.
