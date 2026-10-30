import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:tocExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Nested: StoryObj = {
    args: {
        example_id: 'nested',
    },
};
export const Indicator: StoryObj = {
    args: {
        example_id: 'indicator',
    },
};
export const Rail: StoryObj = {
    args: {
        example_id: 'rail',
    },
};
export const Collapsible: StoryObj = {
    args: {
        example_id: 'collapsible',
    },
};
