import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:fileUpload.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};

export const WithField: StoryObj = {
    args: {
        example_id: 'with-field',
    },
};

export const RejectedFiles: StoryObj = {
    args: {
        example_id: 'rejected-files',
    },
};

export const Directory: StoryObj = {
    args: {
        example_id: 'directory',
    },
};
