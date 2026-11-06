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
- Markup per type, such as an icon, and a template per type, which change in place with the type
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
            <primitives:toast.indicator />
            <primitives:toast.title />
            <primitives:toast.description />
            <primitives:toast.actionTrigger />
            <primitives:toast.closeTrigger />
        </primitives:toast.root>
    </primitives:toaster.toastTemplate>
</primitives:toaster>
```

The toaster is the region the toasts appear in and holds their settings. Its `toastTemplate` is the template of one toast: the client clones it for every toast and fills in the title, the description and the label of the action trigger. A part keeps what the template puts into it while the toast has no text for it. An `indicator` shows markup for one type, and a `toastTemplate` with a `type` is the template of one type.

A toast template holds plain markup. A component that hydrates, such as `ui:button`, or a `ui:portal` inside it is cloned without its script, so it doesn't work in a toast.

## Installation

{% component: "ui:installationSection", arguments: { "name": ["Toaster", "Toast"] } %}

Render the toaster once in your page layout, with the `rootId` its entry file mounts:

```html
<ui:toaster rootId="toaster" />
```

Without content, it renders the toast template you see in the demo above, with an icon for four types. Put a `ui:toaster.toastTemplate` inside to use one of your own. That replaces the default template, so one of yours has no `type`, for the toasts of every type without a template of its own. A toast template has exactly one root element, a `ui:toast.root`. The entry file exports the store of the toaster as `toast`. Import it wherever you create a toast:

```ts
import { toast } from '../Toaster/Toaster.entry';

toast.create({ type: 'success', title: 'Message sent' });
```

## Examples

### Create a toast

`toast.create()` returns the id of the toast, for `toast.update()` and `toast.dismiss()`. A toast takes a `title`, a `description`, a `type`, a `duration`, an `action`, `closable` and a `priority` from 1 (highest) to 8 for the queue. The store also has `remove()` (without the exit animation), `pause()` and `resume()`, `promise()`, and `success()`, `info()`, `warning()`, `error()` and `loading()` as shortcuts for the `type`.

### Pick a type

Each button below shows a toast of another `type`. The toast renders it as `data-type` and shows the `toast.indicator` of its type, the others are `hidden`. The toaster of the first demo has an indicator with an icon for four types, an `info` toast shows none. When the type changes, as in a promise, the indicators change with it. Only these five types can have an indicator and a template of their own. An icon in an indicator is decoration, so give it `aria-hidden="true"`. For screen readers, name the type in the title, or put visually hidden text such as "Error:" into the indicator. A `success` toast stays for 2 seconds, the others for 5 seconds. Set `duration` on the toast or on the toaster to change that.

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

The toasts overlap by default and spread out while you hover or focus them. Set `overlap` to `false` to list them one below the other. This demo has a toaster of its own, with another `placement` and its own `mount()`. Give every toaster of a page its own `placement`: two toasters with the same one share the id of their region, and `Alt+T` focuses only one of them.

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.stack", "additionalFiles": {"Stack.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Stack.entry.ts"} } %}

### Give a type a template of its own

A `toastTemplate` with a `type` is the template of the toasts of that type, the one without a `type` the template of all others. When a toast changes to a type with another template, as when a promise fails, it stays in its place and takes the content of the new template:

```html
<ui:toaster rootId="toaster">
    <ui:toaster.toastTemplate>...</ui:toaster.toastTemplate>
    <ui:toaster.toastTemplate
        type="{f:constant(name: 'Jramke\FluidPrimitives\Enum\ToastType::Error')}"
    >
        <ui:toast.root class="border-destructive">
            <div class="grid flex-1 gap-1">
                <ui:toast.title>Something went wrong</ui:toast.title>
                <ui:toast.description />
                <a
                    href="{f:uri.page(pageUid: settings.supportPage)}"
                    class="text-sm underline underline-offset-4"
                    >Contact support</a
                >
            </div>
            <ui:toast.closeTrigger />
        </ui:toast.root>
    </ui:toaster.toastTemplate>
</ui:toaster>
```

The title of this template says "Something went wrong" while the toast has no title of its own.

### Fill an element of your own

Pass the data of a toast as `meta`, and mark the element in the template with `ui:ref`. A subclass of `Toast` fills it, and a subclass of `Toaster` creates that toast. This demo has a toaster of its own for the cart, and each button passes the image of its product:

{% component: "ui:componentExample", arguments: { "componentName": "ToastExamples.cart", "additionalFiles": {"Cart.entry.ts": "EXT:docs/Resources/Private/Components/ui/ToastExamples/Cart.entry.ts"} } %}

Give such an element a fixed size, an image a `width` and a `height`: the toaster measures a toast when its content changes, not when an image loads.

A type change can swap every part of a toast except its root for the ones of another template. Query the parts in `render()`, as the cart toast does, and don't keep them or add listeners to them in `init()`. To animate the swap, override `replaceContent()` and call `super.replaceContent()` inside.

### Style the toasts with your own CSS

The client places every toast through CSS variables on its root, so your CSS has to use them: `--x` and `--y` for `translate`, `--scale` for `scale`, and `--z-index`, `--height` and `--opacity`. Transition them, and the toasts slide in, pile up and spread out. A closing toast has `data-state="closed"`, and a toast behind the front one has `data-sibling`:

```html
<primitives:toast.root class="toast">...</primitives:toast.root>
```

```css
.toast {
    translate: var(--x) var(--y);
    scale: var(--scale);
    z-index: var(--z-index);
    height: var(--height);
    opacity: var(--opacity);
    transition-property: translate, scale, opacity, height;
    transition-duration: 400ms;
    transition-timing-function: cubic-bezier(0.21, 1.02, 0.73, 1);
}

