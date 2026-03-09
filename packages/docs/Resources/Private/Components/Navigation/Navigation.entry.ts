import { getHydrationData, mount } from 'fluid-primitives';
import { Dialog } from 'fluid-primitives/dialog';
import { ScrollArea } from 'fluid-primitives/scroll-area';

mount('navigation', ({ props }) => {
	const dialogProps = getHydrationData('dialog', 'nav-drawer-' + props.id)!.props;
	const dialog = new Dialog(dialogProps);
	dialog.init();

	const sidebarScrollAreaProps = getHydrationData('scroll-area', 'sidebar-' + props.id)!.props;
	const scrollArea = new ScrollArea(sidebarScrollAreaProps);
	scrollArea.init();

	// Cleanup the fixed values when the machine computed all values needed for layout styles
	setTimeout(() => {
		scrollArea.getElements('thumb').forEach(thumbEl => {
			thumbEl.classList.remove('!transform-[var(--sidebar-stored-transform)]');
			thumbEl.classList.remove('!h-[var(--sidebar-stored-thumb-height)]');
		});
		document.documentElement.style.removeProperty('--sidebar-stored-transform');
		document.documentElement.style.removeProperty('--sidebar-stored-thumb-height');
	}, 100);

	window.addEventListener('beforeunload', () => {
		const hovered = scrollArea.api.getScrollbarState({ orientation: 'vertical' }).hovering;
		if (hovered) {
			sessionStorage.setItem(
				'sidebar-scroll-top',
				scrollArea.getElement('viewport')?.scrollTop.toString() ?? '0'
			);
			sessionStorage.setItem(
				'sidebar-thumb-height',
				scrollArea.getElement('root')?.style.getPropertyValue('--thumb-height') ?? ''
			);
			sessionStorage.setItem(
				'sidebar-thumb-transform',
				scrollArea.getElements('thumb')[0]?.style.getPropertyValue('transform') ?? ''
			);
		} else {
			sessionStorage.removeItem('sidebar-scroll-top');
			sessionStorage.removeItem('sidebar-thumb-height');
			sessionStorage.removeItem('sidebar-thumb-transform');
		}
	});

	window.addEventListener('resize', () => {
		dialog.api.setOpen(false);
	});
});
