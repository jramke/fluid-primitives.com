import { mount, mountAll } from 'fluid-primitives';
import { Button } from 'fluid-primitives/button';

mountAll('ui:buttonExamples.backupCard', ({ props, createHydrator }) => {
    const h = createHydrator();
    const statusEl = h.query('status');

    const button = mount('ui:button', `${props.id}-action`, ({ props }) => {
        const button = new Button(props);
        button.init();
        return button;
    });

    button?.api.addEventListener('click', async () => {
        button.api.setLoading(true);
        await new Promise(resolve => setTimeout(resolve, 2000));
        button.api.setLoading(false);
        if (statusEl) statusEl.textContent = 'Last backup just now';
    });
});
