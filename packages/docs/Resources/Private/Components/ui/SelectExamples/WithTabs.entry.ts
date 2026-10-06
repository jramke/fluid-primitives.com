import { mount } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';
import { Tabs } from 'fluid-primitives/tabs';

mount('ui:select', 'select-with-tabs', ({ props }) => {
    const select = new Select(props);
    select.init();

    // The select keeps a single listbox, so the tabs share it: the list moves into the active
    // panel, and its keyboard navigation has to skip what the tab hides - swap the machine's
    // collection and hide the rest of the items to match.
    const source = select.api.collection;

    function showCategory(tabs: Tabs, category: string) {
        const panel = tabs.hydrator.queryAll('content').find(el => el.dataset.value === category);
        const listEl = select.hydrator.query('list');
        if (panel && listEl) panel.append(listEl);

        const collection =
            category === 'all'
                ? source
                : source.filter((_itemString, _index, item) => item.category === category);

        select.updateProps({ collection });
        select.hydrator.queryAll('item').forEach(itemEl => {
            itemEl.hidden = !collection.has(itemEl.dataset.value ?? null);
        });
    }

    mount('ui:tabs', 'select-with-tabs-tabs', ({ props }) => {
        const tabs = new Tabs({
            ...props,
            onValueChange: ({ value }) => showCategory(tabs, value),
        });
        tabs.init();
        return tabs;
    });

    return select;
});
