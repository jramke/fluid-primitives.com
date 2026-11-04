import { mountAll } from 'fluid-primitives';
import { toast } from '../Toaster/Toaster.entry';

mountAll('ui:toastExamples.simple', ({ createHydrator }) => {
    createHydrator()
        .query('actions')
        ?.addEventListener('click', () => {
            toast.create({
                type: 'success',
                title: 'Address saved',
                description: 'We ship your next order to Torstraße 12, Berlin.',
            });
        });
});
