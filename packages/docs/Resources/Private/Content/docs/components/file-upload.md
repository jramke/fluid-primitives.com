# File Upload

**A file picker with drag and drop support, file lists, and validation hooks.**

{% component: "ui:referenceButtons", arguments: { "name": "FileUpload" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.simple", "withEntryFile": true } %}

## Features

- Button-triggered file selection and drag and drop
- Accepted file types, file count limits, size limits and directory selection
- Accepted and rejected files through the client-side API
- Clear all files or delete a single one
- Already-stored files and newly picked files in one list, for edit forms
- Items authored in Fluid with an `itemTemplate`, plus a template of their own for rejected items
- Preview variants per item, for example an image with a file-extension fallback
- `fileValue()` maps a rendered item back to its `File`
- Works with [Field](/docs/components/field) and with Extbase's `#[FileUpload]` attribute, as a drop-in for `f:form.upload`

## Anatomy

```html
<primitives:fileUpload.root>
    <primitives:fileUpload.label />
    <primitives:fileUpload.dropzone>
        <primitives:fileUpload.trigger />
    </primitives:fileUpload.dropzone>
    <primitives:fileUpload.hiddenInput />

    <primitives:fileUpload.itemTemplate>
        <primitives:fileUpload.item>
            <primitives:fileUpload.itemPreview match="image/*">
                <primitives:fileUpload.itemPreviewImage />
            </primitives:fileUpload.itemPreview>
            <primitives:fileUpload.itemPreview match=".*">
                <primitives:fileUpload.itemPreviewFallback />
            </primitives:fileUpload.itemPreview>
            <primitives:fileUpload.itemName />
            <primitives:fileUpload.itemSizeText />
            <primitives:fileUpload.itemError />
            <primitives:fileUpload.itemDeleteTrigger />
        </primitives:fileUpload.item>
    </primitives:fileUpload.itemTemplate>

    <f:comment>Optional - falls back to the itemTemplate above when omitted.</f:comment>
    <primitives:fileUpload.itemTemplate
        type="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FileUploadItemType::Rejected')}"
    >
        <primitives:fileUpload.item>
            <primitives:fileUpload.itemName />
            <primitives:fileUpload.itemError />
            <primitives:fileUpload.itemDeleteTrigger />
        </primitives:fileUpload.item>
    </primitives:fileUpload.itemTemplate>

    <primitives:fileUpload.itemGroup>
        <primitives:fileUpload.emptyState />
    </primitives:fileUpload.itemGroup>
    <primitives:fileUpload.clearTrigger />
</primitives:fileUpload.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "FileUpload" } %}

## Examples

### Use a file upload in a field

Wrap the file upload in a [Field](/docs/components/field) to add helper text and error text, and to mark it `required`.

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.withField" } %}

### Show accepted and rejected files

Picked files are listed from an `itemTemplate`: author the item once and `FileUpload` clones it for every accepted or rejected file. A rejected item shows why in its `itemError` part. Drop a file over 1.5 MB, a non-image or a third file to see a rejection.

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.rejectedFiles" } %}

### Preview images and other files

Add several `itemPreview` parts to the item template, each with a `match` MIME type pattern such as `image/*`. The first part that matches the file shows, the others hide: an image preview and a generic fallback for everything else.

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.simple" } %}

### Translate the labels

`FileUpload` translates three labels: `dropzone` (the `aria-label` of the dropzone), `itemPreview` (the `alt` text of a preview image) and `deleteFile` (the `aria-label` of a delete button). The last two take a `%fileName%` placeholder, which the browser replaces with the name of the file:

```html
<ui:fileUpload.root
    translations="{itemPreview: 'Vorschau von %fileName%', deleteFile: 'Datei %fileName% entfernen'}"
></ui:fileUpload.root>
```

Write `%fileName%`, not `{fileName}`: Fluid reads a curly-brace expression inside a string as a variable and strips it. Leave out `itemPreview` or `deleteFile` to keep the built-in text. To translate per locale, read the texts from your own `locallang.xlf` with `f:translate`.

### Upload a folder

Set `directory` to pick a whole folder in browsers that support `webkitdirectory`.

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.directory" } %}

### Build your own item layout

The `itemTemplate` takes any markup. Leave out parts you can do without (this demo has no `itemSizeText`) and add your own, like a running "3 / 5 files selected" counter. Rejected files can have a template of their own: add a second `itemTemplate` with `type="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FileUploadItemType::Rejected')}"`, here one without a preview. Without it, rejected items use the accepted template.

Your own elements are `ui:ref`s with `context="fileUpload"`, in the root's slot or in the item template. A subclass of `FileUpload` fills them in `render()`, which runs after every change. `fileValue()` maps the `data-value` of a rendered item back to its `File`, which is how the demo shows each photo's modified date.

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.customLayout", "additionalFiles": { "CustomLayout.entry.ts": "EXT:docs/Resources/Private/Components/ui/FileUploadExamples/CustomLayout.entry.ts" } } %}

### Ask before deleting a file

`itemDeleteTrigger` deletes right away. To ask first, pair the file upload with a [Dialog](/docs/components/dialog): render both with `autoMount="{false}"` and a fixed `rootId`, and listen for the click on the item group in the capture phase. Call `event.stopImmediatePropagation()` there to keep the built-in delete from running, find the file with `fileValue()` and call `api.deleteFile()` once the dialog is confirmed.

Items are cloned again on every change, so the listener belongs on the item group.

{% component: "ui:componentExample", arguments: { "componentName": "FileUploadExamples.deleteConfirmation", "withEntryFile": true } %}

### Save files with Extbase

`FileUpload` renders a native `<input type="file">`, so Extbase's [`#[FileUpload]` property attribute](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/Extbase/Domain/FileUpload.html) works as it does with `f:form.upload`, without special controller code.

```php
class Conference extends AbstractEntity
{
    #[FileUpload(
        validation: [
            'required' => false,
            'maxFiles' => 1,
            'fileSize' => ['minimum' => '10K', 'maximum' => '2M'],
            'mimeType' => ['allowedMimeTypes' => ['image/jpeg', 'image/png']],
            'fileExtension' => ['allowedFileExtensions' => ['jpg', 'jpeg', 'png']],
        ],
        uploadFolder: '1:/user_upload/conference_logos/',
    )]
    protected ?FileReference $logo = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[FileUpload(
        validation: [
            'fileSize' => ['minimum' => '10K', 'maximum' => '10M'],
            'mimeType' => ['allowedMimeTypes' => ['image/jpeg', 'image/png']],
            'fileExtension' => ['allowedFileExtensions' => ['jpg', 'jpeg', 'png']],
        ],
        uploadFolder: '1:/user_upload/conference_photos/',
    )]
    protected ObjectStorage $photos;
}
```

Use `ui:form.root` and set `maxFiles` on `FileUpload` to match the property: `1` for a single `FileReference`, more for an `ObjectStorage<FileReference>`. For a multi-file property, add the `[]` suffix to `name` yourself, as for a [`CheckboxGroup`](/docs/components/checkbox-group) bound to an array property:

```html
<ui:form.root action="update" objectName="conference" object="{conference}">
    <ui:field.root name="logo">
        <ui:fileUpload.root name="logo" accept="image/jpeg,image/png" maxFiles="1">
            <ui:fileUpload.label>Logo</ui:fileUpload.label>
            <ui:fileUpload.dropzone>
                <ui:fileUpload.trigger>Choose logo</ui:fileUpload.trigger>
            </ui:fileUpload.dropzone>
            <ui:fileUpload.itemGroup>
                <ui:fileUpload.emptyState>No logo selected yet.</ui:fileUpload.emptyState>
            </ui:fileUpload.itemGroup>
        </ui:fileUpload.root>
    </ui:field.root>

    <ui:field.root name="photos">
        <ui:fileUpload.root name="photos[]" accept="image/jpeg,image/png" maxFiles="10">
            <ui:fileUpload.label>Photos</ui:fileUpload.label>
            <ui:fileUpload.dropzone>
                <ui:fileUpload.trigger>Choose photos</ui:fileUpload.trigger>
            </ui:fileUpload.dropzone>
            <ui:fileUpload.itemGroup>
                <ui:fileUpload.emptyState>No photos selected yet.</ui:fileUpload.emptyState>
            </ui:fileUpload.itemGroup>
        </ui:fileUpload.root>
    </ui:field.root>
</ui:form.root>
```

The form posts with [`extbase.post()`](/docs/client-utilities/extbase), which sends `multipart/form-data`, so the `<form>` needs no `enctype`.

`accept` only filters the file picker in the browser, `#[FileUpload]` validates the upload itself. List the same MIME types and extensions in `accept`, in `#[FileUpload]` and in the `allowed` key of the TCA `type=file` column.

### Edit files that are already uploaded

In an edit form, render the stored files as `item`s with `type="existing"` inside the same `itemGroup` as the newly picked files. Pair each delete trigger with `ui:fileUploadDeleteCheckbox`, which renders the signed `@delete` token Extbase uses to remove the file reference on submit. Give each existing item a `value`, such as the `uid` of its file reference, so their ids stay unique.

Keep the `itemTemplate` next to the existing items: newly picked files are cloned from it, and picking one throws without it.

```html
<ui:form.root action="update" objectName="conference" object="{conference}">
    <ui:field.root name="photos[]">
        <ui:fileUpload.root
            accept="image/jpeg,image/png"
            maxFiles="10"
            existingFilesCount="{conference.photos -> f:count()}"
        >
            <ui:fileUpload.label>Photos</ui:fileUpload.label>
            <ui:fileUpload.dropzone>
                <ui:fileUpload.trigger>Choose photos</ui:fileUpload.trigger>
            </ui:fileUpload.dropzone>

            <primitives:fileUpload.itemTemplate>
                <!-- the item markup for newly picked files -->
            </primitives:fileUpload.itemTemplate>

            <ui:fileUpload.itemGroup>
                <f:for each="{conference.photos}" as="fileReference">
                    <ui:fileUpload.fileItem type="existing" value="{fileReference.uid}">
                        <f:image image="{fileReference}" width="40" height="40" />
                        <ui:fileUpload.fileName
                            >{fileReference.originalResource.name}</ui:fileUpload.fileName
                        >
                        <ui:fileUploadDeleteCheckbox
                            fileReference="{fileReference}"
                            class="hidden"
                        />
                        <ui:fileUpload.fileDeleteTrigger />
                    </ui:fileUpload.fileItem>
                </f:for>
                <ui:fileUpload.emptyState>No photos selected yet.</ui:fileUpload.emptyState>
            </ui:fileUpload.itemGroup>
        </ui:fileUpload.root>
    </ui:field.root>
</ui:form.root>
```

A click on the delete trigger checks the checkbox next to it and hides the item right away, like removing a newly picked file. The `FileReference` is deleted when the form is submitted. Put the checkbox next to `fileDeleteTrigger`, a `<button>` cannot contain an `<input>`, and hide it with `class="hidden"`.

`ui:fileUploadDeleteCheckbox` needs an `objectName` on the enclosing `ui:form.root` that matches the argument name of the controller action. It reads `property` from the surrounding `ui:field.root` unless you pass it.

`existingFilesCount` counts the slots the existing items use, so `maxFiles` and the `TOO_MANY_FILES` rejection include them.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "FileUpload",
        "parts": [
            ["root", "Contains every part of the file upload. Renders a `<div>` element."],
            ["label", "The label for the file input. Renders a `<label>` element."],
            ["dropzone", "The drag-and-drop target and click-to-open surface. Renders a `<div>` element with `role=\"button\"`."],
            ["hiddenInput", "The native file input used for form submission. Renders a visually hidden `<input type=\"file\">` element."],
            ["trigger", "Opens the file picker dialog. Renders a `<button>` element."],
            ["itemGroup", "Groups accepted or rejected file items, selected via the `type` prop (`Jramke\\FluidPrimitives\\Enum\\FileUploadItemType`). Renders a `<div>` element."],
            ["emptyState", "Shown while an `itemGroup` has no items yet. Renders a `<div>` element."],
            ["itemTemplate", "Wraps the item markup for accepted files, or rejected files when `type` is set to `Jramke\\FluidPrimitives\\Enum\\FileUploadItemType::Rejected` (falls back to the accepted template when no dedicated rejected one is given). Renders a `<template>` element - never visible itself, cloned per file."],
            ["item", "One file. Accepted/rejected items are only ever rendered from an `itemTemplate`; an already-persisted file can be authored directly with `type=\"existing\"`. Renders a `<div>` element."],
            ["itemName", "Displays the file's name. Renders a `<div>` element."],
            ["itemSizeText", "Displays the file's formatted size. Renders a `<div>` element."],
            ["itemError", "Displays a rejected file's validation errors, hidden by default. Renders a `<div>` element."],
            ["itemPreview", "A preview variant for an item, shown when its `match` MIME type pattern matches the file. Renders a `<div>` element."],
            ["itemPreviewImage", "Displays an image preview, only rendered for image files. Renders an `<img>` element."],
            ["itemPreviewFallback", "Displays a generic fallback (e.g. the file extension) inside a non-image `itemPreview` variant. Renders a `<div>` element."],
            ["itemDeleteTrigger", "Removes a single accepted or rejected file. Renders a `<button>` element."],
            ["clearTrigger", "Clears every accepted file. Renders a `<button>` element."]
        ]
    }
%}
