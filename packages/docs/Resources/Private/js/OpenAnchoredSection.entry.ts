import { getComponentInstance } from 'fluid-primitives';
import type { Accordion } from 'fluid-primitives/accordion';

const openAnchoredSection = () => {
    const id = decodeURIComponent(location.hash.slice(1));
    const accordion = getComponentInstance<Accordion>('ui:accordion', 'page-sections');
    if (!id || !accordion || accordion.api.value.includes(id)) return;
    if (!document.getElementById(id)?.closest('[data-accordion-item]')) return;

    accordion.api.setValue([...accordion.api.value, id]);
};

window.addEventListener('hashchange', openAnchoredSection);
openAnchoredSection();
