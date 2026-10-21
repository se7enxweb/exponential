# xrowmetadata (Metadaten und Sitemaps): Chronik

Die Erweiterung für Metadaten und Sitemaps erhielt im August 2026 ein Open-Graph-Bild (1.3.6, 1.3.7), im Herbst dann Korrekturen für dauerhaft laufende Worker, Übersetzungen und jQuery 4 sowie eine Korrektur des Sitemap-Cronjobs. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/xrowmetadata.md).

Diese Seite führt **jede der 24 Änderungen** des Repositorys `xrowmetadata` zwischen 2023-12-24 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/xrowmetadata.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/xrowmetadata.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/xrowmetadata.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 10 |
| Fehlerbehebung | 1 |
| Upgrade-Hinweis | 1 |
| Dokumentation | 4 |
| Werkzeuge | 2 |
| Release | 3 |
| ohne Nutzen für Anwender | 3 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-01-29 | v1.3.5 | [`828fcd5`](https://github.com/se7enxweb/xrowmetadata/commit/828fcd5) |
| 2026-08-06 | v1.3.6 | [`51ae4c6`](https://github.com/se7enxweb/xrowmetadata/commit/51ae4c6) |
| 2026-08-07 | v1.3.7 | [`603e137`](https://github.com/se7enxweb/xrowmetadata/commit/603e137) |
| 2026-09-22 | v1.4.0 | [`9e91560`](https://github.com/se7enxweb/xrowmetadata/commit/9e91560) |
| 2026-09-27 | v1.4.1 | [`3c0e57e`](https://github.com/se7enxweb/xrowmetadata/commit/3c0e57e) |
| 2026-09-30 | v1.4.2 | [`7cd1a57`](https://github.com/se7enxweb/xrowmetadata/commit/7cd1a57) |
| 2026-10-01 | v1.4.3 | [`dd5a18a`](https://github.com/se7enxweb/xrowmetadata/commit/dd5a18a) |
| 2026-10-02 | v1.4.4 | [`ec158f9`](https://github.com/se7enxweb/xrowmetadata/commit/ec158f9) |

## Zeitleiste

### 2023-12

Der Monat über alle Erweiterungen: [Dezember 2023](monate/2023-12.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowmetadata.md#2023-12-1-änderung).

- 2023-12-24 [`8af4413`](https://github.com/se7enxweb/xrowmetadata/commit/8af4413) (ohne Nutzen für Anwender) Create FUNDING.yml

### 2024-01

Der Monat über alle Erweiterungen: [Januar 2024](monate/2024-01.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowmetadata.md#2024-01-3-änderungen).

- 2024-01-28 [`c54b6bb`](https://github.com/se7enxweb/xrowmetadata/commit/c54b6bb) (ohne Nutzen für Anwender) Added github funding information
- 2024-01-29 [`dc7ee86`](https://github.com/se7enxweb/xrowmetadata/commit/dc7ee86) (Werkzeuge) Update composer.json
- 2024-01-29 [`828fcd5`](https://github.com/se7enxweb/xrowmetadata/commit/828fcd5) (Dokumentation) Update and rename README.txt to README.md **Release v1.3.5.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowmetadata.md#2026-03-1-änderung).

- 2026-03-02 [`b80d85f`](https://github.com/se7enxweb/xrowmetadata/commit/b80d85f) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-08

Der Monat über alle Erweiterungen: [August 2026](monate/2026-08.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowmetadata.md#2026-08-7-änderungen).

- 2026-08-06 [`4a26f81`](https://github.com/se7enxweb/xrowmetadata/commit/4a26f81) (Funktion) Open Graph image support to the xrowmetadata datatype  - Extended xrowMetaData struct with og_image, width, height, alt and type. - xrowMetaDataType now persists og_image in data_text and falls back to class-level data_text5 default. - Added class and object edit templates for selecting the Open Graph image object ID.
- 2026-08-06 [`fbe1865`](https://github.com/se7enxweb/xrowmetadata/commit/fbe1865) (Funktion) Object-relation style GUI for Open Graph image selection in xrowmetadata  - xrowMetaDataType now supports custom HTTP actions for both object and class attributes. - Replaced numeric ID inputs with a browse/remove object relation interface for og_image. - Class-level default and per-object override both use data_int4 / data_t...
- 2026-08-06 [`737cb6f`](https://github.com/se7enxweb/xrowmetadata/commit/737cb6f) (Funktion) Show no-relation message when no Open Graph image is selected
- 2026-08-06 [`23580ee`](https://github.com/se7enxweb/xrowmetadata/commit/23580ee) (Funktion) class-level Open Graph image content for xrowmetadata class attributes
- 2026-08-06 [`1493317`](https://github.com/se7enxweb/xrowmetadata/commit/1493317) (Funktion) Simplify class-level OG image display and avoid content fetches in class template
- 2026-08-06 [`51ae4c6`](https://github.com/se7enxweb/xrowmetadata/commit/51ae4c6) (Funktion) Open Graph image preview and class-level default fallback to v1.3.6 **Release v1.3.6.**
- 2026-08-07 [`603e137`](https://github.com/se7enxweb/xrowmetadata/commit/603e137) (Funktion) bump xrowmetadata version to 1.3.7 **Release v1.3.7.**

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowmetadata.md#2026-09-5-änderungen).

- 2026-09-22 [`9e91560`](https://github.com/se7enxweb/xrowmetadata/commit/9e91560) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.4.0.**
- 2026-09-27 [`3c0e57e`](https://github.com/se7enxweb/xrowmetadata/commit/3c0e57e) (Dokumentation) The extension states its version, license and website **Release v1.4.1.**
- 2026-09-30 [`9154723`](https://github.com/se7enxweb/xrowmetadata/commit/9154723) (Dokumentation) The about page names the extension Xrow Meta Data
- 2026-09-30 [`5c3bf97`](https://github.com/se7enxweb/xrowmetadata/commit/5c3bf97) (Dokumentation) The description calls the product Exponential
- 2026-09-30 [`7cd1a57`](https://github.com/se7enxweb/xrowmetadata/commit/7cd1a57) (Release) Version 1.4.2 **Release v1.4.2.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowmetadata.md#2026-10-7-änderungen).

- 2026-10-01 [`65de202`](https://github.com/se7enxweb/xrowmetadata/commit/65de202) (Funktion) An English translation catalogue, and German for the canonical link, Open Graph and page texts
- 2026-10-01 [`d824416`](https://github.com/se7enxweb/xrowmetadata/commit/d824416) (Upgrade-Hinweis) Updated the metadata field's "more" checkbox for jQuery 4, so that it uses .on() instead of the deprecated click shorthand.
- 2026-10-01 [`dd5a18a`](https://github.com/se7enxweb/xrowmetadata/commit/dd5a18a) (Release) Version 1.4.3 **Release v1.4.3.**
- 2026-10-02 [`86ff6bb`](https://github.com/se7enxweb/xrowmetadata/commit/86ff6bb) (Werkzeuge) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`1e51a37`](https://github.com/se7enxweb/xrowmetadata/commit/1e51a37) (Funktion) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`d9bac19`](https://github.com/se7enxweb/xrowmetadata/commit/d9bac19) (Funktion) The sitemap cronjob prints the name of each file it wrote instead of failing on the file object, and the news sitemap stops quietly when the news subtree is empty
- 2026-10-02 [`ec158f9`](https://github.com/se7enxweb/xrowmetadata/commit/ec158f9) (Release) Version 1.4.4 **Release v1.4.4.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/xrowmetadata.md)
- [Release Notes](../../../changelogs/extensions/xrowmetadata.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/xrowmetadata.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
