import type { Props } from '@zag-js/toast';
import { mount, mountAll } from 'fluid-primitives';
import { Toast } from 'fluid-primitives/toast';
import { Toaster } from 'fluid-primitives/toaster';

class CartToast extends Toast {
    render() {
        super.render();
        const imageEl = this.hydrator.query<HTMLImageElement>('image');
        const image = this.machine.service.prop('meta')?.image;
        if (imageEl && image && imageEl.getAttribute('src') !== image) imageEl.src = image;
    }
}

class CartToaster extends Toaster {
    protected createToast(props: Props) {
        return new CartToast(props).init();
    }
}

const cartToast = mount('ui:toaster', 'cart-toaster', ({ props }) =>
    new CartToaster(props).init()
)!.store;

mountAll('ui:toastExamples.cart', ({ createHydrator }) => {
    createHydrator()
        .query('actions')
        ?.addEventListener('click', event => {
            const product = (event.target as Element).closest<HTMLElement>('[data-image]')?.dataset;
            if (!product) return;
            cartToast.create({
                title: `${product.name} added to your cart`,
                description: 'Ships in 1-2 business days.',
                meta: { image: product.image },
            });
        });
});
