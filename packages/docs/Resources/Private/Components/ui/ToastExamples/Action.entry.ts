import { mountAll } from 'fluid-primitives';
import { toast } from '../Toaster/Toaster.entry';

mountAll('ui:toastExamples.action', ({ createHydrator }) => {
    createHydrator()
        .query('actions')
        ?.addEventListener('click', () => {
            toast.create({
                title: 'Linen shirt removed from your cart',
                action: {
                    label: 'Undo',
                    onClick: () => toast.create({ title: 'Linen shirt is back in your cart' }),
                },
            });
        });
});
