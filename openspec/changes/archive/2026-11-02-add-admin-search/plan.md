# add-admin-search Implementation Plan

**Goal:** Suggestions while typing under the administration's header search and a search page with a filter bar
that takes in the advanced search, for every search engine the installation may have.

**Spec:** `specs/` and `design.md` in this change directory; they win over this plan.

Written after the implementation (02.11.2026); every step below was done. Test runs: see `verify.md`.

## Global Constraints

- admin and admin4 carry identical search templates, scripts and the stylesheet `modules/search.css`.
- No inline script or style, no jQuery or YUI; server text into the DOM only as text.
- Only the upstream eZ Find 3.x API; extended filters only when registered and present.
- Other designs keep the views' behavior; the views change only for a template that is the filtered page.
- English comments without ticket numbers (upstream); tests in the root suites `design` and `kernel-classes`.

---

## Task 1.1: expSearchSuggest

**Files:** `kernel/classes/expsearchsuggest.php`, `tests/tests/kernel/classes/expSearchSuggestTest.php`

**Scenarios:** Groups; Only what the user may see; Document pages with fallback; Search engine fallback

- [x] **Step 1:** Write the failing test (settings, backend matrix, wildcard, matching, `readable()`, menu link extraction, `assemble()`)
- [x] **Step 2:** Run it and confirm it fails
- [x] **Step 3:** Implement the class
- [x] **Step 4:** `vendor/bin/phpunit --no-coverage tests/tests/kernel/classes/expSearchSuggestTest.php` passes
- [x] **Step 5:** Commit

## Task 1.2: the view content/suggest

**Files:** `kernel/content/suggest.php`, `kernel/private/classes/views/content/suggest.php`, `kernel/content/module.php`, `autoload/ezp_kernel.php`

**Scenarios:** Switched off; Anonymous request

- [x] **Step 1–5:** view registration test in `expSearchSuggestTest`, implementation, run, commit

## Task 1.3: settings

**Files:** `settings/admininterface.ini`, `settings/site.ini`

- [x] **Step 1–5:** settings test in `expSearchSuggestTest`, settings, run, commit

## Task 1.4: header field

**Files:** `design/{admin,admin4}/templates/page_search.tpl`, `design/{admin,admin4}/templates/parts/search_suggest.tpl`, `design/{admin,admin4}/javascript/page/search_suggest.js`, `tests/tests/design/AdminSearchSuggestTest.php`, `tests/tests/design/js/search_suggest.js`, `tests/tests/design/AdminHeaderTest.php`

**Scenarios:** Suggestions appear after three characters; Fewer characters show nothing; All results; Choose with the keyboard; Close with Escape

- [x] **Step 1–5:** node harness scenarios (keyboard, abort, error, HTML instead of JSON, Escape, click outside), implementation, `--testsuite design`, commit

## Task 1.5: suggestions styles

**Files:** `design/{admin,admin4}/stylesheets/modules/search.css`, `settings/design.ini`

- [x] **Step 1–5:** CSS rule tests of the design suite, styles, run, commit

## Task 1.6: load and portability

**Files:** `kernel/classes/expsearchsuggest.php`

- [x] **Step 1–5:** page look-ups capped at `Limits[pages]`, `ESCAPE '!'`, run `expSearchSuggestTest`, commit

## Task 2.1: expContentSearchFilters

**Files:** `kernel/classes/expcontentsearchfilters.php`, `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`, `tests/tests/kernel/classes/expContentSearchFiltersTest.php`

**Scenarios:** Narrow by class; Attribute only for one class; Built-in engine; Remove one filter; Next page; Old address

- [x] **Step 1–5:** tests SF-01 to SF-10, implementation, run, commit

## Task 2.2: the page templates

**Files:** `design/{admin,admin4}/templates/content/{search,search_page,searchresult,advancedsearch}.tpl`, `design/{admin,admin4}/javascript/page/content_search_filters.js`, `design/admin/javascript/page/content_searchresult.js`

**Scenarios:** Without scripts; Translations; Old address

- [x] **Step 1–5:** `AdminSearchTest`, templates, `--testsuite design`, commit

## Task 2.3: results styles

**Files:** `design/{admin,admin4}/stylesheets/modules/search.css`

- [x] **Step 1–5:** design suite, styles, run, commit

## Task 2.4: review fixes

**Files:** `kernel/classes/expcontentsearchfilters.php`, `tests/tests/kernel/classes/expContentSearchFiltersTest.php`

**Scenarios:** Subtree outside the user's permissions; Owner by e-mail address; Restricted siteaccess; Filters without words, empty search not allowed; Any word keeps an exclusion

- [x] **Step 1–5:** tests SF-07, SF-10, SF-11, implementation, run, commit

## Task 2.5: one search per page

**Files:** `kernel/private/classes/views/content/{search,advancedsearch}.php`

**Scenarios:** Statistics count the filtered result

- [x] **Step 1–5:** tests SF-12, SF-13, implementation, run `expContentSearchFiltersTest` and `ContentSearchRequestTest`, commit

## Task 2.6: design tests

**Files:** `tests/tests/design/{AdminSearchTest,AdminHeaderTest,AdminResponsiveTest}.php`, `tests/tests/design/js/content_search_filters.js`

- [x] **Step 1–5:** tests, run `--testsuite design`, commit

## Task 3.1: translations

**Files:** `share/translations/{ger-DE,eng-US,untranslated}/translation.ts`

- [x] **Step 1–5:** `TranslationFilesValidTest` and the design suite's catalogue checks, run, commit

## Task 3.2: documentation

**Files:** `doc/eng-us/help/searching.md`, `doc/eng-us/help/keyboard-shortcuts.md`, `doc/changelogs/6.0/6.0.15.md`

- [x] **Step 1–5:** written, reviewed, commit

## Task 3.3: eZ Find hands over

**Files (se7enxweb/ezfind):** `design/admin/templates/page_search.tpl`, `design/admin/templates/content/search.tpl`

**Scenarios:** Classic design with eZ Find

- [x] **Step 1–5:** `bin/php/eztemplatecheck.php` on both, a CLI render of `page_search.tpl` showing the kernel's combobox, commit, pull request #17

## Task 3.4: pull request and ticket

- [x] **Step 1–5:** se7enxweb/exponential#349, Redmine #16715 (status Fertig with the pull request links)
