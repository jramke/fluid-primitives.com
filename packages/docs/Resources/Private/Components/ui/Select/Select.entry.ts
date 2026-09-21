import { mountAll } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';

mountAll('select', ({ props }) => {
    // `collection` is the raw wire shape (ListCollectionData) here - Select's own transformProps()
    // turns it into a real ListCollection before the machine sees it, but the constructor's own
    // Props type (unchanged by the generated hydration type) still expects the already-transformed
    // shape statically.
    const select = new Select(props as unknown as ConstructorParameters<typeof Select>[0]);
    select.init();
    return select;
});
