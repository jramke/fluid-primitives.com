import { mount } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';
import { Tabs } from 'fluid-primitives/tabs';

mount('ui:select', 'select-with-tabs', ({ props }) => {
    let tabs: Tabs | undefined;
    const select = new Select({
        ...props,
        // The tabs check whether a panel holds something focusable only while it is visible, so the
        // listbox counts once the select is open, and the panel drops its own tab stop
        onOpenChange: details => {
            if (details.open) requestAnimationFrame(() => tabs?.api.syncTabIndex());
        },
    });
    select.init();

    // The select keeps a single listbox, so the tabs share it: the list moves into the active
    // panel, and its keyboard navigation has to skip what the tab hides - swap the machine's
    // collection and hide the rest of the items to match. The client only gets the keys a collection
    // names, so `category` is there because the template sets it as `groupByKey`.
    const source = select.api.collection;

    function showCategory(tabs: Tabs, category: string) {
        const panel = tabs.hydrator.queryAll('content').find(el => el.dataset.value === category);
        const listEl = select.hydrator.query('list');
        if (panel && listEl) panel.append(listEl);
        tabs.api.syncTabIndex();

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
        const instance = new Tabs({
            ...props,
            // `value` is null once a deselectable tab is deselected, these tabs are not
            onValueChange: ({ value }) => value !== null && showCategory(instance, value),
        });
        tabs = instance;
        instance.init();
        return instance;
    });

    return select;
});
