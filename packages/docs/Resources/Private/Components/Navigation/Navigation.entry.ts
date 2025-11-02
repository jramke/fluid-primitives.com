import { getHydrationData, initAllComponentInstances } from 'fluid-primitives';
import { Dialog } from 'fluid-primitives/dialog';

(() => {
	initAllComponentInstances('navigation', ({ props }) => {
		const dialogProps = getHydrationData('dialog', 'nav-drawer-' + props.id)!.props;
		const dialog = new Dialog(dialogProps);
		dialog.init();

		window.addEventListener('resize', () => {
			dialog.api.setOpen(false);
		});
	});
})();
