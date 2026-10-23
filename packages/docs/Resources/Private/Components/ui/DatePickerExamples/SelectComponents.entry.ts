import type { Cell } from '@zag-js/date-picker';
import { mount, Template } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';
import { Select } from 'fluid-primitives/select';

mount('ui:datePicker', 'date-picker-select-components', ({ props }) => {
    let monthSelect: Select | undefined;
    let yearSelect: Select | undefined;

    // `onFocusChange` runs for the arrow buttons and the keyboard too, so the selects follow them. It runs
    // before the api has the new date, hence the microtask.
    const datePicker = new DatePicker({
        ...props,
        onFocusChange: () => queueMicrotask(syncSelects),
    });
    datePicker.init();

    // The date picker knows the months in its locale and which of them, and which years, `min` and
    // `max` allow, so the selects take their items from it.
    function syncSelects() {
        const { month, year } = datePicker.api.focusedValue;
        update(monthSelect, datePicker.api.getMonths(), month);
        update(yearSelect, datePicker.api.getYears(), year);
    }

    function update(select: Select | undefined, items: Cell[], selected: number) {
        if (!select) return;
        select.updateProps({ collection: select.api.collection.copy(items) });
        select.api.setValue([String(selected)]);
    }

    // The items are only rendered once, the collection carries their labels and disabled state.
    function mountSelect(rootId: string, items: Cell[], onChange: (value: number) => void) {
        return mount('ui:select', rootId, ({ props }) => {
            const select = new Select({
                ...props,
                onValueChange: ({ value }) => onChange(Number(value[0])),
            });
            select.init();

            const listEl = select.hydrator.query('list');
            for (const { value, label } of items) {
                const item = new Template(select.hydrator, 'itemTemplate', {
                    value: String(value),
                });
                const textEl = item.query('itemText');
                if (textEl) textEl.textContent = label;
                listEl?.append(item);
            }
            return select;
        });
    }

    monthSelect = mountSelect('date-picker-month-select', datePicker.api.getMonths(), month =>
        datePicker.api.focusMonth(month)
    );
    yearSelect = mountSelect('date-picker-year-select', datePicker.api.getYears(), year =>
        datePicker.api.focusYear(year)
    );
    syncSelects();

    return datePicker;
});
