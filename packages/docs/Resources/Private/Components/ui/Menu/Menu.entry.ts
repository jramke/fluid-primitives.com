import { mountAll } from 'fluid-primitives';
import { Menu } from 'fluid-primitives/menu';

mountAll('menu', ({ props }) => {
    const menu = new Menu(props);
    menu.init();
    return menu;
});
