# DelayedIndicator

**Delays showing a transient value long enough to avoid flicker, and once shown, keeps it visible for a minimum duration.**

With the defaults, a spinner appears only when a request takes longer than 150 ms and stays on screen for at least 300 ms. Pass every new value to `set()`. Values that `isTransient` marks are held back before they appear, every other value passes through immediately. The value can be anything, not only a loading flag.

## Usage

```typescript
import { DelayedIndicator, extbase } from 'fluid-primitives';

const indicator = new DelayedIndicator({
    isTransient: value => value === true,
    onChange: visible => {
        spinnerEl.hidden = !visible;
    },
});

async function searchProducts(query: string) {
    indicator.set(true);
    try {
        const response = await extbase.post(searchUrl, { q: query });
        renderResults(await response.json());
    } finally {
        indicator.set(false);
    }
}
```

Call `indicator.destroy()` on teardown to cancel a pending timer.

## Options

{% component: "ui:apiReference", arguments: { "symbol": "DelayedIndicatorOptions" } %}

## Timing

- A transient value reaches `onChange` after `showDelayMs`.
- Once shown, it stays current for at least `minVisibleMs`, even if a settled value arrives sooner.
- A settled value that arrives before a transient value was shown goes to `onChange` right away, so the transient value never appears.
- A settled value that arrives while a transient value is shown waits for that value's `minVisibleMs`, then goes to `onChange`.
- A transient value that arrives while another one is shown updates the display right away and does not restart the `minVisibleMs` clock.
- `set()` passes repeated values on: the same value again can call `onChange` again.

## Update a spinner and a status text together

One `DelayedIndicator` can drive several elements when `onChange` derives all of them from the same value, so they always change together. The Combobox [Async Search](/docs/components/combobox) example does this:

```typescript
type SearchStatus = 'loading' | 'error' | 'empty' | 'idle' | 'results';

const status = new DelayedIndicator<SearchStatus>({
    isTransient: value => value === 'loading',
    onChange: value => {
        spinnerEl.hidden = value !== 'loading';
        statusEl.textContent = statusTexts[value];
    },
});

status.set('loading');
```

## API

{% component: "ui:apiReference", arguments: { "symbol": "DelayedIndicator" } %}
