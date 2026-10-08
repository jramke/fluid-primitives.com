import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:fieldExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const WithHelperText: StoryObj = {
    args: {
        example_id: 'with-helper-text',
    },
};
export const WithCheckbox: StoryObj = {
    args: {
        example_id: 'with-checkbox',
    },
};
export const Required: StoryObj = {
    args: {
        example_id: 'required',
    },
};
export const Invalid: StoryObj = {
    args: {
        example_id: 'invalid',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
export const NativeValidation: StoryObj = {
    args: {
        example_id: 'native-validation',
    },
};
export const Indicators: StoryObj = {
    args: {
        example_id: 'indicators',
    },
};
export const ValidationMode: StoryObj = {
    args: {
        example_id: 'validation-mode',
    },
};
export const AsyncValidation: StoryObj = {
    args: {
        example_id: 'async-validation',
    },
};
