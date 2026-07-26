import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:navigationMenu.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const WithLinks: StoryObj = {
    args: {
        example_id: 'with-links',
    },
};
export const WithViewport: StoryObj = {
    args: {
        example_id: 'with-viewport',
    },
};
