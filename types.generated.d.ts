declare module 'fluid-primitives' {
    interface HydrationPropsRegistry {
        accordion: AccordionHydrationProps;
        alert: AlertHydrationProps;
        button: ButtonHydrationProps;
        checkbox: CheckboxHydrationProps;
        checkboxGroup: CheckboxGroupHydrationProps;
        clipboard: ClipboardHydrationProps;
        collapsible: CollapsibleHydrationProps;
        combobox: ComboboxHydrationProps;
        dialog: DialogHydrationProps;
        field: FieldHydrationProps;
        fieldArray: FieldArrayHydrationProps;
        fileUpload: FileUploadHydrationProps;
        form: FormHydrationProps;
        input: InputHydrationProps;
        menu: MenuHydrationProps;
        navigationMenu: NavigationMenuHydrationProps;
        numberInput: NumberInputHydrationProps;
        popover: PopoverHydrationProps;
        radioGroup: RadioGroupHydrationProps;
        scrollArea: ScrollAreaHydrationProps;
        select: SelectHydrationProps;
        slider: SliderHydrationProps;
        switch: SwitchHydrationProps;
        tabs: TabsHydrationProps;
        textarea: TextareaHydrationProps;
        tooltip: TooltipHydrationProps;
        commandMenu: CommandMenuHydrationProps;
        componentExample: ComponentExampleHydrationProps;
        componentPropsTable: ComponentPropsTableHydrationProps;
        counter: CounterHydrationProps;
        editEventRegistration: EditEventRegistrationHydrationProps;
        eventRegistration: EventRegistrationHydrationProps;
        formExample: FormExampleHydrationProps;
        guestList: GuestListHydrationProps;
        homeSectionTitle: HomeSectionTitleHydrationProps;
        installationSection: InstallationSectionHydrationProps;
        navigation: NavigationHydrationProps;
        passwordConfirmation: PasswordConfirmationHydrationProps;
        referenceButtons: ReferenceButtonsHydrationProps;
        skipNavLink: SkipNavLinkHydrationProps;
        textareaSubmitOnEnterExample: TextareaSubmitOnEnterExampleHydrationProps;
        textareaTransformExample: TextareaTransformExampleHydrationProps;
        transformExample: TransformExampleHydrationProps;
    }
}

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
export type AlertHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ButtonHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
defaultValue?: string[],
name?: string,
form?: string,
disabled?: boolean,
readOnly?: boolean,
required?: boolean,
invalid?: boolean,
maxSelectedValues?: number,
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
export type ClipboardHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
value: string,
timeout?: number,
translations: {
triggerLabelIdle: string | false,
triggerLabelCopied: string | false,
},
};
export type CollapsibleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
defaultOpen?: boolean,
disabled?: boolean,
collapsedHeight?: string,
collapsedWidth?: string,
};
export type ComboboxHydrationProps = {
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
placeholder?: string,
defaultOpen?: boolean,
defaultValue?: string[],
defaultInputValue?: string,
defaultHighlightedValue?: string,
openOnClick?: boolean,
openOnKeyPress?: boolean,
openOnChange?: boolean,
closeOnSelect?: boolean,
loopFocus?: boolean,
multiple?: boolean,
allowCustomValue?: boolean,
alwaysSubmitOnEnter?: boolean,
inputBehavior?: ComboboxInputBehavior,
selectionBehavior?: ComboboxSelectionBehavior,
composite?: boolean,
autoFocus?: boolean,
positioning?: unknown,
translations: {
triggerLabel: string | false,
clearTriggerLabel: string | false,
},
searchUrl?: string,
};
export type ComboboxInputBehavior = 'autohighlight' | 'autocomplete' | 'none';
export type ComboboxSelectionBehavior = 'clear' | 'replace' | 'preserve';
export type CommandMenuHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
searchUrl: string,
};
export type ComponentExampleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComponentPropsTableHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CounterHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
trapFocus?: boolean,
preventScroll?: boolean,
modal?: boolean,
restoreFocus?: boolean,
closeOnInteractOutside?: boolean,
closeOnEscape?: boolean,
role?: DialogRole,
defaultOpen?: boolean,
};
export type DialogRole = 'dialog' | 'alertdialog';
export type EditEventRegistrationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type EventRegistrationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldArrayHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
name: string,
itemCount: number,
minItems?: number,
maxItems?: number,
translations: {
rowAdded: string | false,
rowRemoved: string | false,
},
};
export type FieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
name: string,
disabled?: boolean,
invalid?: boolean,
required?: boolean,
readOnly?: boolean,
defaultValue?: unknown,
listenTo?: string[],
};
export type FileUploadCapture = 'user' | 'environment';
export type FileUploadHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
name?: string,
disabled?: boolean,
invalid?: boolean,
required?: boolean,
readOnly?: boolean,
accept?: unknown,
allowDrop?: boolean,
maxFiles?: number,
existingFilesCount?: number,
maxFileSize?: number,
minFileSize?: number,
preventDocumentDrop?: boolean,
capture?: FileUploadCapture,
directory?: boolean,
translations: {
dropzone: string | false,
itemPreview: string | false,
deleteFile: string | false,
},
};
export type FormExampleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FormHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
objectName?: string,
};
export type GuestListHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type HomeSectionTitleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type InstallationSectionHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ListCollection = {
items: Record<string | number, Record<string | number, any> | object>,
size: number,
first: string | null,
last: string | null,
itemToValueKey: string | null,
itemToStringKey: string | null,
isItemDisabledKey: string | null,
groupByKey: string | null,
groupSort: string[] | string | null,
};
export type MenuHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
closeOnSelect?: boolean,
composite?: boolean,
typeahead?: boolean,
loopFocus?: boolean,
positioning?: unknown,
defaultOpen?: boolean,
defaultHighlightedValue?: string,
defaultTriggerValue?: string,
ariaLabel?: string,
parentId?: string,
};
export type NavigationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NavigationMenuHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
orientation?: Orientation,
defaultValue?: string,
openDelay?: number,
closeDelay?: number,
disableHoverTrigger?: boolean,
disableClickTrigger?: boolean,
disablePointerLeaveClose?: boolean,
};
export type NumberInputHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
disabled?: boolean,
invalid?: boolean,
required?: boolean,
readOnly?: boolean,
name?: string,
form?: string,
defaultValue: string,
min?: number,
max?: number,
step?: number,
smallStep?: number,
largeStep?: number,
allowMouseWheel?: boolean,
allowOverflow?: boolean,
clampValueOnBlur?: boolean,
focusInputOnChange?: boolean,
spinOnPress?: boolean,
formatOptions?: unknown,
inputMode?: NumberInputMode,
pattern?: string,
translations: {
incrementLabel: string | false,
decrementLabel: string | false,
},
locale?: string,
};
export type NumberInputMode = 'text' | 'tel' | 'numeric' | 'decimal';
export type Orientation = 'horizontal' | 'vertical';
export type PasswordConfirmationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type PopoverHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
modal?: boolean,
autoFocus?: boolean,
restoreFocus?: boolean,
closeOnInteractOutside?: boolean,
closeOnEscape?: boolean,
positioning?: unknown,
defaultOpen?: boolean,
translations: {
closeTriggerLabel: string | false,
},
};
export type RadioGroupHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
defaultValue?: string,
name?: string,
form?: string,
disabled?: boolean,
readOnly?: boolean,
required?: boolean,
orientation?: Orientation,
invalid?: boolean,
};
export type ReferenceButtonsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ScrollAreaHydrationProps = {
id: string,
ids: {
[key: string]: string,
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
clearTriggerLabel: string | false,
},
};
export type SkipNavLinkHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
disabled?: boolean,
readOnly?: boolean,
invalid?: boolean,
name?: string,
form?: string,
defaultValue?: number[],
min?: number,
max?: number,
step?: number,
largeStep?: number,
minStepsBetweenThumbs?: number,
orientation?: Orientation,
origin?: SliderOrigin,
thumbAlignment?: SliderThumbAlignment,
thumbSize?: unknown,
thumbCollisionBehavior?: SliderThumbCollisionBehavior,
};
export type SliderOrigin = 'start' | 'center' | 'end';
export type SliderThumbAlignment = 'contain' | 'center';
export type SliderThumbCollisionBehavior = 'none' | 'push' | 'swap';
export type SwitchHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
disabled?: boolean,
invalid?: boolean,
required?: boolean,
defaultChecked?: boolean,
name?: string,
form?: string,
readOnly?: boolean,
value?: string,
};
export type TabsActivationMode = 'automatic' | 'manual';
export type TabsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
loopFocus?: boolean,
defaultValue?: string,
orientation?: Orientation,
activationMode?: TabsActivationMode,
composite?: boolean,
deselectable?: boolean,
};
export type TextareaHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
translations: {
wordCount: string | false,
},
};
export type TextareaSubmitOnEnterExampleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TextareaTransformExampleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
defaultOpen?: boolean,
disabled?: boolean,
openDelay?: number,
closeDelay?: number,
closeOnPointerDown?: boolean,
closeOnEscape?: boolean,
closeOnScroll?: boolean,
closeOnClick?: boolean,
interactive?: boolean,
positioning?: unknown,
};
export type TransformExampleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
