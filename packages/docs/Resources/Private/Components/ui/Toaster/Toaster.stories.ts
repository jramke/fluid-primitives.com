import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:toastExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Types: StoryObj = {
    args: {
        example_id: 'types',
    },
};
export const Action: StoryObj = {
    args: {
        example_id: 'action',
    },
};
export const Promise: StoryObj = {
    args: {
        example_id: 'promise',
    },
};
export const Deduplicate: StoryObj = {
    args: {
        example_id: 'deduplicate',
    },
};
export const Stack: StoryObj = {
    args: {
        example_id: 'stack',
    },
};
export const Cart: StoryObj = {
    args: {
        example_id: 'cart',
    },
};
