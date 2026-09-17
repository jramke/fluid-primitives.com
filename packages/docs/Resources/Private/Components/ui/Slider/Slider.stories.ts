import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:slider.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const Range: StoryObj = {
    args: {
        example_id: 'range',
    },
};
export const WithMarks: StoryObj = {
    args: {
        example_id: 'with-marks',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
export const WithNumberInput: StoryObj = {
    args: {
        example_id: 'with-number-input',
    },
};
export const WithField: StoryObj = {
    args: {
        example_id: 'with-field',
    },
};
export const WithDraggingIndicator: StoryObj = {
    args: {
        example_id: 'with-dragging-indicator',
    },
};
export const WithDecimalValues: StoryObj = {
    args: {
        example_id: 'with-decimal-values',
    },
};
