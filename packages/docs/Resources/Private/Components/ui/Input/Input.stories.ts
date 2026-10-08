import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:inputExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const WordCount: StoryObj = {
    args: {
        example_id: 'word-count',
    },
};
export const WithField: StoryObj = {
    args: {
        example_id: 'with-field',
    },
};
