# Add the DatePicker primitive (`@zag-js/date-picker`)

## Context

`@zag-js/date-picker` is already a dependency in `packages/fluid-primitives/package.json` and is
installed locally (`node_modules/@zag-js/date-picker`), but no `DatePicker` primitive exists yet.

Unlike every other primitive in this repo, the calendar grid (day/month/year cells) is **computed
by the machine at runtime** from locale/calendar-system/focused date (`@internationalized/date`),
not authored as static Fluid data the way Accordion/Select/Combobox items are. **Decision: build
the grid 100% client-side**, not in PHP — no `<f:for>` over server-computed dates, no PHP calendar
math. Everything else (popover positioning, form integration, prop shape normalization) mirrors an
existing primitive directly.

## Ground truth (from the installed package, not the docs site)

**24 anatomy parts** (`date-picker.anatomy.mjs`): `clearTrigger, content, control, input, label,
monthSelect, nextTrigger, positioner, presetTrigger, prevTrigger, rangeText, root, table,
tableBody, tableCell, tableCellTrigger, tableHead, tableHeader, tableRow, trigger, view,
viewControl, viewTrigger, yearSelect`.

Gotchas found in `connect.mjs` that the docs-data JSON gets wrong or that are easy to mis-port —
trust `connect.mjs` over `node_modules/@zag-js/docs/data/*.json` wherever they disagree:

- No `arrow`/`anchor`/`backdrop` parts and no `getArrowProps`/`getBackdropProps`, despite
  `css-vars.json` listing CSS vars for them. The popup is just `positioner` + `content`.
- `getWeekNumberHeaderCellProps`/`getWeekNumberCellProps` spread `...parts.tableCell.attrs` — they
  render `data-part="table-cell"`, not their own part. `data-attr.json`'s separate
  `WeekNumberHeaderCell`/`WeekNumberCell` entries are stale.
- **`getTableHeaderProps` is the `<thead>` wrapper. `getTableHeadProps` is each individual
  weekday-name `<th>`** (it sets `aria-hidden: true`). Do not transpose these by pattern-matching
  the name.

Full `connect()` method list: `getRootProps, getLabelProps, getControlProps, getRangeTextProps,
getContentProps, getTableProps, getTableHeadProps, getTableHeaderProps, getTableBodyProps,
getTableRowProps, getWeekNumberHeaderCellProps, getWeekNumberCellProps, getDayTableCellProps,
getDayTableCellTriggerProps, getMonthTableCellProps, getMonthTableCellTriggerProps,
getYearTableCellProps, getYearTableCellTriggerProps, getNextTriggerProps, getPrevTriggerProps,
getClearTriggerProps, getTriggerProps, getViewProps, getViewTriggerProps, getViewControlProps,
getInputProps, getMonthSelectProps, getYearSelectProps, getPositionerProps,
getPresetTriggerProps`.

