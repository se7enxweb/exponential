# Change ledger: cjw_newsletter

Every change made to `cjw_newsletter` since the se7enxweb era began, oldest first: 56 changes touching 230 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 50 |
| Added | 3 |
| Other | 1 |
| Merged | 1 |
| Removed | 1 |

## 2024-01 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `c01b30d` | Added | Added: Added github funding information | 1 | +3 / −0 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `6d07799` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-08 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-13 | `d0707ea` | Updated | Fix PHP 8.5 deprecation warnings | 5 | +14 / −14 |  |
| 2026-08-13 | `ea77ce4` | Merged | Merge pull request #1 from ekked/php85-update | 0 | +0 / −0 |  |
| 2026-08-13 | `d191afc` | Added | Added: Added composer.json file and renamed README file to README.md | 2 | +26 / −0 | 4.0.0.0 |

## 2026-09 (19 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-14 | `866245b` | Updated | Fixed: Fixed the file integrity manifest, which had not been rewritten since the php 8.5 fixes and reported nine files as altered on every installation that checked them. | 3 | +18 / −14 | 3.0.1 |
| 2026-09-14 | `7dd59cf` | Updated | Fixed: Fixed the version this extension reports, which named a release line it had been moved off, and rebuilt the manifest that carries it. | 3 | +5 / −5 | 4.0.0.1 |
| 2026-09-22 | `0dde3ed` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 5 | +202 / −170 | 4.1.0 |
| 2026-09-27 | `d8a6fd6` | Updated | Fixed: The mailbox list and the subscribe view run on PHP 8 | 3 | +8 / −1 |  |
| 2026-09-27 | `d35f884` | Updated | Fixed: The version this extension reports is 4.1.1, and its manifest matches the files it ships | 3 | +13 / −13 | 4.1.1 |
| 2026-09-27 | `b73e486` | Updated | Updated: Visible texts and their translations name Exponential | 8 | +14 / −14 |  |
| 2026-09-27 | `253daf8` | Updated | Updated: Version 4.1.2 | 3 | +5 / −5 | 4.1.2 |
| 2026-09-27 | `5157569` | Updated | Updated: The extension states its version, license and website | 3 | +11 / −10 | 4.1.3 |
| 2026-09-28 | `d7b00ff` | Updated | Updated: Every visible text of the extension is a translation string, with German | 21 | +512 / −46 |  |
| 2026-09-28 | `c344404` | Updated | Updated: Version 4.1.4 | 2 | +2 / −2 | 4.1.4 |
| 2026-09-28 | `d8c408f` | Updated | Updated: Version 4.1.5, with a file manifest that matches the files again | 3 | +26 / −26 | 4.1.5 |
| 2026-09-29 | `f257277` | Updated | Updated: The top menu tab has an English tooltip with a German translation | 4 | +25 / −1 |  |
| 2026-09-29 | `6a23a30` | Updated | Updated: The file manifest carries the checksums of the menu translations | 1 | +4 / −4 |  |
| 2026-09-29 | `b6534a1` | Updated | Updated: Version 4.1.6 | 3 | +5 / −5 | 4.1.6 |
| 2026-09-30 | `89a2d7f` | Updated | Fixed: The CSV export of a subscription list runs on every database, Oracle included | 1 | +8 / −6 |  |
| 2026-09-30 | `a6e8edd` | Updated | Fixed: The newsletter user search works on every database, Oracle included | 1 | +9 / −6 |  |
| 2026-09-30 | `369094e` | Updated | Fixed: Aborting a newsletter send works on every database, Oracle included | 1 | +7 / −3 |  |
| 2026-09-30 | `94da274` | Updated | Updated: The file manifest carries the checksums of the CSV export, user search and send abort fixes | 1 | +3 / −3 |  |
| 2026-09-30 | `37ef638` | Updated | Updated: Version 4.1.7 | 3 | +5 / −5 | 4.1.7 |

