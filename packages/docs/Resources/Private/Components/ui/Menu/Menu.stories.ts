import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:menu.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Grouping: StoryObj = {
    args: {
        example_id: 'grouping',
    },
};
export const WithLinks: StoryObj = {
    args: {
        example_id: 'with-links',
    },
};
export const WithCheckboxes: StoryObj = {
    args: {
        example_id: 'with-checkboxes',
    },
};
export const WithRadios: StoryObj = {
    args: {
        example_id: 'with-radios',
    },
};
export const Nested: StoryObj = {
    args: {
        example_id: 'nested',
    },
};
export const ContextMenu: StoryObj = {
    args: {
        example_id: 'context-menu',
    },
};
export const MultipleTriggers: StoryObj = {
    args: {
        example_id: 'multiple-triggers',
    },
};
export const InsideDialog: StoryObj = {
    args: {
        example_id: 'inside-dialog',
    },
};
