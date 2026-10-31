# GraphQL recipes: a blog, a mobile app, a static build, an AI agent

Four common front ends on the [GraphQL API](../features/6.0/graphql.md), and live updates with webhooks and
subscriptions, each with the queries that do the work and the settings that go with them. The queries were run against the demo content of the media site (Fit & Healthy) of
6.0.15; the answers are shortened with `...`. Switch the API on first: [step by step](graphql-walkthrough.md), or
`./console exp:graphql:enable --apply`.

The class and field names below are those of the demo (`ng_article`, `teaser_intro`, `body`): with the default naming
the schema uses your admin's identifiers, so read yours from `./console exp:graphql:schema --format=summary`.

## Contents

1. [A blog or news front end](#1-a-blog-or-news-front-end)
2. [A mobile app reading the menu and articles](#2-a-mobile-app-reading-the-menu-and-articles)
3. [A static build](#3-a-static-build)
4. [An AI agent reading content through GraphQL](#4-an-ai-agent-reading-content-through-graphql)
5. [Live updates: a webhook for the shop, a live list in the browser](#5-live-updates-a-webhook-for-the-shop-a-live-list-in-the-browser)

## 1. A blog or news front end

A list page with the newest articles of a category, page by page, and an article page.

```graphql
query Articles($path: String!, $after: String) {
  node(urlAlias: $path) {
    name
    children(classes: ["ng_article"], sortField: PUBLISHED, sortOrder: DESC, first: 3, after: $after) {
      totalCount
      pageInfo { hasNextPage endCursor }
      edges {
        node {
          name
          urlAlias
          published
          ... on ng_article {
            teaser_intro { excerpt(length: 80) }
            image { url(alias: "medium") alt }
          }
        }
      }
    }
  }
}
```

With `{"path": "fit-healthy/fitness"}`:

```json
{ "data": { "node": { "name": "Fitness", "children": {
    "totalCount": 14,
    "pageInfo": { "hasNextPage": true, "endCursor": "ZXhwOjI=" },
    "edges": [
      { "node": { "name": "Effective Training Techniques: Isolation vs Compound ...",
                  "urlAlias": "fit-healthy/fitness/effective-training-techniques-isolation-vs-compound",
                  "published": "2018-08-23T22:44:50+00:00", ... } },
      { "node": { "name": "How to Track Your Mobility: Training for Performance and Injury Prevention", ... } },
      { "node": { "name": "Effective Training Techniques: High Intensity Interval Training (HIIT)", ... } } ] } } } }
```

The next page sends `"after": "ZXhwOjI="`, the `endCursor` of this one, until `hasNextPage` is `false`. `limit` and
`offset` work as well, on the same field.

The article page, by its address:

```graphql
query Article($path: String!) {
  node(urlAlias: $path) {
    name
    ownerName
    published
    parent { name urlAlias }
    ... on ng_article {
      full_intro { text }
      body { html }
      image { url(alias: "large") width(alias: "large") height(alias: "large") alt }
    }
  }
}
```

```json
{ "data": { "node": { "name": "Why Track Your Workouts?", "ownerName": "Graham Brookins",
    "parent": { "name": "Fitness", "urlAlias": "fit-healthy/fitness" },
    "full_intro": { "text": "Of all the things I track, one of the most valuable are my workouts. ..." },
    "body": { "html": "<p>Obviously getting to the gym or just doing your runs is ..." }, ... } } }
```

**Settings.** Store both queries (`exp:graphql:persist queries/ --apply`) and call them by name with `GET`: the
[response cache](../features/6.0/graphql.md#speed) answers them, and publishing an article expires it. The
[Next.js or Nuxt starter kit](graphql-walkthrough.md#6-generate-the-starter-project) has these pages ready.

## 2. A mobile app reading the menu and articles

An app opens with the menu, shows a list of a section and an article. It is a public client: give it no key (it reads
what the anonymous user may) or a key with `graphql:read` only, never one that writes.

```graphql
query Menu {
  node(urlAlias: "fit-healthy") {
    name
    children(limit: 10) { nodes { name urlAlias classIdentifier } }
  }
}
```

```json
{ "data": { "node": { "name": "Fit & Healthy", "children": { "nodes": [
    { "name": "Fitness", "urlAlias": "fit-healthy/fitness", "classIdentifier": "ng_landing_page" },
    { "name": "Healthy eating", "urlAlias": "fit-healthy/healthy-eating", "classIdentifier": "ng_landing_page" },
    { "name": "Recipes", "urlAlias": "fit-healthy/recipes", "classIdentifier": "ng_category" },
    { "name": "Running", "urlAlias": "fit-healthy/running", "classIdentifier": "ng_topic" }, ... ] } } } }
```

The section list is the `Articles` query of recipe 1; the article screen reads the text the app lays out itself:

```graphql
query ArticleForApp($path: String!) {
  node(urlAlias: $path) {
    name
    ... on ng_article {
      full_intro { text }
      body { html }
      image { variations(aliases: ["small", "large"]) { alias url width height } }
    }
  }
}
```

`variations` gives every size in one field, so the app picks the one for its screen. Rich text comes as HTML (the
Expo kit renders it with `react-native-render-html`) or as `text`.

**Settings.** `./console exp:graphql:starter --kit=expo --to=../app --apply` generates the app. Store its queries and
set `PersistedQueriesOnly=anonymous`: the app can then only run the queries you stored, whoever takes it apart. The
anonymous rate limit (`RateLimitAnonymous`, 120 a minute per address) counts every phone on a shared network address
together; raise it for the app's siteaccess if many users share one.

## 3. A static build

A site generated into HTML files at build time, served from any host or CDN, rebuilt when content changes.

```bash
./console exp:graphql:starter --kit=astro --to=../site-static --apply
cd ../site-static && npm install && npm run build       # dist/
```

The build walks the content tree from the root (`EXP_BUILD_DEPTH` levels, 3 by default) with the generated `Node`
query and writes one page per URL alias, each with the query of its class:

```graphql
query Node($path: String!) {
  node(urlAlias: $path) {
    id
    name
    classIdentifier
    urlAlias
    mainLocation { id }
    parent { name urlAlias }
    children(limit: 20) {
      totalCount
      nodes { id name urlAlias classIdentifier }
    }
  }
}
```

**Settings.** The build reads with the anonymous user (no key needed for public content) and runs on a server, so no
CORS setting is needed. A build reads many pages in a short time: give it a key (`exp:graphql:keys --create
--for=<a build user> --apply`), so it counts against `RateLimit` (600 a minute) rather than the anonymous limit of its
address. Rebuild from a cron job or a deploy hook after content changed.

## 4. An AI agent reading content through GraphQL

An agent (a chat assistant answering from the site's articles, a summarizer, a tool of an MCP server) needs the content as plain text, found by words, with the rights of one user.

```graphql
query Find($text: String!) {
  search(text: $text, classes: ["ng_article"], limit: 5) {
    totalCount
    engine
    nodes {
      name
      url
      published
      ... on ng_article { full_intro { text } body { excerpt(length: 400) } }
    }
  }
}
```

With `{"text": "workouts"}`:

```json
{ "data": { "search": { "totalCount": 7, "engine": "eZSearchEngine", "nodes": [
    { "name": "The BiPolar Learning Curve", "url": "/fit-healthy/fitness/the-bipolar-learning-curve",
      "published": "2018-07-23T11:02:45+00:00",
      "full_intro": { "text": "People always forget about the nature of a learning curve." },
      "body": { "excerpt": "If you’re relatively new to training or ..." } }, ... ] } } }
```

`search` uses the installation's search engine (the kernel's, or Solr through ezfind, with `facets`); `engine` says
which answered. Then the whole article as text:

```graphql
query Read($path: String!) {
  node(urlAlias: $path) {
    name
    ownerName
    modified
    ... on ng_article { full_intro { text } body { text } }
  }
}
```

```json
{ "data": { "node": { "name": "Why Track Your Workouts?", "ownerName": "Graham Brookins",
    "full_intro": { "text": "Of all the things I track, one of the most valuable are my workouts. Along with tracking your heart and your sleep, I believe tracking your workouts provides a lot of benefits." },
    "body": { "text": "Obviously getting to the gym or just doing your runs is ..." } } } }
```

**Settings.** Give the agent its own user and a key with `graphql:read` only: the agent then reads exactly what that
user's roles allow, and every request is counted per key. `excerpt(length:)` keeps the text an agent puts in its
prompt short. An agent that only answers questions never needs mutations; if one must write (drafts for an editor to
review), give it `graphql:write` and let it send `publish: false`, so a person publishes.

## 5. Live updates: a webhook for the shop, a live list in the browser

Two listeners on the same changes: a shop server that refreshes its product teasers whenever an article is published,
changed or trashed, and a page that shows new articles the moment an editor publishes them, without reloading.

**The webhook.** One command makes the end point and its signing secret (shown once; give it to the shop):

```bash
./console exp:graphql:webhooks --add=shop --url=https://shop.example.com/hooks/exponential \
    --events=publish,update,trash --classes=ng_article --siteaccess=site --apply
./console exp:graphql:webhooks --test=shop --apply
```

The shop checks every request before it acts on it (the signature over the timestamp and the raw body, and a
timestamp at most five minutes old), answers `204` quickly, and does the work afterwards; an answer that is not 2xx
is sent again later, so the same delivery id can arrive twice:

```php
<?php
$body = file_get_contents( 'php://input' );
$ts = $_SERVER['HTTP_X_EXPONENTIAL_TIMESTAMP'] ?? '';
$expected = 'sha256=' . hash_hmac( 'sha256', $ts . '.' . $body, getenv( 'EXPONENTIAL_WEBHOOK_SECRET' ) );
if ( !ctype_digit( $ts ) || abs( time() - (int)$ts ) > 300
     || !hash_equals( $expected, $_SERVER['HTTP_X_EXPONENTIAL_SIGNATURE'] ?? '' ) )
{
    http_response_code( 401 );
    exit;
}
$event = json_decode( $body, true )['data']['contentEvent'];
if ( !already_seen( $_SERVER['HTTP_X_EXPONENTIAL_DELIVERY'] ) )   // your own store of delivery ids
    queue_refresh( $event['objectId'], $event['event'] );            // trash: drop the teaser
http_response_code( 204 );
```

The shop needs the article's fields too? Let the webhook send the answer of a persisted query instead of the bare
event: store the query once, and point the end point at it.

```graphql
# article-for-shop.graphql
query ($objectId: Int) { content(id: $objectId) { name url ... on ng_article { teaser_intro { text } } } }
```

```bash
./console exp:graphql:persist article-for-shop.graphql --name=article-for-shop --apply
./console exp:graphql:webhooks --add=shop --url=https://shop.example.com/hooks/exponential \
    --events=publish,update --classes=ng_article --payload=query --query=article-for-shop --apply
```

The body's `data` is then the query's answer, read as the anonymous user (or `--as=<login>`), and the event is in
`extensions.event`.

**The live list.** The page subscribes over SSE (any server) with the small `graphql-sse` client, signed in with a
read-only key; on Velocity the same subscription works over a WebSocket with `graphql-ws`:

```js
import { createClient } from 'graphql-sse';

const client = createClient({ url: 'https://example.com/api/graphql/subscribe',
                              headers: { Authorization: 'Bearer ' + READ_ONLY_KEY } });
const query = `subscription {
  contentPublished(classes: ["ng_article"], parentId: 556) {
    event
    content { name url ... on ng_article { teaser_intro { text } } }
  }
}`;
for await (const { data } of client.iterate({ query })) {
  const item = data.contentPublished;
  if (item.event === 'publish' && item.content) prependTeaser(item.content);   // your rendering
}
```

```text
event: next
id: 4.182211
data: {"data":{"contentPublished":{"event":"publish","content":{"name":"Ten minutes of mobility","url":"/Fit-Healthy/Ten-minutes-of-mobility","teaser_intro":{"text":"..."}}}}}
```

**Settings.** The key's user must be allowed to read the articles: an event the user may not read never arrives.
For a public page without a key, switch on `AnonymousSubscriptions=enabled` on the siteaccess and keep
`MaxSubscriptionsPerAddress` low; every open page holds one PHP process (or Velocity worker), so size
`MaxSubscriptions` to what the server leaves for pages. The stream ends every 280 seconds and the client reopens it
where it stopped, so a proxy in front must allow a request to stay open that long and must not buffer it.

## Related pages

- [A front end on the GraphQL API, step by step](graphql-walkthrough.md)
- [The GraphQL API](../features/6.0/graphql.md): queries, mutations, settings, errors
