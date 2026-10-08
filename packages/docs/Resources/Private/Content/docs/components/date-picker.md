# Date Picker

**A calendar for picking a single date, several dates, or a range of dates.**

{% component: "ui:referenceButtons", arguments: { "name": "DatePicker" } %}

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.simple", "withEntryFile": true } %}

## Features

- Single, multiple, and range selection
- Day, month, and year views, switched from the calendar heading
- Dates can be typed into the input, in the format of the `locale`
- Keyboard navigation with the arrow keys, `Home`, `End`, `PageUp`, and `PageDown`
- Minimum and maximum dates, and a check for dates that are unavailable
- Range presets, like "last 7 days"
- Formats dates and localizes its labels for the site language
- Works with Field component for form integration
- Opens in a popup, or renders inline without one

## Anatomy

```html
<primitives:datePicker.root>
    <primitives:datePicker.label />
    <primitives:datePicker.control>
        <primitives:datePicker.input />
        <primitives:datePicker.clearTrigger />
        <primitives:datePicker.trigger />
    </primitives:datePicker.control>
    <primitives:datePicker.positioner>
        <primitives:datePicker.content>
            <primitives:datePicker.view view="day">
                <primitives:datePicker.viewControl view="day">
                    <primitives:datePicker.prevTrigger view="day" />
                    <primitives:datePicker.viewTrigger view="day">
                        <primitives:datePicker.rangeText />
                    </primitives:datePicker.viewTrigger>
                    <primitives:datePicker.nextTrigger view="day" />
                </primitives:datePicker.viewControl>
                <primitives:datePicker.table view="day">
                    <primitives:datePicker.tableHeader view="day" />
                    <primitives:datePicker.tableBody view="day" />
                </primitives:datePicker.table>
            </primitives:datePicker.view>
            <primitives:datePicker.monthSelect />
            <primitives:datePicker.yearSelect />
            <primitives:datePicker.presetTrigger />
        </primitives:datePicker.content>
    </primitives:datePicker.positioner>
</primitives:datePicker.root>
```

The calendar has three views: `day`, `month`, and `year`. Repeat the `view` with `view="month"` and `view="year"` to switch between them, as `ui:datePicker.content` does. Its cells, the weekday headings, and the options of `monthSelect` and `yearSelect` are not part of the markup you write: they depend on the locale and on the month shown, so the client builds them. Because of that, the grid of an `inline` date picker is empty until it hydrates: `showSkeleton="{true}"` on `ui:datePicker.content` fills the gap with a placeholder.

## Installation

{% component: "ui:installationSection", arguments: { "name": "DatePicker" } %}

## Examples

### Default Value

Pass `defaultValue` as an ISO date (`2025-06-15`), a `DateTimeInterface`, or a list of them. A Field inside a Form that is bound to an object takes the value of the property the field is named after.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.defaultValue" } %}

### With Form Field

Use with the Field component for form validation.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.withField" } %}

### Range Selection

Set `selectionMode` to `DatePickerSelectionMode::Range` (an enum, so `selectionMode="{f:constant(name: 'Jramke\FluidPrimitives\Enum\DatePickerSelectionMode::Range')}"`) and add an `input` for each end of the range with its `index`. The `presetTrigger` selects a range with one click.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.range" } %}

### Multiple Selection

Set `selectionMode` to `DatePickerSelectionMode::Multiple` and limit the number of dates with `maxSelectedDates`. The input shows the first date only, so the example lists all of them below it, with an `onValueChange` callback it gives the `DatePicker` in its entry file (see [Unavailable Dates](#unavailable-dates)).

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.multiple", "additionalFiles": {"Multiple.entry.ts": "EXT:docs/Resources/Private/Components/ui/DatePickerExamples/Multiple.entry.ts"} } %}

### Min/Max Constraints

Restrict the dates that can be selected with `min` and `max`, both ISO dates.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.minMax" } %}

### Inline

Render the calendar in the page, without a popup, with `inline="{true}"`. It is always open and needs no `control`.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.inline" } %}

### Unavailable Dates

`isDateUnavailable`, `format`, `parse`, `createCalendar`, and the `on...Change` callbacks are functions, so a template can't pass them. Create the `DatePicker` in your entry file instead and hand them to it with the other props. Here the weekends are unavailable.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.unavailableDates", "additionalFiles": {"UnavailableDates.entry.ts": "EXT:docs/Resources/Private/Components/ui/DatePickerExamples/UnavailableDates.entry.ts"} } %}

### Dates in forms

The input submits the date as it shows it, in the format of the `locale`: `15.06.2025` on a German site. Pass a `format` and a `parse` function to the `DatePicker` (see above) if your server expects another format. In range mode both inputs get the same `name`: end it with `[]` (`dates[]`) so that PHP receives both dates.

### Time zone

The calendar highlights today in the time zone of the browser. Set `timeZone` (`Europe/Berlin`) to use another one.

### Localization

Dates are formatted for the language of the site, or for the `locale` you pass. Labels that are plain text (the clear button, the month and year select, the calendar itself, and the week column) are shipped via XLF and follow the current Site Language. For per-template overrides, pass translated strings through the `translations` prop.

```html
<ui:datePicker.root
    translations="{
        clearTrigger: f:translate(key: 'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:datePicker.clear')
    }"
>
    ...
</ui:datePicker.root>
```

The remaining labels (the days, the buttons that move through the calendar, and the one that opens it) are built from the date or the view, so they are functions of it. They are in English unless you pass `translations` to the `DatePicker` in your entry file.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "DatePicker",
        "parts": [
            ["root", "Provides shared date picker state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels an input. Renders a `<label>` element."],
            ["control", "Groups the input and the triggers. Renders a `<div>` element."],
            ["input", "Shows the selected date as text and lets people type one. Renders an `<input>` element."],
            ["clearTrigger", "Clears the selection. Renders a `<button>` element."],
            ["trigger", "Opens and closes the calendar. Renders a `<button>` element."],
            ["positioner", "Positions the floating calendar. Renders a `<div>` element."],
            ["content", "The calendar surface, positioned by `positioner`. Renders a `<div>` element."],
            ["view", "Wraps one view of the calendar: `day`, `month`, or `year`. Renders a `<div>` element."],
            ["viewControl", "Groups the buttons that move through a view. Renders a `<div>` element."],
            ["prevTrigger", "Moves a view back: to the previous month, year, or decade. Renders a `<button>` element."],
            ["nextTrigger", "Moves a view forward: to the next month, year, or decade. Renders a `<button>` element."],
            ["viewTrigger", "Switches to the next larger view. Renders a `<button>` element."],
            ["rangeText", "Shows the range of dates a view displays, like \"June 2025\". Renders a `<span>` element."],
            ["table", "Wraps the grid of a view. Renders a `<table>` element."],
            ["tableHeader", "Holds the weekday headings of the day view, built on the client. Renders a `<thead>` element."],
            ["tableBody", "Holds the days, months, or years of a view, built on the client. Renders a `<tbody>` element."],
            ["monthSelect", "Jumps to a month, its options built on the client. Renders a `<select>` element."],
            ["yearSelect", "Jumps to a year, its options built on the client. Renders a `<select>` element."],
            ["presetTrigger", "Selects a predefined range of dates. Renders a `<button>` element."]
        ]
    }
%}
