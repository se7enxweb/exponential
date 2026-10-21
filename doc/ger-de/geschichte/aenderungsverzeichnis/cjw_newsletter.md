# Änderungsverzeichnis: cjw_newsletter

Jede Änderung an `cjw_newsletter` seit Beginn der se7enxweb-Ära, die älteste zuerst: 56 Änderungen an 230 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 50 |
| Hinzugefügt | 3 |
| Sonstiges | 1 |
| Zusammengeführt | 1 |
| Entfernt | 1 |

## 2024-01 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `c01b30d` | Hinzugefügt | Added: Added github funding information | 1 | +3 / −0 |  |

## 2026-03 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `6d07799` | Sonstiges | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-08 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-13 | `d0707ea` | Aktualisiert | Fix PHP 8.5 deprecation warnings | 5 | +14 / −14 |  |
| 2026-08-13 | `ea77ce4` | Zusammengeführt | Merge pull request #1 from ekked/php85-update | 0 | +0 / −0 |  |
| 2026-08-13 | `d191afc` | Hinzugefügt | Added: Added composer.json file and renamed README file to README.md | 2 | +26 / −0 | 4.0.0.0 |

## 2026-09 (19 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-14 | `866245b` | Aktualisiert | Fixed: Fixed the file integrity manifest, which had not been rewritten since the php 8.5 fixes and reported nine files as altered on every installation that checked them. | 3 | +18 / −14 | 3.0.1 |
| 2026-09-14 | `7dd59cf` | Aktualisiert | Fixed: Fixed the version this extension reports, which named a release line it had been moved off, and rebuilt the manifest that carries it. | 3 | +5 / −5 | 4.0.0.1 |
| 2026-09-22 | `0dde3ed` | Aktualisiert | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 5 | +202 / −170 | 4.1.0 |
| 2026-09-27 | `d8a6fd6` | Aktualisiert | Fixed: The mailbox list and the subscribe view run on PHP 8 | 3 | +8 / −1 |  |
| 2026-09-27 | `d35f884` | Aktualisiert | Fixed: The version this extension reports is 4.1.1, and its manifest matches the files it ships | 3 | +13 / −13 | 4.1.1 |
| 2026-09-27 | `b73e486` | Aktualisiert | Updated: Visible texts and their translations name Exponential | 8 | +14 / −14 |  |
| 2026-09-27 | `253daf8` | Aktualisiert | Updated: Version 4.1.2 | 3 | +5 / −5 | 4.1.2 |
| 2026-09-27 | `5157569` | Aktualisiert | Updated: The extension states its version, license and website | 3 | +11 / −10 | 4.1.3 |
| 2026-09-28 | `d7b00ff` | Aktualisiert | Updated: Every visible text of the extension is a translation string, with German | 21 | +512 / −46 |  |
| 2026-09-28 | `c344404` | Aktualisiert | Updated: Version 4.1.4 | 2 | +2 / −2 | 4.1.4 |
| 2026-09-28 | `d8c408f` | Aktualisiert | Updated: Version 4.1.5, with a file manifest that matches the files again | 3 | +26 / −26 | 4.1.5 |
| 2026-09-29 | `f257277` | Aktualisiert | Updated: The top menu tab has an English tooltip with a German translation | 4 | +25 / −1 |  |
| 2026-09-29 | `6a23a30` | Aktualisiert | Updated: The file manifest carries the checksums of the menu translations | 1 | +4 / −4 |  |
| 2026-09-29 | `b6534a1` | Aktualisiert | Updated: Version 4.1.6 | 3 | +5 / −5 | 4.1.6 |
| 2026-09-30 | `89a2d7f` | Aktualisiert | Fixed: The CSV export of a subscription list runs on every database, Oracle included | 1 | +8 / −6 |  |
| 2026-09-30 | `a6e8edd` | Aktualisiert | Fixed: The newsletter user search works on every database, Oracle included | 1 | +9 / −6 |  |
| 2026-09-30 | `369094e` | Aktualisiert | Fixed: Aborting a newsletter send works on every database, Oracle included | 1 | +7 / −3 |  |
| 2026-09-30 | `94da274` | Aktualisiert | Updated: The file manifest carries the checksums of the CSV export, user search and send abort fixes | 1 | +3 / −3 |  |
| 2026-09-30 | `37ef638` | Aktualisiert | Updated: Version 4.1.7 | 3 | +5 / −5 | 4.1.7 |

