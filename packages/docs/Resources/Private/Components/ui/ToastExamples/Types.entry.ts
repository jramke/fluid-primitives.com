import { mountAll } from 'fluid-primitives';
import { toast } from '../Toaster/Toaster.entry';

const toasts = {
    success: { title: 'Profile saved' },
    info: { title: 'New opening hours', description: 'We open at 9:00 on Saturdays.' },
    warning: {
        title: 'Your session ends in 5 minutes',
        description: 'Save your draft to keep it.',
    },
    error: { title: 'Payment failed', description: 'Your card was declined. Try another one.' },
};

mountAll('ui:toastExamples.types', ({ createHydrator }) => {
    createHydrator()
        .query('actions')
        ?.addEventListener('click', event => {
            const type = (event.target as Element).closest<HTMLElement>('[data-type]')?.dataset
                .type as keyof typeof toasts | undefined;
            if (type) toast.create({ type, ...toasts[type] });
        });
});
