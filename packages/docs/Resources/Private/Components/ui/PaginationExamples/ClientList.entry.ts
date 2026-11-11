import type { PageChangeDetails } from '@zag-js/pagination';
import { mount, Template } from 'fluid-primitives';
import { Pagination } from 'fluid-primitives/pagination';

const teamMembers = [
    'Anna Becker',
    'Ben Schulz',
    'Clara Wagner',
    'David Hoffmann',
    'Emma Koch',
    'Felix Richter',
    'Greta Klein',
    'Hannah Wolf',
    'Jonas Neumann',
    'Lena Schwarz',
    'Lukas Braun',
    'Mia Zimmermann',
    'Noah Krüger',
    'Paula Hartmann',
    'Ruben Lange',
    'Sophie Werner',
    'Tim Krause',
    'Theresa Meier',
    'Vincent Lehmann',
    'Zoe Schmid',
    'Moritz Fuchs',
    'Lara Vogel',
];

mount('ui:pagination', 'team-members', ({ props }) => {
    const pagination: Pagination = new Pagination({
        ...props,
        count: teamMembers.length,
        onPageChange: showPage,
    });

    function showPage({ page, pageSize }: PageChangeDetails) {
        const start = (page - 1) * pageSize;
        const rows = teamMembers.slice(start, start + pageSize).map(name => {
            const row = new Template(pagination.hydrator, 'memberTemplate');
            row.root.textContent = name;
            return row;
        });
        pagination.hydrator.query('members')?.replaceChildren(...rows);
    }

    pagination.init();
    showPage({ page: pagination.api.page, pageSize: pagination.api.pageSize });
    return pagination;
});
