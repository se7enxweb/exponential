---
title: Finding services
audience: government, website-creator, project-manager
category: public-sector
order: 50
summary: How people find services on an Exponential site - task-based menus, an A to Z list made with a fetch, the built-in search or eZ Find, search statistics to find what people miss, and titles in the words people use.
keywords: findability, navigation, A to Z, service list, search, eZ Find, Solr, elevation, search statistics, LogSearchStats, synonyms, titles
type: help
---
# Finding services

This page is for website creators and project managers who make services findable. People arrive at a service page
through a search engine, the site's menus, an A to Z list, a topic or life event page, or the site's own search. Each
way needs a little care; this page lists what Exponential offers for each.

## Contents

1. [Menus](#menus)
2. [An A to Z list](#an-a-to-z-list)
3. [Site search](#site-search)
4. [Learning from searches](#learning-from-searches)
5. [Titles and summaries](#titles-and-summaries)

## Menus

Menus are made from the content tree: folders under the root become menu entries
([Menus and navigation](../../../creators/website-creators/menus-and-navigation.md)). For services:

- name top folders after tasks and topics people recognize, not departments;
- give a service a second location in another folder rather than a copy when it belongs in two places;
- keep the tree shallow; a service three clicks from the front page is easier to find than one six clicks away.

## An A to Z list

A template fetch lists every service alphabetically; group by first letter in the template.

```text
{def $services = fetch( 'content', 'tree', hash(
        'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'service' ),
        'sort_by', array( 'name', true() ) ) )}
<ul>
{foreach $services as $service}
  <li><a href={$service.url_alias|ezurl}>{$service.name|wash}</a></li>
{/foreach}
</ul>
{undef $services}
```

A service with two locations appears twice in a tree fetch; add `'main_node_only', true()` to list each once. Put the
list in a cache block, because a tree fetch over the whole site is not free
([View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md)).

## Site search

| Engine | When | Read |
|---|---|---|
| Built-in search (`eZSearchEngine`) | small and medium sites; no extra server | [Site search](../../../creators/website-creators/site-search.md) |
| eZ Find with Solr | large sites, relevance ranking, facets, elevation of chosen results | [Search with eZ Find and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md) |

For a service catalog:

- mark the service name and **In short** searchable in the class (they are the strongest signals);
- with eZ Find, sort by relevance first; ezjscore ships `SortBy[published]=desc` for the editor dialogs, and
  `ezfind.ini [SearchSettings] SortBy[]` changes it ([Content model and editing](../../../../guides/content-model-and-editing.md#the-order-of-search-results-in-the-editors-dialogs));
- eZ Find's elevation lets you pin a service to the top for a search phrase (the `ezfind/elevate` view of the
  ezfind extension); use it for the top tasks.

## Learning from searches

With `LogSearchStats=enabled` on the public siteaccess, **Setup > Search statistics** (`/search/stats`) shows every
phrase, how often it was searched and the average number of results
([URLs and search statistics](../../../../guides/urls-and-aliases.md#5-search-statistics)). Phrases with no results
show missing words or missing pages:

| Phrase found nothing because | Do |
|---|---|
| People use another word | add the word to the summary, or a tag synonym |
| A misspelling | add the common misspelling as a tag synonym (not in the visible text) |
| The service is not yours | add a short page that links to who provides it |
| The service is missing | add it |

Phrases can contain personal data (people type names and numbers), which is why logging is off by default; agree how
long statistics are kept and clear them on a schedule ([Privacy standards](../../../../standards/privacy.md)).

## Titles and summaries

| Do | Because |
|---|---|
| Start the title with the thing: "Parking permits for residents" | search results and menus show the first words |
| Write the summary for the search result snippet | search engines and the site search show it |
| Fill the meta description with xrowmetadata where the summary is not used | [xrowmetadata](../../../../features/6.0/extensions/xrowmetadata.md) |
| Keep URL aliases short and stable | the alias comes from the name; renaming keeps a redirect ([Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)) |

## Related pages

- [Building a service catalog](README.md)
- [Life events and topics](life-events-and-topics.md)
- [SEO basics](../../../creators/website-creators/seo-basics.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
