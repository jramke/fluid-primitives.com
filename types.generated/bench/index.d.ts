import { Orientation, ListCollection } from '../index.d';
export type AccordionHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
multiple?: boolean,
collapsible?: boolean,
defaultValue?: string[],
disabled?: boolean,
orientation?: Orientation,
};
export type CheckboxHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
disabled?: boolean,
invalid?: boolean,
required?: boolean,
defaultChecked?: unknown,
name?: string,
form?: string,
readOnly?: boolean,
value?: string,
};
export type InputHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
translations: {
wordCount: string | false,
},
};
export type SelectHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
collection: ListCollection,
name?: string,
form?: string,
disabled?: boolean,
invalid?: boolean,
readOnly?: boolean,
required?: boolean,
closeOnSelect?: boolean,
positioning?: unknown,
defaultValue?: string[],
defaultHighlightedValue?: string,
loopFocus?: boolean,
multiple?: boolean,
defaultOpen?: boolean,
composite?: boolean,
deselectable?: boolean,
translations: {
clearTriggerLabel: string,
},
};
export type VanillaAccordionHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type VanillaCheckboxHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type VanillaInputHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type VanillaSelectHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