**45 props** (`date-picker.props.mjs` / `date-picker.types.d.mts`). 9 are function-typed and must
**not** become `ui:prop`s (a Fluid template can't serialize a closure): `createCalendar, format,
parse, isDateUnavailable, onValueChange, onFocusChange, onViewChange, onVisibleRangeChange,
onOpenChange`. A consumer needing one of these subclasses `DatePicker` in TS — note this once on
the docs page, not per-prop.

The remaining 36 props are the `ui:prop` surface, all declared on `Root.fluid.html` (every other
part reads `context.*`, same as `Combobox/Root.fluid.html`). `value`, `defaultValue`, `min`, `max`,
`focusedValue`, `defaultFocusedValue` are `DateValue`/`DateValue[]`-typed — Fluid authors give ISO
date strings, and `DatePicker.ts`'s `transformProps()` converts them via the package's own exported
`parse()`, the same hook `Select.ts`/`Combobox.ts` use to turn a raw items array into a
`ListCollection`.

## Architecture decisions

### 1. Calendar grid, weekday header, and select options are built client-side

`tableHead`, `tableRow`, `tableCell`, `tableCellTrigger` (and week-number cells, which reuse
`tableCell`) get **no `.fluid.html` file**. Only `table` (`<table>` shell), `tableHeader` (empty
`<thead>`), `tableBody` (empty `<tbody>`) are server-rendered, as empty ref'd containers.
`monthSelect`/`yearSelect` are likewise empty `<select>` shells — their `<option>`s are built
client-side too, so the primitive has one rule instead of "grid is client-only, month/year list is
server-only".

Build pattern: mirror `Combobox.ts`'s `syncHiddenInput()` — remove stale elements, create fresh
ones with `this.doc.createElement(...)`, stamp via `this.hydrator.setRefAttributes(el, part,
value)`, spread via `this.spreadProps(el, ...)`. Same mechanism, applied to `<tr>`/`<td>`/cell nodes
instead of hidden inputs.

`Content` is `hidden` until opened (same as `Collapsible`/`Popover`), so the popup case never shows
an empty grid — hydration has run by the time it's visible. Only `inline="{true}"` shows an empty
box until JS boots. State this plainly in the docs page.

**Skeleton for `inline`**: give `TableHeader.fluid.html`/`TableBody.fluid.html` an `<f:slot />`
instead of a fully closed tag — `render()`'s `replaceChildren()` wipes it on first paint regardless,
so it costs nothing. The `/ui` styled wrapper (not the headless primitive) adds a `showSkeleton`
boolean prop to its own `TableBody.fluid.html`/`TableHeader.fluid.html`, same precedent as
`showCloseButton` on `/ui/Popover/Content.fluid.html` (a presentational prop that exists only at
the styled layer). When true, it renders a static 6×7 grid of pulsing placeholder `<td>`/`<th>`
cells inside the slot — no real dates, no locale math, sized to `fixedWeeks`'s 6-week ceiling so
there's no layout shift. Ship as an "Inline with skeleton" example; link it from the docs page's
`inline` note.

### 2. `View` is server-rendered; its contents aren't

`defaultView` is a plain prop with no calendar math, so `View.fluid.html` renders all three view
wrappers (`day`/`month`/`year`), each `hidden` unless it matches `defaultView` — same idiom as
`Collapsible/Content.fluid.html`. The `<table>`/`<select>` shells inside each view are still empty
per decision 1.

### 3. Form integration: `FieldAwareComponent`, no hidden-input mirror

`disabled`/`readOnly`/`required`/`invalid`/`name` map onto the same `propsWithField()` shape
`Select.ts`/`Combobox.ts` already implement — copy verbatim. No hidden-input mirror needed:
`getInputProps()` puts `name: prop("name")` directly on the real `<input>` (its value *is* the
field's value here, unlike Select/Combobox where the visible input holds a label). One zag quirk to
document, not fix: in range mode, `getInputProps({index: 0})` and `({index: 1})` both get the same
`name` — a consumer needing two distinct form values must pass `ids.input` or read
`api.valueAsString` themselves.

### 4. Id overrides — required, one explicit trap

Add to both `Resources/Private/Client/src/lib/hydration.ts` and
`Classes/Utility/ComponentPartIdUtility.php` (confirmed against `date-picker.dom.mjs`):

```ts
ID_NAMESPACE_OVERRIDES: { 'date-picker': 'datepicker' }   // zag ids are "datepicker:<id>:...", not "date-picker:..."
PART_SEGMENT_OVERRIDES: {
  'date-picker': {
    clearTrigger: 'clear',   // datepicker:id:clear
    nextTrigger: 'next',     // datepicker:id:next:<view>
    prevTrigger: 'prev',     // datepicker:id:prev:<view>
    viewTrigger: 'view',     // datepicker:id:view:<view>
  },
}
```

Mirror the same shape in `ComponentPartIdUtility`'s two const arrays. **Do not add a
`positioner: 'popper'` override** — every sibling primitive (Popover, Select, Combobox, Menu,
Tooltip) needs that override because *their* zag `dom.ts` uses the `popper` segment; date-picker's
own `dom.mjs` already uses the plain `positioner` segment, matching this repo's default. Copying the
Popover override here breaks `aria-controls`/positioning lookups. `table`, `input`, `label`,
`control`, `content`, `trigger`, `monthSelect`, `yearSelect` also match the default — no overrides.

### 5. `DatePickerContext` — minimal

- `getState(): string` — `'open'`/`'closed'` from `defaultOpen`, identical to `PopoverContext`.
- `getDefaultValue(): ?array`, `#[ExposeToClient(excludeIfNull: true)]` — copy
  `SelectContext::getDefaultValue()` verbatim (single ISO string → `[string]`, array →
  `Typed::arrayOrNull`, else `null`). Only fixes shape; string→`DateValue` stays client-side.
- `getTranslations(): array`, `#[ExposeToClient]`, via `HasTranslationsTrait` — of
  `IntlTranslations`'s 13 keys, only 5 are plain strings: `monthSelect, yearSelect, clearTrigger,
  content, weekColumnHeader`. Mirror `translationsWithDefaults([...])` over just those 5 (pattern:
  `PopoverContext`/`ComboboxContext`); the other 8 keys are function-typed and left for zag's own
  `mergeWithDefault(defaultTranslations, prop("translations"))` to fill in.

No `getItemState()`-equivalent (no author-provided items to resolve). No backed `Enum` for
`view`/`defaultView`/`minView`/`maxView` — plain strings enforced by zag at runtime, not a
state-variant-sibling pattern.

## File-by-file plan

### `packages/fluid-primitives/Classes/Contexts/DatePickerContext.php`

```php
#[Autoconfigure(public: true)]
class DatePickerContext extends AbstractComponentContext
{
    use HasTranslationsTrait;

    public function __construct(private readonly TranslatorService $translator) {}
    protected function getTranslator(): TranslatorService { return $this->translator; }

    public function getState(): string
    {
        return $this->get('defaultOpen') ? 'open' : 'closed';
    }

    #[ExposeToClient(excludeIfNull: true)]
    public function getDefaultValue(): ?array
    {
        // body = SelectContext::getDefaultValue()
    }

    #[ExposeToClient]
    public function getTranslations(): array
    {
        return $this->translationsWithDefaults([
            'clearTriggerLabel'     => 'date-picker.clearTriggerLabel',
            'monthSelectLabel'      => 'date-picker.monthSelectLabel',
            'yearSelectLabel'       => 'date-picker.yearSelectLabel',
            'contentLabel'          => 'date-picker.contentLabel',
            'weekColumnHeaderLabel' => 'date-picker.weekColumnHeaderLabel',
        ]);
    }
}
```

### Fluid templates — anatomy-to-file mapping

All `ui:prop` declarations live on `Root.fluid.html` only; every other part reads `context.*`.

| Anatomy part | File | Notes |
|---|---|---|
| root | `Root.fluid.html` | all 36 `ui:prop`s; wrapper `<div>`; `data-state`/`data-disabled`/`data-readonly`/`data-empty` |
| label | `Label.fluid.html` | `index` context prop, default `0` |
| control | `Control.fluid.html` | wrapper `<div>` |
| input | `Input.fluid.html` | `{ui:ref(name:'input', value: index, ...)}` — `index` (0/1) as value discriminator, TS uses `spreadPropsByOptionalValue` |
| clearTrigger | `ClearTrigger.fluid.html` | hidden unless `context.defaultValue` (mirror Combobox) |
| trigger | `Trigger.fluid.html` | `<button>` |
| rangeText | `RangeText.fluid.html` | `<span>`, range-mode display text |
| positioner | `Positioner.fluid.html` | `<div>` — no arrow part |
| content | `Content.fluid.html` | `<div role="application">`, hidden unless `defaultOpen`, `data-inline` |
| view | `View.fluid.html` | `value: view` discriminator; hidden unless matches `defaultView` (decision 2) |
| viewControl | `ViewControl.fluid.html` | `value: view` discriminator |
| viewTrigger | `ViewTrigger.fluid.html` | `value: view` discriminator |
| prevTrigger | `PrevTrigger.fluid.html` | `value: view` discriminator |
| nextTrigger | `NextTrigger.fluid.html` | `value: view` discriminator |
| table | `Table.fluid.html` | `value: view` discriminator; empty shell, `<f:slot />` wraps TableHeader/TableBody |
| tableHeader | `TableHeader.fluid.html` | `value: view` discriminator; empty `<thead>`, `<f:slot />` |
| tableBody | `TableBody.fluid.html` | `value: view` discriminator; empty `<tbody>`, `<f:slot />` |
| tableHead, tableRow, tableCell, tableCellTrigger | *(none)* | client-side only (decision 1) |
| monthSelect | `MonthSelect.fluid.html` | empty `<select>` shell, options built client-side |
| yearSelect | `YearSelect.fluid.html` | empty `<select>` shell, options built client-side |
| presetTrigger | `PresetTrigger.fluid.html` | `value` = one of the 13 `DateRangePreset` strings (`thisWeek`, `lastWeek`, `thisMonth`, `lastMonth`, `thisQuarter`, `lastQuarter`, `thisYear`, `lastYear`, `last3Days`, `last7Days`, `last14Days`, `last30Days`, `last90Days`); a literal `DateValue[]` preset is a TS-only escape hatch |

The two new "empty shell" templates:

```html
{# Table.fluid.html #}
<ui:prop name="view" type="string" optional="{true}" default="day" context="{true}" />
<f:variable name="refData" value="{view: view}" />
<table
    {f:if(condition: class, then: 'class="{class}"' )}
    {ui:ref(name: 'table', value: view, data: refData)}
    {ui:attributes()}>
    <f:slot />
</table>
```

```html
{# TableBody.fluid.html #}
<ui:prop name="view" type="string" optional="{true}" default="day" context="{true}" />
<tbody
    {ui:ref(name: 'tableBody', value: view)}
    {ui:attributes()}>
    <f:slot />
</tbody>
```

The `<f:slot />` exists so the `/ui` layer's optional skeleton markup (decision 1) can live inside
the exact element `render()` calls `replaceChildren()` on. `TableHeader.fluid.html` mirrors
`TableBody.fluid.html` for `<thead>`. `View.fluid.html` follows the same `hidden` idiom, keyed off
`view === context.defaultView`.

### `Resources/Private/Primitives/DatePicker/DatePicker.ts`

Extends `FieldAwareComponent`, same shape as `Select.ts`/`Combobox.ts`:

```ts
import * as datePicker from '@zag-js/date-picker';
import { FieldAwareComponent, Machine, normalizeProps } from '../../Client';
import type { FieldMachine } from '../Field/src/field.registry';

const parseDate = (v?: string) => (v ? datePicker.parse(v) : undefined);
const parseDates = (vs?: string[]) => vs?.map(v => datePicker.parse(v));

export class DatePicker extends FieldAwareComponent<datePicker.Props, datePicker.Api> {
    static componentName = 'datePicker'; // kebab: 'date-picker'

    propsWithField(props: datePicker.Props, fieldMachine: FieldMachine): datePicker.Props {
        return {
            ...props,
            disabled: props.disabled ?? fieldMachine.context.get('disabled'),
            readOnly: props.readOnly ?? fieldMachine.context.get('readOnly'),
            required: props.required ?? fieldMachine.context.get('required'),
            invalid: props.invalid ?? fieldMachine.context.get('invalid'),
            name: props.name ?? fieldMachine.prop('name'),
        };
    }

    transformProps(props: datePicker.Props): datePicker.Props {
        return {
            ...props,
            defaultValue: parseDates(props.defaultValue as unknown as string[] | undefined),
            min: parseDate(props.min as unknown as string | undefined),
            max: parseDate(props.max as unknown as string | undefined),
            defaultFocusedValue: parseDate(props.defaultFocusedValue as unknown as string | undefined),
        };
    }

    initMachine(props: datePicker.Props): Machine<any> {
        props = this.withFieldProps(props);
        return new Machine(datePicker.machine, this.transformProps(props));
    }

    initApi() {
        return datePicker.connect(this.machine.service, normalizeProps);
    }

    render() {
        this.subscribeToFieldService();

        // Static/singleton parts: root, label(index), control, clearTrigger, trigger, rangeText,
        // positioner, content — plain getElement()+spreadProps(), same as every other primitive.

        this.spreadPropsByOptionalValue('input', ({ value }) =>
            this.api.getInputProps({ index: Number(value ?? 0) })
        );

        (['day', 'month', 'year'] as const).forEach(view => {
            this.spreadPropsByValue('view', ({ value }) =>
                value === view ? this.api.getViewProps({ view }) : null
            );
            this.spreadPropsByValue('viewControl', ({ value }) =>
                value === view ? this.api.getViewControlProps({ view }) : null
            );
            this.spreadPropsByValue('viewTrigger', ({ value }) =>
                value === view ? this.api.getViewTriggerProps({ view }) : null
            );
            this.spreadPropsByValue('prevTrigger', ({ value }) =>
                value === view ? this.api.getPrevTriggerProps({ view }) : null
            );
            this.spreadPropsByValue('nextTrigger', ({ value }) =>
                value === view ? this.api.getNextTriggerProps({ view }) : null
            );

            const tableEl = this.getElements('table', this.doc).find(el => el.dataset.value === view);
            if (tableEl) this.spreadProps(tableEl, this.api.getTableProps({ view }));

            this.buildTable(view);
        });

        this.buildMonthSelect();
        this.buildYearSelect();
    }

    private buildTable(view: 'day' | 'month' | 'year') { /* see below */ }
    private buildMonthSelect() { /* getElement('monthSelect'); replaceChildren with <option>s from this.api.getMonths() */ }
    private buildYearSelect() { /* same, from this.api.getYears() */ }
}
```

`buildTable()` — the one genuinely new piece, extending `Combobox.ts`'s
remove-then-rebuild-then-stamp-then-spread pattern to row/cell nodes:

```ts
private buildTable(view: 'day' | 'month' | 'year') {
    const theadEl = this.getElements('tableHeader', this.doc).find(el => el.dataset.value === view);
    const tbodyEl = this.getElements('tableBody', this.doc).find(el => el.dataset.value === view);
    if (!theadEl || !tbodyEl) return;

    theadEl.replaceChildren();
    tbodyEl.replaceChildren();

    if (view === 'day') {
        const headerRowEl = this.doc.createElement('tr');
        this.spreadProps(headerRowEl, this.api.getTableRowProps({ view }));
        if (this.api.showWeekNumbers) {
            const thEl = this.doc.createElement('th');
            this.spreadProps(thEl, this.api.getWeekNumberHeaderCellProps({ view }));
            headerRowEl.appendChild(thEl);
        }
        this.api.weekDays.forEach(day => {
            const thEl = this.doc.createElement('th');
            thEl.scope = 'col';
            thEl.ariaLabel = day.long;
            this.spreadProps(thEl, this.api.getTableHeadProps({ view }));
            thEl.textContent = day.narrow;
            headerRowEl.appendChild(thEl);
        });
        theadEl.appendChild(headerRowEl);

        this.api.weeks.forEach((week, weekIndex) => {
            const trEl = this.doc.createElement('tr');
            this.spreadProps(trEl, this.api.getTableRowProps({ view }));
            if (this.api.showWeekNumbers) {
                const tdEl = this.doc.createElement('td');
                this.spreadProps(tdEl, this.api.getWeekNumberCellProps({ weekIndex, week }));
                tdEl.textContent = String(this.api.getWeekNumber(week));
                trEl.appendChild(tdEl);
            }
            week.forEach(value => {
                const tdEl = this.doc.createElement('td');
                this.spreadProps(tdEl, this.api.getDayTableCellProps({ value }));
                const triggerEl = this.doc.createElement('div');
                this.spreadProps(triggerEl, this.api.getDayTableCellTriggerProps({ value }));
                triggerEl.textContent = String(value.day);
                tdEl.appendChild(triggerEl);
                trEl.appendChild(tdEl);
            });
            tbodyEl.appendChild(trEl);
        });
        return;
    }

    // month/year: chunk(getMonthsGrid()/getYearsGrid(), columns: 4) into <tr> rows of
    // <td><div></div></td>, using getMonthTableCellProps/getMonthTableCellTriggerProps or the
    // year equivalents. No header row for these two views.
}
```

`numOfMonths > 1` (multiple offset months): not a framework mechanism, a docs-example concern — an
extra offset month is another `<primitives:date-picker.table>`/`tableHeader`/`tableBody` trio with
a distinguishing `value` (e.g. `"day-1"`), and `buildTable()` reads `api.getOffset({months: n})`
instead of `api.weeks` for the offset ones. Cover it in the "Rendering multiple months" example, not
in the base component.

### Registration bookkeeping

- `tsdown.config.ts` — add `'date-picker': './Resources/Private/Primitives/DatePicker/DatePicker.ts'`
  to the entries map, alphabetically between `combobox` and `dialog`.
- `package.json` `exports` — add `"./date-picker": { "import": "./dist/date-picker.js", "types":
  "./dist/date-picker.d.ts" }` next to the existing `@zag-js/date-picker` dependency entry.
- `@zag-js/date-picker` is already a dependency and already installed — Step 1 of the skill is done.

### `tests/Functional/Components/DatePickerRenderingTest.php`

Mirror `SelectRenderingTest.php` — assert on concrete server-rendered output, not trivial
passthrough:

- `data-scope="date-picker"` / `data-part="root"` present.
- `data-state="closed"` by default, `"open"` when `defaultOpen="{true}"` (same ternary-vs-`f:if`
  null-truthiness trap `SelectRenderingTest` regression-tests for `defaultOpen`).
- `getDefaultValue()` normalization: single ISO string → hydration `defaultValue` is a one-element
  array; array stays as-is; omitted → key absent from hydration data (mirror
  `normalizesStringDefaultValueToArrayInHydrationData`/`passesArrayDefaultValueAsIs`/
  `excludesDefaultValueFromHydrationWhenEmpty`).
- `Content` portaled via `<ui:portal>` still registers for hydration (mirror
  `rendersContentInsidePortalAndStillRegistersForHydration`).
- `ClearTrigger` hidden with no `defaultValue`, visible with one set.
- `Table`/`TableHeader`/`TableBody` render as empty shells with correct `data-part`/`data-view`
  wiring and **no** day/cell markup (confirms decision 1 didn't regress into server-side `<f:for>`).

### `/ui` styled wrapper: `packages/docs/Resources/Private/Components/ui/DatePicker/`

Mirror `Select`'s folder structure. `Content.fluid.html` reuses Popover's `/ui` Content animation
classes near-verbatim (`starting:data-[state=open]:opacity-0 ...` / `transition-discrete`) — no new
`@keyframes` needed, Popover doesn't use one either. `Positioner`+`Content` wrapped in
`<ui:portal>`, same as `/ui/Popover/Content.fluid.html`.

Examples: `Simple`, `WithField`, `Range` (`selectionMode="range"` + presets), `Multiple`
(`selectionMode="multiple"` + `maxSelectedDates`), `MinMax`, `DisabledDates` (`isDateUnavailable` —
TS-only, needs its own small subclass in the example's `entry.ts`), `MonthYearPicker`
(`minView="month"`), `Inline` (`inline="{true}"`, plus an "Inline with skeleton" variant
demonstrating `showSkeleton`). `Examples/All.fluid.html` + `.stories.ts` follow `Select`'s
dispatcher pattern.

### Docs page: `packages/docs/Resources/Private/Content/docs/components/date-picker.md`

Mirror `select.md`'s structure: H1 + description, `ui:referenceButtons`, `ui:componentExample`
(`withEntryFile: true`), `## Features` (from `accessibility.json`'s keyboard list plus
selection-mode/preset/week-numbers/fixed-weeks from `api.json`), `## Installation`, `## Examples`
per example above, `## API Reference` via `ui:ComponentPropsTable` — one row per Fluid-templated
part (skip the 4 client-only parts), `## Anatomy` fenced `html` block. Document in prose: the 9
function props require subclassing; `inline="{true}"` shows an empty calendar until hydration
(link the skeleton example); range mode's dual inputs share one `name`.

Add `docs/components/date-picker` to `nav.yaml`, right before `docs/components/dialog`.

## Verification

```bash
ddev composer format
ddev composer run lint
ddev npm run types
ddev npm run format:check
ddev composer test:functional
```

Then in `ddev npm run docs:dev`: open the DatePicker docs page, confirm the calendar opens/closes,
day/month/year view switching works, keyboard nav (arrows/Home/End/PageUp/PageDown) moves focus per
`accessibility.json`, range/multiple selection modes render correct `data-selected`/`data-in-range`
styling, and the Field-integration example participates in form validation like Select's "With Form
Field" example.
