import { mountAll } from 'fluid-primitives';
import { toast } from '../Toaster/Toaster.entry';

mountAll('ui:toastExamples.simple', ({ createHydrator }) => {
    createHydrator()
        .query('actions')
        ?.addEventListener('click', () => {
            toast.create({
                type: 'success',
                title: 'Message sent',
                description: 'We usually reply within one business day.',
            });
        });
});
