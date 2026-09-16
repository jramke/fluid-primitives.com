import { mountAll } from 'fluid-primitives';

mountAll('textareaSubmitOnEnterExample', ({ createHydrator }) => {
    const hydrator = createHydrator();
    const form = hydrator.getElement<HTMLFormElement>('form');
    if (!form) return;

    form.addEventListener('submit', event => {
        event.preventDefault();
        alert('Form submitted');
    });
});