.toast[data-state='closed'] {
    transition-duration: 200ms;
}

/* a toast behind the front one takes the height of the front one, so hide what it holds */
.toast > * {
    transition: opacity 400ms;
}

.toast[data-overlap][data-sibling] > * {
    opacity: 0;
}
```

## Show TYPO3 flash messages

Write a component of your own that renders the flash messages as JSON and marks the `<script>` with [`ui:ref`](/docs/viewhelpers/ref). `f:page.footerData` puts it at the end of the page, apart from the markup of your plugin:

**`Components/FlashMessages/FlashMessages.html`**:

```html
<ui:prop name="queueIdentifier" type="string" optional="{true}" />

<f:flashMessages as="flashMessages" queueIdentifier="{queueIdentifier}">
    <f:page.footerData>
        <script type="application/json" {ui:ref(name: 'messages')}>{flashMessages -> f:format.json() -> f:format.raw()}</script>
    </f:page.footerData>
</f:flashMessages>

<vite:asset entry="EXT:my_extension/Resources/Private/Components/FlashMessages/FlashMessages.entry.ts" />
```

Use it where your plugin template has `f:flashMessages`. It reads the same queue, the one of the plugin by default:

```html
<ui:flashMessages />
```

`f:format.json` escapes `<` and `>`, so a message cannot end the `<script>` element. The entry file finds the `<script>` by its ref and shows the messages with the toaster. The keys of `types` are the values of `ContextualFeedbackSeverity`:

**`Components/FlashMessages/FlashMessages.entry.ts`**:

```ts
import { mountAll } from 'fluid-primitives';
import { toast } from '../ui/Toaster/Toaster.entry';

const types = { [-2]: 'info', [-1]: 'info', 0: 'success', 1: 'warning', 2: 'error' } as const;

export function showFlashMessages(
    messages: { severity: keyof typeof types; title: string; message: string }[]
) {
    messages.forEach(({ severity, title, message }) =>
        toast.create({ type: types[severity], title, description: message })
    );
}

mountAll('ui:flashMessages', ({ createHydrator }) => {
    const script = createHydrator().query('messages');
    if (!script) return;

    showFlashMessages(JSON.parse(script.textContent ?? '[]'));
    script.remove();
});
```

### Flash messages of an AJAX request

An action you call with [`extbase.post()`](/docs/client-utilities/extbase) answers with JSON, so nothing renders its flash messages. Send them along in the response and flush the queue, otherwise they show up on the next page load:

```php
public function subscribeAction(Subscriber $subscriber): ResponseInterface
{
    $this->subscriberRepository->add($subscriber);
    $this->addFlashMessage('Check your inbox to confirm the subscription.', 'Almost done');

    return $this->jsonResponse(json_encode([
        'flashMessages' => $this->getFlashMessageQueue()->getAllMessagesAndFlush(),
    ], JSON_THROW_ON_ERROR));
}
```

```ts
import { extbase } from 'fluid-primitives';
import { showFlashMessages } from '../FlashMessages/FlashMessages.entry';

const response = await extbase.post(url, values);
showFlashMessages((await response.json()).flashMessages);
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toaster",
        "machine": "",
        "parts": [
            ["", "The region the toasts appear in, with their settings. Renders a `<div>` element."],
            ["toastTemplate", "The template of one toast, with a `toast.root` as its root element, or of the toasts of one `type`. Renders a `<template>` element."]
        ]
    }
%}

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toast",
        "parts": [
            ["root", "One toast, the root element of the toast template. Renders a `<div>` element."],
            ["indicator", "Markup for one `type` of toast, such as its icon, `hidden` while the toast has another type. Renders a `<div>` element."],
            ["title", "The title of the toast, or what the template puts into it while the toast has none. Hidden when both are empty. Renders a `<div>` element."],
            ["description", "The description of the toast, or what the template puts into it while the toast has none. Hidden when both are empty. Renders a `<div>` element."],
            ["actionTrigger", "Runs the action of the toast and closes it, hidden when the toast has no action. Renders a `<button>` element."],
            ["closeTrigger", "Closes the toast, hidden when it is not closable. Renders a `<button>` element."]
        ]
    }
%}
