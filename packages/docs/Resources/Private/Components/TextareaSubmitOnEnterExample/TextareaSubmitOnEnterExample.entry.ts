import { mountAll } from 'fluid-primitives';

mountAll('ui:textareaSubmitOnEnterExample', ({ createHydrator }) => {
    const hydrator = createHydrator();
    const form = hydrator.query<HTMLFormElement>('form');
    if (!form) return;

    form.addEventListener('submit', event => {
        event.preventDefault();
        alert('Form submitted');
    });
});
