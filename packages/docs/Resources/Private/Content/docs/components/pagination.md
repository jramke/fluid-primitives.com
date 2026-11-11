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

The context of the root holds the `pages` to show, each with a `type` (`page` or `ellipsis`), a `value` and an `index`. Loop over them with `ui:context`:

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

An item without content shows its page number, an ellipsis shows `…`. With `itemTemplate` and `ellipsisTemplate`, the client renders the pages when the page changes without a reload.

## Installation

{% component: "ui:installationSection", arguments: { "name": "Pagination" } %}

## Examples

### Paginate a TYPO3 list

Pass the paginator of your list action to `paginator`, not a TYPO3 pagination: the root works out which pages to show. For links, wrap it in a `UrlAwarePaginator` with a function that builds the URL of a page, for example with the `UriBuilder`, which adds the cHash. The root builds the URLs of the pages it shows.

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

Render the root with `autoMount="{false}"` and mount it yourself: `onPageChange` loads the events from the same list action with `format` `json`, and a click adds the URL of its link to the history instead of loading it. With an `itemTemplate`, the server sends the URL of every page as `pageUrls`, for the links the client renders.

```php
public function listAction(int $currentPage = 1): ResponseInterface
{
    $paginator = new UrlAwarePaginator(/* as above */);

    if ($this->request->getFormat() === 'json') {
        $events = array_map(
            static fn(Event $event): array => ['title' => $event->getTitle(), 'city' => $event->getCity()],
            [...$paginator->getPaginatedItems()],
        );
        return $this->jsonResponse(json_encode(['events' => $events]));
    }

    $this->view->assign('paginator', $paginator);

    return $this->htmlResponse();
}
```

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.fetchWithUrl", "id": "event-archive", "additionalFiles": {"FetchWithUrl.entry.ts": "EXT:docs/Resources/Private/Components/ui/PaginationExamples/FetchWithUrl.entry.ts"} } %}

### Show a window of pages

With `boundaryCount="0"` and no ellipsis in the loop, the pagination shows a window of pages around the current one. `siblingCount` sets its width.

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
