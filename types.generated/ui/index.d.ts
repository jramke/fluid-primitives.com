import { Orientation, ListCollection, ComboboxInputBehavior, ComboboxSelectionBehavior, DialogRole, FileUploadCapture, NumberInputMode, SliderOrigin, SliderThumbAlignment, SliderThumbCollisionBehavior, TabsActivationMode, TextareaSubmitOn } from '../index.d';
export type AccordionExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type AccordionExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type AccordionExamplesMultipleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type AccordionExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
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
export type CheckboxExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxExamplesDefaultCheckedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxExamplesIndeterminateHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupExamplesDefaultCheckedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupExamplesDisabledGroupHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupExamplesDisabledItemsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupExamplesMaxSelectedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CheckboxGroupExamplesSelectAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
items: {
value: string,
text: string,
}[],
};
export type CheckboxGroupExamplesSimpleHydrationProps = {
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
export type ClipboardExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ClipboardExamplesCopyButtonOnlyHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ClipboardExamplesCustomTimeoutHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ClipboardExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type CollapsibleExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CollapsibleExamplesDefaultOpenHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CollapsibleExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type CollapsibleExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
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
export type ComboboxExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesAsyncSearchGroupedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesAsyncSearchHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesCustomFilterApiHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesDefaultValueHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesDisabledItemsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesMultipleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ComboboxExamplesWithGroupsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type DialogExamplesAlertHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesInsideScrollHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesNestedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesOutsideScrollHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesPreventCloseEscapeHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesPreventCloseOutsideHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type DialogExamplesWithPopoverHydrationProps = {
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
export type FieldArrayExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldArrayExamplesEmptyHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldArrayExamplesLimitedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldArrayExamplesSimpleHydrationProps = {
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
export type FieldExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldExamplesInvalidHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldExamplesRequiredHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldExamplesWithCheckboxHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FieldExamplesWithDescriptionHydrationProps = {
id: string,
ids: {
[key: string]: string,
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
export type FileUploadExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FileUploadExamplesCustomLayoutHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FileUploadExamplesDeleteConfirmationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FileUploadExamplesDirectoryHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FileUploadExamplesRejectedFilesHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FileUploadExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type FileUploadExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type IconCheckHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconChevronDownHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconCopyHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconGithubHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconInfoHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconListToggleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconMenuHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconMoveRightHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconSearchHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconTocHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type IconXHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type InputExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type InputExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type InputExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type InputExamplesWordCountHydrationProps = {
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
export type MenuExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesContextMenuHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesGroupingHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesInsideDialogHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesMultipleTriggersHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesNestedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesWithCheckboxesHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesWithLinksHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type MenuExamplesWithRadiosHydrationProps = {
id: string,
ids: {
[key: string]: string,
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
export type NavigationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NavigationMenuExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NavigationMenuExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NavigationMenuExamplesWithLinksHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NavigationMenuExamplesWithViewportHydrationProps = {
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
export type NumberInputExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NumberInputExamplesFormatOptionsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NumberInputExamplesMinMaxHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NumberInputExamplesMouseWheelHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NumberInputExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type NumberInputExamplesWithScrubberHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type PasswordConfirmationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type PopoverExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type PopoverExamplesCustomPositioningHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type PopoverExamplesModalHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type PopoverExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type PopoverExamplesWithCloseButtonHydrationProps = {
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
closeTriggerLabel: string,
},
};
export type RadioGroupExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type RadioGroupExamplesDisabledGroupHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type RadioGroupExamplesDisabledItemsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type RadioGroupExamplesNoDefaultHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type RadioGroupExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
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
export type ScrollAreaExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ScrollAreaExamplesBothDirectionsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ScrollAreaExamplesHorizontalHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type ScrollAreaExamplesSimpleHydrationProps = {
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
export type SelectExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SelectExamplesDefaultValueHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SelectExamplesDisabledItemsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SelectExamplesMultipleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SelectExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SelectExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SelectExamplesWithGroupsHydrationProps = {
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
export type SkipNavLinkHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesRangeHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesWithDecimalValuesHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesWithDraggingIndicatorHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesWithMarksHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SliderExamplesWithNumberInputHydrationProps = {
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
export type SwitchExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SwitchExamplesDefaultCheckedHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SwitchExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SwitchExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type SwitchExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type TabsExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TabsExamplesDisabledTabsHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TabsExamplesManualActivationHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TabsExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TabsExamplesVerticalHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
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
export type TextareaExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TextareaExamplesSimpleHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TextareaExamplesWithFieldHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TextareaExamplesWordCountHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TextareaHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
name?: string,
disabled?: boolean,
invalid?: boolean,
required?: boolean,
readOnly?: boolean,
defaultValue?: string,
maxLength?: number,
submitOn?: TextareaSubmitOn,
translations: {
wordCount: string | false,
},
announceDebounce?: number,
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
export type TooltipExamplesAllHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesCloseDelayHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesCustomPositioningHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesDisabledHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesIconButtonHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesMultipleNoDelayHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesOpenDelayHydrationProps = {
id: string,
ids: {
[key: string]: string,
},
};
export type TooltipExamplesSimpleHydrationProps = {
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
