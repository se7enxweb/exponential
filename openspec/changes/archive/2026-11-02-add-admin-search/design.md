# Design

## Context

Before this change the header search of admin and admin4 was a plain field with a scope popup, and
`content/search` drew its results with either `fetch( 'ezfind', 'search' )` (when `site.ini [SearchSettings]
SearchEngine=ezsolr`, `SearchViewHandling=template`) or the view's own `eZSearch::search()` (the kernel default
`SearchViewHandling=default`). `content/advancedsearch` was a separate form. The eZ Find package (se7enxweb/ezfind
3.x) ships its own `design/admin/templates/page_search.tpl` and `content/search.tpl`, read before the kernel's
`design/admin` templates. Our own copy of eZ Find carries more extended attribute filters (`ezfindcollapse`) and a
PDF page index (extension `ezfindpdfdeepsearch`, fetch `pdfdeepsearch/page_hits`); upstream has neither.

## Goals / Non-Goals

**Goals:**
- Suggestions and a filtered search page in admin and admin4, the same templates, scripts and styles in both.
- One code path per search engine with no feature faked: built-in engine, upstream eZ Find 3.x, eZ Find with more filters.
- No change for other designs (admin3, exp_adminui, frontends): the views keep their behavior for them.
- No inline script or style (the admin runs under a Content Security Policy), no jQuery or YUI.

**Non-Goals:**
- A content browser dialog for the subtree, suggestions in the owner field, facet counts for unchosen classes.
- The frontend designs' search pages.

## Decisions

**D1 Suggestions as a kernel view, not an ezjscore function.** `content/suggest/(text)/<text>`
(`kernel/content/suggest.php`, `kernel/private/classes/views/content/suggest.php`) needs the policy `content/read`
like `content/search`, is a GET without side effects and answers private JSON (`Cache-Control: private, no-store`,
`Vary: Cookie`, `X-Content-Type-Options: nosniff`); 403 for anonymous users, 404 when switched off. An ezjscore
function would have needed an extra role grant (`ezjscore/call`) on every installation. The text travels as a
view parameter, encoded with `a4ViewParameter.encode` and limited by `expViewParameters::MAX_TEXT`.

**D2 One class per feature, testable without a database.** `expSearchSuggest` (`kernel/classes/expsearchsuggest.php`)
builds the groups; `expContentSearchFilters` (`kernel/classes/expcontentsearchfilters.php`) reads and checks the
filters, maps the old advanced search parameters, builds the parameters per engine, the chips, facets and query
strings. Both take their catalogue (classes, sections, languages, settings, look-ups) as input, so the logic runs
in unit tests; the database look-ups are closures built in `currentCatalog()`.

**D3 Engine selection.** `expSearchSuggest::backend()`: eZ Find only when `eZSearch::getEngine()` is an `eZSolr`
and the extension is active, the built-in engine otherwise (also when `SearchEngine=ezsolr` cannot be loaded).
PDF pages only with eZ Find, `ezfindpdfdeepsearch` active and its fetch function `page_hits` defined
(`eZFunctionHandler::moduleFunctionInfo`). The search page: `expContentSearchFilters::capabilitiesFor()` lists
what each engine offers; extended filters (`ezfindallwords`, `ezfindcollapse`) only when registered in
`ezfind.ini [ExtendedAttributeFilters] FiltersList` and their class exists.

**D4 Only the upstream eZ Find API.** Suggestions use `eZSearch::search( $text, SearchOffset, SearchLimit )`;
the page uses `fetch( 'ezfind', 'search' )` with `query, offset, limit, class_id, class_attribute_id, section_id,
subtree_array, filter, facet, sort_by, spell_check, extended_attribute_filter`, all checked against
se7enxweb/ezfind 3.0.15. Filters reach Solr only as checked values: dates as Solr dates from integers, ids as
integers, a locale from the language list, sort keys from a whitelist.

