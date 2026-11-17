import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';

// The search of EXT:news takes the start and the end of the period as two arguments. A single day is a period
// that ends on the day it starts. A change submits the form, which RegionLoader.entry.ts loads without a reload.
mount('ui:datePicker', 'news-date', ({ props }) => {
    const datePicker: DatePicker = new DatePicker({
        ...props,
        onValueChange: ({ value }) => {
            const form = datePicker.hydrator.query<HTMLElement>('root')?.closest('form');
            const start = form?.querySelector<HTMLInputElement>('[data-period-start]');
            const end = form?.querySelector<HTMLInputElement>('[data-period-end]');
            if (!form || !start || !end) return;

            // ISO dates, whatever format the inputs show
            const [first = '', last = first] = value.map(date => date.toString());
            start.value = first;
            end.value = last;
            form.requestSubmit();
        },
    });
    return datePicker.init();
});
