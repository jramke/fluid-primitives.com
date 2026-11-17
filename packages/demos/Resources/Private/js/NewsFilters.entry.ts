import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';
import { Input } from 'fluid-primitives/input';
import { Select } from 'fluid-primitives/select';

// The search of EXT:news takes the start and the end of the period as two arguments. A single day is a period
// that ends on the day it starts: pick it twice, or once and close the calendar.
mount('ui:datePicker', 'news-date', ({ props }) => {
    const datePicker: DatePicker = new DatePicker({
        ...props,
        onValueChange: ({ value }) => {
            const start = datePicker.hydrator.query<HTMLInputElement>('periodStart');
            const end = datePicker.hydrator.query<HTMLInputElement>('periodEnd');
            if (!start || !end) return;

            // ISO dates, whatever format the site shows
            const [first, last = first] = value;
            start.value = first?.toString() ?? '';
            end.value = last?.toString() ?? '';

            // A range waits for its end, a cleared date and a whole range load the list
            if (value.length !== 1) start.form?.requestSubmit();
        },
        onOpenChange: ({ open }) => {
            if (!open && datePicker.api.value.length === 1) {
                datePicker.hydrator.query<HTMLInputElement>('periodStart')?.form?.requestSubmit();
            }
        },
    });
    return datePicker.init();
});

// The hidden select holds the value the form submits, it changes after the Select
mount('ui:select', 'news-category', ({ props }) => {
    const select = new Select(props).init();
    const hiddenSelect = select.hydrator.query<HTMLSelectElement>('hiddenSelect');
    hiddenSelect?.addEventListener('change', () => hiddenSelect.form?.requestSubmit());
    return select;
});

// An input has no clear button of its own
mount('ui:input', 'news-search', ({ props }) => {
    const search = new Input(props).init();
    search.hydrator.query('clear')?.addEventListener('click', () => {
        const input = search.hydrator.query<HTMLInputElement>('input');
        if (!input) return;
        input.value = '';
        input.form?.requestSubmit();
    });
    return search;
});
