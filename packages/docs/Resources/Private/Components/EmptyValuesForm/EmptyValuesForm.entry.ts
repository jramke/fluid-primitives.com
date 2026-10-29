import { extbase, mount, mountAll } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mountAll('ui:emptyValuesForm', () => {
    mount('ui:form', 'empty-values-form', ({ props }) =>
        new Form({
            ...props,
            onSubmit: async ({ api, values }) => {
                const response = await extbase.post(api.getAction(), values.toObject(), {
                    prefix: api.getFieldNamePrefix(),
                    objectName: api.getObjectName(),
                });
                return response.ok;
            },
        }).init()
    );
});
