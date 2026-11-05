import { createMachine } from '@zag-js/core';
import { Component, Machine, mountAll, normalizeProps } from 'fluid-primitives';

interface WishlistButtonProps {
    id: string;
    productId: string;
}

interface WishlistButtonSchema {
    props: WishlistButtonProps;
    context: { added: boolean };
    state: 'idle';
    event: { type: 'TOGGLE' };
    action: 'toggle';
}

function readWishlist(): string[] {
    return JSON.parse(localStorage.getItem('wishlist') ?? '[]');
}

function saveToWishlist(productId: string, added: boolean) {
    const others = readWishlist().filter(id => id !== productId);
    localStorage.setItem('wishlist', JSON.stringify(added ? [...others, productId] : others));
}

const machine = createMachine<WishlistButtonSchema>({
    context({ prop, bindable }) {
        return {
            added: bindable(() => ({
                defaultValue: readWishlist().includes(prop('productId')),
                onChange: added => saveToWishlist(prop('productId'), added),
            })),
        };
    },
    initialState: () => 'idle',
    states: {
        idle: { on: { TOGGLE: { actions: ['toggle'] } } },
    },
    implementations: {
        actions: {
            toggle({ context }) {
                context.set('added', !context.get('added'));
            },
        },
    },
});

function connect(service: Machine<WishlistButtonSchema>) {
    const added = service.context.get('added');

    return {
        added,
        toggle: () => service.send({ type: 'TOGGLE' }),
        getRootProps: () =>
            normalizeProps.button({
                'aria-pressed': added,
                onClick: () => service.send({ type: 'TOGGLE' }),
            }),
    };
}

export class WishlistButton extends Component<WishlistButtonProps, ReturnType<typeof connect>> {
    static componentName = 'wishlistButton';

    initMachine(props: WishlistButtonProps) {
        return new Machine(machine, props);
    }

    initApi() {
        return connect(this.machine);
    }

    render() {
        const rootEl = this.hydrator.query('root');
        if (rootEl) this.spreadProps(rootEl, this.api.getRootProps());
    }
}

mountAll('ui:wishlistButton', ({ props }) => new WishlistButton(props).init());
