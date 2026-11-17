import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';
import { Input } from 'fluid-primitives/input';

// The search of EXT:news takes the start and the end of the period as two arguments. A single day is a period
// that ends on the day it starts. A change submits the form, which RegionLoader.entry.ts loads without a reload.
mount('ui:datePicker', 'news-date', ({ props }) => {
    const format = new Intl.DateTimeFormat(props.locale as string | undefined, {
        dateStyle: 'medium',
        timeZone: 'UTC',
    });

    const datePicker: DatePicker = new DatePicker({
        ...props,
        onValueChange: ({ value }) => {
            const start = datePicker.hydrator.query<HTMLInputElement>('periodStart');
            const end = datePicker.hydrator.query<HTMLInputElement>('periodEnd');
            const text = datePicker.hydrator.query<HTMLElement>('periodText');
            if (!start || !end || !text) return;

            // ISO dates, whatever format the site shows
            const [first = '', last = first] = value.map(date => date.toString());
            start.value = first;
            end.value = last;

            const dates = [...new Set([first, last])].filter(Boolean);
            text.textContent = dates.length
                ? dates.map(date => format.format(new Date(date))).join(' – ')
                : 'Any date';
            text.toggleAttribute('data-empty', !dates.length);

            start.form?.requestSubmit();
        },
    });
    return datePicker.init();
});

// An input has no clear button of its own: this one empties it, and the input event submits the form
mount('ui:input', 'news-search', ({ props }) => {
    const search = new Input(props).init();
    search.hydrator.query('clear')?.addEventListener('click', () => {
        const input = search.hydrator.query<HTMLInputElement>('input');
        if (!input) return;
        input.value = '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
    });
    return search;
});
