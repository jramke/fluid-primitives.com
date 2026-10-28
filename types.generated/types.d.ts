declare namespace Jramke {
    namespace FluidPrimitives {
        namespace Domain {
            namespace Dto {
                export type ListCollection = {
                    items: Record<string | number, Record<string | number, any> | object>;
                    itemToValueKey: string | null;
                    itemToStringKey: string | null;
                    isItemDisabledKey: string | null;
                    groupByKey: string | null;
                    groupSort: string[] | string | null;
                };
            }
        }
        namespace Enum {
            export type ComboboxInputBehavior = 'autohighlight' | 'autocomplete' | 'none';
            export type ComboboxSelectionBehavior = 'clear' | 'replace' | 'preserve';
            export type DatePickerSelectionMode = 'single' | 'multiple' | 'range';
            export type DatePickerView = 'day' | 'month' | 'year';
            export type DialogRole = 'dialog' | 'alertdialog';
            export type FileUploadCapture = 'user' | 'environment';
            export type InputMode =
                'none' | 'text' | 'tel' | 'url' | 'email' | 'numeric' | 'decimal' | 'search';
            export type NumberInputMode = 'text' | 'tel' | 'numeric' | 'decimal';
            export type Orientation = 'horizontal' | 'vertical';
            export type PopupType = 'listbox' | 'dialog';
            export type SliderOrigin = 'start' | 'center' | 'end';
            export type SliderThumbAlignment = 'contain' | 'center';
            export type SliderThumbCollisionBehavior = 'none' | 'push' | 'swap';
            export type TabsActivationMode = 'automatic' | 'manual';
            export type TextareaSubmitOn = 'enter' | 'mod+enter';
            export type ValidationMode = 'auto' | 'onSubmit' | 'onBlur' | 'onChange';
        }
    }
}
declare namespace bench {
    export type AccordionHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        multiple?: boolean;
        collapsible?: boolean;
        defaultValue?: string[];
        disabled?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
    };
    export type CheckboxHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        defaultChecked?: any;
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
        inputMode?: Jramke.FluidPrimitives.Enum.InputMode;
        translations: {
            wordCount: string;
        };
        announce?: boolean;
        announceDebounce?: number;
    };
    export type SelectHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        collection: Jramke.FluidPrimitives.Domain.Dto.ListCollection;
        name?: string;
        form?: string;
        disabled?: boolean;
        invalid?: boolean;
        readOnly?: boolean;
        required?: boolean;
        closeOnSelect?: boolean;
        positioning?: any;
        defaultValue?: string[];
        defaultHighlightedValue?: string;
        loopFocus?: boolean;
        multiple?: boolean;
        defaultOpen?: boolean;
        popupType?: Jramke.FluidPrimitives.Enum.PopupType;
        deselectable?: boolean;
        translations: {
            clearTriggerLabel: string;
        };
    };
}
declare namespace primitives {
    export type AccordionHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        multiple?: boolean;
        collapsible?: boolean;
        defaultValue?: string[];
        disabled?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
    };
    export type ButtonHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        loading?: boolean;
        focusableWhenDisabled?: boolean;
    };
    export type CheckboxGroupHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultValue?: string[];
        name?: string;
        form?: string;
        disabled?: boolean;
        readOnly?: boolean;
        required?: boolean;
        invalid?: boolean;
        maxSelectedValues?: number;
    };
    export type CheckboxHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        defaultChecked?: any;
        name?: string;
        form?: string;
        readOnly?: boolean;
        value?: string;
    };
    export type ClipboardHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        value: string;
        timeout?: number;
        translations: {
            triggerLabelIdle: string;
            triggerLabelCopied: string;
        };
    };
    export type CollapsibleHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultOpen?: boolean;
        disabled?: boolean;
        collapsedHeight?: string;
        collapsedWidth?: string;
    };
    export type ComboboxHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        collection: Jramke.FluidPrimitives.Domain.Dto.ListCollection;
        name?: string;
        form?: string;
        disabled?: boolean;
        invalid?: boolean;
        readOnly?: boolean;
        required?: boolean;
        placeholder?: string;
        defaultOpen?: boolean;
        defaultValue?: string[];
        defaultInputValue?: string;
        defaultHighlightedValue?: string;
        openOnClick?: boolean;
        openOnKeyPress?: boolean;
        openOnChange?: boolean;
        closeOnSelect?: boolean;
        loopFocus?: boolean;
        multiple?: boolean;
        allowCustomValue?: boolean;
        alwaysSubmitOnEnter?: boolean;
        inputBehavior?: Jramke.FluidPrimitives.Enum.ComboboxInputBehavior;
        selectionBehavior?: Jramke.FluidPrimitives.Enum.ComboboxSelectionBehavior;
        popupType?: Jramke.FluidPrimitives.Enum.PopupType;
        autoFocus?: boolean;
        positioning?: any;
        translations: {
            triggerLabel: string;
            clearTriggerLabel: string;
        };
        searchUrl?: string;
    };
    export type DatePickerHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        locale?: string;
        timeZone?: string;
        name?: string;
        disabled?: boolean;
        readOnly?: boolean;
        required?: boolean;
        invalid?: boolean;
        outsideDaySelectable?: boolean;
        closeOnSelect?: boolean;
        openOnClick?: boolean;
        numOfMonths?: number;
        startOfWeek?: number;
        fixedWeeks?: boolean;
        showWeekNumbers?: boolean;
        selectionMode?: Jramke.FluidPrimitives.Enum.DatePickerSelectionMode;
        maxSelectedDates?: number;
        placeholder?: string;
        defaultView?: Jramke.FluidPrimitives.Enum.DatePickerView;
        minView?: Jramke.FluidPrimitives.Enum.DatePickerView;
        maxView?: Jramke.FluidPrimitives.Enum.DatePickerView;
        positioning?: any;
        defaultOpen?: boolean;
        inline?: boolean;
        translations: {
            clearTrigger: string;
            monthSelect: string;
            yearSelect: string;
            content: string;
            weekColumnHeader: string;
            triggerOpen: string;
            triggerClose: string;
            prevTriggerDay: string;
            prevTriggerMonth: string;
            prevTriggerYear: string;
            nextTriggerDay: string;
            nextTriggerMonth: string;
            nextTriggerYear: string;
            viewTriggerToDay: string;
            viewTriggerToMonth: string;
            viewTriggerToYear: string;
            viewTriggerDay: string;
            viewTriggerMonth: string;
            viewTriggerYear: string;
            presetTrigger: string;
            weekNumberCell: string;
            dayCell: string;
            dayCellSelected: string;
            dayCellRangeStart: string;
            dayCellRangeEnd: string;
            dayCellInRange: string;
            dayCellUnavailable: string;
            contentRoleDescription: string;
            tableRoleDescriptionDay: string;
            tableRoleDescriptionMonth: string;
            tableRoleDescriptionYear: string;
            placeholderDay: string;
            placeholderMonth: string;
            placeholderYear: string;
        };
        defaultValue?: string[];
        min?: string;
        max?: string;
        defaultFocusedValue?: string;
        serverTimeZone: string;
    };
    export type DialogHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        trapFocus?: boolean;
        preventScroll?: boolean;
        modal?: boolean;
        restoreFocus?: boolean;
        closeOnInteractOutside?: boolean;
        closeOnEscape?: boolean;
        role?: Jramke.FluidPrimitives.Enum.DialogRole;
        defaultOpen?: boolean;
    };
    export type FieldArrayHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        name: string;
        itemCount: number;
        minItems?: number;
        maxItems?: number;
        translations: {
            rowAdded: string | false;
            rowRemoved: string | false;
        };
    };
    export type FieldHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        name: string;
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        readOnly?: boolean;
        defaultValue?: any;
        validationMode?: Jramke.FluidPrimitives.Enum.ValidationMode;
        listenTo?: string[];
    };
    export type FieldsetHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
    };
    export type FileUploadHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        name?: string;
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        readOnly?: boolean;
        accept?: any;
        allowDrop?: boolean;
        maxFiles?: number;
        existingFilesCount?: number;
        maxFileSize?: number;
        minFileSize?: number;
        preventDocumentDrop?: boolean;
        capture?: Jramke.FluidPrimitives.Enum.FileUploadCapture;
        directory?: boolean;
        translations: {
            dropzone: string;
            itemPreview: string;
            deleteFile: string;
        };
    };
    export type FormHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        objectName?: string;
        validationMode?: Jramke.FluidPrimitives.Enum.ValidationMode;
        inputDebounceMs?: number;
        fieldNamePrefix: string;
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
        inputMode?: Jramke.FluidPrimitives.Enum.InputMode;
        translations: {
            wordCount: string;
        };
        announce?: boolean;
        announceDebounce?: number;
    };
    export type MenuHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        closeOnSelect?: boolean;
        composite?: boolean;
        typeahead?: boolean;
        loopFocus?: boolean;
        positioning?: any;
        defaultOpen?: boolean;
        defaultHighlightedValue?: string;
        defaultTriggerValue?: string;
        ariaLabel?: string;
        parentId?: string;
    };
    export type NavigationMenuHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        defaultValue?: string;
        openDelay?: number;
        closeDelay?: number;
        disableHoverTrigger?: boolean;
        disableClickTrigger?: boolean;
        disablePointerLeaveClose?: boolean;
    };
    export type NumberInputHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        readOnly?: boolean;
        name?: string;
        form?: string;
        defaultValue: string;
        min?: number;
        max?: number;
        step?: number;
        smallStep?: number;
        largeStep?: number;
        allowMouseWheel?: boolean;
        allowOverflow?: boolean;
        clampValueOnBlur?: boolean;
        focusInputOnChange?: boolean;
        spinOnPress?: boolean;
        formatOptions?: any;
        inputMode?: Jramke.FluidPrimitives.Enum.NumberInputMode;
        pattern?: string;
        translations: {
            incrementLabel: string;
            decrementLabel: string;
        };
        locale?: string;
    };
    export type PopoverHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        modal?: boolean;
        portalled?: boolean;
        autoFocus?: boolean;
        restoreFocus?: boolean;
        closeOnInteractOutside?: boolean;
        closeOnEscape?: boolean;
        positioning?: any;
        defaultOpen?: boolean;
        translations: {
            closeTriggerLabel: string;
        };
    };
    export type RadioGroupHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultValue?: string;
        name?: string;
        form?: string;
        disabled?: boolean;
        readOnly?: boolean;
        required?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        invalid?: boolean;
    };
    export type SelectHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        collection: Jramke.FluidPrimitives.Domain.Dto.ListCollection;
        name?: string;
        form?: string;
        disabled?: boolean;
        invalid?: boolean;
        readOnly?: boolean;
        required?: boolean;
        closeOnSelect?: boolean;
        positioning?: any;
        defaultValue?: string[];
        defaultHighlightedValue?: string;
        loopFocus?: boolean;
        multiple?: boolean;
        defaultOpen?: boolean;
        popupType?: Jramke.FluidPrimitives.Enum.PopupType;
        deselectable?: boolean;
        translations: {
            clearTriggerLabel: string;
        };
    };
    export type SliderHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        readOnly?: boolean;
        invalid?: boolean;
        name?: string;
        form?: string;
        defaultValue?: number[];
        min?: number;
        max?: number;
        step?: number;
        largeStep?: number;
        minStepsBetweenThumbs?: number;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        origin?: Jramke.FluidPrimitives.Enum.SliderOrigin;
        thumbAlignment?: Jramke.FluidPrimitives.Enum.SliderThumbAlignment;
        thumbSize?: any;
        thumbCollisionBehavior?: Jramke.FluidPrimitives.Enum.SliderThumbCollisionBehavior;
    };
    export type SwitchHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        defaultChecked?: boolean;
        name?: string;
        form?: string;
        readOnly?: boolean;
        value?: string;
    };
    export type TabsHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        loopFocus?: boolean;
        defaultValue?: string;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        activationMode?: Jramke.FluidPrimitives.Enum.TabsActivationMode;
        virtualFocus?: boolean;
        deselectable?: boolean;
    };
    export type TextareaHydrationProps = {
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
        submitOn?: Jramke.FluidPrimitives.Enum.TextareaSubmitOn;
        translations: {
            wordCount: string;
        };
        announce?: boolean;
        announceDebounce?: number;
    };
    export type ToggleGroupHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultValue?: string[];
        disabled?: boolean;
        multiple?: boolean;
        deselectable?: boolean;
        loopFocus?: boolean;
        rovingFocus?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
    };
    export type ToggleHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultPressed?: boolean;
        disabled?: boolean;
    };
    export type TooltipHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultOpen?: boolean;
        disabled?: boolean;
        openDelay?: number;
        closeDelay?: number;
        closeOnPointerDown?: boolean;
        closeOnEscape?: boolean;
        closeOnScroll?: boolean;
        closeOnClick?: boolean;
        interactive?: boolean;
        positioning?: any;
    };
}
declare namespace ui {
    export type AccordionHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        multiple?: boolean;
        collapsible?: boolean;
        defaultValue?: string[];
        disabled?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
    };
    export type ButtonHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        loading?: boolean;
        focusableWhenDisabled?: boolean;
    };
    export type CheckboxGroupExamplesSelectAllHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        items: {
            value: string;
            text: string;
        }[];
    };
    export type CheckboxGroupHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultValue?: string[];
        name?: string;
        form?: string;
        disabled?: boolean;
        readOnly?: boolean;
        required?: boolean;
        invalid?: boolean;
        maxSelectedValues?: number;
    };
    export type CheckboxHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        defaultChecked?: any;
        name?: string;
        form?: string;
        readOnly?: boolean;
        value?: string;
    };
    export type ClipboardHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        value: string;
        timeout?: number;
        translations: {
            triggerLabelIdle: string;
            triggerLabelCopied: string;
        };
    };
    export type CollapsibleHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultOpen?: boolean;
        disabled?: boolean;
        collapsedHeight?: string;
        collapsedWidth?: string;
    };
    export type ComboboxHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        collection: Jramke.FluidPrimitives.Domain.Dto.ListCollection;
        name?: string;
        form?: string;
        disabled?: boolean;
        invalid?: boolean;
        readOnly?: boolean;
        required?: boolean;
        placeholder?: string;
        defaultOpen?: boolean;
        defaultValue?: string[];
        defaultInputValue?: string;
        defaultHighlightedValue?: string;
        openOnClick?: boolean;
        openOnKeyPress?: boolean;
        openOnChange?: boolean;
        closeOnSelect?: boolean;
        loopFocus?: boolean;
        multiple?: boolean;
        allowCustomValue?: boolean;
        alwaysSubmitOnEnter?: boolean;
        inputBehavior?: Jramke.FluidPrimitives.Enum.ComboboxInputBehavior;
        selectionBehavior?: Jramke.FluidPrimitives.Enum.ComboboxSelectionBehavior;
        popupType?: Jramke.FluidPrimitives.Enum.PopupType;
        autoFocus?: boolean;
        positioning?: any;
        translations: {
            triggerLabel: string;
            clearTriggerLabel: string;
        };
        searchUrl?: string;
    };
    export type CommandMenuHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        searchUrl: string;
    };
    export type DatePickerHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        locale?: string;
        timeZone?: string;
        name?: string;
        disabled?: boolean;
        readOnly?: boolean;
        required?: boolean;
        invalid?: boolean;
        outsideDaySelectable?: boolean;
        closeOnSelect?: boolean;
        openOnClick?: boolean;
        numOfMonths?: number;
        startOfWeek?: number;
        fixedWeeks?: boolean;
        showWeekNumbers?: boolean;
        selectionMode?: Jramke.FluidPrimitives.Enum.DatePickerSelectionMode;
        maxSelectedDates?: number;
        placeholder?: string;
        defaultView?: Jramke.FluidPrimitives.Enum.DatePickerView;
        minView?: Jramke.FluidPrimitives.Enum.DatePickerView;
        maxView?: Jramke.FluidPrimitives.Enum.DatePickerView;
        positioning?: any;
        defaultOpen?: boolean;
        inline?: boolean;
        translations: {
            clearTrigger: string;
            monthSelect: string;
            yearSelect: string;
            content: string;
            weekColumnHeader: string;
            triggerOpen: string;
            triggerClose: string;
            prevTriggerDay: string;
            prevTriggerMonth: string;
            prevTriggerYear: string;
            nextTriggerDay: string;
            nextTriggerMonth: string;
            nextTriggerYear: string;
            viewTriggerToDay: string;
            viewTriggerToMonth: string;
            viewTriggerToYear: string;
            viewTriggerDay: string;
            viewTriggerMonth: string;
            viewTriggerYear: string;
            presetTrigger: string;
            weekNumberCell: string;
            dayCell: string;
            dayCellSelected: string;
            dayCellRangeStart: string;
            dayCellRangeEnd: string;
            dayCellInRange: string;
            dayCellUnavailable: string;
            contentRoleDescription: string;
            tableRoleDescriptionDay: string;
            tableRoleDescriptionMonth: string;
            tableRoleDescriptionYear: string;
            placeholderDay: string;
            placeholderMonth: string;
            placeholderYear: string;
        };
        defaultValue?: string[];
        min?: string;
        max?: string;
        defaultFocusedValue?: string;
        serverTimeZone: string;
    };
    export type DialogHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        trapFocus?: boolean;
        preventScroll?: boolean;
        modal?: boolean;
        restoreFocus?: boolean;
        closeOnInteractOutside?: boolean;
        closeOnEscape?: boolean;
        role?: Jramke.FluidPrimitives.Enum.DialogRole;
        defaultOpen?: boolean;
    };
    export type FieldArrayHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        name: string;
        itemCount: number;
        minItems?: number;
        maxItems?: number;
        translations: {
            rowAdded: string | false;
            rowRemoved: string | false;
        };
    };
    export type FieldHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        name: string;
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        readOnly?: boolean;
        defaultValue?: any;
        validationMode?: Jramke.FluidPrimitives.Enum.ValidationMode;
        listenTo?: string[];
    };
    export type FieldsetHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
    };
    export type FileUploadHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        name?: string;
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        readOnly?: boolean;
        accept?: any;
        allowDrop?: boolean;
        maxFiles?: number;
        existingFilesCount?: number;
        maxFileSize?: number;
        minFileSize?: number;
        preventDocumentDrop?: boolean;
        capture?: Jramke.FluidPrimitives.Enum.FileUploadCapture;
        directory?: boolean;
        translations: {
            dropzone: string;
            itemPreview: string;
            deleteFile: string;
        };
    };
    export type FormHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        objectName?: string;
        validationMode?: Jramke.FluidPrimitives.Enum.ValidationMode;
        inputDebounceMs?: number;
        fieldNamePrefix: string;
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
        inputMode?: Jramke.FluidPrimitives.Enum.InputMode;
        translations: {
            wordCount: string;
        };
        announce?: boolean;
        announceDebounce?: number;
    };
    export type MenuHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        closeOnSelect?: boolean;
        composite?: boolean;
        typeahead?: boolean;
        loopFocus?: boolean;
        positioning?: any;
        defaultOpen?: boolean;
        defaultHighlightedValue?: string;
        defaultTriggerValue?: string;
        ariaLabel?: string;
        parentId?: string;
    };
    export type NavigationMenuHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        defaultValue?: string;
        openDelay?: number;
        closeDelay?: number;
        disableHoverTrigger?: boolean;
        disableClickTrigger?: boolean;
        disablePointerLeaveClose?: boolean;
    };
    export type NumberInputHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        readOnly?: boolean;
        name?: string;
        form?: string;
        defaultValue: string;
        min?: number;
        max?: number;
        step?: number;
        smallStep?: number;
        largeStep?: number;
        allowMouseWheel?: boolean;
        allowOverflow?: boolean;
        clampValueOnBlur?: boolean;
        focusInputOnChange?: boolean;
        spinOnPress?: boolean;
        formatOptions?: any;
        inputMode?: Jramke.FluidPrimitives.Enum.NumberInputMode;
        pattern?: string;
        translations: {
            incrementLabel: string;
            decrementLabel: string;
        };
        locale?: string;
    };
    export type PopoverHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        modal?: boolean;
        autoFocus?: boolean;
        restoreFocus?: boolean;
        closeOnInteractOutside?: boolean;
        closeOnEscape?: boolean;
        positioning?: any;
        defaultOpen?: boolean;
        translations: {
            closeTriggerLabel: string;
        };
    };
    export type RadioGroupHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultValue?: string;
        name?: string;
        form?: string;
        disabled?: boolean;
        readOnly?: boolean;
        required?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        invalid?: boolean;
    };
    export type SelectHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        collection: Jramke.FluidPrimitives.Domain.Dto.ListCollection;
        name?: string;
        form?: string;
        disabled?: boolean;
        invalid?: boolean;
        readOnly?: boolean;
        required?: boolean;
        closeOnSelect?: boolean;
        positioning?: any;
        defaultValue?: string[];
        defaultHighlightedValue?: string;
        loopFocus?: boolean;
        multiple?: boolean;
        defaultOpen?: boolean;
        popupType?: Jramke.FluidPrimitives.Enum.PopupType;
        deselectable?: boolean;
        translations: {
            clearTriggerLabel: string;
        };
    };
    export type SliderHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        readOnly?: boolean;
        invalid?: boolean;
        name?: string;
        form?: string;
        defaultValue?: number[];
        min?: number;
        max?: number;
        step?: number;
        largeStep?: number;
        minStepsBetweenThumbs?: number;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        origin?: Jramke.FluidPrimitives.Enum.SliderOrigin;
        thumbAlignment?: Jramke.FluidPrimitives.Enum.SliderThumbAlignment;
        thumbSize?: any;
        thumbCollisionBehavior?: Jramke.FluidPrimitives.Enum.SliderThumbCollisionBehavior;
    };
    export type SwitchHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        disabled?: boolean;
        invalid?: boolean;
        required?: boolean;
        defaultChecked?: boolean;
        name?: string;
        form?: string;
        readOnly?: boolean;
        value?: string;
    };
    export type TabsHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        loopFocus?: boolean;
        defaultValue?: string;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
        activationMode?: Jramke.FluidPrimitives.Enum.TabsActivationMode;
        virtualFocus?: boolean;
        deselectable?: boolean;
    };
    export type TextareaHydrationProps = {
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
        submitOn?: Jramke.FluidPrimitives.Enum.TextareaSubmitOn;
        translations: {
            wordCount: string;
        };
        announce?: boolean;
        announceDebounce?: number;
    };
    export type ToggleGroupHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultValue?: string[];
        disabled?: boolean;
        multiple?: boolean;
        deselectable?: boolean;
        loopFocus?: boolean;
        rovingFocus?: boolean;
        orientation?: Jramke.FluidPrimitives.Enum.Orientation;
    };
    export type ToggleHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultPressed?: boolean;
        disabled?: boolean;
    };
    export type TooltipHydrationProps = {
        id: string;
        ids: {
            [key: string]: string;
        };
        defaultOpen?: boolean;
        disabled?: boolean;
        openDelay?: number;
        closeDelay?: number;
        closeOnPointerDown?: boolean;
        closeOnEscape?: boolean;
        closeOnScroll?: boolean;
        closeOnClick?: boolean;
        interactive?: boolean;
        positioning?: any;
    };
}