## 2026-10 (32 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `0a0d760` | Aktualisiert | Updated: Updated the newsletter list and the form builder filter for jQuery 4, so that their handlers use .on() and the disabled state is set with .prop() instead of the deprecated shorthands and boolean .attr(). | 2 | +3 / −3 |  |
| 2026-10-01 | `39353f8` | Aktualisiert | Updated: The file manifest carries the checksums of the newsletter list and the form builder filter on jQuery 4. | 1 | +2 / −2 |  |
| 2026-10-01 | `2f4a328` | Aktualisiert | Updated: Version 4.1.8 | 3 | +5 / −5 | 4.1.8 |
| 2026-10-02 | `fb587b9` | Entfernt | Removed: The admin2 children list's YUI drag and drop, which loaded a script that no longer exists | 4 | +6 / −16 | 4.1.9 |
| 2026-10-02 | `01625ad` | Aktualisiert | Updated: The command line scripts, cronjob parts and module views are classes the files call | 66 | +5680 / −4452 |  |
| 2026-10-02 | `fc19a0f` | Aktualisiert | Updated: The commands and cronjob parts list a description of what they do | 3 | +3 / −0 |  |
| 2026-10-02 | `23c837e` | Aktualisiert | Updated: Version 4.1.10 | 3 | +38 / −38 | 4.1.10 |
| 2026-10-02 | `de10308` | Aktualisiert | Updated: The commands start through the shared command helpers | 2 | +6 / −14 |  |
| 2026-10-02 | `6a1650b` | Aktualisiert | Updated: Version 4.1.11 | 3 | +5 / −5 | 4.1.11 |
| 2026-10-02 | `d00c1fc` | Aktualisiert | Updated: English and German translations for every string the admin showed untranslated | 2 | +720 / −640 |  |
| 2026-10-02 | `af62136` | Aktualisiert | Updated: The file manifest carries the checksums of the translations | 1 | +2 / −2 |  |
| 2026-10-02 | `1de7e0b` | Aktualisiert | Updated: Version 4.1.12 | 2 | +2 / −2 | 4.1.12 |
| 2026-10-02 | `fa3f4b7` | Aktualisiert | Updated: The file manifest carries the checksums of the version files | 1 | +2 / −2 |  |
| 2026-10-02 | `dee2311` | Aktualisiert | Updated: Version 4.1.13 | 3 | +3 / −3 |  |
| 2026-10-02 | `37ba8e5` | Aktualisiert | Updated: The file manifest carries the checksums of the 4.1.13 version files | 1 | +2 / −2 | 4.1.13 |
| 2026-10-02 | `2721362` | Aktualisiert | Updated: The list and filter views post each button to its own form | 2 | +8 / −2 |  |
| 2026-10-02 | `81f94d5` | Hinzugefügt | Added: CjwNewsletterClassInstaller imports the newsletter content classes into a class group | 1 | +133 / −0 |  |
| 2026-10-02 | `7490799` | Aktualisiert | Updated: Version 4.1.14 | 3 | +3 / −3 |  |
| 2026-10-02 | `22b08be` | Aktualisiert | Updated: The file manifest carries the checksums of the 4.1.14 version files and lists the class installer | 1 | +7 / −6 | 4.1.14 |
| 2026-10-02 | `3ecaef5` | Aktualisiert | Updated: The edition output and the siteaccess ini are fetched by a command line call that quotes every argument, passes --allow-root-user when the process is root and answers with an error result instead of a fatal | 2 | +53 / −21 |  |
| 2026-10-02 | `ed65af0` | Aktualisiert | Updated: The preview, preview_archive and archive views answer an unknown edition, version or output format with a clean error and return their content as a result with ?Debug=1 | 3 | +25 / −5 |  |
| 2026-10-02 | `cddea14` | Aktualisiert | Updated: The file transport writes one uniquely named .eml per recipient, the transports report every failure as a result, the mail class no longer calls strpos() on an exception | 3 | +62 / −21 |  |
| 2026-10-02 | `bec45ca` | Aktualisiert | Updated: Redirect targets from a request are paths of this site, the old-post field of the user form is plain data, hashes come from the system random source | 6 | +51 / −19 |  |
| 2026-10-02 | `0191a9c` | Aktualisiert | Updated: The public forms and the send view read their input defensively | 5 | +77 / −39 |  |
| 2026-10-02 | `f513883` | Aktualisiert | Updated: CSV import and export keep their columns, accept a tab and long lines, and cannot be pointed at other files | 5 | +41 / −17 |  |
| 2026-10-02 | `a9e09f4` | Aktualisiert | Updated: Newsletter users are found by an address with an apostrophe or other case, a remote id of nothing matches nobody, a removed user is stored | 3 | +26 / −15 |  |
| 2026-10-02 | `4d707c4` | Aktualisiert | Updated: Open send items of a bounced user are stored as aborted, list fetches without a limit work, an edition send with a damaged output reads as empty, a bounce that cannot be parsed is done with | 5 | +31 / −9 |  |
| 2026-10-02 | `01a249f` | Aktualisiert | Updated: The bounce parser reads codes and headers the way a mail server writes them | 2 | +42 / −19 |  |
| 2026-10-02 | `564ad79` | Aktualisiert | Updated: Virtual lists run their constructor on PHP 8, their send path and several external filters work | 6 | +16 / −12 |  |
| 2026-10-02 | `90d4f4e` | Aktualisiert | Updated: cjw_newsletter.ini describes the simulated sending with the file transport | 1 | +8 / −1 |  |
| 2026-10-02 | `2d2575c` | Aktualisiert | Updated: Version 4.1.15 | 3 | +3 / −3 |  |
| 2026-10-02 | `4487dfd` | Aktualisiert | Updated: The file manifest carries the checksums of the 4.1.15 files and lists the code of the commands, cron jobs and views | 1 | +62 / −29 | 4.1.15 origin/master origin/HEAD |
