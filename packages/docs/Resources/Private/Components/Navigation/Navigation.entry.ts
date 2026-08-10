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

    // Restore scroll position after the machine is initialised so it is not overridden
    // by ResizeObserver callbacks that fire during machine setup.
    const savedScrollTop = sessionStorage.getItem('sidebar-scroll-top');
    if (savedScrollTop !== null) {
        const viewport = scrollArea.getElement('viewport');
        if (viewport) {
            viewport.scrollTop = Number(savedScrollTop);
        }
    }

    // Clean up the fixed thumb classes that were applied by the inline script to give
    // visual continuity during page load.  We do this here (after init + scroll restore)
    // instead of relying on a hard-coded timeout so that the machine has already computed
    // its own layout values before we remove the overrides.
    scrollArea.getElements('thumb').forEach(thumbEl => {
        thumbEl.classList.remove('!transform-[var(--sidebar-stored-transform)]');
        thumbEl.classList.remove('!h-[var(--sidebar-stored-thumb-height)]');
    });
    document.documentElement.style.removeProperty('--sidebar-stored-transform');
    document.documentElement.style.removeProperty('--sidebar-stored-thumb-height');

    // Always persist scroll position regardless of whether the scrollbar is hovered.
    // The previous implementation only saved when hovering, which meant clicking a nav
    // link (without hovering the scrollbar) caused the position to be cleared.
    window.addEventListener('beforeunload', () => {
        const scrollTop = scrollArea.getElement('viewport')?.scrollTop ?? 0;
        if (scrollTop > 0) {
            sessionStorage.setItem('sidebar-scroll-top', scrollTop.toString());
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
