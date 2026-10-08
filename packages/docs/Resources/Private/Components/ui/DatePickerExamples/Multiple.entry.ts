import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';

mount('ui:datePicker', 'date-picker-multiple', ({ props }) => {
    const datePicker = new DatePicker({
        ...props,
        // The input shows the first date only, so every selected date is listed below it.
        onValueChange: ({ valueAsString }) => {
            const datesEl = document.getElementById('date-picker-multiple-dates');
            if (datesEl) datesEl.textContent = valueAsString.join(', ') || 'No dates selected';
        },
    });
    datePicker.init();
    return datePicker;
});
