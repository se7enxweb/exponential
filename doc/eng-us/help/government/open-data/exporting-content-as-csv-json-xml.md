---
title: Exporting content as CSV, JSON or XML
audience: government, admin, editor, developer
category: public-sector
order: 120
summary: How to turn content held in Exponential (registers, lists of service points, directories kept as content objects) into open data files: the xrowextract Export tab and its commands, the kernel's exp:ezcsvexport, and the exports of collected information and user lists.
keywords: CSV export, JSON export, XML export, xrowextract, ezcsvexport, exp:ezcsvexport, export tab, register, directory, bulk export, scheduled export, open data
type: help
---
# Exporting content as CSV, JSON or XML

This page is for administrators and editors whose data already lives in Exponential as content: a list of service
points, a register of decisions, a directory of facilities. Instead of keeping a separate spreadsheet, export the
content itself as a dataset file. The tools are the `xrowextract` extension
([xrowextract](../../../features/6.0/extensions/xrowextract.md)) and the kernel command
[exp:ezcsvexport](../../../examples/commands/ezcsvexport.md).

## Contents

1. [Which tool](#which-tool)
2. [The Export tab of xrowextract](#the-export-tab-of-xrowextract)
3. [From the command line](#from-the-command-line)
4. [The kernel command exp:ezcsvexport](#the-kernel-command-expezcsvexport)
5. [Publishing the exported file](#publishing-the-exported-file)
6. [Before you publish an export](#before-you-publish-an-export)

## Which tool

| Need | Tool |
|---|---|
| One class to CSV, JSON or XML, with a preview, filters and saved presets | `xrowextract`, **Export > One class of content export** |
| Every class below a node as one archive | `xrowextract`, **Multi class of content export** |
| A regular export delivered to a folder, SFTP or S3 | `xrowextract` **Schedules** |
| A quick CSV per class from the shell, no extension | `exp:ezcsvexport` |
| Form submissions | **Setup > Collected information**, CSV export per form ([Collected information](../../../guides/collected-information.md)) |

`xrowextract` is installed with Exponential but must be active on your site (`ActiveExtensions[]`); ask your
administrator. Its pages need the extension's policies ([xrowextract policies](../../../features/6.0/extensions/xrowextract.md#policies)).

## The Export tab of xrowextract

1. Open **Export** in the top menu (`/xrowextract/csv`), then **One class of content export**.
2. Pick the class (for example your `service_point` class) and the start node.
3. Choose the format: CSV, JSON or XML. Choose the languages if the content is translated: one row per translation.
4. Add filters if needed (date, section, state, visibility, attribute values).
5. Press **Preview** to see the first rows as a spreadsheet will show them.
6. Download the file, or save the settings as a preset to repeat the export later.

Large exports can run in the background and be downloaded from the **Jobs** tab when they are done.

## From the command line

```bash
# one class below a node, as CSV
php extension/xrowextract/bin/php/csv.php --class=<class identifier> --node=<node id> --output=var/export/service-points.csv

# the same as JSON, visible content only
php extension/xrowextract/bin/php/csv.php --class=<class identifier> --node=<node id> --format=json \
  --visibility=visible --output=var/export/service-points.json

# which columns a class offers
php extension/xrowextract/bin/php/csv.php --list-columns --class=<class identifier>
```

Schedules (`ext:xrowextract:schedule`) run exports regularly and can deliver the file to SFTP, FTP/FTPS, a NAS folder,
S3, WebDAV or an HTTP endpoint.

## The kernel command exp:ezcsvexport

Without the extension, the kernel exports the subtree below a node to one CSV file per content class:

```bash
./console exp:ezcsvexport --storage-dir=var/export/csv <node id>
php bin/php/ezcsvexport.php --storage-dir=var/export/csv <node id>
```

It has no filters and no column choice; review the columns before publishing.

## Publishing the exported file

The export is a file on the server or in your downloads. To publish it as open data, upload it as a distribution
of the dataset (see "Publishing dataset files"), with the date of the export in the dataset's "Last changed" field.
For a regular export, replace the file in the same distribution each time so the page address stays the same.

## Before you publish an export

- [ ] Remove columns with personal data: owner, creator, modifier, e-mail addresses, internal notes. Exports can
  include such columns, and user accounts can be exported as special columns by users with the right policy.
- [ ] Remove internal identifiers re-users do not need, or keep them deliberately as stable keys.
- [ ] Check the character encoding (UTF-8) and the delimiter in a spreadsheet program and a text editor.
- [ ] Check one row against the published page.
- [ ] Record which export (preset or command) made the file, so the next update is the same.

## Related pages

- [xrowextract](../../../features/6.0/extensions/xrowextract.md)
- [Collected information](../../../guides/collected-information.md)
- [exp:ezcsvexport](../../../examples/commands/ezcsvexport.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
