# Demos

The demos of [fluid-primitives.com/demos](https://fluid-primitives.com/demos): parts of a TYPO3 site you build again and again, done with Fluid Primitives and the extensions you already use. Each demo is a page with content elements, like on your own site. The components are the ones `typo3 ui:add` gives you, from the [docs](../docs/Resources/Private/Components/ui).

Run `typo3 demos:setup` to create the pages, the content elements and the records. The command creates a demo page when no page has its slug, and leaves the pages that exist as they are.

## News

An EXT:news list with a search, a category and a date filter. The list follows every change of a filter without a reload.

- [`SearchResult.fluid.html`](Resources/Private/Extensions/News/Templates/News/SearchResult.fluid.html) - the search result plugin of EXT:news renders the filters and the list. The filters are a GET form with an Input and a Select, named like the arguments of the plugin: `tx_news_pi1[search][subject]` and `tx_news_pi1[overwriteDemand][categories]`.
- The date is one Date Picker in range mode, with presets and a clear button. Pick the same day twice for a single day. EXT:news takes the start and the end as two arguments, `minimumDate` and `maximumDate`, and a range picker submits both dates under one name, so [`NewsFilters.entry.ts`](Resources/Private/js/NewsFilters.entry.ts) copies them into two hidden inputs.
- [`AddFiltersToNewsSearch.php`](Classes/EventListener/AddFiltersToNewsSearch.php) - the search result has no list of categories, and the dates of its search are strings. An event listener adds the items of the Select and the value of the Date Picker.
- [`setup.typoscript`](Configuration/Sets/Demos/setup.typoscript) - `disableOverrideDemand = 0` lets the filter set the categories, `categoryConjunction = or` makes EXT:news use them.
- [`ext_localconf.php`](ext_localconf.php) - excludes the category filter from the cHash, like EXT:news does for its search: a GET form can't sign its values. The plugin is uncached.

## Login

The login of felogin, a registration that shows the validation errors of Extbase on its fields, and a user menu with the account settings.

- [`Login.fluid.html`](Resources/Private/Extensions/Felogin/Templates/Login/Login.fluid.html) - felogin with an Input and a Password Input in fields. It is a native form, felogin logs in on a full request. Keep the names `user`, `pass` and `logintype`.
- [`Form.fluid.html`](Resources/Private/Templates/Registration/Form.fluid.html) and [`Registration.entry.ts`](Resources/Private/js/Registration.entry.ts) - the registration is a `ui:form` that posts with `fetch`. It checks the password confirmation on the client, the server checks everything again.
- [`RegistrationController.php`](Classes/Controller/RegistrationController.php) and [`RegistrationValidator.php`](Classes/Domain/Validator/RegistrationValidator.php) - `JsonValidationErrorsTrait` answers failed validation with a `422`, which the form shows on the fields. A taken email address gets the same answer from the action. After a registration the script fills in the email of the login form.
- [`UserMenu/Show.fluid.html`](Resources/Private/Templates/UserMenu/Show.fluid.html) - after the login, a `ui:menu` with the initials of the user as trigger: their name and email, a link to the account settings and "Log out". The initials are where an Avatar would go. A menu item is no form control, so [`UserMenu.entry.ts`](Resources/Private/js/UserMenu.entry.ts) submits the logout form when "Log out" is selected. The plugin is uncached, see [`UserMenuController.php`](Classes/Controller/UserMenuController.php) and [`CurrentUser.php`](Classes/Service/CurrentUser.php).
- [`Account/Edit.fluid.html`](Resources/Private/Templates/Account/Edit.fluid.html) - the account settings: a `ui:form` that changes the name, see [`AccountController.php`](Classes/Controller/AccountController.php) and [`Account.entry.ts`](Resources/Private/js/Account.entry.ts). The action changes the record of the session, never one the request names. After a save the user menu shows the new initials.

The felogin form and the registration hide at login, the user menu shows at login. The demo deletes accounts a day after they were created, at the next registration.

## Header

The header of a website: a Navigation Menu built from the page tree, and a Dialog with the navigation on small screens.

- [`SiteHeader/Show.fluid.html`](Resources/Private/Templates/SiteHeader/Show.fluid.html) - a `ui:navigationMenu` with a dropdown of the demo pages and two links. Below `@3xl`, a width of a container query, a button opens a `ui:dialog` with the same links, as a drawer from the side. Resize the frame of the demo to see it.
- [`SiteHeaderController.php`](Classes/Controller/SiteHeaderController.php) - reads the pages with [`DemoPages.php`](Classes/Service/DemoPages.php).

## Load a list without a reload

The news list uses [`RegionLoader.entry.ts`](Resources/Private/js/RegionLoader.entry.ts). Mark the part of the page that changes with `data-region="name"` and its filter form with `data-region-form="name"`. A click on a page of the pagination or a submit of the form loads the new page with `fetch` and swaps that part. The address bar follows, and back and forward work. `data-region-live` submits the form whenever a filter changes. Without JavaScript the links and the form load the page as usual.

The pagination is `ui:pagination.root` with the paginator of the plugin. [`PageUrlsViewHelper.php`](Classes/ViewHelpers/PageUrlsViewHelper.php) builds the URL of every page for its `pageUrls`, the current URL with the page argument of the plugin.

The new page brings the hydration data of its components in a `<script>` block. The script adds the block to the `<head>` with the new markup, and `mountAll()` mounts the new pagination.