## 2026-10 (32 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `0a0d760` | Updated | Updated: Updated the newsletter list and the form builder filter for jQuery 4, so that their handlers use .on() and the disabled state is set with .prop() instead of the deprecated shorthands and boolean .attr(). | 2 | +3 / −3 |  |
| 2026-10-01 | `39353f8` | Updated | Updated: The file manifest carries the checksums of the newsletter list and the form builder filter on jQuery 4. | 1 | +2 / −2 |  |
| 2026-10-01 | `2f4a328` | Updated | Updated: Version 4.1.8 | 3 | +5 / −5 | 4.1.8 |
| 2026-10-02 | `fb587b9` | Removed | Removed: The admin2 children list's YUI drag and drop, which loaded a script that no longer exists | 4 | +6 / −16 | 4.1.9 |
| 2026-10-02 | `01625ad` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 66 | +5680 / −4452 |  |
| 2026-10-02 | `fc19a0f` | Updated | Updated: The commands and cronjob parts list a description of what they do | 3 | +3 / −0 |  |
| 2026-10-02 | `23c837e` | Updated | Updated: Version 4.1.10 | 3 | +38 / −38 | 4.1.10 |
| 2026-10-02 | `de10308` | Updated | Updated: The commands start through the shared command helpers | 2 | +6 / −14 |  |
| 2026-10-02 | `6a1650b` | Updated | Updated: Version 4.1.11 | 3 | +5 / −5 | 4.1.11 |
| 2026-10-02 | `d00c1fc` | Updated | Updated: English and German translations for every string the admin showed untranslated | 2 | +720 / −640 |  |
| 2026-10-02 | `af62136` | Updated | Updated: The file manifest carries the checksums of the translations | 1 | +2 / −2 |  |
| 2026-10-02 | `1de7e0b` | Updated | Updated: Version 4.1.12 | 2 | +2 / −2 | 4.1.12 |
| 2026-10-02 | `fa3f4b7` | Updated | Updated: The file manifest carries the checksums of the version files | 1 | +2 / −2 |  |
| 2026-10-02 | `dee2311` | Updated | Updated: Version 4.1.13 | 3 | +3 / −3 |  |
| 2026-10-02 | `37ba8e5` | Updated | Updated: The file manifest carries the checksums of the 4.1.13 version files | 1 | +2 / −2 | 4.1.13 |
| 2026-10-02 | `2721362` | Updated | Updated: The list and filter views post each button to its own form | 2 | +8 / −2 |  |
| 2026-10-02 | `81f94d5` | Added | Added: CjwNewsletterClassInstaller imports the newsletter content classes into a class group | 1 | +133 / −0 |  |
| 2026-10-02 | `7490799` | Updated | Updated: Version 4.1.14 | 3 | +3 / −3 |  |
| 2026-10-02 | `22b08be` | Updated | Updated: The file manifest carries the checksums of the 4.1.14 version files and lists the class installer | 1 | +7 / −6 | 4.1.14 |
| 2026-10-02 | `3ecaef5` | Updated | Updated: The edition output and the siteaccess ini are fetched by a command line call that quotes every argument, passes --allow-root-user when the process is root and answers with an error result instead of a fatal | 2 | +53 / −21 |  |
| 2026-10-02 | `ed65af0` | Updated | Updated: The preview, preview_archive and archive views answer an unknown edition, version or output format with a clean error and return their content as a result with ?Debug=1 | 3 | +25 / −5 |  |
| 2026-10-02 | `cddea14` | Updated | Updated: The file transport writes one uniquely named .eml per recipient, the transports report every failure as a result, the mail class no longer calls strpos() on an exception | 3 | +62 / −21 |  |
| 2026-10-02 | `bec45ca` | Updated | Updated: Redirect targets from a request are paths of this site, the old-post field of the user form is plain data, hashes come from the system random source | 6 | +51 / −19 |  |
| 2026-10-02 | `0191a9c` | Updated | Updated: The public forms and the send view read their input defensively | 5 | +77 / −39 |  |
| 2026-10-02 | `f513883` | Updated | Updated: CSV import and export keep their columns, accept a tab and long lines, and cannot be pointed at other files | 5 | +41 / −17 |  |
| 2026-10-02 | `a9e09f4` | Updated | Updated: Newsletter users are found by an address with an apostrophe or other case, a remote id of nothing matches nobody, a removed user is stored | 3 | +26 / −15 |  |
| 2026-10-02 | `4d707c4` | Updated | Updated: Open send items of a bounced user are stored as aborted, list fetches without a limit work, an edition send with a damaged output reads as empty, a bounce that cannot be parsed is done with | 5 | +31 / −9 |  |
| 2026-10-02 | `01a249f` | Updated | Updated: The bounce parser reads codes and headers the way a mail server writes them | 2 | +42 / −19 |  |
| 2026-10-02 | `564ad79` | Updated | Updated: Virtual lists run their constructor on PHP 8, their send path and several external filters work | 6 | +16 / −12 |  |
| 2026-10-02 | `90d4f4e` | Updated | Updated: cjw_newsletter.ini describes the simulated sending with the file transport | 1 | +8 / −1 |  |
| 2026-10-02 | `2d2575c` | Updated | Updated: Version 4.1.15 | 3 | +3 / −3 |  |
| 2026-10-02 | `4487dfd` | Updated | Updated: The file manifest carries the checksums of the 4.1.15 files and lists the code of the commands, cron jobs and views | 1 | +62 / −29 | 4.1.15 origin/master origin/HEAD |
