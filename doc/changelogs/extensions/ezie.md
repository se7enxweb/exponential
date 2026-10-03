# ezie (image editor): release notes

What each release of `ezie` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/ezie.md); the story is in the [chronicle](../../history/extensions/ezie.md).

## v6.0.8 (2026-10-02)

**Maintenance, documentation and packaging**

- The command line scripts, cronjob parts and module views are classes the files call ([`5447227`](https://github.com/se7enxweb/ezie/commit/5447227))
- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`ce35086`](https://github.com/se7enxweb/ezie/commit/ce35086))

1 version, merge or metadata commit not listed.

## v6.0.7 (2026-10-01)

**Updated**

- Updated the image editor for jQuery 4 and jQuery UI 1.14, so that opening it, its tools, undo, the selection tool and quitting without saving work without jQuery Migrate warnings. ([`6292484`](https://github.com/se7enxweb/ezie/commit/6292484)) Upgrade note.

1 version, merge or metadata commit not listed.

## v6.0.6 (2026-09-30)

**Updated**

- Fixed: The image editor no longer fails on every image when the site allows WebP output ([`0e69bbf`](https://github.com/se7enxweb/ezie/commit/0e69bbf))
- Fixed: The GD handler of the image editor runs on PHP 8.5 without deprecations ([`d0757cd`](https://github.com/se7enxweb/ezie/commit/d0757cd))
- Fixed: A horizontal flip with ImageMagick flips only the selection ([`169df3d`](https://github.com/se7enxweb/ezie/commit/169df3d))
- Fixed: The watermark tool only reads images from the watermark folder ([`abe9847`](https://github.com/se7enxweb/ezie/commit/abe9847))
- Fixed: The image editor checks who edits which image and answers errors as JSON ([`4b2632e`](https://github.com/se7enxweb/ezie/commit/4b2632e))
- Fixed: Working folders the image editor left behind are removed ([`d592cb7`](https://github.com/se7enxweb/ezie/commit/d592cb7))
- Fixed: The image editor loads the image again with jQuery 3 ([`9e33a9a`](https://github.com/se7enxweb/ezie/commit/9e33a9a)) Upgrade note.
- Fixed: Selecting, cropping and placing a watermark work with jQuery 3 ([`34d8504`](https://github.com/se7enxweb/ezie/commit/34d8504)) Upgrade note.
- Fixed: The thumbnail box of the image editor can be detached and attached again ([`4cbd5e6`](https://github.com/se7enxweb/ezie/commit/4cbd5e6))
- Fixed: The image editor opens above the admin columns and fits the window ([`d85455a`](https://github.com/se7enxweb/ezie/commit/d85455a))
- Fixed: Long option panels of the image editor scroll instead of running out of the window ([`810ad16`](https://github.com/se7enxweb/ezie/commit/810ad16))
- Fixed: The slider styles of the image editor no longer request missing images ([`8e72ba7`](https://github.com/se7enxweb/ezie/commit/8e72ba7))
- Fixed: The image editor's tool handlers are not added again each time it opens ([`1b9a8d8`](https://github.com/se7enxweb/ezie/commit/1b9a8d8))
- Fixed: The selection options of the image editor work without throwing an error ([`69f1402`](https://github.com/se7enxweb/ezie/commit/69f1402))
- The image editor sends the form token as a header as well ([`2ea46af`](https://github.com/se7enxweb/ezie/commit/2ea46af))
- Fixed: A failed image editor action shows the server's reason and keeps the editor open ([`f0c3872`](https://github.com/se7enxweb/ezie/commit/f0c3872))
- Fixed: Quitting the image editor asks a translated question, and only when there are changes ([`d9f20e8`](https://github.com/se7enxweb/ezie/commit/d9f20e8))
- Fixed: After Save & Close the edit form shows the saved image wherever the attribute is ([`0b8eab0`](https://github.com/se7enxweb/ezie/commit/0b8eab0))
- Fixed: The image editor's error dialog grows with the server's message ([`e828a1e`](https://github.com/se7enxweb/ezie/commit/e828a1e))

**Removed**

- The blur, levels and saturation views of the image editor, which never worked ([`98dea8f`](https://github.com/se7enxweb/ezie/commit/98dea8f)) Upgrade note.

**Maintenance, documentation and packaging**

- The known issues describe WebP images, the draft check and the JSON errors ([`1a141f5`](https://github.com/se7enxweb/ezie/commit/1a141f5))

1 version, merge or metadata commit not listed.

## v6.0.5 (2026-09-30)

**Maintenance, documentation and packaging**

- The description calls the product Exponential ([`c5ce8e2`](https://github.com/se7enxweb/ezie/commit/c5ce8e2))

1 version, merge or metadata commit not listed.

## v6.0.4 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`407daf8`](https://github.com/se7enxweb/ezie/commit/407daf8))

1 version, merge or metadata commit not listed.

## v6.0.3 (2026-09-27)

**Updated**

- The image editor no longer requests jquery-migrate-1.1.1.min.js ([`38a6cfa`](https://github.com/se7enxweb/ezie/commit/38a6cfa))

**Maintenance, documentation and packaging**

- The extension states its version, license and website ([`e5fa60d`](https://github.com/se7enxweb/ezie/commit/e5fa60d))

1 version, merge or metadata commit not listed.

## v6.0.2 (2024-03-05)

**Updated**

- Bugfix for missing closing tag on version ([`b4d4639`](https://github.com/se7enxweb/ezie/commit/b4d4639))

## v6.0.1 (2024-01-29)

**Maintenance, documentation and packaging**

- Update composer.json license value with valid text ([`cc2d7f5`](https://github.com/se7enxweb/ezie/commit/cc2d7f5))
- Update composer.json update homepage url ([`7d035ee`](https://github.com/se7enxweb/ezie/commit/7d035ee))

## v6.0.0 (2024-01-28)

**Maintenance, documentation and packaging**

- Update composer.json switched package vendor ([`3fcc409`](https://github.com/se7enxweb/ezie/commit/3fcc409))
- Update composer.json replaced vendor name and package version ([`5014666`](https://github.com/se7enxweb/ezie/commit/5014666))

2 version, merge or metadata commits not listed.

## v5.3.2 (2018-09-06)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.1 (2014-05-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v5.3.0 (2014-05-17)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.1.0 (2011-03-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## 1.0.0 (2010-09-24)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## Related

* [Feature page](../../features/6.0/extensions/ezie.md)
* [Chronicle](../../history/extensions/ezie.md)
* [Change ledger](../../history/ledger/ezie.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
