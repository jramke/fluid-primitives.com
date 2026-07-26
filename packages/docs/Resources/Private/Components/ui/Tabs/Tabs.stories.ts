import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:tabs.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Vertical: StoryObj = {
    args: {
        example_id: 'vertical',
    },
};
export const ManualActivation: StoryObj = {
    args: {
        example_id: 'manual-activation',
    },
};
export const DisabledTabs: StoryObj = {
    args: {
        example_id: 'disabled-tabs',
    },
};
