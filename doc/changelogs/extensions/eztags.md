# eztags (Tags): release notes

What each release of `eztags` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/eztags.md); the story is in the [chronicle](../../history/extensions/eztags.md).

See [jQuery 4 and YUI removal](../../features/6.0/jquery4-and-yui-removal.md) and [YUI removed](../../bc/6.0/yui-removal.md).

## v2.4.11 (2026-10-02)

**Maintenance, documentation and packaging**

- The commands start through the shared command helpers ([`a3bb010`](https://github.com/se7enxweb/eztags/commit/a3bb010))

1 version, merge or metadata commit not listed.

## v2.4.10 (2026-10-02)

**Maintenance, documentation and packaging**

- The command line scripts, cronjob parts and module views are classes the files call ([`f2e8f1a`](https://github.com/se7enxweb/eztags/commit/f2e8f1a))
- The entry point files carry a header of 7x and the Exponential Foundation; the original headers move to the classes ([`9a2af2d`](https://github.com/se7enxweb/eztags/commit/9a2af2d))
- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`bbc419d`](https://github.com/se7enxweb/eztags/commit/bbc419d))

1 version, merge or metadata commit not listed.

## v2.4.9 (2026-10-02)

**Removed**

- YUI from eZ Tags; the children list of a tag runs on Exponential UI alone ([`ca15834`](https://github.com/se7enxweb/eztags/commit/ca15834)) Upgrade note.

## v2.4.8 (2026-10-01)

**Updated**

- The children table of a tag runs on Exponential UI's exp::datatable when expui is active, with $.fn.eZTagsChildrenExp, and keeps its YUI 2 DataTable as the fallback, so that the tags admin needs no YUI for it. ([`6059fa5`](https://github.com/se7enxweb/eztags/commit/6059fa5))
- Fixed: Fixed the tags field's edit page in designs that do not load eztags' FrontendJavaScriptList, where it stopped with TagsStructureMenu and $.EzTags not defined, by having the field's template require its own scripts and styles, so that tags can be edited on every site design. ([`56e2988`](https://github.com/se7enxweb/eztags/commit/56e2988))
- Fixed: Fixed the tags administration in the admin design, where the dashboard, tag pages, forms, left menu and node tab were blank because eZ Tags ships its admin templates in design/admin2 only, so that it works in every admin design without a design setting. ([`f218ff7`](https://github.com/se7enxweb/eztags/commit/f218ff7))

**Maintenance, documentation and packaging**

- Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback. ([`ad9e989`](https://github.com/se7enxweb/eztags/commit/ad9e989))

1 version, merge or metadata commit not listed.

## v2.4.7 (2026-10-01)

**Updated**

- Updated eztags for jQuery 4, with jsTree 3.3.17, so that the tag tree, the tag fields and the admin tag pages work on jQuery 4 without jQuery Migrate warnings. ([`d3dc766`](https://github.com/se7enxweb/eztags/commit/d3dc766)) Upgrade note.

1 version, merge or metadata commit not listed.

## v2.4.6 (2026-09-30)

**Added**

- ezinfo.php, with Version 2.4.6 ([`02672e3`](https://github.com/se7enxweb/eztags/commit/02672e3))

## v2.4.5 (2026-09-30)

**Updated**

- Fixed: Tag lookups by main translation work on every database, Oracle included ([`ec57870`](https://github.com/se7enxweb/eztags/commit/ec57870))
- Fixed: The tag tree filter and the tag search by subtree work on every database ([`5c9efce`](https://github.com/se7enxweb/eztags/commit/5c9efce))

1 version, merge or metadata commit not listed.

## v2.4.4 (2026-09-30)

**Maintenance, documentation and packaging**

- The description calls the product Exponential ([`9f61ddd`](https://github.com/se7enxweb/eztags/commit/9f61ddd))

1 version, merge or metadata commit not listed.

## v2.4.3 (2026-09-29)

**Updated**

- The top menu tab and its tooltip have German translations ([`9673498`](https://github.com/se7enxweb/eztags/commit/9673498))

1 version, merge or metadata commit not listed.

## v2.4.2 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`45a97a7`](https://github.com/se7enxweb/eztags/commit/45a97a7))

1 version, merge or metadata commit not listed.

## v2.4.1 (2026-09-27)

**Updated**

- The admin tab, its title and the top menu tooltip call the extension Tags ([`1f1833b`](https://github.com/se7enxweb/eztags/commit/1f1833b))

**Maintenance, documentation and packaging**

- The extension states its version, license and website ([`b7a2860`](https://github.com/se7enxweb/eztags/commit/b7a2860))

## v2.4.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`75ab1a1`](https://github.com/se7enxweb/eztags/commit/75ab1a1))

## v2.3.5 (2026-09-20)

**Updated**

- Updated the top menu entry from "eZ Tags" to "Tags", a shorter name that does not carry the old product's branding now that the extension ships as part of Exponential 6. ([`10872e9`](https://github.com/se7enxweb/eztags/commit/10872e9))

## v2.3.4 (2026-09-19)

**Updated**

- Fixed: Fixed tags rendering as nothing on MongoDB installations, caused by joins with eztags_keyword the MongoDB driver will not translate, so tagged content lists its tags again and each tag links to its own page. ([`80c42c1`](https://github.com/se7enxweb/eztags/commit/80c42c1))

## v2.3.3 (2026-08-07)

**Added**

- SQLite schema for eztags ([`92ab004`](https://github.com/se7enxweb/eztags/commit/92ab004))

## v2.3.2 (2026-04-22)

**Updated**

- fix: replace MOD() with % operator and use positional ORDER BY for SQLite compatibility ([`65dc524`](https://github.com/se7enxweb/eztags/commit/65dc524))

1 version, merge or metadata commit not listed.

## v2.3.1 (2024-01-29)

**Maintenance, documentation and packaging**

- Update composer.json changed package vendor ([`01a07a0`](https://github.com/se7enxweb/eztags/commit/01a07a0))
- Update composer.json switched package name ([`04315e7`](https://github.com/se7enxweb/eztags/commit/04315e7))
- Update composer.json switched vendor name ([`2830721`](https://github.com/se7enxweb/eztags/commit/2830721))

2 version, merge or metadata commits not listed.

## 2.3 (2023-03-31)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.2.2 (2018-01-11)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.2.1 (2016-10-31)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.2 (2016-08-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.1 (2016-03-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.0.2 (2015-09-21)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.4.2 (2015-07-16)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.0.1 (2015-07-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 2.0 (2015-07-16)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.4.1 (2015-05-04)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.4 (2015-04-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.3 (2014-05-15)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.2.3 (2013-12-02)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.2.2 (2012-01-12)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.2.1 (2011-12-22)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.2 (2011-09-07)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.1 (2011-06-13)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0.1 (2011-04-26)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0 (2011-03-31)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0beta (2011-03-08)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0alpha (2010-11-10)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## Related

* [Feature page](../../features/6.0/extensions/eztags.md)
* [Chronicle](../../history/extensions/eztags.md)
* [Change ledger](../../history/ledger/eztags.md)
