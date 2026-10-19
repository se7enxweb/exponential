---
title: Exponential's import and export tools
audience: developer, admin
category: install-upgrade
order: 7
summary: Every import and export tool that really exists in Exponential 6 and the extensions it requires - xrowextract (CSV, XML, JSON, site archives, packages), ezcsvimport/ezcsvexport, packages, the ZIP importer of ezmultiupload, ezodf, RSS import, exp:layouts, the REST API and expservices, database dumps - and the skeleton of a custom import script.
keywords: import, export, xrowextract, csv, json, xml, ezpkg, package, ezpm, ezcsvimport, ezcsvexport, ezmultiupload, zip, ezodf, rss import, exp:layouts, REST, expservices, ezsqldumpschema, sevenx_dse, createAndPublishObject, remote id, script
type: install
---
# Exponential's import and export tools

This page lists the tools a migration can use, what each one reads and writes, and the commands as they are in the
code. Use them before writing your own script; when you do write one, start from the
[skeleton in section 4](#4-a-custom-import-script-the-skeleton).

Each tool names the file it was read from. Tools that come from an extension are available when that extension is
installed and active; the extensions named here are all required by the root `composer.json` of Exponential 6.0.15.
Run console commands from the installation root; on a server where you work as root, add `--allow-root-user`.

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping content to Exponential](content-model-mapping.md)

## Contents

