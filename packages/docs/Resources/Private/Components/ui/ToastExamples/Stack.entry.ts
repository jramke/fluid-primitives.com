import { mount, mountAll } from 'fluid-primitives';
import { Toaster } from 'fluid-primitives/toaster';

const wishlistToast = mount('ui:toaster', 'stack-toaster', ({ props }) =>
    new Toaster(props).init()
)!.store;
const products = ['Linen shirt', 'Canvas tote', 'Wool scarf', 'Leather belt'];

mountAll('ui:toastExamples.stack', ({ createHydrator }) => {
    let added = 0;
    createHydrator()
        .query('actions')
        ?.addEventListener('click', () => {
            wishlistToast.create({
                title: `${products[added++ % products.length]} added to your wishlist`,
            });
        });
});
