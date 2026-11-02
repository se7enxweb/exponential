# Tasks

## 1. Suggestions while typing

- [x] 1.1 Add `expSearchSuggest` with settings, engine selection, groups (content, pages, users, classes, admin pages), permission checks and highlighting data; unit test `expSearchSuggestTest`
- [x] 1.2 Add the view `content/suggest` (JSON, 403 anonymous, 404 when off) and register it in `kernel/content/module.php` and `autoload/ezp_kernel.php`
- [x] 1.3 Add `admininterface.ini [SearchSuggestSettings]` and the cache item `site.ini [Cache_searchsuggest]`
- [x] 1.4 Add `parts/search_suggest.tpl` and `javascript/page/search_suggest.js` to admin and admin4, wire the combobox into `page_search.tpl` (only with `content/suggest` access), Ctrl+/ and Cmd+/; design test `AdminSearchSuggestTest` with the node harness `tests/tests/design/js/search_suggest.js`
- [x] 1.5 Add the "Suggestions" block of `modules/search.css` and register the stylesheet in `design.ini`
- [x] 1.6 Limit PDF page look-ups per request, escape `%` and `_` portably (`ESCAPE '!'`) in the user search

## 2. Search page with filters

- [x] 2.1 Add `expContentSearchFilters` (filters, capabilities per engine, advanced search mapping, parameters per engine, chips, facets, query strings) and the fetch function `content/filtered_search`; unit test `expContentSearchFiltersTest`
- [x] 2.2 Move the page into `content/search_page.tpl`; `content/search.tpl` and `content/advancedsearch.tpl` include it; `searchresult.tpl` with the new columns; `javascript/page/content_search_filters.js`; admin and admin4 identical
- [x] 2.3 Add the "Results" block of `modules/search.css`
- [x] 2.4 Read checks for subtree and owner names, the `PathPrefix` tree as default subtree, empty search only with `AllowEmptySearch`, search modes that keep exclusions
- [x] 2.5 Views `content/search` and `content/advancedsearch`: statistics from the template's `search_data`, no own search when the template is the filtered page (`Search::templateSearchesItself()`)
- [x] 2.6 Design tests `AdminSearchTest`, `AdminHeaderTest`, `AdminResponsiveTest`; node harness `tests/tests/design/js/content_search_filters.js`

## 3. Translations, documentation, eZ Find

- [x] 3.1 Translations ger-DE for the contexts `design/admin/search/suggest` and `design/admin/content/search`, source strings in eng-US and untranslated
- [x] 3.2 `doc/eng-us/help/searching.md` ("Suggestions while typing", "The search page and its filters"), `doc/eng-us/help/keyboard-shortcuts.md`, changelog 6.0.15
- [x] 3.3 eZ Find package: `design/admin` `page_search.tpl` and `content/search.tpl` hand over to the kernel's when `[SearchSuggestSettings]` exists (se7enxweb/ezfind#17)
- [x] 3.4 Pull request se7enxweb/exponential#349, Redmine #16715
