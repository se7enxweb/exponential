# ezautosave (draft autosave): release notes

Read this page before you install or update `ezautosave`, or to find out which release brought a change.

What each release of `ezautosave` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/ezautosave.md); the story is in the [chronicle](../../history/extensions/ezautosave.md).

See [jQuery 4 and YUI removal](../../features/6.0/jquery4-and-yui-removal.md) and [YUI removed](../../bc/6.0/yui-removal.md).

## v6.0.8 (2026-10-02)

**Updated**

- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`1abe15d`](https://github.com/se7enxweb/ezautosave/commit/1abe15d))

1 version, merge or metadata commit not listed.

## v6.0.7 (2026-10-02)

**Removed**

- The front-end YUI autosave; ezautosave runs on Exponential UI alone ([`b7febda`](https://github.com/se7enxweb/ezautosave/commit/b7febda)) Upgrade note.

## v6.0.6 (2026-10-02)

**Removed**

- The admin edit form's YUI autosave and preview; it autosaves and previews on Exponential UI alone ([`2bc1ef1`](https://github.com/se7enxweb/ezautosave/commit/2bc1ef1)) Upgrade note.

## v6.0.5 (2026-10-01)

**Updated**

- Autosave and the draft preview run on Exponential UI's exp::autosave when expui is active, in the admin and the ezwebin templates, and keep the YUI version as the fallback, so that drafts are saved on jQuery 4. ([`f4e9bee`](https://github.com/se7enxweb/ezautosave/commit/f4e9bee)) Upgrade note.
- Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback. ([`abc3c4c`](https://github.com/se7enxweb/ezautosave/commit/abc3c4c))

1 version, merge or metadata commit not listed.

## v6.0.4 (2026-09-30)

**Updated**

- The description calls the product Exponential ([`0fbf2b7`](https://github.com/se7enxweb/ezautosave/commit/0fbf2b7))

1 version, merge or metadata commit not listed.

## v6.0.3 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`f7ac3a5`](https://github.com/se7enxweb/ezautosave/commit/f7ac3a5))

1 version, merge or metadata commit not listed.

## v6.0.2 (2026-09-27)

**Updated**

- The Composer package declares GPL-2.0-or-later, as the extension's own metadata does, in version 6.0.2 ([`aff1aed`](https://github.com/se7enxweb/ezautosave/commit/aff1aed))

## v6.0.1 (2026-09-27)

**Updated**

- The extension states its version, license and website ([`fc1d28d`](https://github.com/se7enxweb/ezautosave/commit/fc1d28d))

1 version, merge or metadata commit not listed.

## v6.0.0 (2024-01-28)

**Updated**

- Update composer.json switched package vendor ([`50b4386`](https://github.com/se7enxweb/ezautosave/commit/50b4386))
- Update composer.json switched package vendor and update package name to remove deprecated -ls switch ([`a30f09d`](https://github.com/se7enxweb/ezautosave/commit/a30f09d))

2 version, merge or metadata commits not listed.

## v5.4.4 (2019-02-18)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.4.3 (2018-12-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.5.1-rc1 (2015-03-30)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.5.1 (2015-03-30)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.4.2 (2015-03-30)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.5-rc1 (2015-03-30)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.5 (2015-03-30)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.1 (2015-02-19)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.0 (2014-05-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## Related pages

- [Feature page](../../features/6.0/extensions/ezautosave.md)
- [Chronicle](../../history/extensions/ezautosave.md)
- [Change ledger](../../history/ledger/ezautosave.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2023-12](../../history/extensions/months/2023-12.md), [2024-01](../../history/extensions/months/2024-01.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
