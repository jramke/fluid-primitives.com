import { mount } from 'fluid-primitives';
import { Menu } from 'fluid-primitives/menu';

// A menu item is no form control: "Log out" submits the logout form when it is selected, by click or by key
mount('ui:menu', 'user-menu', ({ props }) =>
    new Menu({
        ...props,
        onSelect: ({ value }) => {
            if (value !== 'logout') return;
            document.querySelector<HTMLFormElement>('#user-menu-logout')?.requestSubmit();
        },
    }).init()
);

// The account settings report the new name, the user menu shows it without a reload
document.addEventListener('demos:account-updated', event => {
    const { name, initials } = (event as CustomEvent<{ name: string; initials: string }>).detail;
    document.querySelectorAll('[data-user-initials]').forEach(el => (el.textContent = initials));
    document.querySelectorAll('[data-user-name]').forEach(el => (el.textContent = name));
    document
        .querySelectorAll('[aria-label^="Account menu of"]')
        .forEach(el => el.setAttribute('aria-label', `Account menu of ${name}`));
});
