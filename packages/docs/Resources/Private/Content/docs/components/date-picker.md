# Date Picker

**A calendar for picking a single date, several dates, or a range of dates.**

{% component: "ui:referenceButtons", arguments: { "name": "DatePicker" } %}

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.simple", "withEntryFile": true } %}

## Features

- Single, multiple and range selection
- Day, month and year views, switched from the calendar heading, or a month or year picker
- Dates can be typed into the input, in the format of the `locale`
- Keyboard navigation with the arrow keys, `Home`, `End`, `PageUp` and `PageDown`
- Minimum and maximum dates, and a check for unavailable dates
- Range presets, like "last 7 days"
- Formats dates and localizes its labels for the site language
- Submits dates Extbase maps to a `DateTime`, whatever format the input shows
- Works with the Field component
- Opens in a popup, or renders inline

## Anatomy

```html
<primitives:datePicker.root>
    <primitives:datePicker.label />
    <primitives:datePicker.control>
        <primitives:datePicker.input />
        <primitives:datePicker.clearTrigger />
        <primitives:datePicker.trigger />
    </primitives:datePicker.control>
    <primitives:datePicker.hiddenInput />
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

The calendar has three views: `day`, `month` and `year`. Render the views you need inside `content`, each as a `view` with its controls and its `table`. The demos loop over the views. Write them out one by one if they differ. The client builds the cells, the weekday headings and the options of `monthSelect` and `yearSelect`, because they depend on the locale and the month shown. The grid of an `inline` date picker is therefore empty until it hydrates: `showSkeleton="{true}"` on the `tableHeader` and the `tableBody` of the day view shows a placeholder meanwhile (see [Inline](#content-inline)).

## Installation

{% component: "ui:installationSection", arguments: { "name": "DatePicker" } %}

## Examples

### Default value

Pass `defaultValue` as an ISO date (`2025-06-15`), a `DateTimeInterface` or a list of them. A Field inside a Form that is bound to an object takes the value of the property the field is named after.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.defaultValue" } %}

### With form field

Wrap the date picker in `ui:field.root` to share `name`, required and invalid state. The demo is a required date of birth field.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.withField" } %}

### Range selection

Set `selectionMode="{f:constant(name: 'Jramke\FluidPrimitives\Enum\DatePickerSelectionMode::Range')}"` and add an `input` for each end of the range, each with its `index`. A `presetTrigger` selects a range with one click. Its `value` is a case of the `DatePickerRangePreset` enum (`value="{f:constant(name: 'Jramke\FluidPrimitives\Enum\DatePickerRangePreset::Last7Days')}"`): `ThisWeek`, `LastWeek`, `ThisMonth`, `LastMonth`, `ThisQuarter`, `LastQuarter`, `ThisYear`, `LastYear`, `Last3Days`, `Last7Days`, `Last14Days`, `Last30Days` or `Last90Days`. Pick the dates of a stay in the demo, or use one of the presets.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.range" } %}

### Multiple selection

Set `selectionMode="{f:constant(name: 'Jramke\FluidPrimitives\Enum\DatePickerSelectionMode::Multiple')}"` and cap the number of dates with `maxSelectedDates`. The input shows the first date, so the demo lists every selected date below it from an `onValueChange` callback in its entry file (see [Unavailable Dates](#content-unavailable-dates)).

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.multiple", "additionalFiles": {"Multiple.entry.ts": "EXT:docs/Resources/Private/Components/ui/DatePickerExamples/Multiple.entry.ts"} } %}

### Multiple months

Set `numOfMonths` to show several months of the day view next to each other, and render one `table` per month with the `offset` of the month it shows (`0` for the first, `1` for the next). The month and year views show a single table. The `ui` copy of `content` hides the days outside the visible month when `numOfMonths` is above `1`, so no day shows up twice.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.multipleMonths" } %}

### Min/max constraints

Restrict the dates that can be selected with `min` and `max`, each an ISO date (`2025-06-15`) or a `DateTimeInterface`, like a date of your model. `defaultFocusedValue` takes the same. The demo accepts appointments within the next 30 days.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.minMax" } %}

### Inline

Set `inline="{true}"` to render the calendar in the page, without a popup. The calendar stays open and needs no `control`.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.inline" } %}

### Unavailable dates

`isDateUnavailable`, `format`, `parse`, `createCalendar` and the `on...Change` callbacks are functions, so they come from your entry file: create the `DatePicker` there and hand them over with the other props. The weekends of the delivery date in the demo are unavailable.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.unavailableDates", "additionalFiles": {"UnavailableDates.entry.ts": "EXT:docs/Resources/Private/Components/ui/DatePickerExamples/UnavailableDates.entry.ts"} } %}

### Month and year picker

Set `minView="{f:constant(name: 'Jramke\FluidPrimitives\Enum\DatePickerView::Month')}"` for a month picker, and `defaultView` the same way to open on the month view. Render the views the calendar can reach: the day view is left out here. A picked month is submitted as its first day. `format` and `parse` show the input as `06/2025` and read it back. They are functions, so they come from the entry file as in [Unavailable Dates](#content-unavailable-dates). `placeholder` tells people what to type.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.monthYear", "additionalFiles": {"MonthYear.entry.ts": "EXT:docs/Resources/Private/Components/ui/DatePickerExamples/MonthYear.entry.ts"} } %}

A year picker uses `DatePickerView::Year` for `minView` and `defaultView`, renders a single `year` view, and has `format` return `String(date.year)` and `parse` return `new CalendarDate(year, 1, 1)`.

### Native selects

The heading of the calendar is up to you. Swap the `viewTrigger` of the day view for a `monthSelect` and a `yearSelect` and people pick the month and the year from the native `<select>` of their device. They are plain elements, so a class restyles them: here they lose their border and arrow to read like the heading. The client fills the options. Months and years outside `min` and `max` are disabled. The month and year views are not reachable without a `viewTrigger`, so only the day view is rendered.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.nativeSelects" } %}

### Select components

The [Select](/docs/components/select) works in the heading too, and takes its items from the date picker. `getMonths()` and `getYears()` return the months in the locale of the date picker and the years `min` and `max` allow, with the ones outside disabled. The select renders its items from an `itemTemplate`, and the entry file clones one per month and year, then hands the select a collection of the same items. A select calls `focusMonth()` or `focusYear()` of the date picker, and `onFocusChange` of the date picker sets the selects back, so the arrow buttons and the keyboard keep them in sync. The date picker creates the selects with `mount()` after its own `init()`, so mount them with `autoMount="{false}"` and a `rootId`.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.selectComponents", "additionalFiles": {"SelectComponents.entry.ts": "EXT:docs/Resources/Private/Components/ui/DatePickerExamples/SelectComponents.entry.ts"} } %}

### Dates in forms

The `hiddenInput`, which `ui:datePicker.root` renders for you, submits each selected date as the W3C date-time of its midnight (`2025-06-15T00:00:00+02:00`), whatever format the `locale` shows it in. A range or several dates submit one value per date under the same `name`, so end the name with `[]` (`dates[]`) for PHP to receive all of them. A date picker without a date submits an empty value.

Extbase maps that format to a `DateTime` property without any configuration. The offset is the one of the time zone PHP runs in, so the property holds the date that was picked. Extbase maps request values to `DateTime`, so type the property `DateTime`, or write a custom type converter if it is a `DateTimeImmutable`. Outside Extbase, `new DateTimeImmutable($_POST['date'])` reads the value.

### Time zone

The calendar works in the time zone of the browser: it highlights today for the person looking at it, and the presets and `min`/`max` count from there. Set `timeZone` (`Europe/Berlin`) to use another one, like the time zone of a venue. The submitted dates carry the offset of the time zone PHP runs in, whatever `timeZone` is set to.

### Localization

Dates are formatted for the language of the site, or for the `locale` you pass. Every label, from the buttons and the day cells to the month and year select and the placeholder of the input, ships in English and German as a `datePicker.*` unit of the language file and follows the site language. Some units contain a `%placeholder%` that is filled in when the label renders, like the `%date%` of a day cell.

Pass `translations` to override labels for one date picker, keyed by the id of the unit without `datePicker.`.

```html
<ui:datePicker.root
    translations="{
        clearTrigger: f:translate(key: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:datePicker.clear'),
        dayCell: 'Pick %date%'
    }"
>
    ...
</ui:datePicker.root>
```

To change a label from TypeScript, spread the translations your entry file receives and override it with a function of the state:

```ts
const datePicker = new DatePicker({
    ...props,
    translations: { ...props.translations, dayCell: state => `Pick ${state.valueText}` },
});
```

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
            ["hiddenInput", "Submits the selected dates as W3C date-times, one input per date. Renders `<input type=\"hidden\">` elements."],
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