- [1. Import into Exponential](#1-import-into-exponential)
- [2. Export from Exponential](#2-export-from-exponential)
- [3. URLs, redirects and metadata](#3-urls-redirects-and-metadata)
- [4. A custom import script: the skeleton](#4-a-custom-import-script-the-skeleton)
- [5. Which tool for which job](#5-which-tool-for-which-job)

## 1. Import into Exponential

### xrowextract: CSV, XML and JSON import (`se7enxweb/xrowextract`)

The most complete importer. It reads the XML, CSV or JSON files that xrowextract itself writes, or any file whose
columns you map, and creates or updates objects of one class below a parent node. It streams the file (no size
limit for XML and CSV), checks the user's permissions, runs as a background job, and resumes after an interruption.
From `extension/xrowextract/bin/php/import.php`:

```bash
# dry run first (nothing is written without --apply)
./console ext:xrowextract:import --file=articles.xml --class=ng_article --parent=2
./console ext:xrowextract:import --file=articles.xml --class=ng_article --parent=2 --apply
# update existing objects matched by object id, in one language
./console ext:xrowextract:import --file=export.csv --match=object_id --language=ger-DE
# map columns of a foreign file to attributes
./console ext:xrowextract:import --file=export.csv --map=title=title,authors-ids=authors:ids
# large files: in the background, resumable
./console ext:xrowextract:import --file=big.xml --class=ng_article --parent=2 --apply --background
./console ext:xrowextract:import --file=big.xml --class=ng_article --parent=2 --apply --resume-from=40001
```

The admin has the same importer: the node view's **Import/Export** button, *Import into this item*
(`xrowextract/import/(parent)/<node id>`). Every export of xrowextract carries a **typed column manifest**
(`<file>.manifest.json`: per column its datatype, format, language and import target); a file with a manifest maps
back exactly, and `--manifest`, `--no-manifest` and a ZIP of file plus manifest are accepted.

For a migration this means: convert the source into CSV, XML or JSON with one column per attribute (the easiest way
to get the format right is to export one sample object of the target class with xrowextract and copy its columns),
then import with `--map` and a dry run first.

### `ezcsvimport` (kernel)

The kernel's simple importer (`kernel/private/classes/commands/ezcsvimport.php`):

```bash
./console exp:ezcsvimport --class=article --creator=14 --storage-dir=var/tmp/import/files 2 var/tmp/import/articles.csv
```

The arguments are the parent node id and the file. The file is read with `;` as separator and `"` as quote, **with no
header row**: column *n* goes to the class's *n*-th attribute, through the datatype's `fromString()`. Image and
binary file columns name a file below `--storage-dir`. It creates only (no update), so use it for a first load of a
simple class; prefer xrowextract for anything else.

### Packages (`.ezpkg`)

Packages carry content classes and content objects (with their files) from one Exponential installation to another
([Packages](../../guides/packages.md)). From the shell (`ezpm.php` in the root):

```bash
php ezpm.php export <package> -d var/tmp      # write an .ezpkg
php ezpm.php import var/tmp/<package>.ezpkg  # import it into the package repository
php ezpm.php help                            # every command
```

xrowextract adds a package command line (`extension/xrowextract/bin/php/package.php`):

```bash
./console ext:xrowextract:package --export --node=2 --subtree --class=ng_article --file=var/tmp/articles.ezpkg
./console ext:xrowextract:package --inspect=my_package
./console ext:xrowextract:package --install=my_package --parent=2 --dry-run
./console ext:xrowextract:package --install=my_package --parent=2 --object-mode=update --class-mode=skip
./console ext:xrowextract:package --template --class=ng_article --variant=both --file=var/tmp/ng_article_template.ezpkg
```

Packages are the tool for moving **between Exponential installations** (and from older releases of this kernel). They
are not a format other systems produce.

### The ZIP importer of the upload page (`se7enxweb/ezmultiupload`)

The multi-file upload page can unpack ZIP files into content: each file becomes an image or file object by its type,
and *Keep the folder structure* makes a folder object for each folder of the archive. It is off until the user
switches *Import compressed zip file(s) contents* on, and `ezmultiupload.ini [ZipSettings]` limits depth, size, entry
count and compression ratio (ZIP bombs are refused). Images whose class requires an alternative text are skipped,
because a ZIP has none. Source: `extension/ezmultiupload/doc/upload-page.md`. Use it to bring over a media library.

### Office documents (`se7enxweb/ezodf`)

Imports OpenOffice/LibreOffice Writer documents as content objects and exports objects as Writer documents (and, with
the conversion daemon, Word and PDF). It needs OpenOffice or LibreOffice on the server. Source:
`extension/ezodf/README`. Useful when the old "site" is a set of documents.

### RSS import (kernel)

**Setup > RSS** can import a feed into content: each new item becomes an object of the chosen class below a node,
with the feed's fields mapped to attributes; the `rssimport` cronjob part runs it
([RSS feeds](../../guides/rss-feeds.md)). It is a way to keep pulling a blog in during a transition. A feed only
holds its newest items, so it is not a full migration by itself.

### Layouts (`se7enxweb/explayouts`)

Layouts, shared layouts and their mapping rules move between Exponential installations as one ZIP transfer file
(`extension/explayouts_ui/bin/php/layouts.php`):

```bash
./console exp:layouts list [--drafts] [--json]
./console exp:layouts export [--all] [--only=<layouts>] [--shared=all|<layouts>] [--rules=none|selected|all] [--output=<file.zip>]
./console exp:layouts import <file.zip> [--dry-run] [--strategy=skip|overwrite|copy] [--publish] [--unmapped=drop|keep|fail]
```

### The REST API and `expservices`

- **REST** (`se7enxweb/ezprestapi`): reads at `/api/ezp/v1/...` and `/api/ezp/v2/...`; creating a node is
  `POST /api/ezp/v2/content/node/create`, deleting `.../content/node/delete/<id>`; updating answers 501 (not
  implemented yet). Authentication with an OAuth2 token or a personal API key. Details:
  [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md).
- **`expservices`** (in the kernel's `extension/` directory): the admin's functions as named HTTP services, with a
  shell client and Python examples ([Remote services and apps](../../guides/remote-services-and-apps.md)).

Use the HTTP interfaces when the importer runs on another machine (for example a Node.js script). For large imports a
script on the server ([section 4](#4-a-custom-import-script-the-skeleton)) is faster and simpler.

### Coming from LoveStack

`./console exp:explovestack` checks an installation that ran LoveStack (read-only) and records its database as
Exponential's. The steps are in [LoveStack: migration](../../bc/6.0/love/MIGRATION.md).

## 2. Export from Exponential

| Tool | Writes | Use it for |
|---|---|---|
| xrowextract CSV export: `./console ext:xrowextract:csv --class=ng_article --node=2 --output=articles.csv` (also XML, JSON; `--list-classes`, `--list-columns`, `--preview=10`) | one file per class, with a typed manifest | moving content to another system; spreadsheets |
| xrowextract site archive: `./console ext:xrowextract:archive --set=everything --format=tar.xz --output=var/backups/` | per class a CSV (object id, remote id, main node, parent node, URL alias, dates, every attribute), `manifest.json`, `README.txt`, as ZIP, TAR.GZ, TAR.BZ2 or TAR.XZ. Password hashes are never exported | a complete, self-describing export of a site |
| xrowextract packages: `./console ext:xrowextract:package --export ...` | `.ezpkg` | other Exponential installations |
| `./console exp:ezcsvexport` | one CSV per class for a subtree (`--storage-dir` for files) | a quick dump |
| `php ezpm.php export` | `.ezpkg` | classes and content to another Exponential |
| `./console exp:layouts export` | a ZIP of layouts and rules | layouts to another Exponential |
| `./console ext:bccie:export --object=ID --format=csv\|sylk` (`se7enxweb/bccie`) | collected information (form submissions) as CSV or SYLK | form data |
| `./console exp:mailconsent` (export as CSV) | the consent log of the e-mail preferences | data protection records |
| `./console exp:ezsqldumpschema --output-types=all ...` | the database schema and data in Exponential's neutral format or SQL | a full database copy, any engine |
| `sevenx_dse` (admin database editor, `se7enxweb/sevenx_dse`) | SQL dump, CSV, TSV of tables | single tables, by an administrator |
| REST v1/v2 reads | JSON of nodes, children and fields | another system pulling content over HTTP |
| ezodf | Writer, Word, PDF documents | documents for people |

The storage directory (`var/<site>/storage/`) holds the original images and files; copy it together with any export
that references files. Details on leaving Exponential: [Migrating away from Exponential](leaving-exponential.md).

## 3. URLs, redirects and metadata

| Need | Tool |
|---|---|
| An old address that should now show or redirect to a node | a custom URL alias on the node (its **URL aliases** tab), redirecting by default; in code `eZURLAliasML::storePath()` with the action `eznode:<node id>` |
| A group of old addresses with a pattern | URL wildcards (**Setup > URL wildcards**), with *Redirecting URL* for a 301 |
| A module view or address of your choosing | global URL aliases (**Setup > URL translator**) |
| Rebuild the aliases of all nodes after an import | `./console exp:updateniceurls --update-nodes` |
| Check the alias tables | `./console exp:verify_aliases` (`--fix` repairs safe issues) |
| Title, keywords, description per page; XML sitemap | `xrowmetadata` (datatype and the `sitemap` cronjob part) |
| XML sitemap | `bcgooglesitemaps` (cronjob parts `generate`, `generatemultilingual`) |
| Broken links and relations after an import | the link checker ([URLs and aliases](../../guides/urls-and-aliases.md)), `./console exp:contentcheck` |

All of these are described in [Links, URL aliases, wildcards and search statistics](../../guides/urls-and-aliases.md).

## 4. A custom import script: the skeleton

When the tools above cannot read your source, write a PHP script that reads the source export and creates objects
through the kernel's API. Run it with the Exponential bootstrap:

```bash
./console exp:ezexec path/to/your_import.php
```

The skeleton below uses only kernel functions that exist in 6.0.15 (`kernel/classes/ezcontentfunctions.php`,
`kernel/classes/ezcontentobject.php`, `kernel/classes/datatypes/ezxmltext/`, `kernel/classes/ezurlaliasml.php`).
It is a starting point, not a finished importer: the reading of the source, the mapping and the error handling are
yours.

```php
<?php
// your_import.php: run with ./console exp:ezexec your_import.php
$cli = eZCLI::instance();
$parentNodeId = 2;                 // where the items go
$classIdentifier = 'article';     // the target class
$language = 'eng-GB';             // the target translation

// 1. Read your source export (WXR, JSON, CSV ...) into plain arrays. Your code.
$items = array(); // each: array( 'source_id' => ..., 'title' => ..., 'html' => ..., 'old_path' => ... )

foreach ( $items as $item )
{
    $remoteId = 'src-' . $item['source_id'];          // stable: re-runs update instead of duplicating
    $object = eZContentObject::fetchByRemoteID( $remoteId );

    $attributes = array( 'title' => $item['title'] ); // identifier => fromString() value

    if ( !$object )
    {
        $node = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id'   => $parentNodeId,
            'class_identifier' => $classIdentifier,
            'remote_id'        => $remoteId,
            'language'         => $language,
            'attributes'       => $attributes,
        ), false );                                    // false: no notifications for imported content
        if ( !$node ) { $cli->error( "create failed: $remoteId" ); continue; }
        $object = $node->attribute( 'object' );
    }

    // 2. Rich text: HTML reduced to the simplified input (p, b/strong, i/em, a href, h1-h6, ul/ol/li, table/tr/td)
    //    and parsed into XmlText, as the RSS import does (kernel/private/classes/cronjobs/rssimport.php).
    $parser = new eZSimplifiedXMLInputParser( $object->attribute( 'id' ), false, 0, false );
    $document = $parser->process( str_replace( array( "\r", "\n", "\t" ), array( '', '', ' ' ), $item['html'] ) );
    if ( is_object( $document ) )
    {
        $attributes['body'] = eZXMLTextType::domString( $document );
    }

    eZContentFunctions::updateAndPublishObject( $object, array(
        'language'   => $language,
        'attributes' => $attributes,
    ), false );

    // 3. Keep the old address: a redirecting alias on the node (check what cleanURL() makes of it).
    if ( !empty( $item['old_path'] ) )
    {
        eZURLAliasML::storePath( $item['old_path'], 'eznode:' . $object->attribute( 'main_node_id' ), $language );
    }
    $cli->output( "ok $remoteId" );
}
```

The `fromString()` formats of the common datatypes, as the kernel reads them:

| Datatype | String |
|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezcountry` | the text |
| `ezxmltext` | the stored XML (convert HTML first, as above) |
| `ezinteger`, `ezfloat` | the number |
| `ezboolean` | `1` or `0` |
| `ezdate`, `ezdatetime` | a Unix timestamp |
| `ezimage` | `path/to/file.jpg\|alternative text` |
| `ezbinaryfile` | `path/to/file\|original file name` |
| `ezurl` | `https://example.org\|link text` |
| `ezkeyword` | comma separated keywords |
| `ezobjectrelationlist` | object ids separated by `-` |
| `ezselection` | option names separated by `\|` |
| `ezauthor` | authors separated by `&`, each `name\|email\|id` |
| `ezmatrix` | rows separated by `&`, cells by `\|` |
| `ezuser` | `login\|email\|...` (`eZUserType::parseStringRepresentation()`) |

File paths of image and file attributes are prefixed with the `storage_dir` parameter of `createAndPublishObject()` /
`updateAndPublishObject()` when you give one (it is put in front as it is, so end it with a `/`).

Good practice for the script:

- Import in **dependency order**: media first, then content that relates to it, then content that relates to
  content; resolve relations by remote id in a second pass.
- Run it as a dedicated **import user** with exactly the rights it needs; set the creator/owner afterwards if the
  source authors matter ([changing owners](../../guides/changing-owners.md)).
- Rebuild the search index after the import: `./console exp:updatesearchindex`. Clear the content cache:
  `./console exp:cache`.
- Log to a file, and make every run repeatable.

## 5. Which tool for which job

| Source | First choice | Then |
|---|---|---|
| Another Exponential 6 | packages, xrowextract packages, `exp:layouts` | a database copy, when the whole site moves |
| An older release of this kernel | the database update chain ([chapter 11](../11-upgrading.md)) | packages for parts |
| A spreadsheet or any system that writes CSV | xrowextract import with `--map` | `ezcsvimport` for simple classes |
| A system with an XML or JSON export | convert to xrowextract's format, or a custom script | |
| A system with a REST API only | a custom script that reads the API and writes with the kernel API | |
| A media library | ezmultiupload ZIP import | xrowextract for metadata |
| Office documents | ezodf | |
| A feed | RSS import | |

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
