# xrowextract (Export and import): release notes

What each release of `xrowextract` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/xrowextract.md); the story is in the [chronicle](../../history/extensions/xrowextract.md).

## v2.5.6 (2026-10-02)

**Maintenance, documentation and packaging**

- The commands start through the shared command helpers ([`8de6d30`](https://github.com/se7enxweb/xrowextract/commit/8de6d30))

1 version, merge or metadata commit not listed.

## v2.5.5 (2026-10-02)

**Updated**

- The commands and cronjob parts list a description of what they do ([`964e0c6`](https://github.com/se7enxweb/xrowextract/commit/964e0c6))
- The commands and cronjob parts list a description of what they do ([`72ba301`](https://github.com/se7enxweb/xrowextract/commit/72ba301))

**Maintenance, documentation and packaging**

- The command line scripts, cronjob parts and module views are classes the files call ([`0c31776`](https://github.com/se7enxweb/xrowextract/commit/0c31776))

1 version, merge or metadata commit not listed.

## v2.5.4 (2026-09-30)

**Added**

- One check of what the extension needs from PHP and the server ([`512b158`](https://github.com/se7enxweb/xrowextract/commit/512b158))

**Updated**

- Each page names the features this server cannot offer there, and what they miss ([`3d2fd40`](https://github.com/se7enxweb/xrowextract/commit/3d2fd40))
- Fixed: Viewing an empty XML file of a package no longer ends in a fatal error ([`b4089a5`](https://github.com/se7enxweb/xrowextract/commit/b4089a5))
- Fixed: A schedule with text that is not UTF-8 is refused instead of saved with an empty definition ([`c84914b`](https://github.com/se7enxweb/xrowextract/commit/c84914b))
- Fixed: A job whose error text is not UTF-8 no longer disappears from the Jobs page ([`2e9df8b`](https://github.com/se7enxweb/xrowextract/commit/2e9df8b))
- Fixed: A scheduled import deleted from the Jobs page while it runs ends its runner with a message ([`7696495`](https://github.com/se7enxweb/xrowextract/commit/7696495))
- Fixed: The manifest of an export whose class no longer exists is written instead of a fatal error ([`73967e4`](https://github.com/se7enxweb/xrowextract/commit/73967e4))
- Fixed: Scanning the host keys of an SFTP destination without the OpenSSH client reports it instead of failing the page ([`a7932b0`](https://github.com/se7enxweb/xrowextract/commit/a7932b0))
- Fixed: The Destinations page lists a destination of an unknown type instead of failing ([`85d4091`](https://github.com/se7enxweb/xrowextract/commit/85d4091))
- Fixed: Word counts and plain text of a text that is not valid UTF-8 are exported instead of failing ([`1a6325c`](https://github.com/se7enxweb/xrowextract/commit/1a6325c))
- Fixed: A saved preset with text that is not valid UTF-8 is no longer stored empty ([`6ba8f4c`](https://github.com/se7enxweb/xrowextract/commit/6ba8f4c))
- Fixed: A CSV download with its manifest sends the file alone when the zip cannot be written ([`e04d6fb`](https://github.com/se7enxweb/xrowextract/commit/e04d6fb))
- Translations of the refused non-UTF-8 schedule and destination values, with German ([`7fc3c48`](https://github.com/se7enxweb/xrowextract/commit/7fc3c48))
- Fixed: A csv.ini handler class that is not there is logged and left out instead of ending the export ([`e569ab6`](https://github.com/se7enxweb/xrowextract/commit/e569ab6))

**Maintenance, documentation and packaging**

- Type declarations in the import and upload classes and views ([`2dc619d`](https://github.com/se7enxweb/xrowextract/commit/2dc619d))
- Type declarations in the package classes and views ([`e495652`](https://github.com/se7enxweb/xrowextract/commit/e495652))
- Type declarations in the job, archive, manifest, history and preset classes and views ([`04c5e0f`](https://github.com/se7enxweb/xrowextract/commit/04c5e0f))
- Type declarations in the schedule, destination, transport, notifier and secrets classes ([`a33556c`](https://github.com/se7enxweb/xrowextract/commit/a33556c))
- Type declarations in the CSV export, filter, column and datatype handler classes ([`fbb0768`](https://github.com/se7enxweb/xrowextract/commit/fbb0768))
- The stray doc comment of uniquePackageName() sits on that method again ([`4eadf78`](https://github.com/se7enxweb/xrowextract/commit/4eadf78))
- PHPStan checks every parameter, return and property type (level 6 in full) ([`1362b4a`](https://github.com/se7enxweb/xrowextract/commit/1362b4a))
- bin/check.sh runs the requirements check ([`0db904f`](https://github.com/se7enxweb/xrowextract/commit/0db904f))
- The job, archive, manifest and history code passes PHPStan level 8 ([`109fecf`](https://github.com/se7enxweb/xrowextract/commit/109fecf))
- The import, upload and package classes and views pass PHPStan level 8 ([`9709db3`](https://github.com/se7enxweb/xrowextract/commit/9709db3))
- The schedule, destination, notification and transport code passes PHPStan level 8 ([`9d81aab`](https://github.com/se7enxweb/xrowextract/commit/9d81aab))
- The CSV export, catalogue, column, schema and datatype handler code passes PHPStan level 8 ([`9e2b670`](https://github.com/se7enxweb/xrowextract/commit/9e2b670))
- PHPStan runs at level 8 ([`7c2bd9f`](https://github.com/se7enxweb/xrowextract/commit/7c2bd9f))
- README describes the requirements check, the req part of bin/check.sh and PHPStan level 8 ([`9c79e44`](https://github.com/se7enxweb/xrowextract/commit/9c79e44))
- CHANGELOG for 2.5.4 ([`00ada27`](https://github.com/se7enxweb/xrowextract/commit/00ada27))

1 version, merge or metadata commit not listed.

## v2.5.3 (2026-09-30)

**Added**

- Export handlers for date and time, time, keywords, tags and object relation ([`1cb7203`](https://github.com/se7enxweb/xrowextract/commit/1cb7203))
- A spreadsheet preview of the export, before downloading it ([`7f8f503`](https://github.com/se7enxweb/xrowextract/commit/7f8f503))
- A site archive: every class below the chosen nodes in one ZIP or TAR file, one CSV per class ([`e23b382`](https://github.com/se7enxweb/xrowextract/commit/e23b382))
- A whole site scope for the single class export: every object of a class in one CSV ([`359275b`](https://github.com/se7enxweb/xrowextract/commit/359275b))
- Command line exports: ext:xrowextract:csv and ext:xrowextract:archive ([`e96fc89`](https://github.com/se7enxweb/xrowextract/commit/e96fc89))
- Password hash and hash type columns for users allowed to export them ([`2f57557`](https://github.com/se7enxweb/xrowextract/commit/2f57557))
- Content languages for the single class export: counts, all or some, one row per translation ([`1f3cb82`](https://github.com/se7enxweb/xrowextract/commit/1f3cb82))
- A column catalogue: attribute formats, grouped special columns, column sets, and an xrowmetadata handler ([`b0787c7`](https://github.com/se7enxweb/xrowextract/commit/b0787c7))
- Languages and column choices for the site archive, true translations only, and JSON/XML writers ([`bf68196`](https://github.com/se7enxweb/xrowextract/commit/bf68196))
- JSON and XML output, a Sites set for the archive, and smarter defaults for the single class view ([`f861e7e`](https://github.com/se7enxweb/xrowextract/commit/f861e7e))
- The other direction of xrowextract exports, a CSV/JSON importer ([`e2df53d`](https://github.com/se7enxweb/xrowextract/commit/e2df53d))
- An Import tab: upload, column mapping, dry run and apply ([`50ebee0`](https://github.com/se7enxweb/xrowextract/commit/50ebee0))
- German and English strings for the import view ([`dbc35a1`](https://github.com/se7enxweb/xrowextract/commit/dbc35a1))
- A background job runner for large CSV and archive exports ([`739e11b`](https://github.com/se7enxweb/xrowextract/commit/739e11b))
- A "Run in the background" button and a Jobs page ([`b67610e`](https://github.com/se7enxweb/xrowextract/commit/b67610e))
- A Jobs tab, job cards and live progress in the same xe- design ([`fd7bf82`](https://github.com/se7enxweb/xrowextract/commit/fd7bf82))
- German and English strings for the background jobs feature ([`f816571`](https://github.com/se7enxweb/xrowextract/commit/f816571))
- Filters and a sort order for the exports: dates, section, state, visibility, name, attribute conditions ([`b42702a`](https://github.com/se7enxweb/xrowextract/commit/b42702a))
- XML as a first-class import format, a one-click sample, and a complete file format reference ([`f2c8a9d`](https://github.com/se7enxweb/xrowextract/commit/f2c8a9d))
- German and English strings for XML, the sample and the reference ([`e82acf2`](https://github.com/se7enxweb/xrowextract/commit/e82acf2))
- xrowextract/package supports content packages (.ezpkg) ([`0eabe54`](https://github.com/se7enxweb/xrowextract/commit/0eabe54))
- a command line for content packages, ext:xrowextract:package ([`36b078b`](https://github.com/se7enxweb/xrowextract/commit/36b078b))
- German, English and untranslated strings for content packages ([`c8e3912`](https://github.com/se7enxweb/xrowextract/commit/c8e3912))
- two more registered extended attribute filters, and a chain point in the language filter ([`600661c`](https://github.com/se7enxweb/xrowextract/commit/600661c))
- fetchalias.ini named fetches usable from the CSV export ([`2b5601e`](https://github.com/se7enxweb/xrowextract/commit/2b5601e))
- several conditions joined with and/or, kernel-level object/tree fields, an exact depth below the node, a second sort, and applying an extended filter or a named fetch ([`f7ee687`](https://github.com/se7enxweb/xrowextract/commit/f7ee687))
- Chunked upload of any size, with resume and per-user isolation ([`4363309`](https://github.com/se7enxweb/xrowextract/commit/4363309))
- saved export presets, more powerful than a fetchalias.ini named fetch alone ([`2aab54a`](https://github.com/se7enxweb/xrowextract/commit/2aab54a))
- Every extract view keeps its place when a button reloads the page ([`083930a`](https://github.com/se7enxweb/xrowextract/commit/083930a))
- The import CLI streams any file size and can run as a background job ([`f3fe4e4`](https://github.com/se7enxweb/xrowextract/commit/f3fe4e4))
- A large import queues as a background job and reports its size and resume point ([`e31e3fc`](https://github.com/se7enxweb/xrowextract/commit/e31e3fc))
- Chunked upload progress, a queued-job notice and a resume control on the import page ([`d3330be`](https://github.com/se7enxweb/xrowextract/commit/d3330be))
- German and English strings for chunked upload, queuing and job resume ([`7fee4ff`](https://github.com/se7enxweb/xrowextract/commit/7fee4ff))
- direct .ezpkg/class-XML/object-XML upload, inspection and install on xrowextract/import ([`476313b`](https://github.com/se7enxweb/xrowextract/commit/476313b))
- background job support for large content-package imports ([`e989da5`](https://github.com/se7enxweb/xrowextract/commit/e989da5))
- translations for the content-package upload and reference strings ([`374f381`](https://github.com/se7enxweb/xrowextract/commit/374f381))
- sample and template content packages built read-only from existing content, and a richer package dry run ([`c4ee1ce`](https://github.com/se7enxweb/xrowextract/commit/c4ee1ce))
- streaming a built package or a single class/object XML as a download ([`2e8a4ec`](https://github.com/se7enxweb/xrowextract/commit/2e8a4ec))
- German/English/untranslated strings for the content-package Import format ([`7ff7081`](https://github.com/se7enxweb/xrowextract/commit/7ff7081))
- German/English/untranslated strings for the File card tiles, complete ([`67ae0ab`](https://github.com/se7enxweb/xrowextract/commit/67ae0ab))
- German/English/untranslated strings for the File card's per-tile aria-labels ([`623a468`](https://github.com/se7enxweb/xrowextract/commit/623a468))
- Export as package from One class and Site archive (#27 part 2, item 1) ([`b4fed49`](https://github.com/se7enxweb/xrowextract/commit/b4fed49))
- German/English/untranslated strings for Export as package ([`4a72680`](https://github.com/se7enxweb/xrowextract/commit/4a72680))
- --keep for --export, and ext:xrowextract:package --clean ([`cd6ea87`](https://github.com/se7enxweb/xrowextract/commit/cd6ea87))
- A package install shows its real progress and its log live on the Jobs page ([`cb38789`](https://github.com/se7enxweb/xrowextract/commit/cb38789))
- A queued or running job can be cancelled from the Jobs page ([`5650b8a`](https://github.com/se7enxweb/xrowextract/commit/5650b8a))
- A typed column manifest for every export, which the importer uses to map columns exactly ([`2a9b595`](https://github.com/se7enxweb/xrowextract/commit/2a9b595))
- Scheduled exports and imports, delivery destinations and the export history ([`5d3505d`](https://github.com/se7enxweb/xrowextract/commit/5d3505d))
- Scheduled exports and imports, destinations, run history and typed column manifests ([`03f04dd`](https://github.com/se7enxweb/xrowextract/commit/03f04dd))
- The package contents browser (#26) ([`1c15085`](https://github.com/se7enxweb/xrowextract/commit/1c15085))
- The package contents browser: every file of a package, paged, filtered and viewable ([`f2f6bae`](https://github.com/se7enxweb/xrowextract/commit/f2f6bae))
- A 30+ preset catalogue and a Content package (.ezpkg) export format ([`2b9f14f`](https://github.com/se7enxweb/xrowextract/commit/2b9f14f))
- The preset catalogue by audience and the Content package (.ezpkg) export format ([`67a27e4`](https://github.com/se7enxweb/xrowextract/commit/67a27e4))
- A datatype check, object filters and a compare view for content packages ([`4f26a96`](https://github.com/se7enxweb/xrowextract/commit/4f26a96))
- An install history of content packages, and "Export these again" ([`c909895`](https://github.com/se7enxweb/xrowextract/commit/c909895))
- "Open in Import" for a repository package, and package --export with filters or a preset ([`7262c1f`](https://github.com/se7enxweb/xrowextract/commit/7262c1f))
- English and German texts for the package filters, datatype check, compare and install history ([`460e277`](https://github.com/se7enxweb/xrowextract/commit/460e277))
- Package filters, compare, install history, datatype check, Open in Import and export with filters ([`240d0de`](https://github.com/se7enxweb/xrowextract/commit/240d0de))

**Updated**

- Fixed: Every CSV row has the header's columns, the special columns export again, and the export honours read access ([`8302029`](https://github.com/se7enxweb/xrowextract/commit/8302029))
- The CSV view's settings are three cards, and the sidebar explains the tool ([`e37f2fd`](https://github.com/se7enxweb/xrowextract/commit/e37f2fd))
- Fixed: The in-place preview has every column on every server ([`8fd0744`](https://github.com/se7enxweb/xrowextract/commit/8fd0744))
- The column list is part of the Columns card, and the class list counts the objects of every class ([`17b8bf0`](https://github.com/se7enxweb/xrowextract/commit/17b8bf0))
- Fixed: The site archive downloads whole on servers that end a request by throwing ([`2a8a10d`](https://github.com/se7enxweb/xrowextract/commit/2a8a10d))
- The single class view names the datatype and meta information of every column ([`62521c3`](https://github.com/se7enxweb/xrowextract/commit/62521c3))
- Three scopes for the single class export, a readable datatype list, and the node is remembered ([`9bbaf3b`](https://github.com/se7enxweb/xrowextract/commit/9bbaf3b))
- Fixed: Adding a column set, a column or all attributes shows what was added ([`261d094`](https://github.com/se7enxweb/xrowextract/commit/261d094))
- Fixed: The single class view no longer keeps a node and class saved under the old defaults ([`fb426d1`](https://github.com/se7enxweb/xrowextract/commit/fb426d1))
- Fixed: Node links in the export views open the node ([`88dba09`](https://github.com/se7enxweb/xrowextract/commit/88dba09))
- The single class view's sidebar follows the view, and the translations are whole again ([`360fd64`](https://github.com/se7enxweb/xrowextract/commit/360fd64))
- The import view offers its options before an upload, a template per class, and its own sidebar ([`f3d116e`](https://github.com/se7enxweb/xrowextract/commit/f3d116e))
- Fixed: Choosing the parent for imported objects opens the content tree ([`04ddab3`](https://github.com/se7enxweb/xrowextract/commit/04ddab3))
- The Jobs page shows who started each job as a linked user bubble, and its queued, started and ended times as a timeline ([`c3cb542`](https://github.com/se7enxweb/xrowextract/commit/c3cb542))
- The Jobs page shows its totals as tiles: total, completed, running, queued and failed ([`fd9a536`](https://github.com/se7enxweb/xrowextract/commit/fd9a536))
- Fixed: The import page fits the window again on desktop and mobile ([`7922536`](https://github.com/se7enxweb/xrowextract/commit/7922536))
- Fixed: A long language name no longer runs into the parent field on the import page ([`e2691e0`](https://github.com/se7enxweb/xrowextract/commit/e2691e0))
- import links to the package template, tabs show Package ([`3de6839`](https://github.com/se7enxweb/xrowextract/commit/3de6839))
- The import preview says which class the rows go into, where, in which language, and names every object ([`3fb29d0`](https://github.com/se7enxweb/xrowextract/commit/3fb29d0))
- Try a sample works with any class, and the reference's XML section starts closed and remembers its state ([`1728690`](https://github.com/se7enxweb/xrowextract/commit/1728690))
- Fixed: The package template builder no longer publishes on the public site ([`35cb61f`](https://github.com/se7enxweb/xrowextract/commit/35cb61f))
- Fixed: The XML reference text no longer turns its tag names into page markup ([`f241dfa`](https://github.com/se7enxweb/xrowextract/commit/f241dfa))
- Fixed: The file format reference's sections other than XML start open again ([`1b84fae`](https://github.com/se7enxweb/xrowextract/commit/1b84fae))
- Fixed: The import preview's class summary uses no foreachelse, which the template engine does not know ([`e3f34e6`](https://github.com/se7enxweb/xrowextract/commit/e3f34e6))
- Fixed: The import and package pages no longer log "Datatype not found" errors ([`4b676f8`](https://github.com/se7enxweb/xrowextract/commit/4b676f8))
- Fixed: Two strings added by both merged branches appear once in the translations ([`f54defe`](https://github.com/se7enxweb/xrowextract/commit/f54defe))
- XML, CSV and JSON import stream a row at a time instead of loading the whole file ([`0b73582`](https://github.com/se7enxweb/xrowextract/commit/0b73582))
- Fixed: After a reload the highlight lands on the block that was being worked in, and is clearly visible ([`592ad66`](https://github.com/se7enxweb/xrowextract/commit/592ad66))
- Fixed: xrowextract/package uploads a package into the wrong repository ([`0570a5b`](https://github.com/se7enxweb/xrowextract/commit/0570a5b))
- Fixed: --alias-param and --param keep every occurrence, and a node that never resolved no longer fetches silently ([`f9e0732`](https://github.com/se7enxweb/xrowextract/commit/f9e0732))
- Fixed: A package archive is accepted only when every entry is a plain file or folder ([`3397d58`](https://github.com/se7enxweb/xrowextract/commit/3397d58))
- The import page shows content packages where one looks first ([`ed22981`](https://github.com/se7enxweb/xrowextract/commit/ed22981))
- The Presets card explains itself, and a loaded preset (or one setting of it) can be unloaded ([`b0b7d7a`](https://github.com/se7enxweb/xrowextract/commit/b0b7d7a))
- Fixed: The One class page fits a phone again with the Presets card and its More menu open ([`fb189ec`](https://github.com/se7enxweb/xrowextract/commit/fb189ec))
- Fixed: A preset's node placeholder sets the start node of the named fetch it extends, and every file type on the import page links to its reference ([`7acfe07`](https://github.com/se7enxweb/xrowextract/commit/7acfe07))
- Content packages are a fourth Import page format, just like XML/CSV/JSON ([`4b53247`](https://github.com/se7enxweb/xrowextract/commit/4b53247))
- The Import page's File/template cards show the content-package format the same way as XML/CSV/JSON ([`63be546`](https://github.com/se7enxweb/xrowextract/commit/63be546))
- Fixed: The package-template scratch-content warning ignored the class actually chosen ([`5281309`](https://github.com/se7enxweb/xrowextract/commit/5281309))
- The File card is one format chooser, pass 1 of the redesign ([`e2d0600`](https://github.com/se7enxweb/xrowextract/commit/e2d0600))
- File card pass 2 - one class choice, shorter text, no clipped shapes, aligned buttons ([`5b9a256`](https://github.com/se7enxweb/xrowextract/commit/5b9a256))
- File card pass 3 - focus and aria, keep-place lands on the tile, sample state ([`83e497e`](https://github.com/se7enxweb/xrowextract/commit/83e497e))
- Fixed: Try a sample never registers in the package repository ([`1a4adb8`](https://github.com/se7enxweb/xrowextract/commit/1a4adb8))
- Fixed: The package template builder's sample image and file are accepted by the importer ([`dffec42`](https://github.com/se7enxweb/xrowextract/commit/dffec42))
- Fixed: A package sample's update row loses its old -> new diff on Velocity ([`9fdda6d`](https://github.com/se7enxweb/xrowextract/commit/9fdda6d))
- Fixed: Uploading a package on the Package tab no longer fails with an error page, and is checked like any other upload ([`1653227`](https://github.com/se7enxweb/xrowextract/commit/1653227))
- Fixed: An uploaded package is opened by its absolute path ([`d25575f`](https://github.com/se7enxweb/xrowextract/commit/d25575f))
- Fixed: Package upload and download work on Velocity, exported packages can be imported again, and the Package tab builds templates from existing content ([`4325145`](https://github.com/se7enxweb/xrowextract/commit/4325145))
- Fixed: Package uploads work on Velocity without touching its file layer ([`73977e7`](https://github.com/se7enxweb/xrowextract/commit/73977e7))
- Installing a package runs as a background job instead of holding the page ([`79b3c0a`](https://github.com/se7enxweb/xrowextract/commit/79b3c0a))
- The Jobs page reads a job's own progress bar and shows its log without colour codes ([`02e5817`](https://github.com/se7enxweb/xrowextract/commit/02e5817))
- Fixed: A long job log shows its start and its end, without a line cut in two ([`8f72ce6`](https://github.com/se7enxweb/xrowextract/commit/8f72ce6))
- A job log keeps a short progress timeline instead of dropping every progress line ([`eb8b512`](https://github.com/se7enxweb/xrowextract/commit/eb8b512))
- The Package tab's upload goes through chunks, and renames a bad name ([`3691329`](https://github.com/se7enxweb/xrowextract/commit/3691329))
- A job log's progress timeline has no blank lines between its entries ([`716e9c6`](https://github.com/se7enxweb/xrowextract/commit/716e9c6))
- The Import page's package Apply also queues a background job ([`aede1d6`](https://github.com/se7enxweb/xrowextract/commit/aede1d6))
- A package loaded on the import page gets its own review step instead of the row file steps ([`c71befd`](https://github.com/se7enxweb/xrowextract/commit/c71befd))
- Fixed: A package install job shows its whole log, what it installed, and one date format ([`87330b9`](https://github.com/se7enxweb/xrowextract/commit/87330b9))
- Fixed: The package install button says "1 change" for one, and a package's dry run has no rich text note ([`e6143ee`](https://github.com/se7enxweb/xrowextract/commit/e6143ee))
- A package job says how many items were created and how many already existed, and what was done with them ([`d587107`](https://github.com/se7enxweb/xrowextract/commit/d587107))
- German for the Jobs page's "What was installed" ([`d9a083b`](https://github.com/se7enxweb/xrowextract/commit/d9a083b))
- German, English and untranslated strings for the Schedules tab, the manifest and the package pages ([`64d3bb9`](https://github.com/se7enxweb/xrowextract/commit/64d3bb9))
- Fixed: The Package tab opens a large package in a fraction of a second, a page of its objects at a time ([`649c179`](https://github.com/se7enxweb/xrowextract/commit/649c179))
- Fixed: A package file opened directly cannot run script under the admin's origin ([`3f4bfb6`](https://github.com/se7enxweb/xrowextract/commit/3f4bfb6))
- Fixed: The multi class export no longer offers a content package as the format of its files ([`0ad2603`](https://github.com/se7enxweb/xrowextract/commit/0ad2603))
- Fixed: A content package download that matches nothing says so next to the buttons ([`7023414`](https://github.com/se7enxweb/xrowextract/commit/7023414))
- The sidebars of the one and multi class exports describe the page as it is ([`fd5a9e6`](https://github.com/se7enxweb/xrowextract/commit/fd5a9e6))
- Fixed: A package class's attributes are read with their own datatype ([`17a44bd`](https://github.com/se7enxweb/xrowextract/commit/17a44bd))
- Fixed: A cached package dry run is written where PHP-FPM can reuse it ([`9b33299`](https://github.com/se7enxweb/xrowextract/commit/9b33299))
- Fixed: The Package tab links an existing object by its node, and wide tables scroll ([`c4915db`](https://github.com/se7enxweb/xrowextract/commit/c4915db))
- The Package, Import and Jobs sidebars follow their cards, and the package reference covers the new tools ([`605a3a1`](https://github.com/se7enxweb/xrowextract/commit/605a3a1))
- Fixed: The compare and contents browser pages escape a package name from the address ([`ce96a85`](https://github.com/se7enxweb/xrowextract/commit/ce96a85))
- The top menu tab and its tooltip have German translations ([`cb19a0c`](https://github.com/se7enxweb/xrowextract/commit/cb19a0c))
- The XML import reads elements through DOMElement ([`cc45957`](https://github.com/se7enxweb/xrowextract/commit/cc45957))
- Fixed: A delivery to a destination of an unknown type reports no attempt ([`429d931`](https://github.com/se7enxweb/xrowextract/commit/429d931))
- Fixed: package --export with no usable node id ends with a message ([`8f23e2c`](https://github.com/se7enxweb/xrowextract/commit/8f23e2c))
- Fixed: A scheduled import from a destination of an unknown type fails with a message ([`dd2bd25`](https://github.com/se7enxweb/xrowextract/commit/dd2bd25))
- Fixed: An unreadable zip entry or a failed list query no longer ends in PHP errors ([`c68ae20`](https://github.com/se7enxweb/xrowextract/commit/c68ae20))
- Fixed: A file that cannot be opened ends a transfer or an archive with a message ([`021c7c1`](https://github.com/se7enxweb/xrowextract/commit/021c7c1))
- Fixed: A secret that is not UTF-8 is refused instead of stored empty ([`3110c02`](https://github.com/se7enxweb/xrowextract/commit/3110c02))
- Fixed: A webhook is sent even when the run's text is not UTF-8 ([`67298dd`](https://github.com/se7enxweb/xrowextract/commit/67298dd))
- Fixed: An imported file URL must be of this site's own scheme, host and port ([`82adc19`](https://github.com/se7enxweb/xrowextract/commit/82adc19))
- Fixed: A history row keeps its lists when a text in them is not UTF-8 ([`baceff3`](https://github.com/se7enxweb/xrowextract/commit/baceff3))
- Fixed: import refuses an unknown --match, --language, --resume-from or --parent ([`a889ef6`](https://github.com/se7enxweb/xrowextract/commit/a889ef6))
- Fixed: package --install --dry-run checks --object-mode and --class-mode too ([`4190997`](https://github.com/se7enxweb/xrowextract/commit/4190997))
- Fixed: destination --config, --secret-env and --secret-file refuse a value without key= ([`8197d58`](https://github.com/se7enxweb/xrowextract/commit/8197d58))
- Fixed: A background job whose runner dies is marked failed, not left running ([`b10b6bd`](https://github.com/se7enxweb/xrowextract/commit/b10b6bd))
- Fixed: Caches that belong to one request no longer outlive it under Velocity ([`db2c48d`](https://github.com/se7enxweb/xrowextract/commit/db2c48d))
- Fixed: A PHP error in an export, transfer or import row is handled like an exception ([`e814e08`](https://github.com/se7enxweb/xrowextract/commit/e814e08))
- Fixed: A disabled proc_open(), exec() or shell_exec() is a message, not a fatal error ([`6180411`](https://github.com/se7enxweb/xrowextract/commit/6180411))
- Fixed: An id from a form, an address or a command line is checked before a query ([`14c00a3`](https://github.com/se7enxweb/xrowextract/commit/14c00a3))
- Fixed: The site archive view refuses an array posted for a single choice ([`f3b018b`](https://github.com/se7enxweb/xrowextract/commit/f3b018b))
- Fixed: The tab row no longer redefines the Jobs view's schedule_alerts variable ([`8c5b423`](https://github.com/se7enxweb/xrowextract/commit/8c5b423))

**Renamed**

- The Import and Package tabs are now Import content file and Import content package ([`3858c84`](https://github.com/se7enxweb/xrowextract/commit/3858c84)) Upgrade note.
- The extract tabs are One class of content export, Multi class of content export, Import content file, Import content package and Jobs, with Jobs last ([`2adeb78`](https://github.com/se7enxweb/xrowextract/commit/2adeb78)) Upgrade note.

**Maintenance, documentation and packaging**

- README and changelog describe the manifest, schedules, destinations and history ([`dc6f5d8`](https://github.com/se7enxweb/xrowextract/commit/dc6f5d8))
- PHPStan configuration with the Exponential kernel and library classes known ([`7801ee8`](https://github.com/se7enxweb/xrowextract/commit/7801ee8))
- The command-line failure helper is declared as never returning ([`be91b1c`](https://github.com/se7enxweb/xrowextract/commit/be91b1c))
- Translated messages with arguments pass no comment as null ([`5a892e6`](https://github.com/se7enxweb/xrowextract/commit/5a892e6))
- Destination and schedule fetch() return their own class or null ([`ae53cd3`](https://github.com/se7enxweb/xrowextract/commit/ae53cd3))
- The curl_close() call for PHP 7 ([`c042076`](https://github.com/se7enxweb/xrowextract/commit/c042076))
- Conditions that can never change the result are removed ([`25118b5`](https://github.com/se7enxweb/xrowextract/commit/25118b5))
- PHPStan analyses at level 5 ([`b7d9163`](https://github.com/se7enxweb/xrowextract/commit/b7d9163))
- Fixed: Optional object and array parameters are declared nullable ([`279b418`](https://github.com/se7enxweb/xrowextract/commit/279b418))
- PHPStan analyses at level 6, without requiring type declarations ([`78d41ca`](https://github.com/se7enxweb/xrowextract/commit/78d41ca))
- A release gate, bin/check.sh, and a workflow running it on PHP 8.1 to 8.5 ([`2399544`](https://github.com/se7enxweb/xrowextract/commit/2399544))
- Unit tests for the parts that need no database, run by bin/check.sh ([`d5d9a5d`](https://github.com/se7enxweb/xrowextract/commit/d5d9a5d))
- An integration test of every command against a test installation ([`ff624c2`](https://github.com/se7enxweb/xrowextract/commit/ff624c2))
- A view smoke test of every admin view in a browser ([`61d6752`](https://github.com/se7enxweb/xrowextract/commit/61d6752))
- A POST robustness test of every view, and log watching in the integration tests ([`eb11b3c`](https://github.com/se7enxweb/xrowextract/commit/eb11b3c))
- bin/check.sh --help shows the whole header ([`48c0356`](https://github.com/se7enxweb/xrowextract/commit/48c0356))
- CHANGELOG for 2.5.3 ([`7abb26e`](https://github.com/se7enxweb/xrowextract/commit/7abb26e))

26 version, merge or metadata commits not listed.

## v2.5.2 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`8b98792`](https://github.com/se7enxweb/xrowextract/commit/8b98792))

1 version, merge or metadata commit not listed.

## v2.5.1 (2026-09-27)

**Updated**

- The CSV export form writes its line break as <br>, as the installed copy does ([`2031a58`](https://github.com/se7enxweb/xrowextract/commit/2031a58))
- Fixed: The ezinfo.php declares xrowextractInfo with a static info() and states the extension's version, license and website ([`7e2d0b4`](https://github.com/se7enxweb/xrowextract/commit/7e2d0b4))

## v2.5.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`92f6e8c`](https://github.com/se7enxweb/xrowextract/commit/92f6e8c))

1 version, merge or metadata commit not listed.

## v2.4.2 (2024-08-22)

**Updated**

- PHP 8 Bugfix for ezmatrix dayatype ([`431f2ea`](https://github.com/se7enxweb/xrowextract/commit/431f2ea))
- Bugfixes post integration into default Exponential 6 distribution to clean up main csv module code formating and export error in fetching code ([`b4e5fc1`](https://github.com/se7enxweb/xrowextract/commit/b4e5fc1))

## v2.4.1 (2024-07-01)

**Updated**

- Settings Bugfix for hmregexpline class handler name ([`222b21c`](https://github.com/se7enxweb/xrowextract/commit/222b21c))

## v2.4.0 (2024-07-01)

**Updated**

- Mass improvments of php8 support and recent testing and refinements ([`c518dac`](https://github.com/se7enxweb/xrowextract/commit/c518dac))

## v2.3.2 (2024-04-02)

**Updated**

- Minor bugfix to prevent fatal error under php 8 ([`f6de469`](https://github.com/se7enxweb/xrowextract/commit/f6de469))

## v2.3.1 (2024-03-01)

**Maintenance, documentation and packaging**

- Update composer.json switched vendor on requirements ([`ba8ddfa`](https://github.com/se7enxweb/xrowextract/commit/ba8ddfa))

## v2.3.0 (2024-02-28)

**Maintenance, documentation and packaging**

- Update composer.json switch package vendor ([`c0d37fb`](https://github.com/se7enxweb/xrowextract/commit/c0d37fb))

1 version, merge or metadata commit not listed.

## Related

* [Feature page](../../features/6.0/extensions/xrowextract.md)
* [Chronicle](../../history/extensions/xrowextract.md)
* [Change ledger](../../history/ledger/xrowextract.md)
* [Specification](../../specifications/6.0/xrowextract.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)

## See also

* months: [2024-01](../../history/extensions/months/2024-01.md), [2024-02](../../history/extensions/months/2024-02.md), [2024-03](../../history/extensions/months/2024-03.md), [2024-04](../../history/extensions/months/2024-04.md), [2024-07](../../history/extensions/months/2024-07.md), [2024-08](../../history/extensions/months/2024-08.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
