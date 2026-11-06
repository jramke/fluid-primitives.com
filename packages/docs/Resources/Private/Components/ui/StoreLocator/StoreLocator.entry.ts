import { createMachine } from '@zag-js/core';
import { Component, Machine, mountAll, normalizeProps } from 'fluid-primitives';

interface StoreLocatorProps {
    id: string;
}

// Plain numbers: a GeolocationPosition loses its values when the machine copies it.
interface Position {
    latitude: number;
    longitude: number;
}

interface StoreLocatorSchema {
    props: StoreLocatorProps;
    context: { position: Position | null };
    state: 'idle' | 'locating' | 'located' | 'failed';
    event: { type: 'LOCATE' } | ({ type: 'LOCATED' } & Position) | { type: 'FAILED' };
    action: 'setPosition';
    effect: 'locate';
}

const machine = createMachine<StoreLocatorSchema>({
    context({ bindable }) {
        return {
            position: bindable<Position | null>(() => ({ defaultValue: null })),
        };
    },
    initialState: () => 'idle',
    states: {
        idle: { on: { LOCATE: { target: 'locating' } } },
        // No LOCATE here: a click while it locates starts no second request.
        locating: {
            effects: ['locate'],
            on: {
                LOCATED: { target: 'located', actions: ['setPosition'] },
                FAILED: { target: 'failed' },
            },
        },
        located: { on: { LOCATE: { target: 'locating' } } },
        failed: { on: { LOCATE: { target: 'locating' } } },
    },
    implementations: {
        effects: {
            locate({ send }) {
                let active = true;
                navigator.geolocation.getCurrentPosition(
                    ({ coords: { latitude, longitude } }) =>
                        active && send({ type: 'LOCATED', latitude, longitude }),
                    () => active && send({ type: 'FAILED' }),
                    { timeout: 10_000 }
                );
                // Runs when the machine leaves `locating` or stops, so a late answer changes nothing.
                return () => {
                    active = false;
                };
            },
        },
        actions: {
            setPosition({ context, event }) {
                if (event.type !== 'LOCATED') return;
                context.set('position', { latitude: event.latitude, longitude: event.longitude });
            },
        },
    },
});

function connect(service: Machine<StoreLocatorSchema>) {
    const state = service.state.get();
    const position = service.context.get('position');

    return {
        state,
        coordinates: position
            ? `${position.latitude.toFixed(3)}, ${position.longitude.toFixed(3)}`
            : '',
        locate: () => service.send({ type: 'LOCATE' }),
        getRootProps: () =>
            normalizeProps.element({ 'data-state': state, 'aria-busy': state === 'locating' }),
        getTriggerProps: () =>
            normalizeProps.button({ onClick: () => service.send({ type: 'LOCATE' }) }),
    };
}

export class StoreLocator extends Component<StoreLocatorProps, ReturnType<typeof connect>> {
    static componentName = 'storeLocator';

    initMachine(props: StoreLocatorProps) {
        return new Machine(machine, props);
    }

    initApi() {
        return connect(this.machine);
    }

    render() {
        const rootEl = this.hydrator.query('root');
        if (rootEl) this.spreadProps(rootEl, this.api.getRootProps());

        const triggerEl = this.hydrator.query('trigger');
        if (triggerEl) this.spreadProps(triggerEl, this.api.getTriggerProps());

        const coordinatesEl = this.hydrator.query('coordinates');
        if (coordinatesEl) coordinatesEl.textContent = this.api.coordinates;
    }
}

mountAll('ui:storeLocator', ({ props }) => new StoreLocator(props).init());
