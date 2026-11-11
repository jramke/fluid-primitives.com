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
    <ui:template name="itemTemplate" context="pagination">
        <ui:pagination.item />
    </ui:template>
    <ui:template name="ellipsisTemplate" context="pagination">
        <ui:pagination.ellipsis />
    </ui:template>
    <ui:pagination.nextTrigger />
</ui:pagination.root>
```

An item without content shows its page number, an ellipsis without content shows `…`. With the two templates, the client renders the pages it shows when the page changes without a reload: the pages go where the templates are. Pages that reload with every click need no templates. With `type` `link`, the templates make the server send the URL of every page, see [Load pages without a reload](#content-load-pages-without-a-reload).

## Installation

{% component: "ui:installationSection", arguments: { "name": "Pagination" } %}

## Examples

### Paginate a TYPO3 list

Pass the paginator of your list action to `paginator`, and set `type` to `link`. Every page is a link with a cHash that goes through the route enhancers of your site, so search engines find every page. The links set the `currentPage` argument of your plugin, `pageArgument` names another one. `section` takes the id of an element the links jump to, so the next page opens at the list instead of the top of the page.

```php
public function listAction(int $currentPage = 1): ResponseInterface
{
    $paginator = new QueryResultPaginator($this->eventRepository->findAll(), $currentPage, 4);
    $this->view->assign('paginator', $paginator);

    return $this->htmlResponse();
}
```

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.links", "id": "event-list" } %}

### Load pages without a reload

Keep the links, and change the page on the client instead. Render the root with `autoMount="{false}"` and mount it yourself: `onPageChange` loads the events of the page from a JSON endpoint and puts the URL of the page into the address bar, so the back button, a bookmark and a search engine get the same page. With `type` `link` and the item template, the server sends the URL of every page along, and the links the client renders get theirs from it.

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

### Paginate a list in the browser

With buttons, the pagination changes the page on the client and calls `onPageChange`. Render the items of the page there from a `ui:template`. `count` and `defaultPageSize` set the number of pages.

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.clientList", "additionalFiles": {"ClientList.entry.ts": "EXT:docs/Resources/Private/Components/ui/PaginationExamples/ClientList.entry.ts"} } %}

### Jump to the first and last page

Add `firstTrigger` and `lastTrigger`. `siblingCount` sets the number of pages next to the current one, `boundaryCount` the number at the start and the end.

{% component: "ui:componentExample", arguments: { "componentName": "PaginationExamples.firstAndLast" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Pagination",
        "parts": [
            ["root", "Provides the pages and labels the navigation. Renders a `<nav>` element."],
            ["item", "Goes to one page. Renders an `<a>` element with `type` `link`, a `<button>` element otherwise."],
            ["ellipsis", "Stands for the pages that are left out. Renders a `<span>` element."],
            ["firstTrigger", "Goes to the first page. Renders an `<a>` or a `<button>` element, as the item."],
            ["prevTrigger", "Goes to the previous page. Renders an `<a>` or a `<button>` element, as the item."],
            ["nextTrigger", "Goes to the next page. Renders an `<a>` or a `<button>` element, as the item."],
            ["lastTrigger", "Goes to the last page. Renders an `<a>` or a `<button>` element, as the item."]
        ]
    }
%}
