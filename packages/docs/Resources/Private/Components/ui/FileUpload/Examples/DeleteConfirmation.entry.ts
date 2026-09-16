import { mount } from 'fluid-primitives';
import { Dialog } from 'fluid-primitives/dialog';
import { FileUpload, fileValue } from 'fluid-primitives/file-upload';

type PendingDeletion = { file: File; type: 'accepted' | 'rejected' };

function findFileByValue(files: File[], value: string): File | undefined {
    return files.find(file => fileValue(file) === value);
}

mount('fileUpload', 'delete-confirm', ({ props }) => {
    const fileUpload = new FileUpload(props);
    fileUpload.init();

    const dialog = mount('dialog', props.id, ({ props }) => {
        const instance = new Dialog(props);
        instance.init();
        return instance;
    });

    let pending: PendingDeletion | null = null;

    const pendingFileNameEl = dialog?.getElement<HTMLElement>('pendingFileName');
    const confirmTriggerEl = dialog?.getElement<HTMLButtonElement>('confirmTrigger');

    // FileUpload re-clones its item markup from `itemTemplate` on every accepted/rejected-files
    // change, so there's no stable per-item element a plain addEventListener could ever attach to
    // once. Delegating on the (stable) item group and using the capturing phase is the only way to
    // intercept a click before FileUpload's own bubbling-phase click handler - wired directly onto
    // each item's delete trigger via getItemDeleteTriggerProps() - runs and deletes immediately.
    // stopImmediatePropagation() is what actually stops that handler from firing.
    fileUpload.getElements<HTMLElement>('itemGroup').forEach(itemGroupEl => {
        itemGroupEl.addEventListener(
            'click',
            event => {
                const triggerEl = (event.target as HTMLElement).closest(
                    '[data-part="item-delete-trigger"]'
                );
                if (!triggerEl) return;

                const itemEl = triggerEl.closest<HTMLElement>('[data-part="item"]');
                const value = itemEl?.dataset.value;
                if (!value) return;

                const type = (itemEl?.dataset.type as PendingDeletion['type']) || 'accepted';
                const files =
                    type === 'rejected'
                        ? fileUpload.api.rejectedFiles.map(r => r.file)
                        : fileUpload.api.acceptedFiles;
                const file = findFileByValue(files, value);
                if (!file) return;

                event.preventDefault();
                event.stopImmediatePropagation();

                pending = { file, type };
                if (pendingFileNameEl) pendingFileNameEl.textContent = file.name;
                dialog?.api.setOpen(true);
            },
            { capture: true }
        );
    });

    confirmTriggerEl?.addEventListener('click', () => {
        if (pending) fileUpload.api.deleteFile(pending.file, pending.type);
        pending = null;
        dialog?.api.setOpen(false);
    });

    return fileUpload;
});
