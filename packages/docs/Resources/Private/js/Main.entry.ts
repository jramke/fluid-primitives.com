import '@fontsource-variable/inter';
import { createFilter } from '@zag-js/i18n-utils';
import '../css/main.css';

const startsWithFilter = createFilter({ sensitivity: 'base' });

window.FluidPrimitives = window.FluidPrimitives || {
	hydrationData: {},
	uncontrolledInstances: {},
};
window.FluidPrimitives.hooks = window.FluidPrimitives.hooks || {};
window.FluidPrimitives.hooks.combobox = window.FluidPrimitives.hooks.combobox || {};
window.FluidPrimitives.hooks.combobox.filters = window.FluidPrimitives.hooks.combobox.filters || {};

window.FluidPrimitives.hooks.combobox.filters.startsWith = ({ inputValue, collection }) => {
	const query = inputValue.trim();

	if (!query) {
		return collection;
	}

	return collection.filter(itemString => startsWithFilter.startsWith(itemString, query));
};
