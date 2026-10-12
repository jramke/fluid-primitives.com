import { mount } from 'fluid-primitives';
import { Button } from 'fluid-primitives/button';

mount('ui:button', 'loading-button', ({ props }) => {
    const button = new Button(props);
    button.init();

    // A click while the button is loading never reaches this listener.
    button.api.addEventListener('click', () => {
        button.api.setLoading(true);
        setTimeout(() => button.api.setLoading(false), 4000);
    });

    return button;
});
