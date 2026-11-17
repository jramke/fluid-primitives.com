// After a registration, the login form comes with the email of the new account, the password is next
const KEY = 'demos-new-account';

let username: string | null = null;
try {
    username = sessionStorage.getItem(KEY);
    sessionStorage.removeItem(KEY);
} catch {
    // No storage, as in some private windows: the email stays empty
}
const form = document.getElementById('login-form');
const user = form?.querySelector<HTMLInputElement>('input[name="user"]');
if (username && user) {
    user.value = username;
    user.dispatchEvent(new Event('input', { bubbles: true }));
    form?.querySelector<HTMLInputElement>('input[name="pass"]')?.focus();
}
