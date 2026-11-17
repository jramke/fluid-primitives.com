# Demos

Acme, the imaginary site of [fluid-primitives.com/demos](https://fluid-primitives.com/demos): parts of a TYPO3 site you build again and again, done with Fluid Primitives and the extensions you already use. The docs show its pages in a frame, open one in a new tab to see it on its own. The components are the ones `typo3 ui:add` gives you, from the [docs](../docs/Resources/Private/Components/ui), and the forms use the entry file `ui:add form` ships.

The pages and records come from `typo3 demos:setup` of the docs extension.

## News

An EXT:news list with a search, a category and a date filter. The list follows every change of a filter without a reload.

- [`SearchResult.fluid.html`](Resources/Private/Extensions/News/Templates/News/SearchResult.fluid.html) - the search result plugin of EXT:news renders the filters and the list. The filters are a GET form with an Input and a Select, named like the arguments of the plugin: `tx_news_pi1[search][subject]` and `tx_news_pi1[overwriteDemand][categories]`.
- The date is one Date Picker in range mode, with presets and a clear button. Pick the same day twice for a single day. EXT:news takes the start and the end as two arguments, `minimumDate` and `maximumDate`, and a range picker submits both dates under one name, so [`NewsFilters.entry.ts`](Resources/Private/js/NewsFilters.entry.ts) copies them into two hidden inputs.
- [`AddFiltersToNewsSearch.php`](Classes/EventListener/AddFiltersToNewsSearch.php) - the search result has no list of categories, and the dates of its search are strings. An event listener adds the items of the Select and the value of the Date Picker.
- [`setup.typoscript`](Configuration/Sets/Demos/setup.typoscript) - `disableOverrideDemand = 0` lets the filter set the categories, `categoryConjunction = or` makes EXT:news use them.
- [`ext_localconf.php`](ext_localconf.php) - excludes the category filter from the cHash, like EXT:news does for its search: a GET form can't sign its values. The plugin is uncached.

[`RegionLoader.entry.ts`](Resources/Private/js/RegionLoader.entry.ts) loads the list without a reload. Mark the part of the page that changes with `data-region="name"` and its filter form with `data-region-form="name"`. A click on a page of the pagination or a submit of the form loads the new page with `fetch` and swaps that part. The address bar follows, and back and forward work. `data-region-live` submits the form whenever a filter changes. Without JavaScript the links and the form load the page as usual. The new page brings the hydration data of its components in a `<script>` block, the script adds it to the `<head>` with the new markup, and `mountAll()` mounts the new pagination.

The pagination is `ui:pagination.root` with the paginator of the plugin. [`PageUrlsViewHelper.php`](Classes/ViewHelpers/PageUrlsViewHelper.php) builds the URL of every page for its `pageUrls`, the current URL with the page argument of the plugin.

## Login

The login of felogin, a registration that shows the validation errors of Extbase on its fields, and account settings behind the user menu of the header.

- [`Login.fluid.html`](Resources/Private/Extensions/Felogin/Templates/Login/Login.fluid.html) - felogin with an Input and a Password Input in fields. It is a native form, felogin logs in on a full request and redirects to the account settings. Keep the names `user`, `pass` and `logintype`.
- [`Form.fluid.html`](Resources/Private/Templates/Registration/Form.fluid.html) - the registration is a `ui:form` that posts with `fetch`. The fields check their native constraints (`required`, `type="email"`, `minlength`) on the client. HTML has no constraint for a repeated password, so [`Registration.entry.ts`](Resources/Private/js/Registration.entry.ts) gives that field a `validate` that compares it with `formValues.password`, and `listenTo` checks it again when the password changes. [`RegistrationController.php`](Classes/Controller/RegistrationController.php) and [`RegistrationValidator.php`](Classes/Domain/Validator/RegistrationValidator.php): `JsonValidationErrorsTrait` answers failed validation with a `422`, which the form shows on the fields. A taken email address gets the same answer from the action.
- [`UserMenu/Show.fluid.html`](Resources/Private/Templates/UserMenu/Show.fluid.html) - in the header, a `ui:menu` with the initials of the user as trigger: their name and email, a link to the account settings and a link that logs out. The initials are where an Avatar would go. Logged out, a link to the login. The plugin is uncached, see [`UserMenuController.php`](Classes/Controller/UserMenuController.php) and [`CurrentUser.php`](Classes/Service/CurrentUser.php).
- [`Account/Edit.fluid.html`](Resources/Private/Templates/Account/Edit.fluid.html) - a `ui:form` that changes the name, see [`AccountController.php`](Classes/Controller/AccountController.php). The action changes the record of the session, never one the request names.

## Header

[`Header.fluid.html`](Resources/Private/PageView/Partials/Header.fluid.html), the header of every page: a `ui:navigationMenu` with two dropdowns and two links, and the user menu. Below `md` a button opens a `ui:dialog` with the same links, as a drawer from the side, and each dropdown is a `ui:collapsible` there. The links of the dropdowns lead nowhere, on your site they come from the page tree.
