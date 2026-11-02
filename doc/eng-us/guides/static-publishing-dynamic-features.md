# Static publishing: dynamic features

This guide is for administrators and developers of an Exponential 6.0.15+ site published as a static copy. It explains
how a build finds the parts of a page that need a server, what it does with each, how the search of a copy works, how
forms reach the live site, and how a copy follows the content automatically.

[Guides](README.md) · Related: [Search, forms and comments on a static site](../help/static-publishing/search-forms-and-comments-on-a-static-site.md),
[The form endpoint](../specifications/6.0/static-publishing-form-endpoint.md), [Static publishing](../features/6.0/static-publishing.md)

## What you'll have at the end

- A target whose search, forms and logins degrade the way you chose, with the report to check it.
- The search index and page, the form endpoint and the redirect pages, understood well enough to adapt a design to
  them.
- Publishing on content publish and on a schedule, with notices.

## How a build finds what needs a server

After a page is fetched, stripped and its links rewritten, `expStaticDegrader` looks at its forms and scripts:

| Feature | Found by | Fallback |
| --- | --- | --- |
| `search` | a form with an action to `content/search`, or a GET form with a `SearchText` field | the copy's `search.html` (Search=enabled), else the live search |
| `collect` | a form with a `ActionCollectInformation` button | the form endpoint (Forms=endpoint), else the fallback text |
| `comments` | an action to `comment/` or `ezcomments/`, or a comment button | the fallback text |
| `shop` | `ActionAddToBasket`, `ActionAddToWishList`, or an action to `shop/` | the fallback text |
| `login` | an action to `user/login`, or a `Login` field with a password | removed |
| `ajax` | `ezjscore/call`, `ezjscore/run`, `ezjscServer` or `$.ez(` in the page | reported only |
| `form` | any other form with `method="post"` | left; it posts to the live site |

The report of the build (`exp:static:build --report`, the preview, the job's page) has `degraded`: per feature the
count, the pages, what was done (`search-page`, `endpoint`, `fallback`, `removed`, `live`) and up to ten example
pages. The audit event `system.staticpublish.build` names the features found.

The fallback text is a `<p class="exp-static-fallback">` with `[StaticPublishSettings] FormFallbackText` and a link
(`FormFallbackLink`) to the same page on the live site when the target knows one.

## Search

With `Search=enabled` the build adds every page that is not `noindex` to `expStaticSearchIndex`: the `lang` of
`<html>`, the title, the meta description (or the beginning of the text) and up to 40 words of the main content by
frequency (navigation, forms and scripts left out). It writes:

```
search.html                       the page; reads ?SearchText= or ?q=
static-search/search.js           the script, no library
static-search/search.css          a minimal style sheet, light and dark
static-search/index-<lang>.json   schema exp-static-search/1: { lang, pages: [ { t, u, d, k } ] }
```

`u` is the page's address relative to the root of the copy. A partial build keeps the entries of the pages it did not
build. Above `SearchMaxBytes` (2 MB) an index keeps 10 words per page, then none, then drops the teasers, then the
last pages. A site that wants its own look for the search can link to `search.html` from its design, or use the index
files with its own script.

## Forms

With `Forms=endpoint`, a collector's form gets the action `<live site>/staticpublish/form` and two hidden fields,
`ExpStaticTarget` and `ExpStaticToken`. The token is `v1.<issued>.<object id>.<HMAC>`, signed with a key derived from
the installation's secret, valid for `FormTokenDays` days. The endpoint takes a post only from the target's public
address or custom domain, with a valid token and below the rate limit, then hands it to `content/action` with
`ActionCollectInformation`: the collector's validation, e-mail and settings apply as on the live site. Every post is
audited as `system.staticpublish.form`. The full contract is in
[the form endpoint specification](../specifications/6.0/static-publishing-form-endpoint.md).

A design can show a message after the post by looking for `#exp-static-form-sent` or `#exp-static-form-failed` in the
address, with CSS (`:target`) or a line of script. A script can also post with `fetch` and
`Accept: application/json` and read `{ "ok": true, "message": "..." }`.

The build warns and uses the fallback text when the endpoint cannot be used: `FormEndpoint=disabled`, no live site
address, or no public address.

## Redirect pages

With `Extras[]=redirects` a full build reads `ezurlalias_ml` and `ezurlwildcard` and writes a page for every history
entry and custom alias of a node it holds, and for every wildcard without `*` that leads to a page of the copy. Each
is a meta refresh, a canonical link to the public address and `noindex`. A partial build keeps those of the last full
build.

## Publishing automatically

`eZContentCacheManager` calls `expStaticPublishQueue::nodes()` with the nodes whose view cache it clears. For every
target with `Triggers[]=publish`, the rows of those nodes in `exp_static_page` get `stale` set to the time; a node
without a page gets a row `.queue/<node>`. The cronjob part `staticpublish` builds the stale nodes of a target once
`PublishDebounce` seconds passed since the last change (a full build above 200 nodes) and publishes. A change made
while a build runs stays stale for the next run.

`Triggers[]=schedule` with `Schedule=<cron expression>` makes the same cronjob part build and publish in full when
the expression matched since the target's last scheduled run (kept in `<WorkDir>/.schedule.json`).

## Notices

After each publish and rollback, `expStaticNotifier` sends the e-mail of `Notify[]` and posts to `Webhook`; see
[Notifications and webhooks](../help/static-publishing/notifications-and-webhooks.md).

## Next step

[The form endpoint specification](../specifications/6.0/static-publishing-form-endpoint.md).
