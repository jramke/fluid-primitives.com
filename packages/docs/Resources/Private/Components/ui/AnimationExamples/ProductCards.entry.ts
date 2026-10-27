import { mount, mountAll } from 'fluid-primitives';
import { Dialog } from 'fluid-primitives/dialog';
import { animateLayout } from 'motion';

// Seconds, shared by the layout and the colour transition of the card.
const duration = 0.4;

mountAll('ui:animationExamples.productCards', ({ props, createHydrator }) => {
    const hydrator = createHydrator();
    const slot = hydrator.query('slot');
    const extras = hydrator.query('extras');
    const description = hydrator.query('description');
    const content = slot?.parentElement;
    const positioner = content?.parentElement;
    if (!slot || !extras || !description || !content || !positioner) return;

    let current: { button: HTMLElement; card: HTMLElement } | null = null;
    let landing: (() => void) | null = null;
    let timer: number | undefined;

    // The same elements move between the grid and the dialog, so the layout animation flies them over.
    const move = (update: () => void, done?: () => void) =>
        animateLayout(
            update,
            matchMedia('(prefers-reduced-motion: reduce)').matches
                ? { duration: 0 }
                : { type: 'spring', duration, bounce: 0 }
        ).then(animation => animation.finished.then(done));

    const open = () => {
        if (!current) return;
        const { button, card } = current;
        // A card that is still flying back lands in its list first, then this one lifts off.
        landing?.();

        move(() => {
            button.style.height = `${button.offsetHeight}px`;
            card.toggleAttribute('data-expanded', true);
            slot.append(card);
            card.append(extras);
            description.textContent = button.dataset.description ?? '';
            content.ariaLabel = card.querySelector('[data-title]')?.textContent ?? null;
        });
    };

    // The card stays in the dialog layer and flies to the place of its button there, so it never
    // passes below the backdrop. Only after it has landed it moves back into the list.
    const close = () => {
        if (!current) return;
        const { button, card } = current;
        const land = () => {
            Object.assign(card.style, { position: '', top: '', left: '', width: '' });
            button.append(card);
            button.style.height = '';
            if (landing === land) landing = null;
        };
        landing = land;

        move(
            () => {
                const { top, left, width } = button.getBoundingClientRect();
                card.toggleAttribute('data-expanded', false);
                slot.after(extras);
                positioner.append(card);
                Object.assign(card.style, {
                    position: 'fixed',
                    top: `${top}px`,
                    left: `${left}px`,
                    width: `${width}px`,
                });
            },
            // A newer move has taken over when `landing` is not this one any more.
            () => landing === land && land()
        );
    };

    mount('ui:dialog', `${props.id}-dialog`, ({ props }) =>
        new Dialog({
            ...props,
            // The dialog knows which of its triggers is active, so the card needs no click listener.
            onTriggerValueChange: ({ triggerElement: button }) => {
                const card = button?.querySelector<HTMLElement>('[data-card]');
                current = button && card ? { button, card } : null;
                card?.style.setProperty('--duration', `${duration}s`);
            },
            onOpenChange: ({ open: isOpen }) => {
                clearTimeout(timer);
                // The description and the button fade out first, then the card flies back.
                // Zag sets the active trigger right after this callback, so the card is known one microtask later.
                if (isOpen) queueMicrotask(open);
                else timer = window.setTimeout(close, 100);
            },
        }).init()
    );
});
