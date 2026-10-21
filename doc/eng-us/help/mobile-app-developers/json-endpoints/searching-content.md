---
title: Searching content
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 320
summary: Add search to an app through the expservices search domain, since the REST API has no search route - expsearch::search with text, limit, offset and a JSON filter (class, section, subtree, date), searching below a node or within classes, autocomplete with suggest, the answer and its limits.
keywords: search, expsearch, search service, filter, class, subtree, section, date, inSubtree, byClass, suggest, autocomplete, stop words, search engine, Solr, eZ Find, mobile app
type: help
---
# Searching content

This page is for app developers who add a search field to an app. The REST content API has no search route, so search
goes through the remote services' `expsearch` domain. The services are declared in
`extension/expservices/classes/content/expsearchservices.php`.

## Contents

1. [The main call](#the-main-call)
2. [Filters](#filters)
3. [Narrower searches](#narrower-searches)
4. [Autocomplete](#autocomplete)
5. [The answer](#the-answer)
6. [Limits and good practice](#limits-and-good-practice)

## The main call

```text
expsearch::search::<text>::<limit>::<offset>::<filter json>
```

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expsearch::search::solar%20panels::20::0?ContentType=json'
```

Percent-encode the text: spaces, and above all `:`, which would otherwise split arguments. The service needs
`content/read`, and every hit is checked with `canRead()` before it is returned, so the results follow the user's
rights.

## Filters

The fourth argument is a JSON object, percent-encoded:

| Key | Value | Effect |
|---|---|---|
| `class` | a list of class identifiers | only these classes |
| `section` | a section id | only this section |
| `subtree` | a list of node ids | only below these nodes |
| `date` | `day`, `week`, `month`, `3months` or `year` | only content published in that period |
| `attribute` | a class attribute id | only matches in that field |

```text
expsearch::search::solar::20::0::{"class":["article"],"subtree":[<node id>]}
```

(Shown unencoded for reading; encode the JSON in the URL.)

## Narrower searches

| Service | Arguments |
|---|---|
| `expsearch::inSubtree` | text, node id, limit, offset |
| `expsearch::byClass` | text, classes (a list), limit, offset |
| `expsearch::bySection` | text, section id, limit, offset |
| `expsearch::byAttribute` | text, class attribute id, limit, offset |

Use `inSubtree` to keep an app's search inside the part of the tree the app shows.

## Autocomplete

```text
expsearch::suggest::<prefix>::<limit>
```

Returns words of the search index that start with the prefix, most used first. Call it after a short pause in
typing, not on every keystroke.

## The answer

`data` is a list of nodes in the expservices node format (`node_id`, `object_id`, `class`, `name`, `url_alias`,
`published`, `modified` and more, from `expContentBase::exportNode()`). `meta` has the paging keys (`total`, `offset`,
`limit`, `count`, `has_more`) plus `stop_words` (words the engine ignored) and `text` (the searched text). To show a
hit, open it by `node_id` with the REST API ([Reading a node](../rest-api/reading-a-node.md)).

## Limits and good practice

- The text must not be empty and may be at most 250 characters; otherwise `400`.
- If search is disabled on the installation, the service answers `403` "Search is disabled".
- The quality of results depends on the search engine the installation uses: the built-in engine or Solr through
  eZ Find ([Search with eZ Find and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)). Ask which one
  yours runs (`expsearch::engine`, for signed-in users).
- New content is found once it is indexed; with a delayed indexing setup that can take until the next cron run.
- Show `stop_words` to the user when a search finds nothing because the words were ignored.

## Related pages

- [Calling expservices from an app](calling-expservices-from-an-app.md)
- [Site search](../../creators/website-creators/site-search.md)
- [Backend services over ezjscore (reference)](../../../bc/6.0/backend_ezjscore_services.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
