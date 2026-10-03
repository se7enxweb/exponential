# Change ledger: syndication

Every change made to `syndication` since the se7enxweb era began, oldest first: 19 changes touching 230 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 16 |
| Added | 2 |
| Other | 1 |

## 2025-09 (9 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-13 | `cc6b2a7` | Added | Added: Initial patched import of mostly working extension ported from php5 to php8. Requires some kernel changes atm. Inital Import. | 85 | +9167 / −0 |  |
| 2025-09-13 | `6942894` | Updated | Updated: Bugfixes for php8 support. Bugfixes. | 9 | +10 / −10 |  |
| 2025-09-13 | `c401648` | Updated | Updated: Mass commit of debug statement removal with appologies to others affected. These changes represent a working upgraded to php8.3 syndication extension that has been tested and proven to once again work as designed with minor issues (feature click specific rot in admin views). Stable Changeset. Bugfixes + Code Standards. | 9 | +26 / −29 |  |
| 2025-09-13 | `fd374b3` | Updated | Updated: Bugfix to remove testing die statement. Bugfix. | 1 | +1 / −3 |  |
| 2025-09-13 | `2757d0a` | Updated | Update composer.json updated description | 1 | +1 / −1 |  |
| 2025-09-13 | `018b318` | Updated | Update ezinfo.php version bump for upgraded and tested solution | 1 | +3 / −2 | v1.1.0 |
| 2025-09-18 | `6a80394` | Updated | Updated: Massive rewrite of extension documentation. Reorganization of file placement. Switched to markdown for docs. Doc. | 7 | +103 / −11 |  |
| 2025-09-18 | `c0a4b13` | Updated | Updated: Expanded install doc to mention kernel patch provided and suggest a new feature for the extension to provide in the future. Doc | 1 | +3 / −1 |  |
| 2025-09-18 | `c325218` | Updated | Updated: Branding change. Doc. | 1 | +1 / −1 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `96e2b3e` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-07 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-19 | `cb365d3` | Updated | Updated: Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. | 67 | +88 / −30 |  |
| 2026-07-19 | `d6bfbd6` | Updated | Updated: Version Bump | 1 | +1 / −1 |  |
| 2026-07-19 | `42ff4f3` | Updated | Updated: Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. | 1 | +37 / −0 | v1.2.0 |

## 2026-09 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-22 | `ee6e4c3` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +4 / −0 | v1.3.0 |

## 2026-10 (5 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-02 | `58dbf3c` | Added | Added: Added share/db_schema.dba, the syndication tables in the engine-neutral schema format, so that an installer can create them on every database Exponential supports and not only on MySQL. | 1 | +796 / −0 |  |
| 2026-10-02 | `687bdae` | Updated | Updated: Version 1.3.1, with ezinfo.php's keys in the form the about page reads and an extension.xml carrying the version, license and website. | 2 | +15 / −5 | v1.3.1 |
| 2026-10-02 | `9bbf346` | Updated | Updated: The commands and cronjob parts list a description of what they do | 2 | +226 / −224 |  |
| 2026-10-02 | `2593c19` | Updated | Updated: The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices | 37 | +37 / −0 |  |
| 2026-10-02 | `09c7779` | Updated | Updated: Version 1.3.2 | 2 | +2 / −2 | v1.3.2 origin/main origin/HEAD |
