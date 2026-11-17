import { extbase, mount } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';

// "Your initials would be": the server makes them, with the same function as for the user menu
mount('ui:field', 'account-name', ({ props }) => {
    const field = new Field(props).init();
    const initials = field.hydrator.query<HTMLElement>('initials');
    let request: AbortController | undefined;
    let typing: ReturnType<typeof setTimeout> | undefined;

    async function preview(name: string) {
        request?.abort();
        request = new AbortController();
        try {
            const response = await extbase.post(
                initials!.dataset.url!,
                { name },
                { signal: request.signal }
            );
            const data: { initials?: string } = await response.json();
            if (data.initials !== undefined) initials!.textContent = data.initials;
        } catch {
            // A newer name aborted the request
        }
    }

    field.hydrator.query('root')?.addEventListener('input', event => {
        const name = (event.target as HTMLInputElement).value;
        clearTimeout(typing);
        typing = setTimeout(() => preview(name), 300);
    });

    return field;
});
