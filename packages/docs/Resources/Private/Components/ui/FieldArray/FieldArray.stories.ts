import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:fieldArray.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Empty: StoryObj = {
    args: {
        example_id: 'empty',
    },
};
export const Limited: StoryObj = {
    args: {
        example_id: 'limited',
    },
};
