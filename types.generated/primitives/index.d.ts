import { Orientation, ListCollection, ComboboxInputBehavior, ComboboxSelectionBehavior, DatePickerSelectionMode, DatePickerView, Direction, DialogRole, FileUploadCapture, NumberInputMode, SliderOrigin, SliderThumbAlignment, SliderThumbCollisionBehavior, TabsActivationMode } from '../index.d';
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
triggerLabelIdle: string,
triggerLabelCopied: string,
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
triggerLabel: string,
clearTriggerLabel: string,
},
searchUrl?: string,
};
export type DatePickerHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
locale?: string,
timeZone?: string,
name?: string,
disabled?: boolean,
readOnly?: boolean,
required?: boolean,
invalid?: boolean,
outsideDaySelectable?: boolean,
min?: string,
max?: string,
closeOnSelect?: boolean,
openOnClick?: boolean,
defaultValue?: unknown,
defaultFocusedValue?: string,
numOfMonths?: number,
startOfWeek?: number,
fixedWeeks?: boolean,
showWeekNumbers?: boolean,
selectionMode?: DatePickerSelectionMode,
maxSelectedDates?: number,
placeholder?: string,
defaultView?: DatePickerView,
minView?: DatePickerView,
maxView?: DatePickerView,
positioning?: unknown,
defaultOpen?: boolean,
inline?: boolean,
dir?: Direction,
translations: unknown,
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
dropzone: string,
itemPreview: string,
deleteFile: string,
},
};
export type FormHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
objectName?: string,
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
incrementLabel: string,
decrementLabel: string,
},
locale?: string,
};
export type PopoverHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
modal?: boolean,
portalled?: boolean,
autoFocus?: boolean,
restoreFocus?: boolean,
closeOnInteractOutside?: boolean,
closeOnEscape?: boolean,
positioning?: unknown,
defaultOpen?: boolean,
translations: {
closeTriggerLabel: string,
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
clearTriggerLabel: string,
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
