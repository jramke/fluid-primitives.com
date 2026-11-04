---
preview: true
---

# Toast

**Short messages at the edge of the screen that close on their own.**

{% component: "ui:referenceButtons", arguments: { "name": "Toast" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.simple", "additionalFiles": {"Toaster.entry.ts": "EXT:docs/Resources/Private/Components/ui/Toaster/Toaster.entry.ts", "Simple.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Simple.entry.ts"} } %}

## Features

- Settings as Fluid props, toasts from any script through one `toast` export
- `success`, `info`, `warning`, `error` and `loading` toasts, styled through `data-type`
- Closes after a duration per type and waits while you hover or focus it, or while the tab is in the background
- Lets the toasts overlap or lists them, and queues them beyond `max`
- Updates a toast in place instead of adding the same one twice
- Action trigger, close trigger and loading toasts that follow a promise
- `Alt+T` moves the focus to the toasts, `Escape` closes the focused one
- Screen readers announce new toasts through a live region

## Anatomy

```html
<primitives:toaster>
    <primitives:toaster.toastTemplate>
        <primitives:toast.root>
            <primitives:toast.title />
            <primitives:toast.description />
            <primitives:toast.actionTrigger />
            <primitives:toast.closeTrigger />
        </primitives:toast.root>
    </primitives:toaster.toastTemplate>
</primitives:toaster>
```

The toaster is the region the toasts appear in and holds their settings. Its `toastTemplate` is the template of one toast: the client clones it for every toast and fills in the title, the description and the label of the action trigger. Anything else in it, such as an icon per type, comes from your markup and CSS.

## Installation

{% component: "ui:installationSection", arguments: { "name": ["Toaster", "Toast"] } %}

Render the toaster of the demo above once in your page layout. Its `rootId` is `toaster`, the one its entry file mounts. The entry file exports the store of the toaster as `toast`. Import it wherever you create a toast:

```ts
import { toast } from '../Toaster/Toaster.entry';

toast.create({ type: 'success', title: 'Address saved' });
```

## Examples

### Create a toast

`toast.create()` returns the id of the toast, for `toast.update()` and `toast.dismiss()`. A toast takes a `title`, a `description`, a `type`, a `duration`, an `action`, `closable` and a `priority` from 1 (highest) to 8 for the queue. The store also has `remove()` (without the exit animation), `pause()` and `resume()`, `promise()`, and `success()`, `info()`, `warning()`, `error()` and `loading()` as shortcuts for the `type`.

### Pick a type

Each button below shows a toast of another `type`. The toast renders it as `data-type`, and the styled toast shows an icon for it. A `success` toast stays for 2 seconds, the others for 5 seconds. Set `duration` on the toast or on the toaster to change that.

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.types", "additionalFiles": {"Types.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Types.entry.ts"} } %}

### Add an action

Pass an `action` with a `label` and an `onClick`. Click "Undo" in the toast: the action runs, and the toast closes.

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.action", "additionalFiles": {"Action.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Action.entry.ts"} } %}

### Follow a promise

`toast.promise()` shows a `loading` toast until the promise settles, then turns it into the `success` or the `error` toast.

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.promise", "additionalFiles": {"Promise.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Promise.entry.ts"} } %}

### Update a toast instead of adding it twice

Give the toast an `id`, and update it while `toast.isVisible()` says it is still there. Click "Save draft" a few times: one toast counts the saves and shakes on every click. Screen readers announce the new description.

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.deduplicate", "additionalFiles": {"Deduplicate.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Deduplicate.entry.ts"} } %}

### List the toasts

The toasts overlap by default and spread out while you hover or focus them. Set `overlap` to `false` to list them one below the other. This demo has a toaster of its own, with another `placement` and its own `mount()`.

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.stack", "additionalFiles": {"Stack.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Stack.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toaster",
        "machine": "",
        "parts": [
            ["", "The region the toasts appear in, with their settings. Renders a `<div>` element."],
            ["toastTemplate", "The template of one toast, with a `toast.root` as its root element. Renders a `<template>` element."]
        ]
    }
%}

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toast",
        "parts": [
            ["root", "One toast, the root element of the toast template. Renders a `<div>` element."],
            ["title", "The title of the toast, hidden when it has none. Renders a `<div>` element."],
            ["description", "The description of the toast, hidden when it has none. Renders a `<div>` element."],
            ["actionTrigger", "Runs the action of the toast and closes it, hidden when the toast has no action. Renders a `<button>` element."],
            ["closeTrigger", "Closes the toast, hidden when it is not closable. Renders a `<button>` element."]
        ]
    }
%}
