# Change ledger: xrowextract

Every change made to `xrowextract` since the se7enxweb era began, oldest first: 245 changes touching 1087 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 171 |
| Added | 66 |
| Other | 4 |
| Renamed | 2 |
| Merged | 1 |
| Removed | 1 |

## 2024-01 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `07db95b` | Added | Added: Added github funding information | 1 | +3 / −0 |  |

## 2024-02 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-02-28 | `c0d37fb` | Updated | Update composer.json switch package vendor | 1 | +1 / −1 | v2.3.0 |

## 2024-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-03-01 | `ba8ddfa` | Updated | Update composer.json switched vendor on requirements | 1 | +1 / −1 | v2.3.1 |

## 2024-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-04-02 | `f6de469` | Updated | Updated: Minor bugfix to prevent fatal error under php 8 | 1 | +1 / −1 | v2.3.2 |

## 2024-07 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-07-01 | `c518dac` | Updated | Updated: Mass improvments of php8 support and recent testing and refinements | 31 | +163 / −126 | v2.4.0 |
| 2024-07-01 | `222b21c` | Updated | Updated: Settings Bugfix for hmregexpline class handler name | 1 | +1 / −1 | v2.4.1 |

## 2024-08 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-08-22 | `431f2ea` | Updated | Updated: PHP 8 Bugfix for ezmatrix dayatype | 1 | +2 / −1 |  |
| 2024-08-22 | `b4e5fc1` | Updated | Updated: Bugfixes post integration into default Exponential 6 distribution to clean up main csv module code formating and export error in fetching code | 1 | +155 / −143 | v2.4.2 |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `a08416f` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-09 (230 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-22 | `92f6e8c` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +5 / −1 | v2.5.0 |
| 2026-09-27 | `2031a58` | Updated | Updated: The CSV export form writes its line break as <br>, as the installed copy does | 1 | +1 / −1 |  |
| 2026-09-27 | `7e2d0b4` | Updated | Fixed: The ezinfo.php declares xrowextractInfo with a static info() and states the extension's version, license and website | 1 | +8 / −7 | v2.5.1 |
| 2026-09-28 | `8b98792` | Updated | Updated: Every visible text of the extension is a translation string, with German | 5 | +411 / −34 |  |
| 2026-09-28 | `cabd1c1` | Updated | Updated: Version 2.5.2 | 1 | +1 / −1 | v2.5.2 |
| 2026-09-29 | `8302029` | Updated | Fixed: Every CSV row has the header's columns, the special columns export again, and the export honours read access | 20 | +392 / −373 |  |
| 2026-09-29 | `1cb7203` | Added | Added: Export handlers for date and time, time, keywords, tags and object relation | 5 | +73 / −3 |  |
| 2026-09-29 | `7f8f503` | Added | Added: A spreadsheet preview of the export, before downloading it | 10 | +939 / −21 |  |
| 2026-09-29 | `e37f2fd` | Updated | Updated: The CSV view's settings are three cards, and the sidebar explains the tool | 8 | +1410 / −107 |  |
| 2026-09-29 | `8fd0744` | Updated | Fixed: The in-place preview has every column on every server | 1 | +3 / −1 |  |
| 2026-09-29 | `17b8bf0` | Updated | Updated: The column list is part of the Columns card, and the class list counts the objects of every class | 7 | +579 / −51 |  |
| 2026-09-29 | `e23b382` | Added | Added: A site archive: every class below the chosen nodes in one ZIP or TAR file, one CSV per class | 17 | +2104 / −157 |  |
| 2026-09-29 | `2a8a10d` | Updated | Fixed: The site archive downloads whole on servers that end a request by throwing | 1 | +17 / −7 |  |
| 2026-09-29 | `62521c3` | Updated | Updated: The single class view names the datatype and meta information of every column | 9 | +573 / −4 |  |
| 2026-09-29 | `359275b` | Added | Added: A whole site scope for the single class export: every object of a class in one CSV | 6 | +112 / −14 |  |
| 2026-09-29 | `e96fc89` | Added | Added: Command line exports: ext:xrowextract:csv and ext:xrowextract:archive | 2 | +450 / −0 |  |
| 2026-09-29 | `2f57557` | Added | Added: Password hash and hash type columns for users allowed to export them | 13 | +163 / −21 |  |
| 2026-09-29 | `9bbaf3b` | Updated | Updated: Three scopes for the single class export, a readable datatype list, and the node is remembered | 7 | +158 / −13 |  |
| 2026-09-29 | `1f3cb82` | Added | Added: Content languages for the single class export: counts, all or some, one row per translation | 9 | +288 / −76 |  |
| 2026-09-29 | `b0787c7` | Added | Added: A column catalogue: attribute formats, grouped special columns, column sets, and an xrowmetadata handler | 12 | +1324 / −13 |  |
| 2026-09-29 | `261d094` | Updated | Fixed: Adding a column set, a column or all attributes shows what was added | 7 | +77 / −1 |  |
| 2026-09-29 | `bf68196` | Added | Added: Languages and column choices for the site archive, true translations only, and JSON/XML writers | 15 | +574 / −47 |  |
| 2026-09-29 | `f861e7e` | Added | Added: JSON and XML output, a Sites set for the archive, and smarter defaults for the single class view | 14 | +372 / −29 |  |
| 2026-09-29 | `fb426d1` | Updated | Fixed: The single class view no longer keeps a node and class saved under the old defaults | 5 | +38 / −1 |  |
| 2026-09-29 | `88dba09` | Updated | Fixed: Node links in the export views open the node | 2 | +2 / −2 |  |
| 2026-09-29 | `e2df53d` | Added | Added: The other direction of xrowextract exports, a CSV/JSON importer | 2 | +1166 / −0 |  |
| 2026-09-29 | `50ebee0` | Added | Added: An Import tab: upload, column mapping, dry run and apply | 6 | +525 / −1 |  |
| 2026-09-29 | `dbc35a1` | Added | Added: German and English strings for the import view | 3 | +588 / −0 |  |
| 2026-09-29 | `739e11b` | Added | Added: A background job runner for large CSV and archive exports | 5 | +588 / −3 |  |
| 2026-09-29 | `b67610e` | Added | Added: A "Run in the background" button and a Jobs page | 6 | +352 / −1 |  |
| 2026-09-29 | `fd7bf82` | Added | Added: A Jobs tab, job cards and live progress in the same xe- design | 6 | +277 / −1 |  |
| 2026-09-29 | `f816571` | Added | Added: German and English strings for the background jobs feature | 3 | +308 / −0 |  |
| 2026-09-29 | `b42702a` | Added | Added: Filters and a sort order for the exports: dates, section, state, visibility, name, attribute conditions | 12 | +1275 / −14 |  |
| 2026-09-29 | `00d174c` | Updated | Updated: Merge the background export jobs | 0 | +0 / −0 |  |
| 2026-09-29 | `2205203` | Updated | Updated: Merge the CSV and JSON import | 0 | +0 / −0 |  |
| 2026-09-29 | `360fd64` | Updated | Updated: The single class view's sidebar follows the view, and the translations are whole again | 4 | +185 / −6 |  |
| 2026-09-29 | `f3d116e` | Updated | Updated: The import view offers its options before an upload, a template per class, and its own sidebar | 8 | +662 / −5 |  |
| 2026-09-29 | `04ddab3` | Updated | Fixed: Choosing the parent for imported objects opens the content tree | 1 | +6 / −0 |  |
| 2026-09-29 | `c3cb542` | Updated | Updated: The Jobs page shows who started each job as a linked user bubble, and its queued, started and ended times as a timeline | 7 | +290 / −8 |  |
| 2026-09-29 | `fd9a536` | Updated | Updated: The Jobs page shows its totals as tiles: total, completed, running, queued and failed | 7 | +94 / −1 |  |
| 2026-09-29 | `f2c8a9d` | Added | Added: XML as a first-class import format, a one-click sample, and a complete file format reference | 6 | +693 / −20 |  |
| 2026-09-29 | `e82acf2` | Added | Added: German and English strings for XML, the sample and the reference | 3 | +852 / −0 |  |
| 2026-09-29 | `93005c5` | Updated | Updated: Merge the XML import format, the import sample and the file format reference | 0 | +0 / −0 |  |
| 2026-09-29 | `7922536` | Updated | Fixed: The import page fits the window again on desktop and mobile | 1 | +10 / −1 |  |
| 2026-09-29 | `e2691e0` | Updated | Fixed: A long language name no longer runs into the parent field on the import page | 5 | +8 / −6 |  |
| 2026-09-29 | `0eabe54` | Added | Added: xrowextract/package supports content packages (.ezpkg) | 7 | +1448 / −0 |  |
| 2026-09-29 | `36b078b` | Added | Added: a command line for content packages, ext:xrowextract:package | 1 | +208 / −0 |  |
| 2026-09-29 | `3de6839` | Updated | Updated: import links to the package template, tabs show Package | 2 | +14 / −3 |  |
| 2026-09-29 | `c8e3912` | Added | Added: German, English and untranslated strings for content packages | 3 | +1308 / −0 |  |
| 2026-09-29 | `d8e51c2` | Merged | Merge branch 'master' into wip/ezpkg-packages | 0 | +0 / −0 |  |
| 2026-09-29 | `600661c` | Added | Added: two more registered extended attribute filters, and a chain point in the language filter | 4 | +124 / −3 |  |
| 2026-09-29 | `2b5601e` | Added | Added: fetchalias.ini named fetches usable from the CSV export | 2 | +279 / −0 |  |
| 2026-09-29 | `f7ee687` | Added | Added: several conditions joined with and/or, kernel-level object/tree fields, an exact depth below the node, a second sort, and applying an extended filter or a named fetch | 9 | +1855 / −64 |  |
| 2026-09-29 | `3fb29d0` | Updated | Updated: The import preview says which class the rows go into, where, in which language, and names every object | 7 | +177 / −2 |  |
| 2026-09-29 | `8c2f271` | Updated | Updated: Merge master (c3cb542, the Jobs page user bubble and timeline; e2691e0, later import fixes) into the advanced-filters branch | 0 | +0 / −0 |  |
| 2026-09-29 | `1728690` | Updated | Updated: Try a sample works with any class, and the reference's XML section starts closed and remembers its state | 7 | +131 / −9 |  |
| 2026-09-29 | `35cb61f` | Updated | Fixed: The package template builder no longer publishes on the public site | 5 | +158 / −16 |  |
| 2026-09-29 | `f241dfa` | Updated | Fixed: The XML reference text no longer turns its tag names into page markup | 2 | +2 / −2 |  |
| 2026-09-29 | `1b84fae` | Updated | Fixed: The file format reference's sections other than XML start open again | 2 | +10 / −9 |  |
| 2026-09-29 | `24e71eb` | Updated | Updated: Merge content packages (.ezpkg): inspect, install, export, package template and command line | 0 | +0 / −0 |  |
| 2026-09-29 | `e3f34e6` | Updated | Fixed: The import preview's class summary uses no foreachelse, which the template engine does not know | 1 | +3 / −2 |  |
| 2026-09-29 | `4b676f8` | Updated | Fixed: The import and package pages no longer log "Datatype not found" errors | 1 | +14 / −0 |  |
| 2026-09-29 | `4363309` | Added | Added: Chunked upload of any size, with resume and per-user isolation | 4 | +622 / −2 |  |
| 2026-09-29 | `2aab54a` | Added | Added: saved export presets, more powerful than a fetchalias.ini named fetch alone | 13 | +1323 / −17 |  |
| 2026-09-29 | `78ddc73` | Updated | Updated: Merge advanced export filters, named fetches and saved export presets | 0 | +0 / −0 |  |
| 2026-09-29 | `f54defe` | Updated | Fixed: Two strings added by both merged branches appear once in the translations | 3 | +0 / −24 |  |
| 2026-09-29 | `083930a` | Added | Added: Every extract view keeps its place when a button reloads the page | 4 | +128 / −0 |  |
| 2026-09-29 | `0b73582` | Updated | Updated: XML, CSV and JSON import stream a row at a time instead of loading the whole file | 2 | +375 / −19 |  |
| 2026-09-29 | `f3fe4e4` | Added | Added: The import CLI streams any file size and can run as a background job | 3 | +174 / −31 |  |
| 2026-09-29 | `e31e3fc` | Added | Added: A large import queues as a background job and reports its size and resume point | 3 | +205 / −24 |  |
| 2026-09-29 | `d3330be` | Added | Added: Chunked upload progress, a queued-job notice and a resume control on the import page | 5 | +66 / −8 |  |
| 2026-09-29 | `7fee4ff` | Added | Added: German and English strings for chunked upload, queuing and job resume | 3 | +228 / −0 |  |
| 2026-09-29 | `592ad66` | Updated | Fixed: After a reload the highlight lands on the block that was being worked in, and is clearly visible | 2 | +36 / −8 |  |
| 2026-09-29 | `46e826a` | Other | Merged: master into wip/import-queue (package view, advanced filters, keep-place-on-reload) | 0 | +0 / −0 |  |
| 2026-09-29 | `0570a5b` | Updated | Fixed: xrowextract/package uploads a package into the wrong repository | 1 | +3 / −1 |  |
| 2026-09-29 | `476313b` | Added | Added: direct .ezpkg/class-XML/object-XML upload, inspection and install on xrowextract/import | 6 | +627 / −10 |  |
| 2026-09-29 | `e989da5` | Added | Added: background job support for large content-package imports | 2 | +30 / −3 |  |
| 2026-09-29 | `374f381` | Added | Added: translations for the content-package upload and reference strings | 3 | +552 / −0 |  |
| 2026-09-29 | `0d0b590` | Updated | Updated: Merge content packages, class XML and object XML in the import view, with the package format reference | 0 | +0 / −0 |  |
| 2026-09-29 | `f9e0732` | Updated | Fixed: --alias-param and --param keep every occurrence, and a node that never resolved no longer fetches silently | 1 | +35 / −29 |  |
| 2026-09-29 | `d52d6e3` | Updated | Updated: Merge master (083930a/592ad66, keeps its place across a reload) into the alias-param-node branch | 0 | +0 / −0 |  |
| 2026-09-29 | `3397d58` | Updated | Fixed: A package archive is accepted only when every entry is a plain file or folder | 1 | +13 / −1 |  |
| 2026-09-29 | `ed22981` | Updated | Updated: The import page shows content packages where one looks first | 8 | +205 / −5 |  |
| 2026-09-29 | `63e3731` | Other | Merged: master into wip/import-queue (content package / class-XML / object-XML upload, #25) | 0 | +0 / −0 |  |
| 2026-09-29 | `509b644` | Other | Merged: master into wip/import-queue (content packages shown up front) | 0 | +0 / −0 |  |
| 2026-09-29 | `b0b7d7a` | Updated | Updated: The Presets card explains itself, and a loaded preset (or one setting of it) can be unloaded | 7 | +608 / −76 |  |
| 2026-09-29 | `b3bcce8` | Updated | Updated: Merge the named fetch parameter fix and the reworked Presets card | 0 | +0 / −0 |  |
| 2026-09-29 | `fd8f7c1` | Updated | Updated: Merge imports of any size: chunked uploads with resume, streaming, and large imports queued as jobs | 0 | +0 / −0 |  |
| 2026-09-29 | `3858c84` | Renamed | Renamed: The Import and Package tabs are now Import content file and Import content package | 4 | +26 / −2 |  |
| 2026-09-29 | `fb189ec` | Updated | Fixed: The One class page fits a phone again with the Presets card and its More menu open | 1 | +6 / −2 |  |
| 2026-09-29 | `7acfe07` | Updated | Fixed: A preset's node placeholder sets the start node of the named fetch it extends, and every file type on the import page links to its reference | 3 | +18 / −10 |  |
| 2026-09-29 | `c4ee1ce` | Added | Added: sample and template content packages built read-only from existing content, and a richer package dry run | 1 | +617 / −4 |  |
| 2026-09-29 | `2e8a4ec` | Added | Added: streaming a built package or a single class/object XML as a download | 1 | +30 / −0 |  |
| 2026-09-29 | `4b53247` | Updated | Updated: Content packages are a fourth Import page format, just like XML/CSV/JSON | 3 | +154 / −106 |  |
| 2026-09-29 | `63be546` | Updated | Updated: The Import page's File/template cards show the content-package format the same way as XML/CSV/JSON | 3 | +150 / −19 |  |
| 2026-09-29 | `83292c8` | Updated | Updated: Merge content packages as a fourth import format: sample, template and the same dry run as XML, CSV and JSON | 0 | +0 / −0 |  |
| 2026-09-29 | `7ff7081` | Added | Added: German/English/untranslated strings for the content-package Import format | 3 | +228 / −0 |  |
| 2026-09-29 | `5fc3dbb` | Updated | Updated: Merge the German and English strings of the content package import format | 0 | +0 / −0 |  |
| 2026-09-29 | `5281309` | Updated | Fixed: The package-template scratch-content warning ignored the class actually chosen | 1 | +5 / −0 |  |
| 2026-09-29 | `e2d0600` | Updated | Updated: The File card is one format chooser, pass 1 of the redesign | 4 | +174 / −101 |  |
| 2026-09-29 | `6095e76` | Updated | Updated: Merge the File card redesign, pass 1: one format chooser with a tile per format | 0 | +0 / −0 |  |
| 2026-09-29 | `5b9a256` | Updated | Updated: File card pass 2 - one class choice, shorter text, no clipped shapes, aligned buttons | 4 | +44 / −9 |  |
| 2026-09-29 | `67ae0ab` | Added | Added: German/English/untranslated strings for the File card tiles, complete | 3 | +180 / −0 |  |
| 2026-09-29 | `810e1ac` | Updated | Updated: Merge the File card redesign, pass 2: one class choice, shorter texts, German for every string | 0 | +0 / −0 |  |
| 2026-09-29 | `83e497e` | Updated | Updated: File card pass 3 - focus and aria, keep-place lands on the tile, sample state | 3 | +32 / −25 |  |
| 2026-09-29 | `623a468` | Added | Added: German/English/untranslated strings for the File card's per-tile aria-labels | 3 | +60 / −0 |  |
| 2026-09-29 | `e58624a` | Updated | Updated: Merge the File card redesign, pass 3: focus, labels for screen readers, keep-place on the tile, sample badge | 0 | +0 / −0 |  |
| 2026-09-29 | `b4fed49` | Added | Added: Export as package from One class and Site archive (#27 part 2, item 1) | 8 | +195 / −24 |  |
| 2026-09-29 | `4a72680` | Added | Added: German/English/untranslated strings for Export as package | 3 | +36 / −0 |  |
| 2026-09-29 | `e0540f0` | Updated | Updated: Merge Export as package from One class and Site archive | 0 | +0 / −0 |  |
| 2026-09-29 | `1a4adb8` | Updated | Fixed: Try a sample never registers in the package repository | 2 | +171 / −27 |  |
| 2026-09-29 | `cd6ea87` | Added | Added: --keep for --export, and ext:xrowextract:package --clean | 1 | +50 / −9 |  |
| 2026-09-29 | `5098773` | Updated | Updated: Merge the package repository fix: samples and exports are no longer left in the repository | 0 | +0 / −0 |  |
| 2026-09-29 | `dffec42` | Updated | Fixed: The package template builder's sample image and file are accepted by the importer | 1 | +7 / −1 |  |
| 2026-09-29 | `9fdda6d` | Updated | Fixed: A package sample's update row loses its old -> new diff on Velocity | 2 | +48 / −0 |  |
| 2026-09-29 | `1653227` | Updated | Fixed: Uploading a package on the Package tab no longer fails with an error page, and is checked like any other upload | 4 | +26 / −19 |  |
| 2026-09-29 | `5552bd6` | Updated | Updated: Merge the fix for package dry runs on Velocity showing no field changes | 0 | +0 / −0 |  |
| 2026-09-29 | `d25575f` | Updated | Fixed: An uploaded package is opened by its absolute path | 1 | +5 / −0 |  |
| 2026-09-29 | `4325145` | Updated | Fixed: Package upload and download work on Velocity, exported packages can be imported again, and the Package tab builds templates from existing content | 4 | +59 / −8 |  |
| 2026-09-29 | `73977e7` | Updated | Fixed: Package uploads work on Velocity without touching its file layer | 2 | +11 / −21 |  |
| 2026-09-29 | `79b3c0a` | Updated | Updated: Installing a package runs as a background job instead of holding the page | 4 | +45 / −1 |  |
| 2026-09-29 | `cb38789` | Added | Added: A package install shows its real progress and its log live on the Jobs page | 11 | +270 / −6 |  |
| 2026-09-29 | `02e5817` | Updated | Updated: The Jobs page reads a job's own progress bar and shows its log without colour codes | 3 | +144 / −2 |  |
| 2026-09-29 | `5650b8a` | Added | Added: A queued or running job can be cancelled from the Jobs page | 6 | +79 / −0 |  |
| 2026-09-29 | `8f72ce6` | Updated | Fixed: A long job log shows its start and its end, without a line cut in two | 1 | +19 / −1 |  |
| 2026-09-29 | `eb8b512` | Updated | Updated: A job log keeps a short progress timeline instead of dropping every progress line | 8 | +99 / −35 |  |
| 2026-09-29 | `3691329` | Updated | Updated: The Package tab's upload goes through chunks, and renames a bad name | 5 | +266 / −48 |  |
| 2026-09-29 | `716e9c6` | Updated | Updated: A job log's progress timeline has no blank lines between its entries | 1 | +3 / −1 |  |
| 2026-09-29 | `aede1d6` | Updated | Updated: The Import page's package Apply also queues a background job | 5 | +138 / −5 |  |
| 2026-09-29 | `639735d` | Updated | Updated: Merge package installs as jobs from the import page, chunked package uploads and renaming of invalid package names | 0 | +0 / −0 |  |
| 2026-09-29 | `c71befd` | Updated | Updated: A package loaded on the import page gets its own review step instead of the row file steps | 4 | +123 / −5 |  |
| 2026-09-29 | `87330b9` | Updated | Fixed: A package install job shows its whole log, what it installed, and one date format | 8 | +353 / −9 |  |
| 2026-09-29 | `e6143ee` | Updated | Fixed: The package install button says "1 change" for one, and a package's dry run has no rich text note | 4 | +14 / −2 |  |
| 2026-09-29 | `d587107` | Updated | Updated: A package job says how many items were created and how many already existed, and what was done with them | 5 | +113 / −1 |  |
| 2026-09-29 | `d9a083b` | Updated | Updated: German for the Jobs page's "What was installed" | 3 | +12 / −0 |  |
| 2026-09-29 | `2a9b595` | Added | Added: A typed column manifest for every export, which the importer uses to map columns exactly | 11 | +1067 / −32 |  |
| 2026-09-29 | `5d3505d` | Added | Added: Scheduled exports and imports, delivery destinations and the export history | 46 | +6541 / −48 |  |
| 2026-09-29 | `64d3bb9` | Updated | Updated: German, English and untranslated strings for the Schedules tab, the manifest and the package pages | 3 | +3816 / −0 |  |
| 2026-09-29 | `dc6f5d8` | Updated | Updated: README and changelog describe the manifest, schedules, destinations and history | 2 | +50 / −0 |  |
| 2026-09-29 | `7526df2` | Updated | Updated: Merge master: the package contents browser, the install history and job cancelling, with the schedules | 0 | +0 / −0 |  |
| 2026-09-29 | `649c179` | Updated | Fixed: The Package tab opens a large package in a fraction of a second, a page of its objects at a time | 8 | +296 / −1 |  |
| 2026-09-29 | `2adeb78` | Renamed | Renamed: The extract tabs are One class of content export, Multi class of content export, Import content file, Import content package and Jobs, with Jobs last | 1 | +10 / −10 |  |
| 2026-09-29 | `03f04dd` | Added | Added: Scheduled exports and imports, destinations, run history and typed column manifests | 0 | +0 / −0 |  |
| 2026-09-29 | `1c15085` | Added | Added: The package contents browser (#26) | 14 | +624 / −3 |  |
| 2026-09-29 | `f2f6bae` | Added | Added: The package contents browser: every file of a package, paged, filtered and viewable | 0 | +0 / −0 |  |
| 2026-09-29 | `3f4bfb6` | Updated | Fixed: A package file opened directly cannot run script under the admin's origin | 1 | +4 / −1 |  |
| 2026-09-29 | `2b9f14f` | Added | Added: A 30+ preset catalogue and a Content package (.ezpkg) export format | 15 | +1839 / −50 |  |
| 2026-09-29 | `67a27e4` | Added | Added: The preset catalogue by audience and the Content package (.ezpkg) export format | 0 | +0 / −0 |  |
| 2026-09-29 | `0ad2603` | Updated | Fixed: The multi class export no longer offers a content package as the format of its files | 5 | +21 / −7 |  |
| 2026-09-29 | `7023414` | Updated | Fixed: A content package download that matches nothing says so next to the buttons | 2 | +9 / −3 |  |
| 2026-09-29 | `fd5a9e6` | Updated | Updated: The sidebars of the one and multi class exports describe the page as it is | 6 | +294 / −21 |  |
| 2026-09-29 | `17a44bd` | Updated | Fixed: A package class's attributes are read with their own datatype | 1 | +10 / −2 |  |
| 2026-09-29 | `9b33299` | Updated | Fixed: A cached package dry run is written where PHP-FPM can reuse it | 1 | +18 / −3 |  |
| 2026-09-29 | `c4915db` | Updated | Fixed: The Package tab links an existing object by its node, and wide tables scroll | 2 | +5 / −1 |  |
| 2026-09-29 | `4f26a96` | Added | Added: A datatype check, object filters and a compare view for content packages | 15 | +1151 / −52 |  |
| 2026-09-29 | `c909895` | Added | Added: An install history of content packages, and "Export these again" | 12 | +438 / −3 |  |
| 2026-09-29 | `7262c1f` | Added | Added: "Open in Import" for a repository package, and package --export with filters or a preset | 3 | +100 / −7 |  |
| 2026-09-29 | `605a3a1` | Updated | Updated: The Package, Import and Jobs sidebars follow their cards, and the package reference covers the new tools | 5 | +50 / −16 |  |
| 2026-09-29 | `460e277` | Added | Added: English and German texts for the package filters, datatype check, compare and install history | 3 | +1380 / −0 |  |
| 2026-09-29 | `ce96a85` | Updated | Fixed: The compare and contents browser pages escape a package name from the address | 2 | +9 / −5 |  |
| 2026-09-29 | `240d0de` | Added | Added: Package filters, compare, install history, datatype check, Open in Import and export with filters | 0 | +0 / −0 |  |
| 2026-09-29 | `cb19a0c` | Updated | Updated: The top menu tab and its tooltip have German translations | 3 | +33 / −0 |  |
| 2026-09-29 | `40d6e39` | Updated | Updated: Merge the German top menu tab and tooltip | 0 | +0 / −0 |  |
| 2026-09-30 | `7801ee8` | Added | Added: PHPStan configuration with the Exponential kernel and library classes known | 4 | +104 / −0 |  |
| 2026-09-30 | `be91b1c` | Updated | Updated: The command-line failure helper is declared as never returning | 7 | +14 / −7 |  |
| 2026-09-30 | `cc45957` | Updated | Updated: The XML import reads elements through DOMElement | 1 | +5 / −5 |  |
| 2026-09-30 | `5a892e6` | Updated | Updated: Translated messages with arguments pass no comment as null | 5 | +7 / −7 |  |
| 2026-09-30 | `ae53cd3` | Updated | Updated: Destination and schedule fetch() return their own class or null | 5 | +66 / −3 |  |
| 2026-09-30 | `429d931` | Updated | Fixed: A delivery to a destination of an unknown type reports no attempt | 1 | +1 / −1 |  |
| 2026-09-30 | `c042076` | Removed | Removed: The curl_close() call for PHP 7 | 1 | +2 / −2 |  |
| 2026-09-30 | `25118b5` | Updated | Updated: Conditions that can never change the result are removed | 11 | +27 / −23 |  |
| 2026-09-30 | `b7d9163` | Updated | Updated: PHPStan analyses at level 5 | 3 | +52 / −8 |  |
| 2026-09-30 | `8f23e2c` | Updated | Fixed: package --export with no usable node id ends with a message | 1 | +2 / −0 |  |
| 2026-09-30 | `279b418` | Updated | Fixed: Optional object and array parameters are declared nullable | 5 | +6 / −6 |  |
| 2026-09-30 | `dd2bd25` | Updated | Fixed: A scheduled import from a destination of an unknown type fails with a message | 1 | +7 / −2 |  |
| 2026-09-30 | `c68ae20` | Updated | Fixed: An unreadable zip entry or a failed list query no longer ends in PHP errors | 3 | +8 / −1 |  |
| 2026-09-30 | `021c7c1` | Updated | Fixed: A file that cannot be opened ends a transfer or an archive with a message | 6 | +13 / −1 |  |
| 2026-09-30 | `3110c02` | Updated | Fixed: A secret that is not UTF-8 is refused instead of stored empty | 1 | +12 / −3 |  |
| 2026-09-30 | `67298dd` | Updated | Fixed: A webhook is sent even when the run's text is not UTF-8 | 1 | +1 / −1 |  |
| 2026-09-30 | `82adc19` | Updated | Fixed: An imported file URL must be of this site's own scheme, host and port | 1 | +11 / −2 |  |
| 2026-09-30 | `baceff3` | Updated | Fixed: A history row keeps its lists when a text in them is not UTF-8 | 1 | +1 / −1 |  |
| 2026-09-30 | `78d41ca` | Updated | Updated: PHPStan analyses at level 6, without requiring type declarations | 2 | +12 / −1 |  |
| 2026-09-30 | `2399544` | Added | Added: A release gate, bin/check.sh, and a workflow running it on PHP 8.1 to 8.5 | 4 | +264 / −0 |  |
| 2026-09-30 | `d5d9a5d` | Added | Added: Unit tests for the parts that need no database, run by bin/check.sh | 13 | +998 / −36 |  |
| 2026-09-30 | `a889ef6` | Updated | Fixed: import refuses an unknown --match, --language, --resume-from or --parent | 1 | +11 / −0 |  |
| 2026-09-30 | `4190997` | Updated | Fixed: package --install --dry-run checks --object-mode and --class-mode too | 1 | +7 / −6 |  |
| 2026-09-30 | `8197d58` | Updated | Fixed: destination --config, --secret-env and --secret-file refuse a value without key= | 1 | +8 / −5 |  |
| 2026-09-30 | `ff624c2` | Added | Added: An integration test of every command against a test installation | 12 | +224 / −0 |  |
| 2026-09-30 | `61d6752` | Added | Added: A view smoke test of every admin view in a browser | 2 | +179 / −0 |  |
| 2026-09-30 | `b10b6bd` | Updated | Fixed: A background job whose runner dies is marked failed, not left running | 2 | +88 / −4 |  |
| 2026-09-30 | `db2c48d` | Updated | Fixed: Caches that belong to one request no longer outlive it under Velocity | 6 | +83 / −14 |  |
| 2026-09-30 | `e814e08` | Updated | Fixed: A PHP error in an export, transfer or import row is handled like an exception | 9 | +15 / −15 |  |
| 2026-09-30 | `6180411` | Updated | Fixed: A disabled proc_open(), exec() or shell_exec() is a message, not a fatal error | 3 | +11 / −4 |  |
| 2026-09-30 | `14c00a3` | Updated | Fixed: An id from a form, an address or a command line is checked before a query | 19 | +87 / −54 |  |
| 2026-09-30 | `f3b018b` | Updated | Fixed: The site archive view refuses an array posted for a single choice | 1 | +17 / −9 |  |
| 2026-09-30 | `8c5b423` | Updated | Fixed: The tab row no longer redefines the Jobs view's schedule_alerts variable | 1 | +4 / −3 |  |
| 2026-09-30 | `eb11b3c` | Added | Added: A POST robustness test of every view, and log watching in the integration tests | 7 | +249 / −15 |  |
| 2026-09-30 | `48c0356` | Updated | Updated: bin/check.sh --help shows the whole header | 1 | +1 / −1 |  |
| 2026-09-30 | `7abb26e` | Updated | Updated: CHANGELOG for 2.5.3 | 1 | +13 / −1 |  |
| 2026-09-30 | `414dfe7` | Updated | Updated: Version 2.5.3 | 1 | +1 / −1 | v2.5.3 |
| 2026-09-30 | `2dc619d` | Updated | Updated: Type declarations in the import and upload classes and views | 2 | +345 / −103 |  |
| 2026-09-30 | `e495652` | Updated | Updated: Type declarations in the package classes and views | 1 | +397 / −116 |  |
| 2026-09-30 | `04c5e0f` | Updated | Updated: Type declarations in the job, archive, manifest, history and preset classes and views | 5 | +516 / −174 |  |
| 2026-09-30 | `a33556c` | Updated | Updated: Type declarations in the schedule, destination, transport, notifier and secrets classes | 14 | +597 / −205 |  |
| 2026-09-30 | `fbb0768` | Updated | Updated: Type declarations in the CSV export, filter, column and datatype handler classes | 42 | +673 / −190 |  |
| 2026-09-30 | `4eadf78` | Updated | Updated: The stray doc comment of uniquePackageName() sits on that method again | 1 | +1 / −1 |  |
| 2026-09-30 | `1362b4a` | Updated | Updated: PHPStan checks every parameter, return and property type (level 6 in full) | 3 | +3 / −12 |  |
| 2026-09-30 | `512b158` | Added | Added: One check of what the extension needs from PHP and the server | 4 | +590 / −0 |  |
| 2026-09-30 | `0db904f` | Updated | Updated: bin/check.sh runs the requirements check | 2 | +42 / −2 |  |
| 2026-09-30 | `3d2fd40` | Updated | Updated: Each page names the features this server cannot offer there, and what they miss | 10 | +600 / −7 |  |
| 2026-09-30 | `b4089a5` | Updated | Fixed: Viewing an empty XML file of a package no longer ends in a fatal error | 2 | +30 / −3 |  |
| 2026-09-30 | `c84914b` | Updated | Fixed: A schedule with text that is not UTF-8 is refused instead of saved with an empty definition | 2 | +82 / −16 |  |
| 2026-09-30 | `2e9df8b` | Updated | Fixed: A job whose error text is not UTF-8 no longer disappears from the Jobs page | 2 | +62 / −1 |  |
| 2026-09-30 | `7696495` | Updated | Fixed: A scheduled import deleted from the Jobs page while it runs ends its runner with a message | 1 | +5 / −0 |  |
| 2026-09-30 | `73967e4` | Updated | Fixed: The manifest of an export whose class no longer exists is written instead of a fatal error | 1 | +3 / −1 |  |
| 2026-09-30 | `a7932b0` | Updated | Fixed: Scanning the host keys of an SFTP destination without the OpenSSH client reports it instead of failing the page | 2 | +50 / −4 |  |
| 2026-09-30 | `85d4091` | Updated | Fixed: The Destinations page lists a destination of an unknown type instead of failing | 1 | +3 / −1 |  |
| 2026-09-30 | `109fecf` | Updated | Updated: The job, archive, manifest and history code passes PHPStan level 8 | 10 | +62 / −27 |  |
| 2026-09-30 | `9709db3` | Updated | Updated: The import, upload and package classes and views pass PHPStan level 8 | 8 | +47 / −35 |  |
| 2026-09-30 | `1a6325c` | Updated | Fixed: Word counts and plain text of a text that is not valid UTF-8 are exported instead of failing | 2 | +53 / −3 |  |
| 2026-09-30 | `9d81aab` | Updated | Updated: The schedule, destination, notification and transport code passes PHPStan level 8 | 14 | +65 / −38 |  |
| 2026-09-30 | `6ba8f4c` | Updated | Fixed: A saved preset with text that is not valid UTF-8 is no longer stored empty | 2 | +45 / −1 |  |
| 2026-09-30 | `e04d6fb` | Updated | Fixed: A CSV download with its manifest sends the file alone when the zip cannot be written | 1 | +9 / −2 |  |
| 2026-09-30 | `9e2b670` | Updated | Updated: The CSV export, catalogue, column, schema and datatype handler code passes PHPStan level 8 | 15 | +79 / −61 |  |
| 2026-09-30 | `7c2bd9f` | Updated | Updated: PHPStan runs at level 8 | 3 | +12 / −2 |  |
| 2026-09-30 | `7fc3c48` | Updated | Updated: Translations of the refused non-UTF-8 schedule and destination values, with German | 3 | +24 / −0 |  |
| 2026-09-30 | `9c79e44` | Updated | Updated: README describes the requirements check, the req part of bin/check.sh and PHPStan level 8 | 1 | +27 / −2 |  |
| 2026-09-30 | `e569ab6` | Updated | Fixed: A csv.ini handler class that is not there is logged and left out instead of ending the export | 2 | +77 / −10 |  |
| 2026-09-30 | `00ada27` | Updated | Updated: CHANGELOG for 2.5.4 | 1 | +12 / −0 |  |
| 2026-09-30 | `e9935e8` | Updated | Updated: Version 2.5.4 | 1 | +1 / −1 | v2.5.4 |

## 2026-10 (6 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-02 | `0c31776` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 48 | +9153 / −8096 |  |
| 2026-10-02 | `964e0c6` | Updated | Updated: The commands and cronjob parts list a description of what they do | 2 | +2 / −0 |  |
| 2026-10-02 | `72ba301` | Updated | Updated: The commands and cronjob parts list a description of what they do | 2 | +2 / −0 |  |
| 2026-10-02 | `26ea832` | Updated | Updated: Version 2.5.5 | 1 | +1 / −1 | v2.5.5 |
| 2026-10-02 | `8de6d30` | Updated | Updated: The commands start through the shared command helpers | 9 | +27 / −45 |  |
| 2026-10-02 | `fbea964` | Updated | Updated: Version 2.5.6 | 1 | +1 / −1 | v2.5.6 origin/master origin/HEAD |
