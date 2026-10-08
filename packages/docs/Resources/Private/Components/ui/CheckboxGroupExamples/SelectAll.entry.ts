import { mount, mountAll } from 'fluid-primitives';
import { Checkbox } from 'fluid-primitives/checkbox';
import { CheckboxGroup } from 'fluid-primitives/checkbox-group';

mountAll('ui:checkboxGroupExamples.selectAll', ({ props }) => {
    const allValues = props.items.map(item => item.value);

    let group: CheckboxGroup | undefined;
    let selectAllCheckbox: Checkbox | undefined;

    group = mount('ui:checkboxGroup', 'select-all-group', ({ props }) => {
        const instance = new CheckboxGroup({
            ...props,
            onValueChange: details => {
                const value = details.value;
                const allSelected = value.length === allValues.length && allValues.length > 0;
                const noneSelected = value.length === 0;

                instance.updateProps({ value });

                if (noneSelected) {
                    selectAllCheckbox?.updateProps({ checked: false });
                    return;
                }

                if (allSelected) {
                    selectAllCheckbox?.updateProps({ checked: true });
                    return;
                }

                selectAllCheckbox?.updateProps({ checked: 'indeterminate' });
            },
        });
        instance.init();
        return instance;
    });

    selectAllCheckbox = mount('ui:checkbox', 'select-all', ({ props }) => {
        const instance = new Checkbox({
            ...props,
            onCheckedChange: ({ checked }) => {
                instance.updateProps({ checked });
                group?.updateProps({ value: checked ? allValues : [] });
            },
        });
        instance.init();
        return instance;
    });
});
