import {
    AccordionHydrationProps as AccordionHydrationPropsImport2,
    CheckboxHydrationProps as CheckboxHydrationPropsImport2,
    InputHydrationProps as InputHydrationPropsImport2,
    SelectHydrationProps as SelectHydrationPropsImport2,
} from './bench/index.d';
import {
    AccordionHydrationProps as AccordionHydrationPropsImport,
    CheckboxGroupHydrationProps as CheckboxGroupHydrationPropsImport,
    CheckboxHydrationProps as CheckboxHydrationPropsImport,
    ClipboardHydrationProps as ClipboardHydrationPropsImport,
    CollapsibleHydrationProps as CollapsibleHydrationPropsImport,
    ComboboxHydrationProps as ComboboxHydrationPropsImport,
    DialogHydrationProps as DialogHydrationPropsImport,
    FieldArrayHydrationProps as FieldArrayHydrationPropsImport,
    FieldHydrationProps as FieldHydrationPropsImport,
    FileUploadHydrationProps as FileUploadHydrationPropsImport,
    FormHydrationProps as FormHydrationPropsImport,
    InputHydrationProps as InputHydrationPropsImport,
    MenuHydrationProps as MenuHydrationPropsImport,
    NavigationMenuHydrationProps as NavigationMenuHydrationPropsImport,
    NumberInputHydrationProps as NumberInputHydrationPropsImport,
    PopoverHydrationProps as PopoverHydrationPropsImport,
    RadioGroupHydrationProps as RadioGroupHydrationPropsImport,
    SelectHydrationProps as SelectHydrationPropsImport,
    SliderHydrationProps as SliderHydrationPropsImport,
    SwitchHydrationProps as SwitchHydrationPropsImport,
    TabsHydrationProps as TabsHydrationPropsImport,
    TextareaHydrationProps as TextareaHydrationPropsImport,
    TooltipHydrationProps as TooltipHydrationPropsImport,
} from './primitives/index.d';
import {
    AccordionHydrationProps,
    CheckboxGroupExamplesSelectAllHydrationProps,
    CheckboxGroupHydrationProps,
    CheckboxHydrationProps,
    ClipboardHydrationProps,
    CollapsibleHydrationProps,
    ComboboxHydrationProps,
    CommandMenuHydrationProps,
    DialogHydrationProps,
    FieldArrayHydrationProps,
    FieldHydrationProps,
    FileUploadHydrationProps,
    FormHydrationProps,
    InputHydrationProps,
    MenuHydrationProps,
    NavigationMenuHydrationProps,
    NumberInputHydrationProps,
    PopoverHydrationProps,
    RadioGroupHydrationProps,
    SelectHydrationProps,
    SliderHydrationProps,
    SwitchHydrationProps,
    TabsHydrationProps,
    TextareaHydrationProps,
    TooltipHydrationProps,
} from './ui/index.d';
declare module 'fluid-primitives' {
    interface HydrationPropsRegistry {
        'ui:accordion': AccordionHydrationProps;
        'ui:checkbox': CheckboxHydrationProps;
        'ui:checkboxGroup': CheckboxGroupHydrationProps;
        'ui:checkboxGroupExamples.selectAll': CheckboxGroupExamplesSelectAllHydrationProps;
        'ui:clipboard': ClipboardHydrationProps;
        'ui:collapsible': CollapsibleHydrationProps;
        'ui:combobox': ComboboxHydrationProps;
        'ui:dialog': DialogHydrationProps;
        'ui:field': FieldHydrationProps;
        'ui:fieldArray': FieldArrayHydrationProps;
        'ui:fileUpload': FileUploadHydrationProps;
        'ui:form': FormHydrationProps;
        'ui:input': InputHydrationProps;
        'ui:menu': MenuHydrationProps;
        'ui:navigationMenu': NavigationMenuHydrationProps;
        'ui:numberInput': NumberInputHydrationProps;
        'ui:popover': PopoverHydrationProps;
        'ui:radioGroup': RadioGroupHydrationProps;
        'ui:select': SelectHydrationProps;
        'ui:slider': SliderHydrationProps;
        'ui:switch': SwitchHydrationProps;
        'ui:tabs': TabsHydrationProps;
        'ui:textarea': TextareaHydrationProps;
        'ui:tooltip': TooltipHydrationProps;
        'ui:commandMenu': CommandMenuHydrationProps;
        'primitives:accordion': AccordionHydrationPropsImport;
        'primitives:checkbox': CheckboxHydrationPropsImport;
        'primitives:checkboxGroup': CheckboxGroupHydrationPropsImport;
        'primitives:clipboard': ClipboardHydrationPropsImport;
        'primitives:collapsible': CollapsibleHydrationPropsImport;
        'primitives:combobox': ComboboxHydrationPropsImport;
        'primitives:dialog': DialogHydrationPropsImport;
        'primitives:field': FieldHydrationPropsImport;
        'primitives:fieldArray': FieldArrayHydrationPropsImport;
        'primitives:fileUpload': FileUploadHydrationPropsImport;
        'primitives:form': FormHydrationPropsImport;
        'primitives:input': InputHydrationPropsImport;
        'primitives:menu': MenuHydrationPropsImport;
        'primitives:navigationMenu': NavigationMenuHydrationPropsImport;
        'primitives:numberInput': NumberInputHydrationPropsImport;
        'primitives:popover': PopoverHydrationPropsImport;
        'primitives:radioGroup': RadioGroupHydrationPropsImport;
        'primitives:select': SelectHydrationPropsImport;
        'primitives:slider': SliderHydrationPropsImport;
        'primitives:switch': SwitchHydrationPropsImport;
        'primitives:tabs': TabsHydrationPropsImport;
        'primitives:textarea': TextareaHydrationPropsImport;
        'primitives:tooltip': TooltipHydrationPropsImport;
        'bench:accordion': AccordionHydrationPropsImport2;
        'bench:checkbox': CheckboxHydrationPropsImport2;
        'bench:input': InputHydrationPropsImport2;
        'bench:select': SelectHydrationPropsImport2;
    }
}

export type ComboboxInputBehavior = 'autohighlight' | 'autocomplete' | 'none';
export type ComboboxSelectionBehavior = 'clear' | 'replace' | 'preserve';
export type DialogRole = 'dialog' | 'alertdialog';
export type FileUploadCapture = 'user' | 'environment';
export type ListCollection = {
    items: Record<string | number, Record<string | number, any> | object>;
    size: number;
    first: string | null;
    last: string | null;
    itemToValueKey: string | null;
    itemToStringKey: string | null;
    isItemDisabledKey: string | null;
    groupByKey: string | null;
    groupSort: string[] | string | null;
};
export type NumberInputMode = 'text' | 'tel' | 'numeric' | 'decimal';
export type Orientation = 'horizontal' | 'vertical';
export type SliderOrigin = 'start' | 'center' | 'end';
export type SliderThumbAlignment = 'contain' | 'center';
export type SliderThumbCollisionBehavior = 'none' | 'push' | 'swap';
export type TabsActivationMode = 'automatic' | 'manual';
export type TextareaSubmitOn = 'enter' | 'mod+enter';
