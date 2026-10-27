import { mount, mountAll } from 'fluid-primitives';
import { Button } from 'fluid-primitives/button';
import { animate } from 'motion';

type Status = 'idle' | 'loading' | 'success';

mountAll('ui:animationExamples.loginLinkButton', ({ props }) => {
    const button = mount('ui:button', `${props.id}-button`, ({ props }) =>
        new Button(props).init()
    );
    const root = button?.hydrator.query('root');
    if (!button || !root) return;

    // A width cannot transition between two `auto` values, so Motion animates it from the old to the new width.
    const show = (status: Status) => {
        const from = root.getBoundingClientRect().width;
        root.dataset.status = status;
        const to = root.getBoundingClientRect().width;
        if (from === to || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        // Pinned before the next paint, so no frame shows the new width early.
        root.style.width = `${from}px`;
        animate(0, 1, {
            type: 'spring',
            duration: 0.3,
            bounce: 0,
            onUpdate: progress => (root.style.width = `${from + (to - from) * progress}px`),
            onComplete: () => root.style.removeProperty('width'),
        });
    };

    let status: Status = 'idle';
    button.api.addEventListener('click', () => {
        if (status !== 'idle') return;

        status = 'loading';
        button.api.setLoading(true);
        show('loading');

        setTimeout(() => {
            status = 'success';
            button.api.setLoading(false);
            show('success');
        }, 1750);
        setTimeout(() => {
            status = 'idle';
            show('idle');
        }, 3500);
    });
});
