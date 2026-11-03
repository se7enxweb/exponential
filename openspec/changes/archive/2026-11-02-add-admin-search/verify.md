# Verification Report

**Change:** `add-admin-search`
**Verified at:** 2026-11-02 21:40
**Commit range:** `21e33483ec..25e388e0f6` on main (search commits f39afdd4cb, 0eb21b07bf, ad0f6623ab, 25e388e0f6);
upstream branch `upstream/main..49997b82d3` (se7enxweb/exponential#349)

Written after the implementation; the change is archived right after this report.

## 1. Structural validation

- [x] `openspec validate add-admin-search --strict` passes

## 2. Task completion

- [x] Every tasks.md checkbox is done

| Task | Reason open | Blocks archive |
|---|---|---|
| — | — | — |

## 3. Scenario coverage

| Requirement | Scenario | Test | Result |
|---|---|---|---|
| Suggestions while typing | Suggestions appear after three characters; Fewer characters show nothing | `AdminSearchSuggestTest` (node harness `js/search_suggest.js`) | pass |
| Suggestions while typing | Switched off | `expSearchSuggestTest` (settings, view registration) | pass |
| Groups | Content and a class | `expSearchSuggestTest::assemble`, matching tests | pass |
| Highlighting and all results | Highlighted words; All results | `AdminSearchSuggestTest` harness | pass |
| Only what the user may see | Content outside the user's permissions; No class module access; Anonymous request | `expSearchSuggestTest` (`readable()`, view 403); live check with an administrator only | pass, restricted user not run in a browser |
| Document pages with fallback | Pages indexed; Pages not indexed | `expSearchSuggestTest` backend matrix | pass |
| Search engine fallback | Without eZ Find | `expSearchSuggestTest` backend matrix | pass |
| Keyboard and screen readers | Choose with the keyboard; Close with Escape | `AdminSearchSuggestTest` harness | pass |
| Filter bar, Further filters | Narrow by class; Attribute only for one class; Last week | `expContentSearchFiltersTest` SF-01–SF-06 | pass |
| Filter form | Without scripts; Apply only after a change | `AdminSearchTest`, harness `js/content_search_filters.js` | pass |
| Only what the engine can do | Built-in engine; Filters without words, empty search not allowed | SF-03, SF-10 | pass |
| Search modes | Any word keeps an exclusion | SF-07 | pass |
| Active filters and sorting | Remove one filter; Next page | SF-08, SF-09 | pass |
| Result table, Page states | Translations; Nothing found | `AdminSearchTest`, browser check on exponential_admin | pass |
| Names only where readable | Subtree outside the user's permissions; Owner by e-mail address | code review; no DB-less test (look-ups need content) | not covered by a test |
| Default subtree | Restricted siteaccess | SF-11 | pass |
| Advanced search in the page | Old address | SF-02, browser: `content/advancedsearch?SearchText=exponential&SearchContentClassID=folder` | pass |
| One search per page | Statistics count the filtered result | SF-12, SF-13 | pass |
| Same page with the eZ Find package | Classic design with eZ Find | `eztemplatecheck.php` and a CLI render of ezfind's `page_search.tpl` | pass |

## 4. Project checks

| Check (suite, fitness test, linter) | Command or CI job | Result |
|---|---|---|
| Unit tests suggestions | `vendor/bin/phpunit --no-coverage tests/tests/kernel/classes/expSearchSuggestTest.php` | OK, 14 tests, 72 assertions |
| Unit tests search filters | `vendor/bin/phpunit --no-coverage tests/tests/kernel/classes/expContentSearchFiltersTest.php` | OK, 14 tests, 153 assertions |
| Search design tests | `vendor/bin/phpunit --no-coverage tests/tests/design/AdminSearchSuggestTest.php tests/tests/design/AdminSearchTest.php` | OK, 24 tests, 473 assertions |
| Search request | `vendor/bin/phpunit --no-coverage tests/tests/kernel/private/classes/ContentSearchRequestTest.php` | OK, 8 tests, 70 assertions |
| Design suite | `vendor/bin/phpunit --no-coverage --testsuite design` (cold INI cache) | OK, 1058 tests |
| Root suite kernel-classes | `vendor/bin/phpunit --no-coverage --testsuite kernel-classes` | not measured: the run hangs after about 50 tests (known since the upstream merge of 31.10., also with `--exclude-filter testEachFormat`) |
| eZ Find templates | `php bin/php/eztemplatecheck.php` on both handed-over templates | valid |
| Upstream branch | the same unit tests and the four search design test classes in the upstream worktree | OK, 27 + 50 tests |

## 5. Platform matrix

| Entry | CI job or run | Result |
|---|---|---|
| Built-in search engine | unit tests (backend matrix, kernel parameters) | pass; no live installation without eZ Find |
| eZ Find (fork, with extended filters and PDF pages) | browser on exponential_admin, CLI | pass |
| eZ Find package 3.0.15 (upstream) | parameters checked against its source; unit tests with the package's filter set | pass |
| MySQL/MariaDB utf8mb4 | user search with `ESCAPE '!'` on MariaDB | pass; SQLite, PostgreSQL, Oracle not run |

## 6. Commit messages

- [x] No tool attribution in `f39afdd4cb^..HEAD` for the touched paths

## 7. Stray workflow output

- [x] `docs/superpowers/` absent, `.superpowers/` untracked

## 8. Working tree

- [x] The search files are committed; other sessions' uncommitted changes in the shared tree are not part of this change

## Overall decision

- [ ] PASS
- [x] PASS WITH WARNINGS: kernel-classes not measured (hangs independently of this change); the read checks for subtree and owner names have no automated test; a restricted user and a live installation without eZ Find were not tried in a browser
- [ ] FAIL
