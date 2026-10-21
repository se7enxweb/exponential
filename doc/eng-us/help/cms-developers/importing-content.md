---
title: Importing content
audience: cms-developer
category: cms-development
order: 27
summary: For CMS developers loading content into a site, the import tools that exist and which one fits which source. It covers xrowextract, the kernel's ezcsvimport, packages, the ZIP importer, office documents, RSS import, layouts transfer files and the HTTP interfaces, and when a script of your own is the right answer.
keywords: import, content import, CSV import, xrowextract, ezcsvimport, packages, ezpkg, ezmultiupload, ZIP import, ezodf, RSS import, layouts export import, REST, expservices, migration, remote id, dry run
type: help
---
# Importing content

This page is for developers who have to fill a site with content from somewhere else: a spreadsheet, another CMS,
an old Exponential site, a folder of photos. Use the tools that exist first and write a script only for what they
cannot do. Every tool below is described in depth in
[Exponential's import and export tools](../../install/migration/exponential-tools.md); this page helps you choose.

## Contents

1. [Which tool for which source](#which-tool-for-which-source)
2. [xrowextract: the main importer](#xrowextract-the-main-importer)
3. [ezcsvimport: the kernel's simple importer](#ezcsvimport-the-kernels-simple-importer)
4. [Packages: between Exponential installations](#packages-between-exponential-installations)
5. [Media, documents and feeds](#media-documents-and-feeds)
6. [Layouts](#layouts)
7. [Over HTTP](#over-http)
8. [A script of your own](#a-script-of-your-own)
9. [Rules for every import](#rules-for-every-import)

## Which tool for which source

| Source | First choice | Notes |
|---|---|---|
| A spreadsheet or CSV of one kind of item | xrowextract with `--map` | dry run by default |
| An export of another CMS (WordPress, Drupal, Joomla) | convert to CSV, XML or JSON, then xrowextract | no tool reads those exports directly |
| Another Exponential site | packages, or a copy of the database when the whole site moves | |
| A folder of images and files | the ZIP importer of the upload page | keeps the folder structure if asked |
| Office documents | `ezodf` | needs LibreOffice or OpenOffice on the server |
| A news feed during a transition | RSS import | only the feed's newest items |
| Layouts and their rules | `exp:layouts export` and `import` | between Exponential installations |
| A system reachable only over HTTP | a script of your own with the kernel API | |

The full table: [Which tool for which job](../../install/migration/exponential-tools.md#5-which-tool-for-which-job).

## xrowextract: the main importer

`xrowextract` (`se7enxweb/xrowextract`, required by the root `composer.json`) reads CSV, XML or JSON and creates or
updates objects of one class below a parent node. It writes nothing without `--apply`, can match existing objects for
updates, maps foreign columns to attributes, runs large files in the background and resumes after an interruption:

```bash
./console ext:xrowextract:import --file=var/tmp/import/events.csv --class=exp_event --parent=<node id> \
    --map=title=title,start=start_date
./console ext:xrowextract:import --file=var/tmp/import/events.csv --class=exp_event --parent=<node id> \
    --map=title=title,start=start_date --apply
```

The easiest way to get a format right: export one sample object of the target class with xrowextract and copy its
columns ([xrowextract](../../install/migration/exponential-tools.md#xrowextract-csv-xml-and-json-import-se7enxwebxrowextract),
[Importing with xrowextract](../trainers/migration/module-03-import-and-export-tools/importing-with-xrowextract.md)).

## ezcsvimport: the kernel's simple importer

```bash
./console exp:ezcsvimport --class=exp_event --creator=<user object id> --storage-dir=var/tmp/import/files <node id> var/tmp/import/events.csv
```

The file uses `;` as separator and has no header row: column *n* goes to the class's *n*-th attribute through
`fromString()`. It only creates, so a second run makes duplicates; use it for a first load of a simple class.

## Packages: between Exponential installations

Packages (`.ezpkg`) carry classes and content objects with their files from one Exponential installation to another,
and are the way to ship classes with your project ([Packages](../../guides/packages.md),
[Site packages and installers](site-packages-and-installers.md)). They are not a format other systems produce.

## Media, documents and feeds

- **ZIP importer** (`ezmultiupload`): each file becomes an image or file object by type; *Keep the folder structure*
  makes folders. Limits are in `ezmultiupload.ini [ZipSettings]`. Images whose class requires an alternative text are
  skipped, because a ZIP has none ([Media and uploads](../media-and-uploads.md)).
- **Office documents** (`ezodf`): Writer documents to content objects and back.
- **RSS import** (**Setup** > **RSS**): new feed items become objects of a chosen class; the `rssimport.php`
  cronjob runs it ([RSS feeds](../../guides/rss-feeds.md)).

## Layouts

```bash
./console exp:layouts export --all --output=var/tmp/layouts.zip
./console exp:layouts import var/tmp/layouts.zip --dry-run
```

Layouts, shared layouts and their mapping rules travel as one ZIP file
([Export and import layouts](../build-two-ways/export-and-import-layouts.md)).

## Over HTTP

The REST interface can create and remove nodes at v2 (updates answer 501, not implemented yet), and `expservices`
offers the administration's functions as HTTP services ([The REST API](the-rest-api.md),
[Remote services and apps](../../guides/remote-services-and-apps.md)). Use them when the importer must run on another
machine; for large imports a script on the server is faster.

## A script of your own

When no tool fits, write a script with the kernel API: read the source, map each item, then create or update through
`eZContentFunctions` with a remote ID per item, so runs can be repeated
([Content from PHP](content-from-php.md), and the skeleton in
[A custom import script](../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton)).

## Rules for every import

1. **Model first.** Create the classes and the folder tree before importing ([Designing a content model](designing-a-content-model.md)).
2. **Remote IDs from the source** (`wp-1234`, `crm-42`) on every object: updates instead of duplicates, and relations
   resolvable in a second pass.
3. **Media first**, then containers in parent order, then items, then relations and inner links.
4. **Dry run, then a copy, then live.** Keep the source files out of the web root (`var/tmp/` is fine on a
   development machine).
5. **Afterwards**: rebuild the search index (`php bin/php/updatesearchindex.php --clean`), check URL aliases, clear
   the content caches ([Search](search.md), [URL aliases and routing](url-aliases-and-routing.md)).
6. **No personal data you do not need.** Import user accounts only with a legal basis, and test with addresses at
   `example.invalid`.

## Related pages

- [Mapping content to Exponential](../../install/migration/content-model-mapping.md)
- [Migration checklist](../../install/migration/checklist.md)
- [Choosing the right import or export tool](../trainers/migration/module-03-import-and-export-tools/choosing-the-right-tool.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