**D5 Permissions.** Content: the engine's permission handling plus `canRead()` per hit. Users: `canRead()` on the
user object. Classes: `expViewAccess::canOpen( 'class/grouplist' )`. Administration pages: the left menus of the top
menu tabs (`eZTopMenuOperator` with `filter_on_access`) rendered for the user, so `PolicyList` and view access apply;
cached per user, roles, limitations, siteaccess, locale and host in the cache item `searchsuggest`
(`site.ini [Cache_searchsuggest]`, cleared with template and user caches) for `AdminPagesCacheTTL` seconds. The
header offers the field only when `fetch( 'user', 'can_open', hash( 'uri', 'content/suggest' ) )`. Subtree and owner
names in the filters only after `canRead()`.

**D6 The page lives in `content/search_page.tpl`.** `content/search.tpl` and `content/advancedsearch.tpl` include it.
eZ Find's `design/admin/templates/content/search.tpl` shadows the kernel's for the classic design; a name it does not
override keeps old advanced search addresses working there, and se7enxweb/ezfind#17 lets the package hand over when
the kernel has `admininterface.ini [SearchSuggestSettings]` (its `page_search.tpl` includes
`file:design/admin/templates/page_search.tpl`, its `content/search.tpl` includes `design:content/search_page.tpl`).

**D7 One search per page.** The page searches in the template (`fetch( 'content', 'filtered_search' )`).
`Search::templateSearchesItself()` looks up the design's `content/search.tpl` (`content/advancedsearch.tpl`) along
the design bases; when it includes `design:content/search_page.tpl`, the views leave their own search out even with
`SearchViewHandling=default`. The template leaves its result in `search_data`; the views log the statistics from it.

**D8 Search modes.** "All words" is the default and passes the text on unchanged (with `ezfindallwords` where it is
registered; upstream eZ Find's minimum match asks for all words of a short query). "Any word" joins the words with
OR and keeps `NOT word` and `-word`. "Phrase" quotes the text. The built-in engine offers all words and phrase.

**D9 Settings.** `admininterface.ini [SearchSuggestSettings]`: `Enabled`, `MinLength`, `Groups[]`, `Limits[]`,
`PagesPerDocument`, `PageHitClasses[]`, `Wildcard` (auto/enabled/disabled), `AdminPagesCacheTTL`,
`AdminMenuSkipParts[]`. `design.ini [StylesheetSettings] AdminComponentCSSFileList[]=modules/search.css`.
`site.ini [Cache_searchsuggest]`. The page reads `site.ini [SearchSettings]` (`AllowEmptySearch`,
`MaximumSearchLimit`, the search text limits, `SiteAccessSettings PathPrefix`).

**D10 Front end.** `javascript/page/search_suggest.js` (combobox, 250 ms pause, AbortController, server text only
through `textContent`, `<mark>` built as DOM nodes, URLs through `a4SafeURL`) and
`javascript/page/content_search_filters.js` (apply enabled on change, reset, auto-submit of the sort), both loaded
with `ezscript`; `modules/search.css` with the blocks "Suggestions" and "Results", only `--a4-*` tokens, forced
colors. design/admin and design/admin4 carry identical copies.

## Risks / Trade-offs

- `templateSearchesItself()` resolves the template along the design bases without override rules; an
  `override.ini` rule for `content/search.tpl` that points elsewhere would not be seen (the view would search once
  more, as before).
- Facet counts show the numbers of the current search; once a class is chosen the other classes have no number
  (tag/exclude is not available in the package).
- With an eZ Find package that does not hand over yet, the classic design admin keeps eZ Find's own header field
  and search page (#17); old advanced search addresses still show the new page.
- Prefix matching while typing ("Oste" finds "Ostern") only with `EnableWildcard=true` or `Wildcard=enabled`.
- PDF page suggestions cost up to `Limits[pages]` extra requests per suggestion request.
