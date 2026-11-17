import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:accordionExamples.all'),
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
export const Multiple: StoryObj = {
    args: {
        example_id: 'multiple',
    },
};
export const FindInPage: StoryObj = {
    args: {
        example_id: 'find-in-page',
    },
};
