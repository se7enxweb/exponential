# Proposal

Redmine #16715 (https://pm.team.in-mv.com/issues/16715). Written after the implementation; it records the
decisions taken while building it. Meant for upstream (se7enxweb): pull requests se7enxweb/exponential#349 and,
for the eZ Find package, se7enxweb/ezfind#17.

## Why

Finding something in the administration took a full search and a page change for every attempt, and the advanced
search was a separate form with one class and one section at a time. Editors know the search of current content
platforms (Ibexa AdminUI 6.0 as the reference): suggestions while typing, and one results page whose filters narrow
the result without starting over.

## What Changes

- Suggestions under the header search field of the administration while the user types: content, the pages of PDF
  documents that hold the words (where those pages are indexed), user accounts, content classes and the
  administration's own pages, in groups, each only as far as the user may see it, with a way to all results.
- The search results page gets a filter bar: content classes, a class attribute, sections, modification and
  publication periods, owner, language, subtree, search mode and page size; active filters as removable chips;
  sorting; a result table with type, modification date, class, translations and an edit action.
- The advanced search becomes part of that page. Its old addresses keep working and show the same search.
- The search works with the built-in search engine, with the eZ Find package as published upstream (3.x) and with
  an eZ Find that has more extended filters; what an engine cannot do is not offered.
- A search runs once per page: the page searches with its filters, and the search statistics count what it showed.

## Capabilities

### New Capabilities
- `admin/search-suggestions`: the suggestions under the administration's header search field while typing.
- `admin/search-page`: the administration's search results page with its filter bar, sorting and result table,
  including the former advanced search.

### Modified Capabilities

## Impact

- Kernel: a new view `content/suggest` and a new fetch function `content/filtered_search`, two new classes, the
  views `content/search` and `content/advancedsearch` (statistics and the skipped second search), settings in
  `admininterface.ini`, `site.ini` (a cache item) and `design.ini` (a stylesheet).
- Designs admin and admin4 (identical templates, scripts and styles for the search); translations ger-DE, with the
  source strings in eng-US and untranslated.
- The eZ Find package: its own `design/admin` templates for the header search and the search page hand over to the
  kernel's when the kernel has the new settings (se7enxweb/ezfind#17).
- Out of scope: a content browser dialog for the subtree filter, suggestions while typing in the owner field,
  facet counts for the classes not chosen (needs Solr tag/exclude, not offered by the package), the frontend
  designs' search pages.
