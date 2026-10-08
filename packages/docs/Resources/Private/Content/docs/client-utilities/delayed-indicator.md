# DelayedIndicator

**Delays showing a transient value long enough to avoid flicker, and once shown, keeps it visible for a minimum duration.**

`DelayedIndicator<T>` wraps any value, not just a boolean loading flag. Feed it every new value through `set()`; values you mark as "transient" are held back briefly before appearing and, once shown, stay for a minimum duration - everything else passes through immediately.

## Usage

```typescript
import { DelayedIndicator } from 'fluid-primitives';

const indicator = new DelayedIndicator({
    isTransient: value => value === true,
    onChange: visible => {
        spinnerEl.hidden = !visible;
    },
});

async function loadResults(query: string) {
    indicator.set(true);
    try {
        const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
        const results = await response.json();
        renderResults(results);
    } finally {
        indicator.set(false);
    }
}
```

Call `indicator.destroy()` when it's no longer needed (e.g. on component teardown) to cancel any in-flight timer.

## Options

| Option         | Type                    | Default | Description                                                                                                                    |
| -------------- | ----------------------- | ------- | ------------------------------------------------------------------------------------------------------------------------------ |
| `isTransient`  | `(value: T) => boolean` | -       | Marks which values are transient and need delay/hold behavior. Every other value is treated as settled.                        |
| `onChange`     | `(value: T) => void`    | -       | Called with each value once it's ready to be shown.                                                                            |
| `showDelayMs`  | `number`                | `150`   | How long a transient value must persist before it's shown at all. Must be finite and `>= 0`.                                   |
| `minVisibleMs` | `number`                | `300`   | Once shown, the minimum time a transient value stays current before a settled value can replace it. Must be finite and `>= 0`. |

## Behavior

- A transient value is held for `showDelayMs` before `onChange` first receives it.
- Once shown, it stays current for at least `minVisibleMs`, even if a settled value arrives sooner.
- A settled value that arrives before any transient value has been shown is passed to `onChange` immediately.
- A settled value that arrives while a transient value is being shown waits for that value's `minVisibleMs` floor, then is passed to `onChange`.
- A transient value arriving while one is already shown updates the displayed content immediately, without resetting the `minVisibleMs` clock.
- `set()` does not deduplicate - passing the same value again may still result in another `onChange` call, since this is a temporal filter on state rather than a `distinctUntilChanged()` operator.

## Driving multiple UI elements from one instance

Because `isTransient`/`onChange` work on any value type, one `DelayedIndicator` can keep several pieces of UI in sync - for example a spinner and a status message that should always change together. From the Combobox [Async Search](/docs/components/combobox) example:

```typescript
type SearchStatus = 'loading' | 'error' | 'empty' | 'idle' | 'results';

const status = new DelayedIndicator<SearchStatus>({
    isTransient: s => s === 'loading',
    onChange: renderStatus, // toggles both the spinner and the status text together
});

status.set(getSearchStatus(api, hasResults));
```

`renderStatus` reads the single incoming `status` value to update both the spinner's visibility and the status text, so the two are always derived from the same source and change in lockstep.
