import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:collapsible.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const DefaultOpen: StoryObj = {
    args: {
        example_id: 'default-open',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
