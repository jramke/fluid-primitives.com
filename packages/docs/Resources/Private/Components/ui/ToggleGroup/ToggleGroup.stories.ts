import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:toggleGroupExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Multiple: StoryObj = {
    args: {
        example_id: 'multiple',
    },
};
export const Vertical: StoryObj = {
    args: {
        example_id: 'vertical',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
export const NotDeselectable: StoryObj = {
    args: {
        example_id: 'not-deselectable',
    },
};
