import { mount, mountAll } from 'fluid-primitives';
import { Dialog } from 'fluid-primitives/dialog';
import { ScrollArea } from 'fluid-primitives/scroll-area';

mountAll('ui:navigation', ({ props }) => {
    const dialog = mount('primitives:dialog', 'nav-drawer-' + props.id, ({ props }) =>
        new Dialog(props).init()
    );

    const scrollArea = mount('ui:scrollArea', 'sidebar-' + props.id, ({ props }) =>
        new ScrollArea(props).init()
    );

    if (!dialog || !scrollArea) return;

    // Cleanup the fixed values when the machine computed all values needed for layout styles
    setTimeout(() => {
        scrollArea.hydrator.queryAll('thumb').forEach(thumbEl => {
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
                scrollArea.hydrator.query('viewport')?.scrollTop.toString() ?? '0'
            );
            sessionStorage.setItem(
                'sidebar-thumb-height',
                scrollArea.hydrator.query('root')?.style.getPropertyValue('--thumb-height') ?? ''
            );
            sessionStorage.setItem(
                'sidebar-thumb-transform',
                scrollArea.hydrator.queryAll('thumb')[0]?.style.getPropertyValue('transform') ?? ''
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
