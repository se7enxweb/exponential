# ezwt (website toolbar): release notes

What each release of `ezwt` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/ezwt.md); the story is in the [chronicle](../../history/extensions/ezwt.md).

See [jQuery 4 and YUI removal](../../features/6.0/jquery4-and-yui-removal.md) and [YUI removed](../../bc/6.0/yui-removal.md).

## v6.0.9 (2026-10-02)

**Maintenance, documentation and packaging**

- The command line scripts, cronjob parts and module views are classes the files call ([`d33a8e0`](https://github.com/se7enxweb/ezwt/commit/d33a8e0))
- The entry point files carry a header of 7x and the Exponential Foundation; the original headers move to the classes ([`3508df1`](https://github.com/se7enxweb/ezwt/commit/3508df1))
- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`7f8296a`](https://github.com/se7enxweb/ezwt/commit/7f8296a))

1 version, merge or metadata commit not listed.

## v6.0.8 (2026-10-02)

**Removed**

- YUI from the website toolbar; sorting and date fields run on jQuery and Exponential UI ([`86b2816`](https://github.com/se7enxweb/ezwt/commit/86b2816)) Upgrade note.

## v6.0.7 (2026-10-01)

**Updated**

- The date and date/time fields of the ezdemo design use Exponential UI's calendar, exp::datepicker, when expui is active and load YUI's calendar only without it, so that editing runs on jQuery 4. ([`f29c572`](https://github.com/se7enxweb/ezwt/commit/f29c572)) Upgrade note.

**Maintenance, documentation and packaging**

- Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback. ([`deffdd1`](https://github.com/se7enxweb/ezwt/commit/deffdd1))

1 version, merge or metadata commit not listed.

## v6.0.6 (2026-09-30)

**Maintenance, documentation and packaging**

- The description calls the product Exponential ([`93afd60`](https://github.com/se7enxweb/ezwt/commit/93afd60))

1 version, merge or metadata commit not listed.

## v6.0.5 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`b8bb553`](https://github.com/se7enxweb/ezwt/commit/b8bb553))

1 version, merge or metadata commit not listed.

## v6.0.4 (2026-09-27)

**Maintenance, documentation and packaging**

- The extension states its version, license and website ([`513a123`](https://github.com/se7enxweb/ezwt/commit/513a123))

## v6.0.3 (2026-07-18)

**Removed**

- Remove XHTML trailing slashes and obsolete vendor prefixes ([`cae2638`](https://github.com/se7enxweb/ezwt/commit/cae2638)) Upgrade note.

1 version, merge or metadata commit not listed.

## v6.0.2 (2024-01-29)

**Maintenance, documentation and packaging**

- Update composer.json changed homepage url ([`fb70415`](https://github.com/se7enxweb/ezwt/commit/fb70415))

## v6.0.1 (2024-01-29)

**Maintenance, documentation and packaging**

- Update composer.json switched package vendor name ([`5ad931c`](https://github.com/se7enxweb/ezwt/commit/5ad931c))

2 version, merge or metadata commits not listed.

## 6.0 (2023-12-23)

**Maintenance, documentation and packaging**

- Update composer.json switched package vendor ([`f6e1b2e`](https://github.com/se7enxweb/ezwt/commit/f6e1b2e))

## v5.3.10 (2018-09-06)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.9 (2018-06-12)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.8 (2016-03-02)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.5-rc1 (2014-11-26)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.5 (2014-11-26)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.1 (2014-05-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.0 (2014-05-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.4.0 (2011-03-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.2.0 (2010-10-15)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.1.0 (2009-09-30)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## Related

* [Feature page](../../features/6.0/extensions/ezwt.md)
* [Chronicle](../../history/extensions/ezwt.md)
* [Change ledger](../../history/ledger/ezwt.md)
