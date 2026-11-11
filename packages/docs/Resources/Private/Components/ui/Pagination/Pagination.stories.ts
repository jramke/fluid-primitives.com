import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:paginationExamples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const FirstAndLast: StoryObj = {
    args: {
        example_id: 'first-and-last',
    },
};
export const ClientList: StoryObj = {
    args: {
        example_id: 'client-list',
    },
};
export const Links: StoryObj = {
    args: {
        example_id: 'links',
    },
};
export const FetchWithUrl: StoryObj = {
    args: {
        example_id: 'fetch-with-url',
    },
};
