# Change ledger: ezwebin

Every change made to `ezwebin` since the se7enxweb era began, oldest first: 33 changes touching 158 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 28 |
| Other | 2 |
| Removed | 2 |
| Added | 1 |

## 2023-12 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-24 | `0360755` | Other | Create FUNDING.yml | 1 | +3 / −0 |  |

## 2024-01 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `1b58ead` | Updated | Updated: Updated github funding information | 1 | +1 / −1 | v6.0.0 |
| 2024-01-28 | `09c63ec` | Updated | Updated: Mass updates from parent repository ezwebin-ezpackage | 2 | +23 / −24 | v6.0.1 |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `854f023` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-23 | `f215980` | Updated | Fix path check and page depth calculation | 1 | +2 / −2 | v6.0.2 |

## 2026-07 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-18 | `f2725ca` | Removed | Remove XHTML self-closing slashes and obsolete type attributes | 57 | +1066 / −1067 | v6.0.3 |

## 2026-09 (18 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-27 | `276cc5b` | Updated | Updated: Visible texts and their translations name Exponential | 26 | +265 / −265 |  |
| 2026-09-27 | `f35962f` | Added | Added: ezinfo.php reports the extension's name, version, copyright and license | 1 | +25 / −0 |  |
| 2026-09-27 | `d6cfb6c` | Updated | Updated: Version 6.0.4 | 1 | +1 / −1 | v6.0.4 |
| 2026-09-27 | `76a541c` | Updated | Updated: extension.xml and ezinfo.php name the license in full, GNU General Public License v2.0 (or any later version) | 2 | +3 / −3 |  |
| 2026-09-27 | `7fe3682` | Updated | Updated: Version 6.0.5 | 2 | +3 / −3 | v6.0.5 |
| 2026-09-27 | `2771674` | Updated | Updated: The extension states its version, license and website | 2 | +4 / −3 | v6.0.6 |
| 2026-09-27 | `eede2b5` | Updated | Fixed: The forgot password page no longer tells whether an address has an account and escapes what it prints | 1 | +3 / −3 |  |
| 2026-09-27 | `1f404a3` | Updated | Updated: Version 6.0.7 | 2 | +3 / −3 | v6.0.7 |
| 2026-09-27 | `55c66ea` | Updated | Fixed: Error pages show their own error in the page title, because the page layout keys its per-URI caches by error type and number as well | 1 | +9 / −3 |  |
| 2026-09-27 | `503c6a7` | Updated | Updated: Version 6.0.8 | 2 | +2 / −2 | v6.0.8 |
| 2026-09-28 | `66df8ca` | Updated | Updated: Every visible text of the extension is a translation string, with German | 19 | +3453 / −137 |  |
| 2026-09-28 | `13c8108` | Updated | Updated: Version 6.0.9 | 2 | +3 / −3 | v6.0.9 |
| 2026-09-29 | `19d949b` | Updated | Updated: The German translation covers the RSS export, import and list pages, the order confirmation summary and totals, the blog post tags and the document import message | 3 | +247 / −27 |  |
| 2026-09-30 | `ac7c10b` | Updated | Updated: Version 6.0.10 | 2 | +3 / −3 | v6.0.10 |
| 2026-09-30 | `a1ad269` | Updated | Updated: The description calls the product Exponential | 1 | +1 / −1 |  |
| 2026-09-30 | `26e1651` | Updated | Updated: Version 6.0.11 | 2 | +3 / −3 | v6.0.11 |
| 2026-09-30 | `c41f506` | Updated | Fixed: The blog archive operator lists the months on Oracle too | 1 | +23 / −0 |  |
| 2026-09-30 | `f6f7ea4` | Updated | Updated: Version 6.0.12 | 2 | +3 / −3 | v6.0.12 |

## 2026-10 (9 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `00d0663` | Updated | Updated: Updated the edit page's collapsible attribute groups for jQuery 4, so that they open and close with .on() instead of the deprecated click shorthand. | 1 | +1 / −1 |  |
| 2026-10-01 | `7dbb72a` | Updated | Updated: Version 6.0.13 | 2 | +2 / −2 | v6.0.13 |
| 2026-10-01 | `af489c3` | Updated | Updated: The date and date/time fields of the ezwebin design use Exponential UI's calendar, exp::datepicker, when expui is active and load YUI's calendar only without it, so that editing on the site runs on jQuery 4. | 2 | +16 / −0 |  |
| 2026-10-01 | `7962d1e` | Updated | Updated: Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback. | 1 | +2 / −1 |  |
| 2026-10-01 | `0818261` | Updated | Updated: Version 6.0.14 | 2 | +2 / −2 | v6.0.14 |
| 2026-10-02 | `d081367` | Removed | Removed: YUI from the ezwebin design; its date fields use Exponential UI's calendar | 5 | +6 / −63 | v6.0.15 |
| 2026-10-02 | `b55459e` | Updated | Updated: The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices | 7 | +8 / −1 |  |
| 2026-10-02 | `11964d8` | Updated | Updated: The about page names 1998 - 2026 7x & Exponential Foundation first in its copyright notice, followed by eZ Systems AS | 1 | +1 / −1 |  |
| 2026-10-02 | `c052c26` | Updated | Updated: Version 6.0.16 | 2 | +2 / −2 | v6.0.16 origin/master origin/HEAD |
