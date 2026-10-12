import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:toggleExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const DefaultPressed: StoryObj = {
    args: {
        example_id: 'default-pressed',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
export const Indicator: StoryObj = {
    args: {
        example_id: 'indicator',
    },
};
