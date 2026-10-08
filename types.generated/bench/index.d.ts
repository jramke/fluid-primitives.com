import { ListCollection, Orientation, PopupType } from '../index.d';
export type AccordionHydrationProps = {
    id: string;
    ids: {
        [key: string]: string;
    };
    multiple?: boolean;
    collapsible?: boolean;
    defaultValue?: string[];
    disabled?: boolean;
    orientation?: Orientation;
};
export type CheckboxHydrationProps = {
    id: string;
    ids: {
        [key: string]: string;
    };
    disabled?: boolean;
    invalid?: boolean;
    required?: boolean;
    defaultChecked?: unknown;
    name?: string;
    form?: string;
    readOnly?: boolean;
    value?: string;
};
export type InputHydrationProps = {
    id: string;
    ids: {
        [key: string]: string;
    };
    name?: string;
    disabled?: boolean;
    invalid?: boolean;
    required?: boolean;
    readOnly?: boolean;
    defaultValue?: string;
    maxLength?: number;
    pattern?: string;
    inputMode?: string;
    translations: {
        wordCount: string | false;
    };
    announce?: boolean;
    announceDebounce?: number;
};
export type SelectHydrationProps = {
    id: string;
    ids: {
        [key: string]: string;
    };
    collection: ListCollection;
    name?: string;
    form?: string;
    disabled?: boolean;
    invalid?: boolean;
    readOnly?: boolean;
    required?: boolean;
    closeOnSelect?: boolean;
    positioning?: unknown;
    defaultValue?: string[];
    defaultHighlightedValue?: string;
    loopFocus?: boolean;
    multiple?: boolean;
    defaultOpen?: boolean;
    popupType?: PopupType;
    deselectable?: boolean;
    translations: {
        clearTriggerLabel: string;
    };
};
