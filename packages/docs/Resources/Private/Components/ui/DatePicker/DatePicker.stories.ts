import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:datePickerExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const DefaultValue: StoryObj = {
    args: {
        example_id: 'default-value',
    },
};
export const WithField: StoryObj = {
    args: {
        example_id: 'with-field',
    },
};
export const Range: StoryObj = {
    args: {
        example_id: 'range',
    },
};
export const Multiple: StoryObj = {
    args: {
        example_id: 'multiple',
    },
};
export const MultipleMonths: StoryObj = {
    args: {
        example_id: 'multiple-months',
    },
};
export const MinMax: StoryObj = {
    args: {
        example_id: 'min-max',
    },
};
export const Inline: StoryObj = {
    args: {
        example_id: 'inline',
    },
};
export const UnavailableDates: StoryObj = {
    args: {
        example_id: 'unavailable-dates',
    },
};
export const MonthYear: StoryObj = {
    args: {
        example_id: 'month-year',
    },
};
