# ezwebin (Website Interface design): release notes

Read this page before you install or update `ezwebin`, or to find out which release brought a change.

What each release of `ezwebin` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/ezwebin.md); the story is in the [chronicle](../../history/extensions/ezwebin.md).

See [jQuery 4 and YUI removal](../../features/6.0/jquery4-and-yui-removal.md) and [YUI removed](../../bc/6.0/yui-removal.md).

## v6.0.16 (2026-10-02)

**Updated**

- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`b55459e`](https://github.com/se7enxweb/ezwebin/commit/b55459e))
- The about page names 1998 - 2026 7x & Exponential Foundation first in its copyright notice, followed by eZ Systems AS ([`11964d8`](https://github.com/se7enxweb/ezwebin/commit/11964d8))

1 version, merge or metadata commit not listed.

## v6.0.15 (2026-10-02)

**Removed**

- YUI from the ezwebin design; its date fields use Exponential UI's calendar ([`d081367`](https://github.com/se7enxweb/ezwebin/commit/d081367)) Upgrade note.

## v6.0.14 (2026-10-01)

**Updated**

- The date and date/time fields of the ezwebin design use Exponential UI's calendar, exp::datepicker, when expui is active and load YUI's calendar only without it, so that editing on the site runs on jQuery 4. ([`af489c3`](https://github.com/se7enxweb/ezwebin/commit/af489c3)) Upgrade note.
- Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback. ([`7962d1e`](https://github.com/se7enxweb/ezwebin/commit/7962d1e))

1 version, merge or metadata commit not listed.

## v6.0.13 (2026-10-01)

**Updated**

- Updated the edit page's collapsible attribute groups for jQuery 4, so that they open and close with .on() instead of the deprecated click shorthand. ([`00d0663`](https://github.com/se7enxweb/ezwebin/commit/00d0663)) Upgrade note.

1 version, merge or metadata commit not listed.

## v6.0.12 (2026-09-30)

**Updated**

- Fixed: The blog archive operator lists the months on Oracle too ([`c41f506`](https://github.com/se7enxweb/ezwebin/commit/c41f506))

1 version, merge or metadata commit not listed.

## v6.0.11 (2026-09-30)

**Updated**

- The description calls the product Exponential ([`a1ad269`](https://github.com/se7enxweb/ezwebin/commit/a1ad269))

1 version, merge or metadata commit not listed.

## v6.0.10 (2026-09-30)

**Updated**

- The German translation covers the RSS export, import and list pages, the order confirmation summary and totals, the blog post tags and the document import message ([`19d949b`](https://github.com/se7enxweb/ezwebin/commit/19d949b))

1 version, merge or metadata commit not listed.

## v6.0.9 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`66df8ca`](https://github.com/se7enxweb/ezwebin/commit/66df8ca))

1 version, merge or metadata commit not listed.

## v6.0.8 (2026-09-27)

**Updated**

- Fixed: Error pages show their own error in the page title, because the page layout keys its per-URI caches by error type and number as well ([`55c66ea`](https://github.com/se7enxweb/ezwebin/commit/55c66ea))

1 version, merge or metadata commit not listed.

## v6.0.7 (2026-09-27)

**Updated**

- Fixed: The forgot password page no longer tells whether an address has an account and escapes what it prints ([`eede2b5`](https://github.com/se7enxweb/ezwebin/commit/eede2b5))

1 version, merge or metadata commit not listed.

## v6.0.6 (2026-09-27)

**Updated**

- The extension states its version, license and website ([`2771674`](https://github.com/se7enxweb/ezwebin/commit/2771674))

## v6.0.5 (2026-09-27)

**Updated**

- extension.xml and ezinfo.php name the license in full, GNU General Public License v2.0 (or any later version) ([`76a541c`](https://github.com/se7enxweb/ezwebin/commit/76a541c))

1 version, merge or metadata commit not listed.

## v6.0.4 (2026-09-27)

**Added**

- ezinfo.php reports the extension's name, version, copyright and license ([`f35962f`](https://github.com/se7enxweb/ezwebin/commit/f35962f))

**Updated**

- Visible texts and their translations name Exponential ([`276cc5b`](https://github.com/se7enxweb/ezwebin/commit/276cc5b))

1 version, merge or metadata commit not listed.

## v6.0.3 (2026-07-18)

**Removed**

- Remove XHTML self-closing slashes and obsolete type attributes ([`f2725ca`](https://github.com/se7enxweb/ezwebin/commit/f2725ca)) Upgrade note.

## v6.0.2 (2026-04-23)

**Updated**

- Fix path check and page depth calculation ([`f215980`](https://github.com/se7enxweb/ezwebin/commit/f215980))

1 version, merge or metadata commit not listed.

## v6.0.1 (2024-01-28)

**Updated**

- Mass updates from parent repository ezwebin-ezpackage ([`09c63ec`](https://github.com/se7enxweb/ezwebin/commit/09c63ec))

## v6.0.0 (2024-01-28)

2 version, merge or metadata commits not listed.

## v5.3.2 (2014-05-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.1 (2014-05-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.0 (2014-05-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.8.0 (2011-03-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.7.0 (2010-09-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.5.0 (2009-09-28)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.4-0 (2009-03-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.3-0 (2007-12-10)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.1-1 (2007-04-02)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## Related pages

- [Feature page](../../features/6.0/extensions/ezwebin.md)
- [Chronicle](../../history/extensions/ezwebin.md)
- [Change ledger](../../history/ledger/ezwebin.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2023-12](../../history/extensions/months/2023-12.md), [2024-01](../../history/extensions/months/2024-01.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-04](../../history/extensions/months/2026-04.md), [2026-07](../../history/extensions/months/2026-07.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
