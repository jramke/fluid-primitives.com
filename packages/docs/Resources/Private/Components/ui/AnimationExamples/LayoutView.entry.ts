import { mount, mountAll } from 'fluid-primitives';
import { ToggleGroup } from 'fluid-primitives/toggle-group';
import { animateLayout } from 'motion';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

mountAll('ui:animationExamples.layoutView', ({ props, createHydrator }) => {
    const products = createHydrator().query('products');
    if (!products) return;

    mount('ui:toggleGroup', `${props.id}-view`, ({ props }) =>
        new ToggleGroup({
            ...props,
            onValueChange: ({ value }) => {
                animateLayout(
                    products,
                    () => (products.dataset.view = value[0]),
                    reduceMotion.matches
                        ? { duration: 0 }
                        : { type: 'spring', duration: 0.4, bounce: 0 }
                );
            },
        }).init()
    );
});
