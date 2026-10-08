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
- Submits ISO dates, whatever format the input shows
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

The calendar has three views: `day`, `month`, and `year`. Repeat the `view` with `view="month"` and `view="year"` to switch between them, as `ui:datePicker.content` does. The `tableHeader` and `tableBody` belong to the `table` they sit in. Its cells, the weekday headings, and the options of `monthSelect` and `yearSelect` are not part of the markup you write: they depend on the locale and on the month shown, so the client builds them. Because of that, the grid of an `inline` date picker is empty until it hydrates: `showSkeleton="{true}"` on `ui:datePicker.content` fills the gap with a placeholder.

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

### Multiple Months

`numOfMonths` shows several months of the day view next to each other. Render one `table` per month, with the `offset` of the month it shows (`0` for the first, `1` for the next): `ui:datePicker.content` does it for you, with a table for each of the `numOfMonths`. The month and year views always show a single table.

{% component: "ui:componentExample", arguments: { "componentName": "DatePickerExamples.multipleMonths" } %}

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

The `hiddenInput` submits the selected dates as ISO dates (`2025-06-15`), whatever format the `locale` shows them in. The visible `input` has no `name` and is not submitted, and `ui:datePicker.root` renders the `hiddenInput` for you. A range or several dates submit one value per date under the same `name`, so end the name with `[]` (`dates[]`) for PHP to receive all of them. A date picker without a date submits an empty value.

Extbase expects `Y-m-d\TH:i:sP` for a `DateTime` property by default. Tell it to take the ISO date instead:

```php
public function initializeCreateAction(): void
{
    $this->arguments['event']
        ->getPropertyMappingConfiguration()
        ->forProperty('date')
        ->setTypeConverterOption(
            DateTimeConverter::class,
            DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'Y-m-d'
        );
}
```

### Time zone

The calendar highlights today in the time zone of the browser. Set `timeZone` (`Europe/Berlin`) to use another one.

### Localization

Dates are formatted for the language of the site, or for the `locale` you pass. Every label, from the buttons and the day cells to the month and year select and the placeholder of the input, is shipped via XLF in English and German and follows the current Site Language. They are the `datePicker.*` units of the language file. Some contain a `%placeholder%` that is filled in when the label renders, like the `%date%` of a day cell.

For per-template overrides, pass translated strings through the `translations` prop, by the id of the unit without `datePicker.`.

```html
<ui:datePicker.root
    translations="{
        clearTrigger: f:translate(key: 'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:datePicker.clear'),
        dayCell: 'Pick %date%'
    }"
>
    ...
</ui:datePicker.root>
```

The client builds the labels Zag wants as functions of the date or the view from these texts. To change one of those from TypeScript, spread the translations your entry file receives and override it the way Zag takes it:

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
            ["hiddenInput", "Submits the selected dates as ISO dates, one input per date. Renders `<input type=\"hidden\">` elements."],
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
