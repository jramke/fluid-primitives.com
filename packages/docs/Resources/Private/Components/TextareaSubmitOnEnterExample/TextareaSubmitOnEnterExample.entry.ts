import { mountAll } from 'fluid-primitives';

mountAll('textarea-submit-on-enter-example', ({ createHydrator }) => {
    const hydrator = createHydrator();
    const form = hydrator.getElement<HTMLFormElement>('form');
    if (!form) return;

    form.addEventListener('submit', event => {
        event.preventDefault();
        alert('Form submitted');
    });
});
