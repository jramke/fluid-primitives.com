import { mount } from 'fluid-primitives';

(() => {
	mount('checkbox', ({ props, createHydrator }) => {
		const hydrator = createHydrator();
		console.log(props, hydrator, hydrator.getElement('root'));
	});
})();
