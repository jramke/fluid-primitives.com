# Date Picker

**A calendar-based input for picking a single date, multiple dates, or a date range.**

{% component: "ui:referenceButtons", arguments: { "name": "DatePicker" } %}

{% component: "ui:componentExample", arguments: { "componentName": "DatePicker.examples.simple", "withEntryFile": true } %}

## Features

- Single, multiple, and range selection modes
- Keyboard navigation across days, months, and years (arrow keys, Home/End, Page Up/Down)
- Min/max date constraints and per-date availability checks
- Date range presets (this week, last month, last 7 days, ...)
- Works with the Field component for form integration
- Can render as a popover or inline, without a popover

## Installation

{% component: "ui:installationSection", arguments: { "name": "DatePicker" } %}

## Examples

### With Form Field

Use with the Field component for form validation.

{% component: "ui:componentExample", arguments: { "componentName": "DatePicker.examples.withField" } %}

### Range Selection

Select a range of dates, with quick-pick presets.

{% component: "ui:componentExample", arguments: { "componentName": "DatePicker.examples.range" } %}

### Multiple Selection

Select several individual dates, up to a maximum count.

{% component: "ui:componentExample", arguments: { "componentName": "DatePicker.examples.multiple" } %}

### Min/Max Constraints

Restrict the selectable range with `min`/`max`.

{% component: "ui:componentExample", arguments: { "componentName": "DatePicker.examples.minMax" } %}

### Inline

Render the calendar directly in the page, without a popover, via `inline="{true}"`. Because there's
no `defaultOpen`/popup state gating its first paint, an inline calendar's grid is visible - and
empty - until client-side hydration runs and builds it. `showSkeleton="{true}"` on the styled
`ui:datePicker.content` renders a static placeholder grid to cover that gap; it costs nothing once
hydrated, since `render()` replaces the table's children unconditionally on first paint.

{% component: "ui:componentExample", arguments: { "componentName": "DatePicker.examples.inline" } %}

### Client-rendered grid

Unlike every other primitive in this library, the calendar grid (day/month/year cells, and the
weekday header row) is **not** authored as Fluid templates - it's computed at runtime from
locale/calendar-system/focused date and built entirely client-side in `DatePicker.ts`. Only the
`table`, `tableHeader`, and `tableBody` shells are server-rendered, as empty ref'd containers.
Styling for the cells themselves is applied via CSS selectors on the shared `table` wrapper rather
than a `class` prop on the cells (see `ui:datePicker.table`'s own template for the selectors used).

### Custom date formatting or availability

`format`, `parse`, `isDateUnavailable`, `createCalendar`, and the `onXChange` callbacks are
function-typed and can't be expressed as a Fluid template argument. To customize any of them,
subclass `DatePicker` in TypeScript and override `transformProps()`/`initMachine()` before mounting
it, the same way you would for any other client-side-only behavior.

### Localization

Default labels (clear button, month/year select, calendar dialog, week column header) are shipped
via XLF and follow the current Site Language. For per-template overrides, pass translated strings
through the `translations` prop.

```html
<ui:datePicker.root
    translations="{
        clearTriggerLabel: f:translate(key: 'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:datePicker.clear')
    }"
>
    ...
</ui:datePicker.root>
```

Note that in range mode, `getInputProps({index: 0})` and `({index: 1})` both receive the same
`name` from zag - a consumer needing two distinct form values for the range endpoints should pass
`ids.input` or read `api.valueAsString` directly instead.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "DatePicker",
        "parts": [
            ["root", "Provides shared date picker state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels an input. Renders a `<label>` element."],
            ["control", "Groups the input and trigger. Renders a `<div>` element."],
            ["input", "Allows typing or displaying the date as text. Renders an `<input>` element."],
            ["clearTrigger", "Clears the current selection. Renders a `<button>` element."],
            ["trigger", "Opens and closes the calendar. Renders a `<button>` element."],
            ["rangeText", "Displays a separator or text between range inputs. Renders a `<span>` element."],
            ["positioner", "Positions the floating calendar content. Renders a `<div>` element."],
            ["content", "Contains the calendar views. Renders a `<div>` element."],
            ["view", "Wraps one calendar view (day/month/year). Renders a `<div>` element."],
            ["viewControl", "Groups the previous/next/view-switch triggers for a view. Renders a `<div>` element."],
            ["viewTrigger", "Switches between day/month/year views. Renders a `<button>` element."],
            ["prevTrigger", "Navigates to the previous month/year/decade. Renders a `<button>` element."],
            ["nextTrigger", "Navigates to the next month/year/decade. Renders a `<button>` element."],
            ["table", "Wraps a calendar view's grid. Renders a `<table>` element."],
            ["tableHeader", "Wraps the weekday header row, built client-side. Renders a `<thead>` element."],
            ["tableBody", "Wraps the day/month/year cells, built client-side. Renders a `<tbody>` element."],
            ["monthSelect", "A quick-jump select for the month, options built client-side. Renders a `<select>` element."],
            ["yearSelect", "A quick-jump select for the year, options built client-side. Renders a `<select>` element."],
            ["presetTrigger", "Applies a predefined date range. Renders a `<button>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:datePicker.root>
    <primitives:datePicker.label />
    <primitives:datePicker.control>
        <primitives:datePicker.input />
        <primitives:datePicker.clearTrigger />
        <primitives:datePicker.trigger />
        <primitives:datePicker.rangeText />
    </primitives:datePicker.control>
    <primitives:datePicker.positioner>
        <primitives:datePicker.content>
            <primitives:datePicker.view>
                <primitives:datePicker.viewControl>
                    <primitives:datePicker.prevTrigger />
                    <primitives:datePicker.viewTrigger />
                    <primitives:datePicker.nextTrigger />
                </primitives:datePicker.viewControl>
                <primitives:datePicker.table>
                    <primitives:datePicker.tableHeader />
                    <primitives:datePicker.tableBody />
                </primitives:datePicker.table>
            </primitives:datePicker.view>
            <primitives:datePicker.monthSelect />
            <primitives:datePicker.yearSelect />
            <primitives:datePicker.presetTrigger />
        </primitives:datePicker.content>
    </primitives:datePicker.positioner>
</primitives:datePicker.root>
```
