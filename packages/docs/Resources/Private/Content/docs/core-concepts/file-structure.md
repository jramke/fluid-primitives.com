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
    <ui:alert.content> <strong>Error:</strong> Custom layout with multiple paragraphs... </ui:alert.content>
</ui:alert.root>
```

This pattern gives you both convenience and flexibility.
