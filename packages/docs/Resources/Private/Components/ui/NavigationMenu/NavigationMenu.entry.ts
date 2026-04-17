import { mount } from 'fluid-primitives';
import { NavigationMenu } from 'fluid-primitives/navigation-menu';

mount('navigation-menu', ({ props }) => {
	const navigationMenu = new NavigationMenu(props);
	navigationMenu.init();
	return navigationMenu;
});
