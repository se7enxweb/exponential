# GraphQL recipes: a blog, a mobile app, a static build, an AI agent

Four common front ends on the [GraphQL API](../features/6.0/graphql.md), each with the queries that do the work and
the settings that go with them. The queries were run against the demo content of the media site (Fit & Healthy) of
6.0.15; the answers are shortened with `...`. Switch the API on first: [step by step](graphql-walkthrough.md), or
`./console exp:graphql:enable --apply`.

The class and field names below are those of the demo (`ng_article`, `teaser_intro`, `body`): with the default naming
the schema uses your admin's identifiers, so read yours from `./console exp:graphql:schema --format=summary`.

## Contents

1. [A blog or news front end](#1-a-blog-or-news-front-end)
2. [A mobile app reading the menu and articles](#2-a-mobile-app-reading-the-menu-and-articles)
3. [A static build](#3-a-static-build)
4. [An AI agent reading content through GraphQL](#4-an-ai-agent-reading-content-through-graphql)

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

## Related pages

- [A front end on the GraphQL API, step by step](graphql-walkthrough.md)
- [The GraphQL API](../features/6.0/graphql.md): queries, mutations, settings, errors
