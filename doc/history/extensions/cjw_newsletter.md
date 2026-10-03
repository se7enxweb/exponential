# cjw_newsletter (Newsletter): chronicle

The newsletter system arrived in this repository with the PHP 8.5 fixes of August 2026 (release line 4.0.0.0). September and October made it run on PHP 8, on a persistent worker and on Oracle, fixed its integrity manifest, translated it, and on 2 October a review of the whole extension closed dozens of security and robustness defects (release 4.1.15). See the [feature page](../../features/6.0/extensions/cjw_newsletter.md).

This page lists **every one of the 56 changes** of the repository `cjw_newsletter` between 2024-01-28 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/cjw_newsletter.md); what each release contains is in the [release notes](../../changelogs/extensions/cjw_newsletter.md); how to use the extension is on its [feature page](../../features/6.0/extensions/cjw_newsletter.md).

| Kind | Changes |
|---|---|
| feature | 16 |
| fix | 9 |
| security | 2 |
| upgrade note | 2 |
| docs | 1 |
| tooling | 11 |
| release | 12 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-08-13 | 4.0.0.0 | [`d191afc`](https://github.com/se7enxweb/cjw_newsletter/commit/d191afc) |
| 2026-09-14 | 3.0.1 | [`866245b`](https://github.com/se7enxweb/cjw_newsletter/commit/866245b) |
| 2026-09-14 | 4.0.0.1 | [`7dd59cf`](https://github.com/se7enxweb/cjw_newsletter/commit/7dd59cf) |
| 2026-09-22 | 4.1.0 | [`0dde3ed`](https://github.com/se7enxweb/cjw_newsletter/commit/0dde3ed) |
| 2026-09-27 | 4.1.1 | [`d35f884`](https://github.com/se7enxweb/cjw_newsletter/commit/d35f884) |
| 2026-09-27 | 4.1.2 | [`253daf8`](https://github.com/se7enxweb/cjw_newsletter/commit/253daf8) |
| 2026-09-27 | 4.1.3 | [`5157569`](https://github.com/se7enxweb/cjw_newsletter/commit/5157569) |
| 2026-09-28 | 4.1.4 | [`c344404`](https://github.com/se7enxweb/cjw_newsletter/commit/c344404) |
| 2026-09-28 | 4.1.5 | [`d8c408f`](https://github.com/se7enxweb/cjw_newsletter/commit/d8c408f) |
| 2026-09-29 | 4.1.6 | [`b6534a1`](https://github.com/se7enxweb/cjw_newsletter/commit/b6534a1) |
| 2026-09-30 | 4.1.7 | [`37ef638`](https://github.com/se7enxweb/cjw_newsletter/commit/37ef638) |
| 2026-10-01 | 4.1.8 | [`2f4a328`](https://github.com/se7enxweb/cjw_newsletter/commit/2f4a328) |
| 2026-10-02 | 4.1.9 | [`fb587b9`](https://github.com/se7enxweb/cjw_newsletter/commit/fb587b9) |
| 2026-10-02 | 4.1.10 | [`23c837e`](https://github.com/se7enxweb/cjw_newsletter/commit/23c837e) |
| 2026-10-02 | 4.1.11 | [`6a1650b`](https://github.com/se7enxweb/cjw_newsletter/commit/6a1650b) |
| 2026-10-02 | 4.1.12 | [`1de7e0b`](https://github.com/se7enxweb/cjw_newsletter/commit/1de7e0b) |
| 2026-10-02 | 4.1.13 | [`37ba8e5`](https://github.com/se7enxweb/cjw_newsletter/commit/37ba8e5) |
| 2026-10-02 | 4.1.14 | [`22b08be`](https://github.com/se7enxweb/cjw_newsletter/commit/22b08be) |
| 2026-10-02 | 4.1.15 | [`4487dfd`](https://github.com/se7enxweb/cjw_newsletter/commit/4487dfd) |

## Timeline

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/cjw_newsletter.md#2024-01-1-changes).

- 2024-01-28 [`c01b30d`](https://github.com/se7enxweb/cjw_newsletter/commit/c01b30d) (no user benefit) Added github funding information

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/cjw_newsletter.md#2026-03-1-changes).

- 2026-03-02 [`6d07799`](https://github.com/se7enxweb/cjw_newsletter/commit/6d07799) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-08

The month across all extensions: [August 2026](months/2026-08.md). [Ledger of this month](../ledger/cjw_newsletter.md#2026-08-3-changes).

- 2026-08-13 [`d0707ea`](https://github.com/se7enxweb/cjw_newsletter/commit/d0707ea) (fix) Fix PHP 8.5 deprecation warnings
- 2026-08-13 [`ea77ce4`](https://github.com/se7enxweb/cjw_newsletter/commit/ea77ce4) (no user benefit) Merge pull request #1 from a contributor
- 2026-08-13 [`d191afc`](https://github.com/se7enxweb/cjw_newsletter/commit/d191afc) (tooling) Added composer.json file and renamed README file to README.md **Release 4.0.0.0.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/cjw_newsletter.md#2026-09-19-changes).

- 2026-09-14 [`866245b`](https://github.com/se7enxweb/cjw_newsletter/commit/866245b) (fix) Fixed: Fixed the file integrity manifest, which had not been rewritten since the php 8.5 fixes and reported nine files as altered on every installation that checked them. **Release 3.0.1.**
- 2026-09-14 [`7dd59cf`](https://github.com/se7enxweb/cjw_newsletter/commit/7dd59cf) (fix) Fixed: Fixed the version this extension reports, which named a release line it had been moved off, and rebuilt the manifest that carries it. **Release 4.0.0.1.**
- 2026-09-22 [`0dde3ed`](https://github.com/se7enxweb/cjw_newsletter/commit/0dde3ed) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release 4.1.0.**
- 2026-09-27 [`d8a6fd6`](https://github.com/se7enxweb/cjw_newsletter/commit/d8a6fd6) (fix) Fixed: The mailbox list and the subscribe view run on PHP 8
- 2026-09-27 [`d35f884`](https://github.com/se7enxweb/cjw_newsletter/commit/d35f884) (fix) Fixed: The version this extension reports is 4.1.1, and its manifest matches the files it ships **Release 4.1.1.**
- 2026-09-27 [`b73e486`](https://github.com/se7enxweb/cjw_newsletter/commit/b73e486) (feature) Visible texts and their translations name Exponential
- 2026-09-27 [`253daf8`](https://github.com/se7enxweb/cjw_newsletter/commit/253daf8) (release) Version 4.1.2 **Release 4.1.2.**
- 2026-09-27 [`5157569`](https://github.com/se7enxweb/cjw_newsletter/commit/5157569) (docs) The extension states its version, license and website **Release 4.1.3.**
- 2026-09-28 [`d7b00ff`](https://github.com/se7enxweb/cjw_newsletter/commit/d7b00ff) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`c344404`](https://github.com/se7enxweb/cjw_newsletter/commit/c344404) (release) Version 4.1.4 **Release 4.1.4.**
- 2026-09-28 [`d8c408f`](https://github.com/se7enxweb/cjw_newsletter/commit/d8c408f) (release) Version 4.1.5, with a file manifest that matches the files again **Release 4.1.5.**
- 2026-09-29 [`f257277`](https://github.com/se7enxweb/cjw_newsletter/commit/f257277) (feature) The top menu tab has an English tooltip with a German translation
- 2026-09-29 [`6a23a30`](https://github.com/se7enxweb/cjw_newsletter/commit/6a23a30) (tooling) The file manifest carries the checksums of the menu translations
- 2026-09-29 [`b6534a1`](https://github.com/se7enxweb/cjw_newsletter/commit/b6534a1) (release) Version 4.1.6 **Release 4.1.6.**
- 2026-09-30 [`89a2d7f`](https://github.com/se7enxweb/cjw_newsletter/commit/89a2d7f) (fix) Fixed: The CSV export of a subscription list runs on every database, Oracle included
- 2026-09-30 [`a6e8edd`](https://github.com/se7enxweb/cjw_newsletter/commit/a6e8edd) (fix) Fixed: The newsletter user search works on every database, Oracle included
- 2026-09-30 [`369094e`](https://github.com/se7enxweb/cjw_newsletter/commit/369094e) (fix) Fixed: Aborting a newsletter send works on every database, Oracle included
- 2026-09-30 [`94da274`](https://github.com/se7enxweb/cjw_newsletter/commit/94da274) (tooling) The file manifest carries the checksums of the CSV export, user search and send abort fixes
- 2026-09-30 [`37ef638`](https://github.com/se7enxweb/cjw_newsletter/commit/37ef638) (release) Version 4.1.7 **Release 4.1.7.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/cjw_newsletter.md#2026-10-32-changes).

- 2026-10-01 [`0a0d760`](https://github.com/se7enxweb/cjw_newsletter/commit/0a0d760) (upgrade note) Updated the newsletter list and the form builder filter for jQuery 4, so that their handlers use .on() and the disabled state is set with .prop() instead of the deprecated shorthands and boolean .attr().
- 2026-10-01 [`39353f8`](https://github.com/se7enxweb/cjw_newsletter/commit/39353f8) (tooling) The file manifest carries the checksums of the newsletter list and the form builder filter on jQuery 4.
- 2026-10-01 [`2f4a328`](https://github.com/se7enxweb/cjw_newsletter/commit/2f4a328) (release) Version 4.1.8 **Release 4.1.8.**
- 2026-10-02 [`fb587b9`](https://github.com/se7enxweb/cjw_newsletter/commit/fb587b9) (upgrade note) The admin2 children list's YUI drag and drop, which loaded a script that no longer exists **Release 4.1.9.**
- 2026-10-02 [`01625ad`](https://github.com/se7enxweb/cjw_newsletter/commit/01625ad) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`fc19a0f`](https://github.com/se7enxweb/cjw_newsletter/commit/fc19a0f) (feature) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`23c837e`](https://github.com/se7enxweb/cjw_newsletter/commit/23c837e) (release) Version 4.1.10 **Release 4.1.10.**
- 2026-10-02 [`de10308`](https://github.com/se7enxweb/cjw_newsletter/commit/de10308) (tooling) The commands start through the shared command helpers
- 2026-10-02 [`6a1650b`](https://github.com/se7enxweb/cjw_newsletter/commit/6a1650b) (release) Version 4.1.11 **Release 4.1.11.**
- 2026-10-02 [`d00c1fc`](https://github.com/se7enxweb/cjw_newsletter/commit/d00c1fc) (feature) English and German translations for every string the admin showed untranslated
- 2026-10-02 [`af62136`](https://github.com/se7enxweb/cjw_newsletter/commit/af62136) (tooling) The file manifest carries the checksums of the translations
- 2026-10-02 [`1de7e0b`](https://github.com/se7enxweb/cjw_newsletter/commit/1de7e0b) (release) Version 4.1.12 **Release 4.1.12.**
- 2026-10-02 [`fa3f4b7`](https://github.com/se7enxweb/cjw_newsletter/commit/fa3f4b7) (tooling) The file manifest carries the checksums of the version files
- 2026-10-02 [`dee2311`](https://github.com/se7enxweb/cjw_newsletter/commit/dee2311) (release) Version 4.1.13
- 2026-10-02 [`37ba8e5`](https://github.com/se7enxweb/cjw_newsletter/commit/37ba8e5) (tooling) The file manifest carries the checksums of the 4.1.13 version files **Release 4.1.13.**
- 2026-10-02 [`2721362`](https://github.com/se7enxweb/cjw_newsletter/commit/2721362) (feature) The list and filter views post each button to its own form
- 2026-10-02 [`81f94d5`](https://github.com/se7enxweb/cjw_newsletter/commit/81f94d5) (feature) CjwNewsletterClassInstaller imports the newsletter content classes into a class group
- 2026-10-02 [`7490799`](https://github.com/se7enxweb/cjw_newsletter/commit/7490799) (release) Version 4.1.14
- 2026-10-02 [`22b08be`](https://github.com/se7enxweb/cjw_newsletter/commit/22b08be) (tooling) The file manifest carries the checksums of the 4.1.14 version files and lists the class installer **Release 4.1.14.**
- 2026-10-02 [`3ecaef5`](https://github.com/se7enxweb/cjw_newsletter/commit/3ecaef5) (feature) The edition output and the siteaccess ini are fetched by a command line call that quotes every argument, passes --allow-root-user when the process is root and answers with an error result instead of a fatal
- 2026-10-02 [`ed65af0`](https://github.com/se7enxweb/cjw_newsletter/commit/ed65af0) (feature) The preview, preview_archive and archive views answer an unknown edition, version or output format with a clean error and return their content as a result with ?Debug=1
- 2026-10-02 [`cddea14`](https://github.com/se7enxweb/cjw_newsletter/commit/cddea14) (feature) The file transport writes one uniquely named .eml per recipient, the transports report every failure as a result, the mail class no longer calls strpos() on an exception
- 2026-10-02 [`bec45ca`](https://github.com/se7enxweb/cjw_newsletter/commit/bec45ca) (security) Redirect targets from a request are paths of this site, the old-post field of the user form is plain data, hashes come from the system random source
- 2026-10-02 [`0191a9c`](https://github.com/se7enxweb/cjw_newsletter/commit/0191a9c) (security) The public forms and the send view read their input defensively
- 2026-10-02 [`f513883`](https://github.com/se7enxweb/cjw_newsletter/commit/f513883) (feature) CSV import and export keep their columns, accept a tab and long lines, and cannot be pointed at other files
- 2026-10-02 [`a9e09f4`](https://github.com/se7enxweb/cjw_newsletter/commit/a9e09f4) (feature) Newsletter users are found by an address with an apostrophe or other case, a remote id of nothing matches nobody, a removed user is stored
- 2026-10-02 [`4d707c4`](https://github.com/se7enxweb/cjw_newsletter/commit/4d707c4) (feature) Open send items of a bounced user are stored as aborted, list fetches without a limit work, an edition send with a damaged output reads as empty, a bounce that cannot be parsed is done with
- 2026-10-02 [`01a249f`](https://github.com/se7enxweb/cjw_newsletter/commit/01a249f) (feature) The bounce parser reads codes and headers the way a mail server writes them
- 2026-10-02 [`564ad79`](https://github.com/se7enxweb/cjw_newsletter/commit/564ad79) (feature) Virtual lists run their constructor on PHP 8, their send path and several external filters work
- 2026-10-02 [`90d4f4e`](https://github.com/se7enxweb/cjw_newsletter/commit/90d4f4e) (feature) cjw_newsletter.ini describes the simulated sending with the file transport
- 2026-10-02 [`2d2575c`](https://github.com/se7enxweb/cjw_newsletter/commit/2d2575c) (release) Version 4.1.15
- 2026-10-02 [`4487dfd`](https://github.com/se7enxweb/cjw_newsletter/commit/4487dfd) (tooling) The file manifest carries the checksums of the 4.1.15 files and lists the code of the commands, cron jobs and views **Release 4.1.15.**

### Added after the ledger was cut (2 October 2026)

- 2026-10-02 `fffda2f` (feature) The installer creates the newsletter tree of a new installation (4.1.16)
- 2026-10-02 `bb3542b` (feature) The subject prefix of newsletter mails defaults to the host of the site (4.1.16)
- 2026-10-02 `b4acb1e` (tooling) The file manifest carries the checksums of the 4.1.16 files, with the release 4.1.16

The 4.1.15 commits above also exist in the clone under other hashes (a second copy of the same subjects is reachable from another reference); the links on this page use the hashes of the ledger, which may need checking against the remote.

## Related

* [Feature page](../../features/6.0/extensions/cjw_newsletter.md)
* [Release notes](../../changelogs/extensions/cjw_newsletter.md)
* [Change ledger](../ledger/cjw_newsletter.md)
