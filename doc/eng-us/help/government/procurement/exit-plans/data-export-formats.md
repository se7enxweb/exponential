---
title: Data export formats for leaving or changing supplier
audience: government, project-manager, admin, developer
category: public-sector
order: 40
summary: Every way to get data out of an Exponential site and the format each one writes - the xrowextract site archive, CSV/XML/JSON per class, packages (.ezpkg), database dumps, storage files, layouts, form submissions, consent records, the audit trail and REST - with the commands and which receiving party each format suits.
keywords: data export, export formats, site archive, xrowextract, CSV, JSON, XML, ezpkg, packages, database dump, ezsqldumpschema, storage, layouts export, bccie, consent log, audit export, REST, portability
type: help
---
# Data export formats for leaving or changing supplier

This page is for project managers who must name the export formats in a contract or exit plan, and for the
administrators who produce them. Every tool and format below is documented in
[Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) and
[Exponential's import and export tools](../../../../install/migration/exponential-tools.md#2-export-from-exponential).
Commands are run in the installation root.

## Contents
1. [Which export for which receiver](#which-export-for-which-receiver)
2. [The site archive](#the-site-archive)
3. [Single classes](#single-classes)
4. [Packages](#packages)
5. [The database](#the-database)
6. [Files](#files)
7. [Layouts, forms, consent and audit](#layouts-forms-consent-and-audit)
8. [Content over HTTP](#content-over-http)
9. [What to name in the contract](#what-to-name-in-the-contract)

## Which export for which receiver

| Receiver | Needs | Exports |
|---|---|---|
| New host or supplier, same software | the whole installation | database dump, files, settings, code ([Transition to another provider](transition-to-another-provider.md)) |
| Another Exponential or Exponential Platform Legacy | classes and content | packages, plus the above |
| Another CMS | content, files, addresses | site archive, files, address list ([Transition to another system](transition-to-another-system.md)) |
| Your archive or records service | a durable copy | site archive, database dump, files, audit bundle |
| Data protection officer | consent and access records | consent CSV, audit export per user |

## The site archive

```bash
./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>
```

- Per class a CSV with object id, remote id, main node, parent node, URL alias, dates and every attribute.
- A `manifest.json` that describes the archive and a `README.txt`.
- ZIP, TAR.GZ, TAR.BZ2 or TAR.XZ.
- **Password hashes are never included.**

This is the most portable export: plain CSV that spreadsheets, scripts and most importers read.

## Single classes

```bash
./console ext:xrowextract:csv --class=<identifier> --node=<id> --output=<file>
```

Also writes XML and JSON, with a typed manifest (datatype, format, language per column). Options such as
`--list-classes`, `--list-columns` and `--preview=10` help to look before exporting. `./console exp:ezcsvexport`
writes one CSV per class for a subtree, with `--storage-dir` for files.

## Packages

`php ezpm.php export` or `./console ext:xrowextract:package --export` write `.ezpkg` packages of classes and content,
for another Exponential installation. See [Packages](../../../../guides/packages.md) for what a package carries.

## The database

- `./console exp:ezsqldumpschema --output-types=all ...` writes schema and data in Exponential's neutral format or SQL,
  for any of the supported engines.
- Or the database's own dump tool ([Database backups per engine](../../../hosting/operations/database-backups-per-engine.md)).
- `sevenx_dse`, the administration database editor, exports single tables as SQL, CSV or TSV.

A database dump is the most complete copy but only useful to someone who runs Exponential or reads its schema. Rich
text in `ezxmltext` fields is stored as Exponential's XML: render it or transform it for another system
([Leaving Exponential, section 3](../../../../install/migration/leaving-exponential.md#3-rich-text)).

## Files

Images, documents and media are in `var/storage/` and `var/<site>/storage/`: the originals. Copy them with every
export that refers to files. On a database cluster, run `exp:clusterize -u` first so the files are on disk.

## Layouts, forms, consent and audit

| Data | Command | Format |
|---|---|---|
| Layouts (Exponential Layouts) | `./console exp:layouts export --all --output=<file.zip>` | ZIP transfer file |
| Form submissions | `./console ext:bccie:export --object=<id> --format=csv` | CSV or SYLK |
| E-mail consent records | `./console exp:mailconsent` | CSV |
| Audit trail | `./console exp:audit export --format=jsonl\|csv\|bundle --out=<path>` | JSON lines, CSV, or a bundle with `records.jsonl` and a signed `manifest.json` ([Audit guide](../../../../bc/6.0/audit.md)) |

## Content over HTTP

The REST API v1 and v2 return nodes, children and fields as JSON, so another system can pull content over HTTP
([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)). Version 2 can return rich text as XHTML.

## What to name in the contract

```text
On request, and at the end of the contract, the Supplier delivers within <n> working days:
- a site archive of all classes (xrowextract, <ZIP|TAR.XZ>) with manifest;
- a full database dump in <engine dump format | Exponential neutral format>;
- the storage directories of every site;
- layouts, form submissions, consent records and the audit trail export with signed manifest;
- settings and keys, transferred privately;
- the source of all custom code in Git with history.
```

## Related pages
- [Exit plans: overview](README.md)
- [Exit plan template](exit-plan-template.md)
- [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
