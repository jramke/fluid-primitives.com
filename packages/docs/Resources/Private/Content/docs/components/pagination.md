---
preview: true
---

# Pagination

**Page links or buttons for a list that is split into pages.**

{% component: "ui:referenceButtons", arguments: { "name": "Pagination" } %}

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.simple", "withEntryFile": true } %}

## Features

- Takes the paginator of TYPO3's pagination API
- Renders links that reload the page, or buttons that change the page on the client
- Shows the first and last pages and an ellipsis for the pages in between
- Labels every page and trigger for screen readers

## Anatomy

```html
<primitives:pagination.root>
    <primitives:pagination.firstTrigger />
    <primitives:pagination.prevTrigger />
    <primitives:pagination.item />
    <primitives:pagination.ellipsis />
    <primitives:pagination.itemTemplate />
    <primitives:pagination.ellipsisTemplate />
    <primitives:pagination.nextTrigger />
    <primitives:pagination.lastTrigger />
</primitives:pagination.root>
```

The root has the pages to show in its context, `pages`: a list of entries with a `type` (`page` or `ellipsis`), the page number as `value` and their position as `index`. Read it with `ui:context` inside the root and render an item or an ellipsis for each entry:

```html
<ui:pagination.root count="120">
    <ui:context name="ui:pagination" as="paginationContext" />
    <ui:pagination.prevTrigger />
    <f:for each="{paginationContext.pages}" as="page">
        <f:if condition="{page.type} == 'page'">
            <f:then><ui:pagination.item value="{page.value}" /></f:then>
            <f:else><ui:pagination.ellipsis index="{page.index}" /></f:else>
        </f:if>
    </f:for>
    <primitives:pagination.itemTemplate>
        <ui:pagination.item />
    </primitives:pagination.itemTemplate>
    <primitives:pagination.ellipsisTemplate>
        <ui:pagination.ellipsis />
    </primitives:pagination.ellipsisTemplate>
    <ui:pagination.nextTrigger />
</ui:pagination.root>
```

An item without content shows its page number, an ellipsis without content shows `…`. With `itemTemplate` and `ellipsisTemplate`, the client renders the pages it shows when the page changes without a reload: the pages go where the templates are. Pages that reload with every click need no templates.

## Installation

{% component: "ui:installationSection", arguments: { "name": "Pagination" } %}

## Examples

### Paginate a TYPO3 list

Pass the paginator of your list action to `paginator`: it sets the current page and the number of pages. The root works out which pages to show, so the paginator is all it needs. If your action already builds a TYPO3 pagination such as `SlidingWindowPagination`, pass its paginator, `{pagination.paginator}`, and set the pages to show as in [Show a window of pages](#content-show-a-window-of-pages).

For links, wrap the paginator in a `UrlAwarePaginator` with a function that returns the URL of a page. Build it with the `UriBuilder` of your controller: the URLs carry a cHash and go through the route enhancers of your site, so search engines find every page. `setSection()` makes the links jump to the list, so the next page opens at the list. The root builds the URLs of the pages it shows.

```php
public function listAction(int $currentPage = 1): ResponseInterface
{
    $paginator = new UrlAwarePaginator(
        new QueryResultPaginator($this->eventRepository->findAll(), $currentPage, 4),
        fn(int $page): string => $this->uriBuilder->reset()
            ->setSection('event-list')
            ->uriFor(null, ['currentPage' => $page]),
    );
    $this->view->assign('paginator', $paginator);

    return $this->htmlResponse();
}
```

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.links", "id": "event-list" } %}

### Load pages without a reload

Keep the links of the list action above, and change the page on the client instead. Render the root with `autoMount="{false}"` and mount it yourself: `onPageChange` loads the events of the page from a JSON endpoint, and a click on a link adds its URL to the browser history instead of loading it. The back button, a reload and a bookmark open the same page, and search engines follow the links. With an `itemTemplate`, the server sends the URL of every page as `pageUrls`, so the links the client renders lead to their page too.

```php
public function eventsAction(int $page = 1): ResponseInterface
{
    $paginator = new QueryResultPaginator($this->eventRepository->findAll(), $page, 4);
    $events = array_map(
        static fn(Event $event): array => ['title' => $event->getTitle(), 'city' => $event->getCity()],
        [...$paginator->getPaginatedItems()],
    );

    return $this->jsonResponse(json_encode(['events' => $events]));
}
```

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.fetchWithUrl", "id": "event-archive", "additionalFiles": {"FetchWithUrl.entry.ts": "EXT:docs/Resources/Private/Components/ui/PaginationExamples/FetchWithUrl.entry.ts"} } %}

The client takes `pageUrls` as Zag's `getPageUrl` for the links it renders. Pass a `getPageUrl` of your own when you mount the root, and the links the client renders get its URLs. Keep the cHash in mind: a query argument the server did not put into the URL breaks it.

### Paginate a list in the browser

With buttons, the pagination changes the page on the client and calls `onPageChange`. Render the items of the page there from a `ui:template`. `count` and `defaultPageSize` set the number of pages.

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.clientList", "additionalFiles": {"ClientList.entry.ts": "EXT:docs/Resources/Private/Components/ui/PaginationExamples/ClientList.entry.ts"} } %}

### Show a window of pages

`siblingCount` sets the number of pages on each side of the current one, `boundaryCount` the number at the start and the end. With `boundaryCount="0"` and the ellipsis left out of the loop and of the templates, the pagination shows a window of pages that moves with the current one.

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.window" } %}

### Jump to the first and last page

Add `firstTrigger` and `lastTrigger`.

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.firstAndLast" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Pagination",
        "parts": [
            ["root", "Provides the pages and labels the navigation. Renders a `<nav>` element."],
            ["item", "Goes to one page. Renders an `<a>` element with a `UrlAwarePaginator` or `pageUrls`, a `<button>` element otherwise."],
            ["ellipsis", "Stands for the pages that are left out. Renders a `<span>` element."],
            ["itemTemplate", "Holds the item the client clones for a page it renders. Renders a `<template>` element."],
            ["ellipsisTemplate", "Holds the ellipsis the client clones. Renders a `<template>` element."],
            ["firstTrigger", "Goes to the first page. Renders an `<a>` or a `<button>` element, as the item."],
            ["prevTrigger", "Goes to the previous page. Renders an `<a>` or a `<button>` element, as the item."],
            ["nextTrigger", "Goes to the next page. Renders an `<a>` or a `<button>` element, as the item."],
            ["lastTrigger", "Goes to the last page. Renders an `<a>` or a `<button>` element, as the item."]
        ]
    }
%}
