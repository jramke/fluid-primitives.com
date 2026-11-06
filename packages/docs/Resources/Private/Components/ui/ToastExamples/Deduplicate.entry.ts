import { mountAll } from 'fluid-primitives';
import { toast } from '../Toaster/Toaster.entry';

const shake = [
    { transform: 'translateX(0)' },
    { transform: 'translateX(-4px)' },
    { transform: 'translateX(4px)' },
    { transform: 'translateX(-2px)' },
    { transform: 'translateX(0)' },
];

mountAll('ui:toastExamples.deduplicate', ({ createHydrator }) => {
    let saves = 0;
    createHydrator()
        .query('actions')
        ?.addEventListener('click', () => {
            if (!toast.isVisible('draft')) saves = 0;
            saves++;
            const draft = { title: 'Draft saved', description: `Saved ${saves} times.` };

            if (saves === 1) {
                toast.create({ id: 'draft', ...draft });
                return;
            }

            // the new text is what screen readers announce, the shake is for everyone else
            toast.update('draft', draft);
            if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document
                    .querySelector('[data-toast-root="draft"]')
                    ?.animate(shake, { duration: 300 });
            }
        });
});
