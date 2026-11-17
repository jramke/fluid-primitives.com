# Demos

The demos of [fluid-primitives.com/demos](https://fluid-primitives.com/demos): parts of a TYPO3 site you build again and again, done with Fluid Primitives and the extensions you already use. Each demo is a page with content elements, like on your own site. The components are the ones `typo3 ui:add` gives you, from the [docs](../docs/Resources/Private/Components/ui).

Run `typo3 demos:setup` to create the pages, the content elements and the records, then `typo3 ke_search:indexing` for the search index. The command creates a demo page when no page has its slug, and leaves the pages that exist as they are.

## Search

ke_search with suggestions while you type, and paginated results that load without a reload.

- [`SearchForm.html`](Resources/Private/Extensions/KeSearch/Templates/SearchForm.html) - the search box is a `ui:combobox`. Its input carries the name of the search word, because a search word is free text and the hidden input of a combobox submits the selected item.
- [`SearchSuggestions.entry.ts`](Resources/Private/js/SearchSuggestions.entry.ts) - loads the suggestions while you type and submits the one you pick.
- [`SearchSuggestions.php`](Classes/Middleware/SearchSuggestions.php) - the free ke_search has no autocomplete. This endpoint returns the titles of its index that contain the text.
- [`ResultList.html`](Resources/Private/Extensions/KeSearch/Templates/ResultList.html) - the results and a `ui:pagination`, see [Load a list without a reload](#load-a-list-without-a-reload).

## News

An EXT:news list with a search, a category and a date filter.

- [`SearchResult.fluid.html`](Resources/Private/Extensions/News/Templates/News/SearchResult.fluid.html) - the search result plugin of EXT:news renders the filters and the list. The filters are a GET form with an Input, a Select and two Date Pickers, named like the arguments of the plugin: `tx_news_pi1[search][subject]`, `tx_news_pi1[overwriteDemand][categories]`, `tx_news_pi1[search][minimumDate]` and `maximumDate`. A Date Picker submits a W3C date-time, which EXT:news reads with `strtotime()`.
- [`AddCategoryFilterToNewsSearch.php`](Classes/EventListener/AddCategoryFilterToNewsSearch.php) - the search result has no list of categories, an event listener adds the items of the Select.
- [`setup.typoscript`](Configuration/Sets/Demos/setup.typoscript) - `disableOverrideDemand = 0` lets the filter set the categories, `categoryConjunction = or` makes EXT:news use them.
- [`ext_localconf.php`](ext_localconf.php) - excludes the category filter from the cHash, like EXT:news does for its search: a GET form can't sign its values. The plugin is uncached.

## Login

The login of felogin and a registration that shows the validation errors of Extbase on its fields.

- [`Login.fluid.html`](Resources/Private/Extensions/Felogin/Templates/Login/Login.fluid.html) - felogin with an Input and a Password Input in fields. It is a native form, felogin logs in on a full request. Keep the names `user`, `pass` and `logintype`.
- [`Form.fluid.html`](Resources/Private/Templates/Registration/Form.fluid.html) and [`Registration.entry.ts`](Resources/Private/js/Registration.entry.ts) - the registration is a `ui:form` that posts with `fetch`. It checks the password confirmation on the client, the server checks everything again.
- [`RegistrationController.php`](Classes/Controller/RegistrationController.php) and [`RegistrationValidator.php`](Classes/Domain/Validator/RegistrationValidator.php) - `JsonValidationErrorsTrait` answers failed validation with a `422`, which the form shows on the fields. A taken email address gets the same answer from the action. After a registration the script fills in the email of the login form.

The demo deletes accounts a day after they were created, at the next registration.

## Header

The header of a website: a Navigation Menu built from the page tree, a Dialog for the navigation on small screens and a Menu for the logged-in user.

- [`Show.fluid.html`](Resources/Private/Templates/SiteHeader/Show.fluid.html) - the header. A `ui:navigationMenu` with a dropdown of the demo pages and two links. Below a width of the header, `@3xl` of a container query, a button opens a `ui:dialog` with the same links, as a drawer from the side. The demo shows the header in two widths.
- The user menu is a `ui:menu` with the initials of the logged-in user as trigger: their name and email, a link to the account settings and "Log out". A menu item is no form control, so [`SiteHeader.entry.ts`](Resources/Private/js/SiteHeader.entry.ts) submits the logout form when "Log out" is selected. The initials are where an Avatar would go.
- [`SiteHeaderController.php`](Classes/Controller/SiteHeaderController.php) - reads the pages with [`DemoPages.php`](Classes/Service/DemoPages.php) and the user with [`CurrentUser.php`](Classes/Service/CurrentUser.php). The plugin is uncached: it shows who is logged in.

## Account

The settings page the user menu links to: a `ui:form` that changes the name of the logged-in user, see [`AccountController.php`](Classes/Controller/AccountController.php) and [`Account.entry.ts`](Resources/Private/js/Account.entry.ts). The action changes the record of the session, never one the request names. After a save, the header on the page shows the new initials.

## Load a list without a reload

The news list and the search results use the same script, [`RegionLoader.entry.ts`](Resources/Private/js/RegionLoader.entry.ts). Mark the part of the page that changes with `data-region="name"` and its filter form with `data-region-form="name"`. A click on a page of the pagination or a submit of the form loads the new page with `fetch` and swaps that part. The address bar follows, and back and forward work. `data-region-live` submits the form whenever a filter changes. Without JavaScript the links and the form load the page as usual.

The pagination is `ui:pagination.root` with the paginator of the plugin. [`PageUrlsViewHelper.php`](Classes/ViewHelpers/PageUrlsViewHelper.php) builds the URL of every page for its `pageUrls`, the current URL with the page argument of the plugin.

The new page brings the hydration data of its components in a `<script>` block. The script adds the block to the `<head>` with the new markup, and `mountAll()` mounts the new pagination.
