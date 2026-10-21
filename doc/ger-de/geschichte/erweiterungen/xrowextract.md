# xrowextract (Export und Import): Chronik

`xrowextract` begann 2008 als kleine Seite für den CSV-Export und änderte sich bis zum Sommer 2026 selten: ein Wechsel des Vendors im Februar 2024, Korrekturen für PHP 8 im Jahr 2024 (Releases 2.3.1 bis 2.4.2). Am 29. September 2026 wurde es an einem Tag mit 130 zusammengeführten Änderungen zu einem Werkzeug für den Datenaustausch umgebaut: Vorschauen, JSON und XML, Archive ganzer Websites, Filter, Voreinstellungen, Hintergrundjobs, ein Importer für CSV, JSON und XML in jeder Größe, Inhaltspakete, geplante Läufe mit Auslieferung über SFTP, S3 und WebDAV und ein Spaltenmanifest. Am nächsten Tag wurde es gehärtet (PHPStan Level 8, eine Release-Prüfung, Integrationstests, Korrekturen für Daten, die nicht UTF-8 sind, und für fehlerhafte Eingaben), und am 2. Oktober wurden seine Befehle zu Klassen. Was Sie damit tun können, steht auf der [Funktionsseite](../../funktionen/6.0/erweiterungen/xrowextract.md); die Referenz ist die [Spezifikation](../../spezifikationen/6.0/xrowextract.md).

