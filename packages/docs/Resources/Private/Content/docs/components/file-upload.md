# File Upload

**A file picker with drag and drop support, file lists, and validation hooks.**

{% component: "ui:referenceButtons", arguments: { "name": "FileUpload" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FileUpload.examples.simple", "withEntryFile": true } %}

## Features

- Supports button-triggered file selection and drag-and-drop uploads
- Works with the `Field` primitive for form labels, descriptions, and validation
- Supports accepted file types, file count limits, size limits, and directory selection
- Exposes accepted and rejected files through the client-side API
- Supports clearing files and deleting individual accepted files
- Lets already-persisted files sit in the same list as newly-picked ones, so an edit form needs only one file list
- Renders accepted/rejected file items from a single Fluid-authored `ui:template`, since the actual `File` objects only ever exist in the browser
- Supports multiple preview variants per item, for example an image preview with a generic file-extension fallback
- Replaces TYPO3's `f:form.upload` and integrates with Extbase's `#[FileUpload]` attribute out of the box

## Installation

{% component: "ui:installationSection", arguments: { "name": "FileUpload" } %}

## Examples

### With Field

Use file upload inside `Field` for form semantics and validation messaging.

{% component: "ui:componentExample", arguments: { "componentName": "FileUpload.examples.withField" } %}

### Accepted and Rejected Files

Accepted and rejected files only ever exist as browser `File` objects, so they can never be part of the server-rendered markup. The `item` part is instead authored once as a [`ui:template`](/docs/core-concepts/hydration) inside `ui:fileUpload.root`, and the primitive clones/populates it for every accepted or rejected file - the same pattern the Combobox uses for asynchronously loaded results.

{% component: "ui:componentExample", arguments: { "componentName": "FileUpload.examples.rejectedFiles" } %}

### Preview Variants

Declare multiple `itemPreview` parts inside the item template, each with a `match` MIME type pattern (e.g. `image/*`). The primitive shows the first matching variant for a given file and hides the rest, which makes an image preview plus a generic fallback icon possible without a custom render callback.

{% component: "ui:componentExample", arguments: { "componentName": "FileUpload.examples.simple" } %}

### Translations

`FileUpload` translates three labels: `dropzone` (the dropzone's `aria-label`), `itemPreview` (an accepted item's preview `alt` text), and `deleteFile` (an item's delete button `aria-label`). The last two are functions in zag-js (`(file: File) => string`), since they interpolate the file's name - but Fluid has no callbacks to hand over, and the `File` only exists in the browser anyway. So they're translated as plain strings containing a literal `%fileName%` placeholder, which the primitive substitutes with the real file name client-side:

```html
<ui:fileUpload.root translations="{itemPreview: 'Vorschau von %fileName%', deleteFile: 'Datei %fileName% entfernen'}"></ui:fileUpload.root>
```

Use `%fileName%`, not `{fileName}` - Fluid's own inline array/object syntax already treats a bare `{...}` inside a string as a nested variable expression, so a curly-brace placeholder would silently get stripped from an override written this way. Omit `itemPreview`/`deleteFile` to keep the built-in translation, or set an entry to `{false}` to omit that `aria-label`/`alt` entirely. Per-locale overrides can also live in your own `locallang.xlf` and be read with `f:translate` instead.

### Directory Upload

Enable directory selection in browsers that support `webkitdirectory`.

{% component: "ui:componentExample", arguments: { "componentName": "FileUpload.examples.directory" } %}

## Using with Extbase

`FileUpload` produces a plain, native `<input type="file">` under the hood, so it works with Extbase's [`#[FileUpload]` property attribute](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/Extbase/Domain/FileUpload.html) without any special controller code - it replaces `f:form.upload` one-to-one.

```php
<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\FileUpload;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
        uploadFolder: '1:/user_upload/conference_impressions/',
    )]
    protected ObjectStorage $impressions;

    // constructor, initializeObject(), getters and setters...
}
```

Use `ui:form.root`, and set `maxFiles` on `FileUpload` to match the property's cardinality - `1` for a single `FileReference`, greater than `1` for an `ObjectStorage<FileReference>`. For a multi-file property, write the `[]` suffix on `name` yourself, the same way you would for a [`CheckboxGroup`](/docs/components/checkbox-group) field bound to an array property - the primitive never appends it for you:

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

    <ui:field.root name="impressions">
        <ui:fileUpload.root name="impressions[]" accept="image/jpeg,image/png" maxFiles="10">
            <ui:fileUpload.label>Impressions</ui:fileUpload.label>
            <ui:fileUpload.dropzone>
                <ui:fileUpload.trigger>Choose images</ui:fileUpload.trigger>
            </ui:fileUpload.dropzone>
            <ui:fileUpload.itemGroup>
                <ui:fileUpload.emptyState>No images selected yet.</ui:fileUpload.emptyState>
            </ui:fileUpload.itemGroup>
        </ui:fileUpload.root>
    </ui:field.root>
</ui:form.root>
```

No `enctype` is needed on the form: `Form` never submits natively - it always posts a `FormData` body via `fetch` (see the [Forms guide](/docs/core-concepts/forms)), and `fetch` always sends a `FormData` body as `multipart/form-data` regardless of any `enctype` attribute, since it isn't reading the `<form>` element at all.

{% component: "ui:alert", arguments: {"title": "Keep validation in sync", "text": "Keep the allowed MIME types/extensions in #[FileUpload] in sync with the TCA type=file 'allowed' key on the same column, and pass the same list to FileUpload's accept prop so the browser's file picker pre-filters accordingly. accept is a client-side hint only, never a security control.", "variant": "warning"} %}

### Editing: Mixing Already-Uploaded Files With New Ones

`FileUpload`'s own file list only ever holds files newly picked in the browser - it has no concept of an already-persisted `FileReference` from a previous request. For an edit form, render existing files as ordinary `item`s directly inside `ui:fileUpload.itemGroup`, alongside the newly-picked ones, instead of showing them in a second, separate list. Give each one `type="existing"` and pair its delete trigger with `ui:fileUploadDeleteCheckbox`, which renders TYPO3's HMAC-signed `@delete` token so Extbase removes the file reference on submit:

Existing items still sit inside the same `ui:template`-driven `itemGroup` as newly-picked files (see [Accepted and Rejected Files](#accepted-and-rejected-files) above), so don't drop the item template when adding them - without it, the `itemGroup` renders the existing files fine, but selecting a _new_ file has nothing to clone and populate, and throws:

```html
<ui:form.root action="update" objectName="conference" object="{conference}">
    <ui:field.root name="impressions[]">
        <ui:fileUpload.root accept="image/jpeg,image/png" maxFiles="10" existingFilesCount="{conference.impressions -> f:count()}">
            <ui:fileUpload.label>Impressions</ui:fileUpload.label>
            <ui:fileUpload.dropzone>
                <ui:fileUpload.trigger>Choose images</ui:fileUpload.trigger>
            </ui:fileUpload.dropzone>

            <ui:template name="itemTemplate" context="file-upload">
                <ui:fileUpload.fileItem>
                    <primitives:fileUpload.itemPreview match="image/*" class="bg-muted flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-md border">
                        <primitives:fileUpload.itemPreviewImage class="size-full object-cover" />
                    </primitives:fileUpload.itemPreview>
                    <primitives:fileUpload.itemPreview match=".*" class="bg-muted text-muted-foreground flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-md border">
                        <div {ui:ref(name: 'previewFallback', withId: false)} class="text-[11px] font-medium uppercase"></div>
                    </primitives:fileUpload.itemPreview>
                    <div class="grid min-w-0 flex-1 gap-0.5">
                        <ui:fileUpload.fileName />
                        <ui:fileUpload.fileMeta />
                        <div {ui:ref(name: 'itemErrors', withId: false)} hidden class="text-destructive text-xs"></div>
                    </div>
                    <ui:fileUpload.fileDeleteTrigger />
                </ui:fileUpload.fileItem>
            </ui:template>

            <ui:fileUpload.itemGroup>
                <f:for each="{conference.impressions}" as="fileReference">
                    <ui:fileUpload.fileItem type="existing">
                        <f:image image="{fileReference}" width="40" height="40" class="size-10 shrink-0 rounded-md border object-cover" />
                        <div class="grid min-w-0 flex-1 gap-0.5">
                            <ui:fileUpload.fileName>{fileReference.originalResource.name}</ui:fileUpload.fileName>
                        </div>
                        <ui:fileUploadDeleteCheckbox fileReference="{fileReference}" class="hidden" />
                        <ui:fileUpload.fileDeleteTrigger />
                    </ui:fileUpload.fileItem>
                </f:for>
                <ui:fileUpload.emptyState>No images selected yet.</ui:fileUpload.emptyState>
            </ui:fileUpload.itemGroup>
        </ui:fileUpload.root>
    </ui:field.root>
</ui:form.root>
```

Clicking that delete trigger checks the sibling checkbox and hides the item immediately, exactly like removing a newly-picked file - the actual `FileReference` is only deleted once the form is submitted. The checkbox must be a sibling of `fileDeleteTrigger`, never nested inside it: a native `<button>` isn't allowed to contain interactive descendants like `<input>`, so `class="hidden"` (rather than nesting) is how it's kept out of view.

`ui:fileUploadDeleteCheckbox` requires the enclosing `ui:form.root` to have an `objectName` matching the controller action's argument name, and reads `property` from the surrounding `ui:field.root` when not given explicitly.

`existingFilesCount` tells the machine how many slots the existing items above already use, so `maxFiles` and the rejected-as-`TOO_MANY_FILES` behavior account for them too. It's deliberately a plain count, not the existing files themselves: whatever the machine holds as an accepted file also ends up in the hidden input's native `FileList` and gets resubmitted on the next form post - passing the existing `FileReference`s in as reconstructed `File` placeholders would silently overwrite each one with garbage content built from just its name and size.

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
            ["item", "One file. Accepted/rejected items are only ever rendered from a `ui:template`; an already-persisted file can be authored directly with `type=\"existing\"`. Renders a `<div>` element."],
            ["itemName", "Displays the file's name. Renders a `<div>` element."],
            ["itemSizeText", "Displays the file's formatted size. Renders a `<div>` element."],
            ["itemPreview", "A preview variant for an item, shown when its `match` MIME type pattern matches the file. Renders a `<div>` element."],
            ["itemPreviewImage", "Displays an image preview, only rendered for image files. Renders an `<img>` element."],
            ["itemDeleteTrigger", "Removes a single accepted or rejected file. Renders a `<button>` element."],
            ["clearTrigger", "Clears every accepted file. Renders a `<button>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:fileUpload.root>
    <primitives:fileUpload.label />
    <primitives:fileUpload.dropzone>
        <primitives:fileUpload.trigger />
    </primitives:fileUpload.dropzone>
    <primitives:fileUpload.hiddenInput />

    <ui:template name="itemTemplate" context="file-upload">
        <primitives:fileUpload.item>
            <primitives:fileUpload.itemPreview match="image/*">
                <primitives:fileUpload.itemPreviewImage />
            </primitives:fileUpload.itemPreview>
            <primitives:fileUpload.itemPreview match=".*" />
            <primitives:fileUpload.itemName />
            <primitives:fileUpload.itemSizeText />
            <primitives:fileUpload.itemDeleteTrigger />
        </primitives:fileUpload.item>
    </ui:template>

    <primitives:fileUpload.itemGroup>
        <primitives:fileUpload.emptyState />
    </primitives:fileUpload.itemGroup>
    <primitives:fileUpload.clearTrigger />
</primitives:fileUpload.root>
```
