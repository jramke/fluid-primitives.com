import { isWeekend } from '@internationalized/date';
import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';

mount('ui:datePicker', 'date-picker-unavailable-dates', ({ props }) => {
    const datePicker = new DatePicker({
        ...props,
        // A function can't travel from the template to the client, so it is passed here.
        isDateUnavailable: (date, locale) => isWeekend(date, locale),
    });
    datePicker.init();
    return datePicker;
});