Diese Seite führt **jede der 245 Änderungen** des Repositorys `xrowextract` zwischen 2024-01-28 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/xrowextract.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/xrowextract.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/xrowextract.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 97 |
| Fehlerbehebung | 64 |
| Sicherheit | 9 |
| Leistung | 5 |
| Upgrade-Hinweis | 2 |
| Dokumentation | 3 |
| Werkzeuge | 33 |
| Release | 5 |
| ohne Nutzen für Anwender | 27 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-02-28 | v2.3.0 | [`c0d37fb`](https://github.com/se7enxweb/xrowextract/commit/c0d37fb) |
| 2024-03-01 | v2.3.1 | [`ba8ddfa`](https://github.com/se7enxweb/xrowextract/commit/ba8ddfa) |
| 2024-04-02 | v2.3.2 | [`f6de469`](https://github.com/se7enxweb/xrowextract/commit/f6de469) |
| 2024-07-01 | v2.4.0 | [`c518dac`](https://github.com/se7enxweb/xrowextract/commit/c518dac) |
| 2024-07-01 | v2.4.1 | [`222b21c`](https://github.com/se7enxweb/xrowextract/commit/222b21c) |
| 2024-08-22 | v2.4.2 | [`b4e5fc1`](https://github.com/se7enxweb/xrowextract/commit/b4e5fc1) |
| 2026-09-22 | v2.5.0 | [`92f6e8c`](https://github.com/se7enxweb/xrowextract/commit/92f6e8c) |
| 2026-09-27 | v2.5.1 | [`7e2d0b4`](https://github.com/se7enxweb/xrowextract/commit/7e2d0b4) |
| 2026-09-28 | v2.5.2 | [`cabd1c1`](https://github.com/se7enxweb/xrowextract/commit/cabd1c1) |
| 2026-09-30 | v2.5.3 | [`414dfe7`](https://github.com/se7enxweb/xrowextract/commit/414dfe7) |
| 2026-09-30 | v2.5.4 | [`e9935e8`](https://github.com/se7enxweb/xrowextract/commit/e9935e8) |
| 2026-10-02 | v2.5.5 | [`26ea832`](https://github.com/se7enxweb/xrowextract/commit/26ea832) |
| 2026-10-02 | v2.5.6 | [`fbea964`](https://github.com/se7enxweb/xrowextract/commit/fbea964) |

## Zeitleiste

### 2024-01

Der Monat über alle Erweiterungen: [Januar 2024](monate/2024-01.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2024-01-1-änderung).

- 2024-01-28 [`07db95b`](https://github.com/se7enxweb/xrowextract/commit/07db95b) (ohne Nutzen für Anwender) Added github funding information

### 2024-02

Der Monat über alle Erweiterungen: [Februar 2024](monate/2024-02.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2024-02-1-änderung).

- 2024-02-28 [`c0d37fb`](https://github.com/se7enxweb/xrowextract/commit/c0d37fb) (Werkzeuge) Update composer.json switch package vendor **Release v2.3.0.**

### 2024-03

Der Monat über alle Erweiterungen: [März 2024](monate/2024-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2024-03-1-änderung).

- 2024-03-01 [`ba8ddfa`](https://github.com/se7enxweb/xrowextract/commit/ba8ddfa) (Werkzeuge) Update composer.json switched vendor on requirements **Release v2.3.1.**

### 2024-04

Der Monat über alle Erweiterungen: [April 2024](monate/2024-04.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2024-04-1-änderung).

- 2024-04-02 [`f6de469`](https://github.com/se7enxweb/xrowextract/commit/f6de469) (Fehlerbehebung) Minor bugfix to prevent fatal error under php 8 **Release v2.3.2.**

### 2024-07

Der Monat über alle Erweiterungen: [Juli 2024](monate/2024-07.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2024-07-2-änderungen).

- 2024-07-01 [`c518dac`](https://github.com/se7enxweb/xrowextract/commit/c518dac) (Funktion) Mass improvments of php8 support and recent testing and refinements **Release v2.4.0.**
- 2024-07-01 [`222b21c`](https://github.com/se7enxweb/xrowextract/commit/222b21c) (Fehlerbehebung) Settings Bugfix for hmregexpline class handler name **Release v2.4.1.**

### 2024-08

Der Monat über alle Erweiterungen: [August 2024](monate/2024-08.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2024-08-2-änderungen).

- 2024-08-22 [`431f2ea`](https://github.com/se7enxweb/xrowextract/commit/431f2ea) (Fehlerbehebung) PHP 8 Bugfix for ezmatrix dayatype
- 2024-08-22 [`b4e5fc1`](https://github.com/se7enxweb/xrowextract/commit/b4e5fc1) (Fehlerbehebung) Bugfixes post integration into default Exponential 6 distribution to clean up main csv module code formating and export error in fetching code **Release v2.4.2.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2026-03-1-änderung).

- 2026-03-02 [`a08416f`](https://github.com/se7enxweb/xrowextract/commit/a08416f) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2026-09-230-änderungen).

- 2026-09-22 [`92f6e8c`](https://github.com/se7enxweb/xrowextract/commit/92f6e8c) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v2.5.0.**
- 2026-09-27 [`2031a58`](https://github.com/se7enxweb/xrowextract/commit/2031a58) (Funktion) The CSV export form writes its line break as <br>, as the installed copy does
- 2026-09-27 [`7e2d0b4`](https://github.com/se7enxweb/xrowextract/commit/7e2d0b4) (Fehlerbehebung) Fixed: The ezinfo.php declares xrowextractInfo with a static info() and states the extension's version, license and website **Release v2.5.1.**
- 2026-09-28 [`8b98792`](https://github.com/se7enxweb/xrowextract/commit/8b98792) (Funktion) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`cabd1c1`](https://github.com/se7enxweb/xrowextract/commit/cabd1c1) (Release) Version 2.5.2 **Release v2.5.2.**
- 2026-09-29 [`8302029`](https://github.com/se7enxweb/xrowextract/commit/8302029) (Fehlerbehebung) Fixed: Every CSV row has the header's columns, the special columns export again, and the export honours read access
- 2026-09-29 [`1cb7203`](https://github.com/se7enxweb/xrowextract/commit/1cb7203) (Funktion) Export handlers for date and time, time, keywords, tags and object relation
- 2026-09-29 [`7f8f503`](https://github.com/se7enxweb/xrowextract/commit/7f8f503) (Funktion) A spreadsheet preview of the export, before downloading it
- 2026-09-29 [`e37f2fd`](https://github.com/se7enxweb/xrowextract/commit/e37f2fd) (Funktion) The CSV view's settings are three cards, and the sidebar explains the tool
- 2026-09-29 [`8fd0744`](https://github.com/se7enxweb/xrowextract/commit/8fd0744) (Fehlerbehebung) Fixed: The in-place preview has every column on every server
- 2026-09-29 [`17b8bf0`](https://github.com/se7enxweb/xrowextract/commit/17b8bf0) (Funktion) The column list is part of the Columns card, and the class list counts the objects of every class
- 2026-09-29 [`e23b382`](https://github.com/se7enxweb/xrowextract/commit/e23b382) (Funktion) A site archive: every class below the chosen nodes in one ZIP or TAR file, one CSV per class
- 2026-09-29 [`2a8a10d`](https://github.com/se7enxweb/xrowextract/commit/2a8a10d) (Fehlerbehebung) Fixed: The site archive downloads whole on servers that end a request by throwing
- 2026-09-29 [`62521c3`](https://github.com/se7enxweb/xrowextract/commit/62521c3) (Funktion) The single class view names the datatype and meta information of every column
- 2026-09-29 [`359275b`](https://github.com/se7enxweb/xrowextract/commit/359275b) (Funktion) A whole site scope for the single class export: every object of a class in one CSV
- 2026-09-29 [`e96fc89`](https://github.com/se7enxweb/xrowextract/commit/e96fc89) (Funktion) Command line exports: ext:xrowextract:csv and ext:xrowextract:archive
- 2026-09-29 [`2f57557`](https://github.com/se7enxweb/xrowextract/commit/2f57557) (Funktion) Password hash and hash type columns for users allowed to export them
- 2026-09-29 [`9bbaf3b`](https://github.com/se7enxweb/xrowextract/commit/9bbaf3b) (Funktion) Three scopes for the single class export, a readable datatype list, and the node is remembered
- 2026-09-29 [`1f3cb82`](https://github.com/se7enxweb/xrowextract/commit/1f3cb82) (Funktion) Content languages for the single class export: counts, all or some, one row per translation
- 2026-09-29 [`b0787c7`](https://github.com/se7enxweb/xrowextract/commit/b0787c7) (Funktion) A column catalogue: attribute formats, grouped special columns, column sets, and an xrowmetadata handler
- 2026-09-29 [`261d094`](https://github.com/se7enxweb/xrowextract/commit/261d094) (Fehlerbehebung) Fixed: Adding a column set, a column or all attributes shows what was added
- 2026-09-29 [`bf68196`](https://github.com/se7enxweb/xrowextract/commit/bf68196) (Funktion) Languages and column choices for the site archive, true translations only, and JSON/XML writers
- 2026-09-29 [`f861e7e`](https://github.com/se7enxweb/xrowextract/commit/f861e7e) (Funktion) JSON and XML output, a Sites set for the archive, and smarter defaults for the single class view
- 2026-09-29 [`fb426d1`](https://github.com/se7enxweb/xrowextract/commit/fb426d1) (Fehlerbehebung) Fixed: The single class view no longer keeps a node and class saved under the old defaults
- 2026-09-29 [`88dba09`](https://github.com/se7enxweb/xrowextract/commit/88dba09) (Fehlerbehebung) Fixed: Node links in the export views open the node
- 2026-09-29 [`e2df53d`](https://github.com/se7enxweb/xrowextract/commit/e2df53d) (Funktion) The other direction of xrowextract exports, a CSV/JSON importer
- 2026-09-29 [`50ebee0`](https://github.com/se7enxweb/xrowextract/commit/50ebee0) (Funktion) An Import tab: upload, column mapping, dry run and apply
- 2026-09-29 [`dbc35a1`](https://github.com/se7enxweb/xrowextract/commit/dbc35a1) (Funktion) German and English strings for the import view
- 2026-09-29 [`739e11b`](https://github.com/se7enxweb/xrowextract/commit/739e11b) (Funktion) A background job runner for large CSV and archive exports
- 2026-09-29 [`b67610e`](https://github.com/se7enxweb/xrowextract/commit/b67610e) (Funktion) A "Run in the background" button and a Jobs page
- 2026-09-29 [`fd7bf82`](https://github.com/se7enxweb/xrowextract/commit/fd7bf82) (Funktion) A Jobs tab, job cards and live progress in the same xe- design
- 2026-09-29 [`f816571`](https://github.com/se7enxweb/xrowextract/commit/f816571) (Funktion) German and English strings for the background jobs feature
- 2026-09-29 [`b42702a`](https://github.com/se7enxweb/xrowextract/commit/b42702a) (Funktion) Filters and a sort order for the exports: dates, section, state, visibility, name, attribute conditions
- 2026-09-29 [`00d174c`](https://github.com/se7enxweb/xrowextract/commit/00d174c) (ohne Nutzen für Anwender) Merge the background export jobs
- 2026-09-29 [`2205203`](https://github.com/se7enxweb/xrowextract/commit/2205203) (ohne Nutzen für Anwender) Merge the CSV and JSON import
- 2026-09-29 [`360fd64`](https://github.com/se7enxweb/xrowextract/commit/360fd64) (Funktion) The single class view's sidebar follows the view, and the translations are whole again
- 2026-09-29 [`f3d116e`](https://github.com/se7enxweb/xrowextract/commit/f3d116e) (Funktion) The import view offers its options before an upload, a template per class, and its own sidebar
- 2026-09-29 [`04ddab3`](https://github.com/se7enxweb/xrowextract/commit/04ddab3) (Fehlerbehebung) Fixed: Choosing the parent for imported objects opens the content tree
- 2026-09-29 [`c3cb542`](https://github.com/se7enxweb/xrowextract/commit/c3cb542) (Funktion) The Jobs page shows who started each job as a linked user bubble, and its queued, started and ended times as a timeline
- 2026-09-29 [`fd9a536`](https://github.com/se7enxweb/xrowextract/commit/fd9a536) (Funktion) The Jobs page shows its totals as tiles: total, completed, running, queued and failed
- 2026-09-29 [`f2c8a9d`](https://github.com/se7enxweb/xrowextract/commit/f2c8a9d) (Funktion) XML as a first-class import format, a one-click sample, and a complete file format reference
- 2026-09-29 [`e82acf2`](https://github.com/se7enxweb/xrowextract/commit/e82acf2) (Funktion) German and English strings for XML, the sample and the reference
- 2026-09-29 [`93005c5`](https://github.com/se7enxweb/xrowextract/commit/93005c5) (ohne Nutzen für Anwender) Merge the XML import format, the import sample and the file format reference
- 2026-09-29 [`7922536`](https://github.com/se7enxweb/xrowextract/commit/7922536) (Fehlerbehebung) Fixed: The import page fits the window again on desktop and mobile
- 2026-09-29 [`e2691e0`](https://github.com/se7enxweb/xrowextract/commit/e2691e0) (Fehlerbehebung) Fixed: A long language name no longer runs into the parent field on the import page
- 2026-09-29 [`0eabe54`](https://github.com/se7enxweb/xrowextract/commit/0eabe54) (Funktion) xrowextract/package supports content packages (.ezpkg)
- 2026-09-29 [`36b078b`](https://github.com/se7enxweb/xrowextract/commit/36b078b) (Funktion) a command line for content packages, ext:xrowextract:package
- 2026-09-29 [`3de6839`](https://github.com/se7enxweb/xrowextract/commit/3de6839) (Funktion) import links to the package template, tabs show Package
- 2026-09-29 [`c8e3912`](https://github.com/se7enxweb/xrowextract/commit/c8e3912) (Funktion) German, English and untranslated strings for content packages
- 2026-09-29 [`d8e51c2`](https://github.com/se7enxweb/xrowextract/commit/d8e51c2) (ohne Nutzen für Anwender) Merge branch 'master' into wip/ezpkg-packages
- 2026-09-29 [`600661c`](https://github.com/se7enxweb/xrowextract/commit/600661c) (Funktion) two more registered extended attribute filters, and a chain point in the language filter
- 2026-09-29 [`2b5601e`](https://github.com/se7enxweb/xrowextract/commit/2b5601e) (Funktion) fetchalias.ini named fetches usable from the CSV export
- 2026-09-29 [`f7ee687`](https://github.com/se7enxweb/xrowextract/commit/f7ee687) (Funktion) several conditions joined with and/or, kernel-level object/tree fields, an exact depth below the node, a second sort, and applying an extended filter or a named fetch
- 2026-09-29 [`3fb29d0`](https://github.com/se7enxweb/xrowextract/commit/3fb29d0) (Funktion) The import preview says which class the rows go into, where, in which language, and names every object
- 2026-09-29 [`8c2f271`](https://github.com/se7enxweb/xrowextract/commit/8c2f271) (ohne Nutzen für Anwender) Merge master (c3cb542, the Jobs page user bubble and timeline; e2691e0, later import fixes) into the advanced-filters branch
- 2026-09-29 [`1728690`](https://github.com/se7enxweb/xrowextract/commit/1728690) (Funktion) Try a sample works with any class, and the reference's XML section starts closed and remembers its state
- 2026-09-29 [`35cb61f`](https://github.com/se7enxweb/xrowextract/commit/35cb61f) (Fehlerbehebung) Fixed: The package template builder no longer publishes on the public site
- 2026-09-29 [`f241dfa`](https://github.com/se7enxweb/xrowextract/commit/f241dfa) (Fehlerbehebung) Fixed: The XML reference text no longer turns its tag names into page markup
- 2026-09-29 [`1b84fae`](https://github.com/se7enxweb/xrowextract/commit/1b84fae) (Fehlerbehebung) Fixed: The file format reference's sections other than XML start open again
- 2026-09-29 [`24e71eb`](https://github.com/se7enxweb/xrowextract/commit/24e71eb) (ohne Nutzen für Anwender) Merge content packages (.ezpkg): inspect, install, export, package template and command line
- 2026-09-29 [`e3f34e6`](https://github.com/se7enxweb/xrowextract/commit/e3f34e6) (Fehlerbehebung) Fixed: The import preview's class summary uses no foreachelse, which the template engine does not know
- 2026-09-29 [`4b676f8`](https://github.com/se7enxweb/xrowextract/commit/4b676f8) (Fehlerbehebung) Fixed: The import and package pages no longer log "Datatype not found" errors
- 2026-09-29 [`4363309`](https://github.com/se7enxweb/xrowextract/commit/4363309) (Leistung) Chunked upload of any size, with resume and per-user isolation
- 2026-09-29 [`2aab54a`](https://github.com/se7enxweb/xrowextract/commit/2aab54a) (Funktion) saved export presets, more powerful than a fetchalias.ini named fetch alone
- 2026-09-29 [`78ddc73`](https://github.com/se7enxweb/xrowextract/commit/78ddc73) (ohne Nutzen für Anwender) Merge advanced export filters, named fetches and saved export presets
- 2026-09-29 [`f54defe`](https://github.com/se7enxweb/xrowextract/commit/f54defe) (Funktion) Fixed: Two strings added by both merged branches appear once in the translations
- 2026-09-29 [`083930a`](https://github.com/se7enxweb/xrowextract/commit/083930a) (Funktion) Every extract view keeps its place when a button reloads the page
- 2026-09-29 [`0b73582`](https://github.com/se7enxweb/xrowextract/commit/0b73582) (Leistung) XML, CSV and JSON import stream a row at a time instead of loading the whole file
- 2026-09-29 [`f3fe4e4`](https://github.com/se7enxweb/xrowextract/commit/f3fe4e4) (Funktion) The import CLI streams any file size and can run as a background job
- 2026-09-29 [`e31e3fc`](https://github.com/se7enxweb/xrowextract/commit/e31e3fc) (Funktion) A large import queues as a background job and reports its size and resume point
- 2026-09-29 [`d3330be`](https://github.com/se7enxweb/xrowextract/commit/d3330be) (Funktion) Chunked upload progress, a queued-job notice and a resume control on the import page
- 2026-09-29 [`7fee4ff`](https://github.com/se7enxweb/xrowextract/commit/7fee4ff) (Funktion) German and English strings for chunked upload, queuing and job resume
- 2026-09-29 [`592ad66`](https://github.com/se7enxweb/xrowextract/commit/592ad66) (Fehlerbehebung) Fixed: After a reload the highlight lands on the block that was being worked in, and is clearly visible
- 2026-09-29 [`46e826a`](https://github.com/se7enxweb/xrowextract/commit/46e826a) (ohne Nutzen für Anwender) master into wip/import-queue (package view, advanced filters, keep-place-on-reload)
- 2026-09-29 [`0570a5b`](https://github.com/se7enxweb/xrowextract/commit/0570a5b) (Fehlerbehebung) Fixed: xrowextract/package uploads a package into the wrong repository
- 2026-09-29 [`476313b`](https://github.com/se7enxweb/xrowextract/commit/476313b) (Funktion) direct .ezpkg/class-XML/object-XML upload, inspection and install on xrowextract/import
- 2026-09-29 [`e989da5`](https://github.com/se7enxweb/xrowextract/commit/e989da5) (Funktion) background job support for large content-package imports
- 2026-09-29 [`374f381`](https://github.com/se7enxweb/xrowextract/commit/374f381) (Funktion) translations for the content-package upload and reference strings
- 2026-09-29 [`0d0b590`](https://github.com/se7enxweb/xrowextract/commit/0d0b590) (ohne Nutzen für Anwender) Merge content packages, class XML and object XML in the import view, with the package format reference
- 2026-09-29 [`f9e0732`](https://github.com/se7enxweb/xrowextract/commit/f9e0732) (Fehlerbehebung) Fixed: --alias-param and --param keep every occurrence, and a node that never resolved no longer fetches silently
- 2026-09-29 [`d52d6e3`](https://github.com/se7enxweb/xrowextract/commit/d52d6e3) (ohne Nutzen für Anwender) Merge master (083930a/592ad66, keeps its place across a reload) into the alias-param-node branch
- 2026-09-29 [`3397d58`](https://github.com/se7enxweb/xrowextract/commit/3397d58) (Sicherheit) Fixed: A package archive is accepted only when every entry is a plain file or folder
- 2026-09-29 [`ed22981`](https://github.com/se7enxweb/xrowextract/commit/ed22981) (Funktion) The import page shows content packages where one looks first
- 2026-09-29 [`63e3731`](https://github.com/se7enxweb/xrowextract/commit/63e3731) (ohne Nutzen für Anwender) master into wip/import-queue (content package / class-XML / object-XML upload, #25)
- 2026-09-29 [`509b644`](https://github.com/se7enxweb/xrowextract/commit/509b644) (ohne Nutzen für Anwender) master into wip/import-queue (content packages shown up front)
- 2026-09-29 [`b0b7d7a`](https://github.com/se7enxweb/xrowextract/commit/b0b7d7a) (Funktion) The Presets card explains itself, and a loaded preset (or one setting of it) can be unloaded
- 2026-09-29 [`b3bcce8`](https://github.com/se7enxweb/xrowextract/commit/b3bcce8) (ohne Nutzen für Anwender) Merge the named fetch parameter fix and the reworked Presets card
- 2026-09-29 [`fd8f7c1`](https://github.com/se7enxweb/xrowextract/commit/fd8f7c1) (ohne Nutzen für Anwender) Merge imports of any size: chunked uploads with resume, streaming, and large imports queued as jobs
- 2026-09-29 [`3858c84`](https://github.com/se7enxweb/xrowextract/commit/3858c84) (Upgrade-Hinweis) The Import and Package tabs are now Import content file and Import content package
- 2026-09-29 [`fb189ec`](https://github.com/se7enxweb/xrowextract/commit/fb189ec) (Fehlerbehebung) Fixed: The One class page fits a phone again with the Presets card and its More menu open
- 2026-09-29 [`7acfe07`](https://github.com/se7enxweb/xrowextract/commit/7acfe07) (Fehlerbehebung) Fixed: A preset's node placeholder sets the start node of the named fetch it extends, and every file type on the import page links to its reference
- 2026-09-29 [`c4ee1ce`](https://github.com/se7enxweb/xrowextract/commit/c4ee1ce) (Funktion) sample and template content packages built read-only from existing content, and a richer package dry run
- 2026-09-29 [`2e8a4ec`](https://github.com/se7enxweb/xrowextract/commit/2e8a4ec) (Leistung) streaming a built package or a single class/object XML as a download
- 2026-09-29 [`4b53247`](https://github.com/se7enxweb/xrowextract/commit/4b53247) (Funktion) Content packages are a fourth Import page format, just like XML/CSV/JSON
- 2026-09-29 [`63be546`](https://github.com/se7enxweb/xrowextract/commit/63be546) (Funktion) The Import page's File/template cards show the content-package format the same way as XML/CSV/JSON
- 2026-09-29 [`83292c8`](https://github.com/se7enxweb/xrowextract/commit/83292c8) (ohne Nutzen für Anwender) Merge content packages as a fourth import format: sample, template and the same dry run as XML, CSV and JSON
- 2026-09-29 [`7ff7081`](https://github.com/se7enxweb/xrowextract/commit/7ff7081) (Funktion) German/English/untranslated strings for the content-package Import format
- 2026-09-29 [`5fc3dbb`](https://github.com/se7enxweb/xrowextract/commit/5fc3dbb) (ohne Nutzen für Anwender) Merge the German and English strings of the content package import format
- 2026-09-29 [`5281309`](https://github.com/se7enxweb/xrowextract/commit/5281309) (Fehlerbehebung) Fixed: The package-template scratch-content warning ignored the class actually chosen
- 2026-09-29 [`e2d0600`](https://github.com/se7enxweb/xrowextract/commit/e2d0600) (Funktion) The File card is one format chooser, pass 1 of the redesign
- 2026-09-29 [`6095e76`](https://github.com/se7enxweb/xrowextract/commit/6095e76) (ohne Nutzen für Anwender) Merge the File card redesign, pass 1: one format chooser with a tile per format
- 2026-09-29 [`5b9a256`](https://github.com/se7enxweb/xrowextract/commit/5b9a256) (Funktion) File card pass 2 - one class choice, shorter text, no clipped shapes, aligned buttons
- 2026-09-29 [`67ae0ab`](https://github.com/se7enxweb/xrowextract/commit/67ae0ab) (Funktion) German/English/untranslated strings for the File card tiles, complete
- 2026-09-29 [`810e1ac`](https://github.com/se7enxweb/xrowextract/commit/810e1ac) (ohne Nutzen für Anwender) Merge the File card redesign, pass 2: one class choice, shorter texts, German for every string
- 2026-09-29 [`83e497e`](https://github.com/se7enxweb/xrowextract/commit/83e497e) (Funktion) File card pass 3 - focus and aria, keep-place lands on the tile, sample state
- 2026-09-29 [`623a468`](https://github.com/se7enxweb/xrowextract/commit/623a468) (Funktion) German/English/untranslated strings for the File card's per-tile aria-labels
- 2026-09-29 [`e58624a`](https://github.com/se7enxweb/xrowextract/commit/e58624a) (ohne Nutzen für Anwender) Merge the File card redesign, pass 3: focus, labels for screen readers, keep-place on the tile, sample badge
- 2026-09-29 [`b4fed49`](https://github.com/se7enxweb/xrowextract/commit/b4fed49) (Funktion) Export as package from One class and Site archive (#27 part 2, item 1)
- 2026-09-29 [`4a72680`](https://github.com/se7enxweb/xrowextract/commit/4a72680) (Funktion) German/English/untranslated strings for Export as package
- 2026-09-29 [`e0540f0`](https://github.com/se7enxweb/xrowextract/commit/e0540f0) (ohne Nutzen für Anwender) Merge Export as package from One class and Site archive
- 2026-09-29 [`1a4adb8`](https://github.com/se7enxweb/xrowextract/commit/1a4adb8) (Fehlerbehebung) Fixed: Try a sample never registers in the package repository
- 2026-09-29 [`cd6ea87`](https://github.com/se7enxweb/xrowextract/commit/cd6ea87) (Funktion) --keep for --export, and ext:xrowextract:package --clean
- 2026-09-29 [`5098773`](https://github.com/se7enxweb/xrowextract/commit/5098773) (ohne Nutzen für Anwender) Merge the package repository fix: samples and exports are no longer left in the repository
- 2026-09-29 [`dffec42`](https://github.com/se7enxweb/xrowextract/commit/dffec42) (Fehlerbehebung) Fixed: The package template builder's sample image and file are accepted by the importer
- 2026-09-29 [`9fdda6d`](https://github.com/se7enxweb/xrowextract/commit/9fdda6d) (Fehlerbehebung) Fixed: A package sample's update row loses its old -> new diff on Velocity
- 2026-09-29 [`1653227`](https://github.com/se7enxweb/xrowextract/commit/1653227) (Fehlerbehebung) Fixed: Uploading a package on the Package tab no longer fails with an error page, and is checked like any other upload
- 2026-09-29 [`5552bd6`](https://github.com/se7enxweb/xrowextract/commit/5552bd6) (ohne Nutzen für Anwender) Merge the fix for package dry runs on Velocity showing no field changes
- 2026-09-29 [`d25575f`](https://github.com/se7enxweb/xrowextract/commit/d25575f) (Fehlerbehebung) Fixed: An uploaded package is opened by its absolute path
- 2026-09-29 [`4325145`](https://github.com/se7enxweb/xrowextract/commit/4325145) (Fehlerbehebung) Fixed: Package upload and download work on Velocity, exported packages can be imported again, and the Package tab builds templates from existing content
- 2026-09-29 [`73977e7`](https://github.com/se7enxweb/xrowextract/commit/73977e7) (Fehlerbehebung) Fixed: Package uploads work on Velocity without touching its file layer
- 2026-09-29 [`79b3c0a`](https://github.com/se7enxweb/xrowextract/commit/79b3c0a) (Funktion) Installing a package runs as a background job instead of holding the page
- 2026-09-29 [`cb38789`](https://github.com/se7enxweb/xrowextract/commit/cb38789) (Funktion) A package install shows its real progress and its log live on the Jobs page
- 2026-09-29 [`02e5817`](https://github.com/se7enxweb/xrowextract/commit/02e5817) (Funktion) The Jobs page reads a job's own progress bar and shows its log without colour codes
- 2026-09-29 [`5650b8a`](https://github.com/se7enxweb/xrowextract/commit/5650b8a) (Funktion) A queued or running job can be cancelled from the Jobs page
- 2026-09-29 [`8f72ce6`](https://github.com/se7enxweb/xrowextract/commit/8f72ce6) (Fehlerbehebung) Fixed: A long job log shows its start and its end, without a line cut in two
- 2026-09-29 [`eb8b512`](https://github.com/se7enxweb/xrowextract/commit/eb8b512) (Funktion) A job log keeps a short progress timeline instead of dropping every progress line
- 2026-09-29 [`3691329`](https://github.com/se7enxweb/xrowextract/commit/3691329) (Funktion) The Package tab's upload goes through chunks, and renames a bad name
- 2026-09-29 [`716e9c6`](https://github.com/se7enxweb/xrowextract/commit/716e9c6) (Funktion) A job log's progress timeline has no blank lines between its entries
- 2026-09-29 [`aede1d6`](https://github.com/se7enxweb/xrowextract/commit/aede1d6) (Funktion) The Import page's package Apply also queues a background job
- 2026-09-29 [`639735d`](https://github.com/se7enxweb/xrowextract/commit/639735d) (ohne Nutzen für Anwender) Merge package installs as jobs from the import page, chunked package uploads and renaming of invalid package names
- 2026-09-29 [`c71befd`](https://github.com/se7enxweb/xrowextract/commit/c71befd) (Funktion) A package loaded on the import page gets its own review step instead of the row file steps
- 2026-09-29 [`87330b9`](https://github.com/se7enxweb/xrowextract/commit/87330b9) (Fehlerbehebung) Fixed: A package install job shows its whole log, what it installed, and one date format
- 2026-09-29 [`e6143ee`](https://github.com/se7enxweb/xrowextract/commit/e6143ee) (Fehlerbehebung) Fixed: The package install button says "1 change" for one, and a package's dry run has no rich text note
- 2026-09-29 [`d587107`](https://github.com/se7enxweb/xrowextract/commit/d587107) (Funktion) A package job says how many items were created and how many already existed, and what was done with them
- 2026-09-29 [`d9a083b`](https://github.com/se7enxweb/xrowextract/commit/d9a083b) (Funktion) German for the Jobs page's "What was installed"
- 2026-09-29 [`2a9b595`](https://github.com/se7enxweb/xrowextract/commit/2a9b595) (Funktion) A typed column manifest for every export, which the importer uses to map columns exactly
- 2026-09-29 [`5d3505d`](https://github.com/se7enxweb/xrowextract/commit/5d3505d) (Funktion) Scheduled exports and imports, delivery destinations and the export history
- 2026-09-29 [`64d3bb9`](https://github.com/se7enxweb/xrowextract/commit/64d3bb9) (Funktion) German, English and untranslated strings for the Schedules tab, the manifest and the package pages
- 2026-09-29 [`dc6f5d8`](https://github.com/se7enxweb/xrowextract/commit/dc6f5d8) (Dokumentation) README and changelog describe the manifest, schedules, destinations and history
- 2026-09-29 [`7526df2`](https://github.com/se7enxweb/xrowextract/commit/7526df2) (ohne Nutzen für Anwender) Merge master: the package contents browser, the install history and job cancelling, with the schedules
- 2026-09-29 [`649c179`](https://github.com/se7enxweb/xrowextract/commit/649c179) (Leistung) Fixed: The Package tab opens a large package in a fraction of a second, a page of its objects at a time
- 2026-09-29 [`2adeb78`](https://github.com/se7enxweb/xrowextract/commit/2adeb78) (Upgrade-Hinweis) The extract tabs are One class of content export, Multi class of content export, Import content file, Import content package and Jobs, with Jobs last
- 2026-09-29 [`03f04dd`](https://github.com/se7enxweb/xrowextract/commit/03f04dd) (Funktion) Scheduled exports and imports, destinations, run history and typed column manifests
- 2026-09-29 [`1c15085`](https://github.com/se7enxweb/xrowextract/commit/1c15085) (Funktion) The package contents browser (#26)
- 2026-09-29 [`f2f6bae`](https://github.com/se7enxweb/xrowextract/commit/f2f6bae) (Funktion) The package contents browser: every file of a package, paged, filtered and viewable
- 2026-09-29 [`3f4bfb6`](https://github.com/se7enxweb/xrowextract/commit/3f4bfb6) (Sicherheit) Fixed: A package file opened directly cannot run script under the admin's origin
- 2026-09-29 [`2b9f14f`](https://github.com/se7enxweb/xrowextract/commit/2b9f14f) (Funktion) A 30+ preset catalogue and a Content package (.ezpkg) export format
- 2026-09-29 [`67a27e4`](https://github.com/se7enxweb/xrowextract/commit/67a27e4) (Funktion) The preset catalogue by audience and the Content package (.ezpkg) export format
- 2026-09-29 [`0ad2603`](https://github.com/se7enxweb/xrowextract/commit/0ad2603) (Fehlerbehebung) Fixed: The multi class export no longer offers a content package as the format of its files
- 2026-09-29 [`7023414`](https://github.com/se7enxweb/xrowextract/commit/7023414) (Fehlerbehebung) Fixed: A content package download that matches nothing says so next to the buttons
- 2026-09-29 [`fd5a9e6`](https://github.com/se7enxweb/xrowextract/commit/fd5a9e6) (Funktion) The sidebars of the one and multi class exports describe the page as it is
- 2026-09-29 [`17a44bd`](https://github.com/se7enxweb/xrowextract/commit/17a44bd) (Fehlerbehebung) Fixed: A package class's attributes are read with their own datatype
- 2026-09-29 [`9b33299`](https://github.com/se7enxweb/xrowextract/commit/9b33299) (Fehlerbehebung) Fixed: A cached package dry run is written where PHP-FPM can reuse it
- 2026-09-29 [`c4915db`](https://github.com/se7enxweb/xrowextract/commit/c4915db) (Fehlerbehebung) Fixed: The Package tab links an existing object by its node, and wide tables scroll
- 2026-09-29 [`4f26a96`](https://github.com/se7enxweb/xrowextract/commit/4f26a96) (Funktion) A datatype check, object filters and a compare view for content packages
- 2026-09-29 [`c909895`](https://github.com/se7enxweb/xrowextract/commit/c909895) (Funktion) An install history of content packages, and "Export these again"
- 2026-09-29 [`7262c1f`](https://github.com/se7enxweb/xrowextract/commit/7262c1f) (Funktion) "Open in Import" for a repository package, and package --export with filters or a preset
- 2026-09-29 [`605a3a1`](https://github.com/se7enxweb/xrowextract/commit/605a3a1) (Funktion) The Package, Import and Jobs sidebars follow their cards, and the package reference covers the new tools
- 2026-09-29 [`460e277`](https://github.com/se7enxweb/xrowextract/commit/460e277) (Funktion) English and German texts for the package filters, datatype check, compare and install history
- 2026-09-29 [`ce96a85`](https://github.com/se7enxweb/xrowextract/commit/ce96a85) (Sicherheit) Fixed: The compare and contents browser pages escape a package name from the address
- 2026-09-29 [`240d0de`](https://github.com/se7enxweb/xrowextract/commit/240d0de) (Funktion) Package filters, compare, install history, datatype check, Open in Import and export with filters
- 2026-09-29 [`cb19a0c`](https://github.com/se7enxweb/xrowextract/commit/cb19a0c) (Funktion) The top menu tab and its tooltip have German translations
- 2026-09-29 [`40d6e39`](https://github.com/se7enxweb/xrowextract/commit/40d6e39) (ohne Nutzen für Anwender) Merge the German top menu tab and tooltip
- 2026-09-30 [`7801ee8`](https://github.com/se7enxweb/xrowextract/commit/7801ee8) (Werkzeuge) PHPStan configuration with the Exponential kernel and library classes known
- 2026-09-30 [`be91b1c`](https://github.com/se7enxweb/xrowextract/commit/be91b1c) (Werkzeuge) The command-line failure helper is declared as never returning
- 2026-09-30 [`cc45957`](https://github.com/se7enxweb/xrowextract/commit/cc45957) (Funktion) The XML import reads elements through DOMElement
- 2026-09-30 [`5a892e6`](https://github.com/se7enxweb/xrowextract/commit/5a892e6) (Werkzeuge) Translated messages with arguments pass no comment as null
- 2026-09-30 [`ae53cd3`](https://github.com/se7enxweb/xrowextract/commit/ae53cd3) (Werkzeuge) Destination and schedule fetch() return their own class or null
- 2026-09-30 [`429d931`](https://github.com/se7enxweb/xrowextract/commit/429d931) (Fehlerbehebung) Fixed: A delivery to a destination of an unknown type reports no attempt
- 2026-09-30 [`c042076`](https://github.com/se7enxweb/xrowextract/commit/c042076) (Werkzeuge) The curl_close() call for PHP 7
- 2026-09-30 [`25118b5`](https://github.com/se7enxweb/xrowextract/commit/25118b5) (Werkzeuge) Conditions that can never change the result are removed
- 2026-09-30 [`b7d9163`](https://github.com/se7enxweb/xrowextract/commit/b7d9163) (Werkzeuge) PHPStan analyses at level 5
- 2026-09-30 [`8f23e2c`](https://github.com/se7enxweb/xrowextract/commit/8f23e2c) (Fehlerbehebung) Fixed: package --export with no usable node id ends with a message
- 2026-09-30 [`279b418`](https://github.com/se7enxweb/xrowextract/commit/279b418) (Werkzeuge) Fixed: Optional object and array parameters are declared nullable
- 2026-09-30 [`dd2bd25`](https://github.com/se7enxweb/xrowextract/commit/dd2bd25) (Fehlerbehebung) Fixed: A scheduled import from a destination of an unknown type fails with a message
- 2026-09-30 [`c68ae20`](https://github.com/se7enxweb/xrowextract/commit/c68ae20) (Fehlerbehebung) Fixed: An unreadable zip entry or a failed list query no longer ends in PHP errors
- 2026-09-30 [`021c7c1`](https://github.com/se7enxweb/xrowextract/commit/021c7c1) (Fehlerbehebung) Fixed: A file that cannot be opened ends a transfer or an archive with a message
- 2026-09-30 [`3110c02`](https://github.com/se7enxweb/xrowextract/commit/3110c02) (Sicherheit) Fixed: A secret that is not UTF-8 is refused instead of stored empty
- 2026-09-30 [`67298dd`](https://github.com/se7enxweb/xrowextract/commit/67298dd) (Fehlerbehebung) Fixed: A webhook is sent even when the run's text is not UTF-8
- 2026-09-30 [`82adc19`](https://github.com/se7enxweb/xrowextract/commit/82adc19) (Sicherheit) Fixed: An imported file URL must be of this site's own scheme, host and port
- 2026-09-30 [`baceff3`](https://github.com/se7enxweb/xrowextract/commit/baceff3) (Fehlerbehebung) Fixed: A history row keeps its lists when a text in them is not UTF-8
- 2026-09-30 [`78d41ca`](https://github.com/se7enxweb/xrowextract/commit/78d41ca) (Werkzeuge) PHPStan analyses at level 6, without requiring type declarations
- 2026-09-30 [`2399544`](https://github.com/se7enxweb/xrowextract/commit/2399544) (Werkzeuge) A release gate, bin/check.sh, and a workflow running it on PHP 8.1 to 8.5
- 2026-09-30 [`d5d9a5d`](https://github.com/se7enxweb/xrowextract/commit/d5d9a5d) (Werkzeuge) Unit tests for the parts that need no database, run by bin/check.sh
- 2026-09-30 [`a889ef6`](https://github.com/se7enxweb/xrowextract/commit/a889ef6) (Fehlerbehebung) Fixed: import refuses an unknown --match, --language, --resume-from or --parent
- 2026-09-30 [`4190997`](https://github.com/se7enxweb/xrowextract/commit/4190997) (Fehlerbehebung) Fixed: package --install --dry-run checks --object-mode and --class-mode too
- 2026-09-30 [`8197d58`](https://github.com/se7enxweb/xrowextract/commit/8197d58) (Sicherheit) Fixed: destination --config, --secret-env and --secret-file refuse a value without key=
- 2026-09-30 [`ff624c2`](https://github.com/se7enxweb/xrowextract/commit/ff624c2) (Werkzeuge) An integration test of every command against a test installation
- 2026-09-30 [`61d6752`](https://github.com/se7enxweb/xrowextract/commit/61d6752) (Werkzeuge) A view smoke test of every admin view in a browser
- 2026-09-30 [`b10b6bd`](https://github.com/se7enxweb/xrowextract/commit/b10b6bd) (Fehlerbehebung) Fixed: A background job whose runner dies is marked failed, not left running
- 2026-09-30 [`db2c48d`](https://github.com/se7enxweb/xrowextract/commit/db2c48d) (Leistung) Fixed: Caches that belong to one request no longer outlive it under Velocity
- 2026-09-30 [`e814e08`](https://github.com/se7enxweb/xrowextract/commit/e814e08) (Fehlerbehebung) Fixed: A PHP error in an export, transfer or import row is handled like an exception
- 2026-09-30 [`6180411`](https://github.com/se7enxweb/xrowextract/commit/6180411) (Fehlerbehebung) Fixed: A disabled proc_open(), exec() or shell_exec() is a message, not a fatal error
- 2026-09-30 [`14c00a3`](https://github.com/se7enxweb/xrowextract/commit/14c00a3) (Sicherheit) Fixed: An id from a form, an address or a command line is checked before a query
- 2026-09-30 [`f3b018b`](https://github.com/se7enxweb/xrowextract/commit/f3b018b) (Sicherheit) Fixed: The site archive view refuses an array posted for a single choice
- 2026-09-30 [`8c5b423`](https://github.com/se7enxweb/xrowextract/commit/8c5b423) (Fehlerbehebung) Fixed: The tab row no longer redefines the Jobs view's schedule_alerts variable
- 2026-09-30 [`eb11b3c`](https://github.com/se7enxweb/xrowextract/commit/eb11b3c) (Werkzeuge) A POST robustness test of every view, and log watching in the integration tests
- 2026-09-30 [`48c0356`](https://github.com/se7enxweb/xrowextract/commit/48c0356) (Werkzeuge) bin/check.sh --help shows the whole header
- 2026-09-30 [`7abb26e`](https://github.com/se7enxweb/xrowextract/commit/7abb26e) (Dokumentation) CHANGELOG for 2.5.3
- 2026-09-30 [`414dfe7`](https://github.com/se7enxweb/xrowextract/commit/414dfe7) (Release) Version 2.5.3 **Release v2.5.3.**
- 2026-09-30 [`2dc619d`](https://github.com/se7enxweb/xrowextract/commit/2dc619d) (Werkzeuge) Type declarations in the import and upload classes and views
- 2026-09-30 [`e495652`](https://github.com/se7enxweb/xrowextract/commit/e495652) (Werkzeuge) Type declarations in the package classes and views
- 2026-09-30 [`04c5e0f`](https://github.com/se7enxweb/xrowextract/commit/04c5e0f) (Werkzeuge) Type declarations in the job, archive, manifest, history and preset classes and views
- 2026-09-30 [`a33556c`](https://github.com/se7enxweb/xrowextract/commit/a33556c) (Werkzeuge) Type declarations in the schedule, destination, transport, notifier and secrets classes
- 2026-09-30 [`fbb0768`](https://github.com/se7enxweb/xrowextract/commit/fbb0768) (Werkzeuge) Type declarations in the CSV export, filter, column and datatype handler classes
- 2026-09-30 [`4eadf78`](https://github.com/se7enxweb/xrowextract/commit/4eadf78) (Werkzeuge) The stray doc comment of uniquePackageName() sits on that method again
- 2026-09-30 [`1362b4a`](https://github.com/se7enxweb/xrowextract/commit/1362b4a) (Werkzeuge) PHPStan checks every parameter, return and property type (level 6 in full)
- 2026-09-30 [`512b158`](https://github.com/se7enxweb/xrowextract/commit/512b158) (Funktion) One check of what the extension needs from PHP and the server
- 2026-09-30 [`0db904f`](https://github.com/se7enxweb/xrowextract/commit/0db904f) (Werkzeuge) bin/check.sh runs the requirements check
- 2026-09-30 [`3d2fd40`](https://github.com/se7enxweb/xrowextract/commit/3d2fd40) (Funktion) Each page names the features this server cannot offer there, and what they miss
- 2026-09-30 [`b4089a5`](https://github.com/se7enxweb/xrowextract/commit/b4089a5) (Fehlerbehebung) Fixed: Viewing an empty XML file of a package no longer ends in a fatal error
- 2026-09-30 [`c84914b`](https://github.com/se7enxweb/xrowextract/commit/c84914b) (Sicherheit) Fixed: A schedule with text that is not UTF-8 is refused instead of saved with an empty definition
- 2026-09-30 [`2e9df8b`](https://github.com/se7enxweb/xrowextract/commit/2e9df8b) (Fehlerbehebung) Fixed: A job whose error text is not UTF-8 no longer disappears from the Jobs page
- 2026-09-30 [`7696495`](https://github.com/se7enxweb/xrowextract/commit/7696495) (Fehlerbehebung) Fixed: A scheduled import deleted from the Jobs page while it runs ends its runner with a message
- 2026-09-30 [`73967e4`](https://github.com/se7enxweb/xrowextract/commit/73967e4) (Fehlerbehebung) Fixed: The manifest of an export whose class no longer exists is written instead of a fatal error
- 2026-09-30 [`a7932b0`](https://github.com/se7enxweb/xrowextract/commit/a7932b0) (Fehlerbehebung) Fixed: Scanning the host keys of an SFTP destination without the OpenSSH client reports it instead of failing the page
- 2026-09-30 [`85d4091`](https://github.com/se7enxweb/xrowextract/commit/85d4091) (Fehlerbehebung) Fixed: The Destinations page lists a destination of an unknown type instead of failing
- 2026-09-30 [`109fecf`](https://github.com/se7enxweb/xrowextract/commit/109fecf) (Werkzeuge) The job, archive, manifest and history code passes PHPStan level 8
- 2026-09-30 [`9709db3`](https://github.com/se7enxweb/xrowextract/commit/9709db3) (Werkzeuge) The import, upload and package classes and views pass PHPStan level 8
- 2026-09-30 [`1a6325c`](https://github.com/se7enxweb/xrowextract/commit/1a6325c) (Fehlerbehebung) Fixed: Word counts and plain text of a text that is not valid UTF-8 are exported instead of failing
- 2026-09-30 [`9d81aab`](https://github.com/se7enxweb/xrowextract/commit/9d81aab) (Werkzeuge) The schedule, destination, notification and transport code passes PHPStan level 8
- 2026-09-30 [`6ba8f4c`](https://github.com/se7enxweb/xrowextract/commit/6ba8f4c) (Fehlerbehebung) Fixed: A saved preset with text that is not valid UTF-8 is no longer stored empty
- 2026-09-30 [`e04d6fb`](https://github.com/se7enxweb/xrowextract/commit/e04d6fb) (Fehlerbehebung) Fixed: A CSV download with its manifest sends the file alone when the zip cannot be written
- 2026-09-30 [`9e2b670`](https://github.com/se7enxweb/xrowextract/commit/9e2b670) (Werkzeuge) The CSV export, catalogue, column, schema and datatype handler code passes PHPStan level 8
- 2026-09-30 [`7c2bd9f`](https://github.com/se7enxweb/xrowextract/commit/7c2bd9f) (Werkzeuge) PHPStan runs at level 8
- 2026-09-30 [`7fc3c48`](https://github.com/se7enxweb/xrowextract/commit/7fc3c48) (Funktion) Translations of the refused non-UTF-8 schedule and destination values, with German
- 2026-09-30 [`9c79e44`](https://github.com/se7enxweb/xrowextract/commit/9c79e44) (Werkzeuge) README describes the requirements check, the req part of bin/check.sh and PHPStan level 8
- 2026-09-30 [`e569ab6`](https://github.com/se7enxweb/xrowextract/commit/e569ab6) (Fehlerbehebung) Fixed: A csv.ini handler class that is not there is logged and left out instead of ending the export
- 2026-09-30 [`00ada27`](https://github.com/se7enxweb/xrowextract/commit/00ada27) (Dokumentation) CHANGELOG for 2.5.4
- 2026-09-30 [`e9935e8`](https://github.com/se7enxweb/xrowextract/commit/e9935e8) (Release) Version 2.5.4 **Release v2.5.4.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/xrowextract.md#2026-10-6-änderungen).

- 2026-10-02 [`0c31776`](https://github.com/se7enxweb/xrowextract/commit/0c31776) (Werkzeuge) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`964e0c6`](https://github.com/se7enxweb/xrowextract/commit/964e0c6) (Funktion) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`72ba301`](https://github.com/se7enxweb/xrowextract/commit/72ba301) (Funktion) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`26ea832`](https://github.com/se7enxweb/xrowextract/commit/26ea832) (Release) Version 2.5.5 **Release v2.5.5.**
- 2026-10-02 [`8de6d30`](https://github.com/se7enxweb/xrowextract/commit/8de6d30) (Werkzeuge) The commands start through the shared command helpers
- 2026-10-02 [`fbea964`](https://github.com/se7enxweb/xrowextract/commit/fbea964) (Release) Version 2.5.6 **Release v2.5.6.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/xrowextract.md)
- [Release Notes](../../../changelogs/extensions/xrowextract.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/xrowextract.md)
- [Spezifikation](../../spezifikationen/6.0/xrowextract.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
