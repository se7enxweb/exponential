# xrowmetadata (meta data and sitemaps): chronicle

The meta data and sitemap extension gained an Open Graph image in August 2026 (1.3.6, 1.3.7), then persistent worker, translation and jQuery 4 fixes and a sitemap cronjob fix in the autumn. See the [feature page](../../features/6.0/extensions/xrowmetadata.md).

This page lists **every one of the 24 changes** of the repository `xrowmetadata` between 2023-12-24 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/xrowmetadata.md); what each release contains is in the [release notes](../../changelogs/extensions/xrowmetadata.md); how to use the extension is on its [feature page](../../features/6.0/extensions/xrowmetadata.md).

| Kind | Changes |
|---|---|
| feature | 10 |
| fix | 1 |
| upgrade note | 1 |
| docs | 4 |
| tooling | 2 |
| release | 3 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-29 | v1.3.5 | [`828fcd5`](https://github.com/se7enxweb/xrowmetadata/commit/828fcd5) |
| 2026-08-06 | v1.3.6 | [`51ae4c6`](https://github.com/se7enxweb/xrowmetadata/commit/51ae4c6) |
| 2026-08-07 | v1.3.7 | [`603e137`](https://github.com/se7enxweb/xrowmetadata/commit/603e137) |
| 2026-09-22 | v1.4.0 | [`9e91560`](https://github.com/se7enxweb/xrowmetadata/commit/9e91560) |
| 2026-09-27 | v1.4.1 | [`3c0e57e`](https://github.com/se7enxweb/xrowmetadata/commit/3c0e57e) |
| 2026-09-30 | v1.4.2 | [`7cd1a57`](https://github.com/se7enxweb/xrowmetadata/commit/7cd1a57) |
| 2026-10-01 | v1.4.3 | [`dd5a18a`](https://github.com/se7enxweb/xrowmetadata/commit/dd5a18a) |
| 2026-10-02 | v1.4.4 | [`ec158f9`](https://github.com/se7enxweb/xrowmetadata/commit/ec158f9) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/xrowmetadata.md#2023-12-1-changes).

- 2023-12-24 [`8af4413`](https://github.com/se7enxweb/xrowmetadata/commit/8af4413) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/xrowmetadata.md#2024-01-3-changes).

- 2024-01-28 [`c54b6bb`](https://github.com/se7enxweb/xrowmetadata/commit/c54b6bb) (no user benefit) Added github funding information
- 2024-01-29 [`dc7ee86`](https://github.com/se7enxweb/xrowmetadata/commit/dc7ee86) (tooling) Update composer.json
- 2024-01-29 [`828fcd5`](https://github.com/se7enxweb/xrowmetadata/commit/828fcd5) (docs) Update and rename README.txt to README.md **Release v1.3.5.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/xrowmetadata.md#2026-03-1-changes).

- 2026-03-02 [`b80d85f`](https://github.com/se7enxweb/xrowmetadata/commit/b80d85f) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-08

The month across all extensions: [August 2026](months/2026-08.md). [Ledger of this month](../ledger/xrowmetadata.md#2026-08-7-changes).

- 2026-08-06 [`4a26f81`](https://github.com/se7enxweb/xrowmetadata/commit/4a26f81) (feature) Open Graph image support to the xrowmetadata datatype  - Extended xrowMetaData struct with og_image, width, height, alt and type. - xrowMetaDataType now persists og_image in data_text and falls back to class-level data_text5 default. - Added class and object edit templates for selecting the Open Graph image object ID.
- 2026-08-06 [`fbe1865`](https://github.com/se7enxweb/xrowmetadata/commit/fbe1865) (feature) Object-relation style GUI for Open Graph image selection in xrowmetadata  - xrowMetaDataType now supports custom HTTP actions for both object and class attributes. - Replaced numeric ID inputs with a browse/remove object relation interface for og_image. - Class-level default and per-object override both use data_int4 / data_t...
- 2026-08-06 [`737cb6f`](https://github.com/se7enxweb/xrowmetadata/commit/737cb6f) (feature) Show no-relation message when no Open Graph image is selected
- 2026-08-06 [`23580ee`](https://github.com/se7enxweb/xrowmetadata/commit/23580ee) (feature) class-level Open Graph image content for xrowmetadata class attributes
- 2026-08-06 [`1493317`](https://github.com/se7enxweb/xrowmetadata/commit/1493317) (feature) Simplify class-level OG image display and avoid content fetches in class template
- 2026-08-06 [`51ae4c6`](https://github.com/se7enxweb/xrowmetadata/commit/51ae4c6) (feature) Open Graph image preview and class-level default fallback to v1.3.6 **Release v1.3.6.**
- 2026-08-07 [`603e137`](https://github.com/se7enxweb/xrowmetadata/commit/603e137) (feature) bump xrowmetadata version to 1.3.7 **Release v1.3.7.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/xrowmetadata.md#2026-09-5-changes).

- 2026-09-22 [`9e91560`](https://github.com/se7enxweb/xrowmetadata/commit/9e91560) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.4.0.**
- 2026-09-27 [`3c0e57e`](https://github.com/se7enxweb/xrowmetadata/commit/3c0e57e) (docs) The extension states its version, license and website **Release v1.4.1.**
- 2026-09-30 [`9154723`](https://github.com/se7enxweb/xrowmetadata/commit/9154723) (docs) The about page names the extension Xrow Meta Data
- 2026-09-30 [`5c3bf97`](https://github.com/se7enxweb/xrowmetadata/commit/5c3bf97) (docs) The description calls the product Exponential
- 2026-09-30 [`7cd1a57`](https://github.com/se7enxweb/xrowmetadata/commit/7cd1a57) (release) Version 1.4.2 **Release v1.4.2.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/xrowmetadata.md#2026-10-7-changes).

- 2026-10-01 [`65de202`](https://github.com/se7enxweb/xrowmetadata/commit/65de202) (feature) An English translation catalogue, and German for the canonical link, Open Graph and page texts
- 2026-10-01 [`d824416`](https://github.com/se7enxweb/xrowmetadata/commit/d824416) (upgrade note) Updated the metadata field's "more" checkbox for jQuery 4, so that it uses .on() instead of the deprecated click shorthand.
- 2026-10-01 [`dd5a18a`](https://github.com/se7enxweb/xrowmetadata/commit/dd5a18a) (release) Version 1.4.3 **Release v1.4.3.**
- 2026-10-02 [`86ff6bb`](https://github.com/se7enxweb/xrowmetadata/commit/86ff6bb) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`1e51a37`](https://github.com/se7enxweb/xrowmetadata/commit/1e51a37) (feature) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`d9bac19`](https://github.com/se7enxweb/xrowmetadata/commit/d9bac19) (feature) The sitemap cronjob prints the name of each file it wrote instead of failing on the file object, and the news sitemap stops quietly when the news subtree is empty
- 2026-10-02 [`ec158f9`](https://github.com/se7enxweb/xrowmetadata/commit/ec158f9) (release) Version 1.4.4 **Release v1.4.4.**

## Related

* [Feature page](../../features/6.0/extensions/xrowmetadata.md)
* [Release notes](../../changelogs/extensions/xrowmetadata.md)
* [Change ledger](../ledger/xrowmetadata.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
