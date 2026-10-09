import { CalendarDate } from '@internationalized/date';
import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';

mount('ui:datePicker', 'date-picker-month-year', ({ props }) => {
    const datePicker = new DatePicker({
        ...props,
        // The input shows `06/2025`, the hidden input still submits the first of the month (`2025-06-01`).
        format: date => `${String(date.month).padStart(2, '0')}/${date.year}`,
        parse: value => {
            const match = /^(\d{1,2})\/(\d{4})$/.exec(value);
            if (!match) return undefined;

            const month = Number(match[1]);
            return month >= 1 && month <= 12
                ? new CalendarDate(Number(match[2]), month, 1)
                : undefined;
        },
    });
    datePicker.init();
    return datePicker;
});
