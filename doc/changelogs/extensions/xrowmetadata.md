# xrowmetadata (meta data and sitemaps): release notes

Read this page before you install or update `xrowmetadata`, or to find out which release brought a change.

What each release of `xrowmetadata` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/xrowmetadata.md); the story is in the [chronicle](../../history/extensions/xrowmetadata.md).

## v1.4.4 (2026-10-02)

**Updated**

- The commands and cronjob parts list a description of what they do ([`1e51a37`](https://github.com/se7enxweb/xrowmetadata/commit/1e51a37))
- The sitemap cronjob prints the name of each file it wrote instead of failing on the file object, and the news sitemap stops quietly when the news subtree is empty ([`d9bac19`](https://github.com/se7enxweb/xrowmetadata/commit/d9bac19))
- The command line scripts, cronjob parts and module views are classes the files call ([`86ff6bb`](https://github.com/se7enxweb/xrowmetadata/commit/86ff6bb))

1 version, merge or metadata commit not listed.

## v1.4.3 (2026-10-01)

**Added**

- An English translation catalogue, and German for the canonical link, Open Graph and page texts ([`65de202`](https://github.com/se7enxweb/xrowmetadata/commit/65de202))

**Updated**

- Updated the metadata field's "more" checkbox for jQuery 4, so that it uses .on() instead of the deprecated click shorthand. ([`d824416`](https://github.com/se7enxweb/xrowmetadata/commit/d824416)) Upgrade note.

1 version, merge or metadata commit not listed.

## v1.4.2 (2026-09-30)

**Updated**

- The about page names the extension Xrow Meta Data ([`9154723`](https://github.com/se7enxweb/xrowmetadata/commit/9154723))
- The description calls the product Exponential ([`5c3bf97`](https://github.com/se7enxweb/xrowmetadata/commit/5c3bf97))

1 version, merge or metadata commit not listed.

## v1.4.1 (2026-09-27)

**Updated**

- The extension states its version, license and website ([`3c0e57e`](https://github.com/se7enxweb/xrowmetadata/commit/3c0e57e))

## v1.4.0 (2026-09-22)

**Updated**

- Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`9e91560`](https://github.com/se7enxweb/xrowmetadata/commit/9e91560))

## v1.3.7 (2026-08-07)

**Updated**

- Bump xrowmetadata version to 1.3.7 ([`603e137`](https://github.com/se7enxweb/xrowmetadata/commit/603e137))

## v1.3.6 (2026-08-06)

**Added**

- Open Graph image support to the xrowmetadata datatype  - Extended xrowMetaData struct with og_image, width, height, alt and type. - xrowMetaDataType now persists og_image in data_text and falls back to class-level data_text5 default. - Added class and object edit templates for selecting the Open Graph image object ID. ([`4a26f81`](https://github.com/se7enxweb/xrowmetadata/commit/4a26f81))
- class-level Open Graph image content for xrowmetadata class attributes ([`23580ee`](https://github.com/se7enxweb/xrowmetadata/commit/23580ee))

**Updated**

- Object-relation style GUI for Open Graph image selection in xrowmetadata  - xrowMetaDataType now supports custom HTTP actions for both object and class attributes. - Replaced numeric ID inputs with a browse/remove object relation interface for og_image. - Class-level default and per-object override both use data_int4 / data_t... ([`fbe1865`](https://github.com/se7enxweb/xrowmetadata/commit/fbe1865))
- Show no-relation message when no Open Graph image is selected ([`737cb6f`](https://github.com/se7enxweb/xrowmetadata/commit/737cb6f))
- Simplify class-level OG image display and avoid content fetches in class template ([`1493317`](https://github.com/se7enxweb/xrowmetadata/commit/1493317))
- Open Graph image preview and class-level default fallback to v1.3.6 ([`51ae4c6`](https://github.com/se7enxweb/xrowmetadata/commit/51ae4c6))

1 version, merge or metadata commit not listed.

## v1.3.5 (2024-01-29)

**Updated**

- Update composer.json ([`dc7ee86`](https://github.com/se7enxweb/xrowmetadata/commit/dc7ee86))
- Update and rename README.txt to README.md ([`828fcd5`](https://github.com/se7enxweb/xrowmetadata/commit/828fcd5))

2 version, merge or metadata commits not listed.

## Related pages

- [Feature page](../../features/6.0/extensions/xrowmetadata.md)
- [Chronicle](../../history/extensions/xrowmetadata.md)
- [Change ledger](../../history/ledger/xrowmetadata.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2023-12](../../history/extensions/months/2023-12.md), [2024-01](../../history/extensions/months/2024-01.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-08](../../history/extensions/months/2026-08.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
