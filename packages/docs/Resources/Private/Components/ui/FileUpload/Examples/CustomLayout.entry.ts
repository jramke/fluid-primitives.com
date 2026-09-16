import { mount } from 'fluid-primitives';
import { FileUpload, fileValue } from 'fluid-primitives/file-upload';

function findFileByValue(files: File[], value: string): File | undefined {
    return files.find(file => fileValue(file) === value);
}

class FileUploadWithCounter extends FileUpload {
    render() {
        super.render();
        this.updateFilesCounter();
        this.updateItemModifiedDates();
    }

    private updateFilesCounter() {
        const counterEl = this.getElement<HTMLElement>('filesCounter');
        if (!counterEl) return;

        counterEl.textContent = `${this.api.acceptedFiles.length} / ${this.machine.prop('maxFiles')} files selected`;
    }

    /**
     * `itemModifiedDate` isn't a part FileUpload knows about at all - it's a plain ui:ref inside
     * the item template (see CustomLayout.html), populated entirely here. render() already reruns
     * on every accepted/rejected-files change (see Component.init()), which is also exactly when
     * new item clones need this filled in, so no separate wiring is needed beyond this loop.
     * getElement(part, itemEl) - scoped to one specific item - finds it despite there being one
     * per rendered file: Template's clone-time restamping (see ComponentHydrator.restampValue())
     * gives every ref'd element with an id a unique one per file, the same way itemName/
     * itemSizeText/itemPreview already get theirs, so no special handling is needed here either.
     */
    private updateItemModifiedDates() {
        this.getElements<HTMLElement>('item').forEach(itemEl => {
            const dateEl = this.getElement<HTMLElement>('itemModifiedDate', itemEl);
            const value = itemEl.dataset.value;
            if (!dateEl || !value) return;

            const file = findFileByValue(this.api.acceptedFiles, value);
            if (!file) return;

            dateEl.textContent = `Modified ${new Date(file.lastModified).toLocaleDateString()}`;
        });
    }
}

mount('fileUpload', 'custom-layout', ({ props }) => {
    const fileUpload = new FileUploadWithCounter(props);
    fileUpload.init();
    return fileUpload;
});
