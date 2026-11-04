import { mountAll } from 'fluid-primitives';
import { toast } from '../Toaster/Toaster.entry';

mountAll('ui:toastExamples.promise', ({ createHydrator }) => {
    createHydrator()
        .query('actions')
        ?.addEventListener('click', () => {
            const sending = new Promise(resolve => setTimeout(resolve, 2000));

            toast.promise(sending, {
                loading: { title: 'Sending the newsletter' },
                success: {
                    title: 'Newsletter sent',
                    description: 'It went out to 1,204 subscribers.',
                },
                error: { title: 'The newsletter could not be sent' },
            });
        });
});
