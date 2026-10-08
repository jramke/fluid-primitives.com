# Live Region

**Announce your own messages to screen readers through the same hidden live region Input, Textarea, FieldArray, Select and Combobox use.**

Fluid Primitives has no live-region part or ViewHelper of its own. [`@zag-js/live-region`](https://zagjs.com) keeps one visually hidden node per level (`polite` or `assertive`) at the end of `<body>`, creates it when the first caller asks for it, and every caller of that level shares it. Use it directly for your own messages, e.g. "Saved" after an async request.

## Usage

```bash
npm install @zag-js/live-region
```

```typescript
import { createLiveRegion } from '@zag-js/live-region';

const liveRegion = createLiveRegion({ level: 'polite' });

async function save() {
    await fetch('/save', { method: 'POST' });
    liveRegion.announce('Saved');
}
```

Call `createLiveRegion()` early in your entry file, not at the moment you announce. It adds the hidden node to the page right away, and NVDA and JAWS only announce a `role="status"` region that already exists before its text changes.

## Announcing on page load

A message set while the page is still loading is often missed. Pass a delay in milliseconds as the second argument, a few hundred is enough:

```typescript
liveRegion.announce('Your changes were saved', 500);
```

## Notes

- The node is shared page-wide. `polite` is used by Input, Textarea and FieldArray, `assertive` by Select and Combobox.
- `destroy()` currently removes the node for every user of that level, not just yours. A fix upstream in Zag is pending, so only call it when the whole page is going away.
- Use `level: 'assertive'` only for urgent messages - it interrupts whatever the screen reader is saying.
