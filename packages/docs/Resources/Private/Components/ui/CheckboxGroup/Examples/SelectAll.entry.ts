import { getHydrationData } from 'fluid-primitives';
import { Checkbox } from 'fluid-primitives/checkbox';
import { CheckboxGroup } from 'fluid-primitives/checkbox-group';

(() => {
	const selectAllCheckboxData = getHydrationData('checkbox', 'select-all');
	const selectAllGroupData = getHydrationData('checkbox-group', 'select-all-group');

	const { items } = getHydrationData('checkbox-group', 'select-all-items')?.props as {
		items?: { value: string; text: string }[];
	};
	const allValues = items?.map(item => item.value) ?? [];

	if (!selectAllCheckboxData || !selectAllGroupData) {
		console.error('Missing hydration data for select-all example');
		return;
	}

	let group: CheckboxGroup;
	let selectAllCheckbox: Checkbox;

	group = new CheckboxGroup({
		...selectAllGroupData.props,
		onValueChange: details => {
			const value = details.value;
			const allSelected = value.length === allValues.length && allValues.length > 0;
			const noneSelected = value.length === 0;

			group.updateProps({ value });

			if (noneSelected) {
				selectAllCheckbox.updateProps({ checked: false });
				return;
			}

			if (allSelected) {
				selectAllCheckbox.updateProps({ checked: true });
				return;
			}

			selectAllCheckbox.updateProps({ checked: 'indeterminate' });
		},
	});

	selectAllCheckbox = new Checkbox({
		...selectAllCheckboxData.props,
		onCheckedChange: ({ checked }) => {
			selectAllCheckbox.updateProps({ checked });
			group.updateProps({ value: checked ? allValues : [] });
		},
	});

	selectAllCheckbox.init();
	group.init();
})();
