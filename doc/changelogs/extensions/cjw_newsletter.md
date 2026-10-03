# cjw_newsletter (Newsletter): release notes

What each release of `cjw_newsletter` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/cjw_newsletter.md); the story is in the [chronicle](../../history/extensions/cjw_newsletter.md).

## 4.1.16 (2026-10-02)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 4.1.15 (2026-10-02)

**Updated**

- The edition output and the siteaccess ini are fetched by a command line call that quotes every argument, passes --allow-root-user when the process is root and answers with an error result instead of a fatal ([`3ecaef5`](https://github.com/se7enxweb/cjw_newsletter/commit/3ecaef5))
- The preview, preview_archive and archive views answer an unknown edition, version or output format with a clean error and return their content as a result with ?Debug=1 ([`ed65af0`](https://github.com/se7enxweb/cjw_newsletter/commit/ed65af0))
- The file transport writes one uniquely named .eml per recipient, the transports report every failure as a result, the mail class no longer calls strpos() on an exception ([`cddea14`](https://github.com/se7enxweb/cjw_newsletter/commit/cddea14))
- Redirect targets from a request are paths of this site, the old-post field of the user form is plain data, hashes come from the system random source ([`bec45ca`](https://github.com/se7enxweb/cjw_newsletter/commit/bec45ca))
- The public forms and the send view read their input defensively ([`0191a9c`](https://github.com/se7enxweb/cjw_newsletter/commit/0191a9c))
- CSV import and export keep their columns, accept a tab and long lines, and cannot be pointed at other files ([`f513883`](https://github.com/se7enxweb/cjw_newsletter/commit/f513883))
- Newsletter users are found by an address with an apostrophe or other case, a remote id of nothing matches nobody, a removed user is stored ([`a9e09f4`](https://github.com/se7enxweb/cjw_newsletter/commit/a9e09f4))
- Open send items of a bounced user are stored as aborted, list fetches without a limit work, an edition send with a damaged output reads as empty, a bounce that cannot be parsed is done with ([`4d707c4`](https://github.com/se7enxweb/cjw_newsletter/commit/4d707c4))
- The bounce parser reads codes and headers the way a mail server writes them ([`01a249f`](https://github.com/se7enxweb/cjw_newsletter/commit/01a249f))
- Virtual lists run their constructor on PHP 8, their send path and several external filters work ([`564ad79`](https://github.com/se7enxweb/cjw_newsletter/commit/564ad79))
- cjw_newsletter.ini describes the simulated sending with the file transport ([`90d4f4e`](https://github.com/se7enxweb/cjw_newsletter/commit/90d4f4e))

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the 4.1.15 files and lists the code of the commands, cron jobs and views ([`4487dfd`](https://github.com/se7enxweb/cjw_newsletter/commit/4487dfd))

1 version, merge or metadata commit not listed.

## 4.1.14 (2026-10-02)

**Added**

- CjwNewsletterClassInstaller imports the newsletter content classes into a class group ([`81f94d5`](https://github.com/se7enxweb/cjw_newsletter/commit/81f94d5))

**Updated**

- The list and filter views post each button to its own form ([`2721362`](https://github.com/se7enxweb/cjw_newsletter/commit/2721362))

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the 4.1.14 version files and lists the class installer ([`22b08be`](https://github.com/se7enxweb/cjw_newsletter/commit/22b08be))

1 version, merge or metadata commit not listed.

## 4.1.13 (2026-10-02)

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the version files ([`fa3f4b7`](https://github.com/se7enxweb/cjw_newsletter/commit/fa3f4b7))
- The file manifest carries the checksums of the 4.1.13 version files ([`37ba8e5`](https://github.com/se7enxweb/cjw_newsletter/commit/37ba8e5))

1 version, merge or metadata commit not listed.

## 4.1.12 (2026-10-02)

**Updated**

- English and German translations for every string the admin showed untranslated ([`d00c1fc`](https://github.com/se7enxweb/cjw_newsletter/commit/d00c1fc))

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the translations ([`af62136`](https://github.com/se7enxweb/cjw_newsletter/commit/af62136))

1 version, merge or metadata commit not listed.

## 4.1.11 (2026-10-02)

**Maintenance, documentation and packaging**

- The commands start through the shared command helpers ([`de10308`](https://github.com/se7enxweb/cjw_newsletter/commit/de10308))

1 version, merge or metadata commit not listed.

## 4.1.10 (2026-10-02)

**Updated**

- The commands and cronjob parts list a description of what they do ([`fc19a0f`](https://github.com/se7enxweb/cjw_newsletter/commit/fc19a0f))

**Maintenance, documentation and packaging**

- The command line scripts, cronjob parts and module views are classes the files call ([`01625ad`](https://github.com/se7enxweb/cjw_newsletter/commit/01625ad))

1 version, merge or metadata commit not listed.

## 4.1.9 (2026-10-02)

**Removed**

- The admin2 children list's YUI drag and drop, which loaded a script that no longer exists ([`fb587b9`](https://github.com/se7enxweb/cjw_newsletter/commit/fb587b9)) Upgrade note.

## 4.1.8 (2026-10-01)

**Updated**

- Updated the newsletter list and the form builder filter for jQuery 4, so that their handlers use .on() and the disabled state is set with .prop() instead of the deprecated shorthands and boolean .attr(). ([`0a0d760`](https://github.com/se7enxweb/cjw_newsletter/commit/0a0d760)) Upgrade note.

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the newsletter list and the form builder filter on jQuery 4. ([`39353f8`](https://github.com/se7enxweb/cjw_newsletter/commit/39353f8))

1 version, merge or metadata commit not listed.

## 4.1.7 (2026-09-30)

**Updated**

- Fixed: The CSV export of a subscription list runs on every database, Oracle included ([`89a2d7f`](https://github.com/se7enxweb/cjw_newsletter/commit/89a2d7f))
- Fixed: The newsletter user search works on every database, Oracle included ([`a6e8edd`](https://github.com/se7enxweb/cjw_newsletter/commit/a6e8edd))
- Fixed: Aborting a newsletter send works on every database, Oracle included ([`369094e`](https://github.com/se7enxweb/cjw_newsletter/commit/369094e))

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the CSV export, user search and send abort fixes ([`94da274`](https://github.com/se7enxweb/cjw_newsletter/commit/94da274))

1 version, merge or metadata commit not listed.

## 4.1.6 (2026-09-29)

**Updated**

- The top menu tab has an English tooltip with a German translation ([`f257277`](https://github.com/se7enxweb/cjw_newsletter/commit/f257277))

**Maintenance, documentation and packaging**

- The file manifest carries the checksums of the menu translations ([`6a23a30`](https://github.com/se7enxweb/cjw_newsletter/commit/6a23a30))

1 version, merge or metadata commit not listed.

## 4.1.5 (2026-09-28)

1 version, merge or metadata commit not listed.

## 4.1.4 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`d7b00ff`](https://github.com/se7enxweb/cjw_newsletter/commit/d7b00ff))

1 version, merge or metadata commit not listed.

## 4.1.3 (2026-09-27)

**Maintenance, documentation and packaging**

- The extension states its version, license and website ([`5157569`](https://github.com/se7enxweb/cjw_newsletter/commit/5157569))

## 4.1.2 (2026-09-27)

**Updated**

- Visible texts and their translations name Exponential ([`b73e486`](https://github.com/se7enxweb/cjw_newsletter/commit/b73e486))

1 version, merge or metadata commit not listed.

## 4.1.1 (2026-09-27)

**Updated**

- Fixed: The mailbox list and the subscribe view run on PHP 8 ([`d8a6fd6`](https://github.com/se7enxweb/cjw_newsletter/commit/d8a6fd6))
- Fixed: The version this extension reports is 4.1.1, and its manifest matches the files it ships ([`d35f884`](https://github.com/se7enxweb/cjw_newsletter/commit/d35f884))

## 4.1.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`0dde3ed`](https://github.com/se7enxweb/cjw_newsletter/commit/0dde3ed))

## 4.0.0.1 (2026-09-14)

**Updated**

- Fixed: Fixed the version this extension reports, which named a release line it had been moved off, and rebuilt the manifest that carries it. ([`7dd59cf`](https://github.com/se7enxweb/cjw_newsletter/commit/7dd59cf))

## 3.0.1 (2026-09-14)

**Updated**

- Fixed: Fixed the file integrity manifest, which had not been rewritten since the php 8.5 fixes and reported nine files as altered on every installation that checked them. ([`866245b`](https://github.com/se7enxweb/cjw_newsletter/commit/866245b))

## 4.0.0.0 (2026-08-13)

**Updated**

- Fix PHP 8.5 deprecation warnings ([`d0707ea`](https://github.com/se7enxweb/cjw_newsletter/commit/d0707ea))

**Maintenance, documentation and packaging**

- Added composer.json file and renamed README file to README.md ([`d191afc`](https://github.com/se7enxweb/cjw_newsletter/commit/d191afc))

3 version, merge or metadata commits not listed.

## 3.0.0 (2015-08-23)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.0.0 (2014-09-01)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0.0.201102111706 (2011-02-11)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0.0rc1 (2010-12-06)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## Related

* [Feature page](../../features/6.0/extensions/cjw_newsletter.md)
* [Chronicle](../../history/extensions/cjw_newsletter.md)
* [Change ledger](../../history/ledger/cjw_newsletter.md)
