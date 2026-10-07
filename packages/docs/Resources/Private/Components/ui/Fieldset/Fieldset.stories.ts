import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:fieldsetExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
export const Invalid: StoryObj = {
    args: {
        example_id: 'invalid',
    },
};
export const ToggleDisabled: StoryObj = {
    args: {
        example_id: 'toggle-disabled',
    },
};
