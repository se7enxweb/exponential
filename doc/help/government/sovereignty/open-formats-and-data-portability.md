---
title: Open formats and data portability
audience: government, admin, developer, project-manager
category: public-sector
order: 120
summary: In which formats Exponential stores and exports content, settings and files, which of them are open and documented, and how to keep your own content in formats another system can read.
keywords: open formats, data portability, XML, rich text, ezxmltext, CSV, JSON, INI, UTF-8, ezpkg, files, storage, export, migration
type: help
---
# Open formats and data portability

This page is for administrators, developers and managers who must show that the body's content can be read without
Exponential. It lists where each kind of data lives and in which format, and what to watch for.

## Contents

1. [Where things are stored](#where-things-are-stored)
2. [Export formats](#export-formats)
3. [Rich text](#rich-text)
4. [Encoding](#encoding)
5. [Keeping portability](#keeping-portability)

## Where things are stored

| Data | Stored as | Readable without Exponential |
|---|---|---|
| Content fields | rows in the database (one of six engines) | yes, with database tools; the schema is in the repository |
| Rich text | XML in a database column | yes, it is XML |
| Uploaded files and images | files below `var/<site>/storage/` | yes, as files; their names map to objects in the database |
| Settings | INI files in `settings/` and `extension/*/settings/` | yes, plain text |
| Templates | `.tpl` text files | yes, plain text |
| Translations of the interface | Qt Linguist `.ts` XML | yes ([Internationalization standards](../../../standards/internationalization.md)) |
| Audit records | JSON lines files, archived with manifests | yes ([Audit trail](../../../features/6.0/audit-trail.md)) |

## Export formats

| Format | From | Documentation |
|---|---|---|
| CSV, JSON, XML per class | `xrowextract` | [xrowextract](../../../features/6.0/extensions/xrowextract.md) |
| CSV per class from the shell | `exp:ezcsvexport` | [exp:ezcsvexport](../../../examples/commands/ezcsvexport.md) |
| JSON over HTTP | REST API | [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md) |
| `.ezpkg` packages (classes, content, extensions) | Setup > Packages | [Packages](../../../guides/packages.md) |
| `.dba` database dumps, engine neutral | `bin/php/ezsqldumpschema.php` | [Database support](../../database-support.md#moving-a-site-to-another-database) |
| RSS, Atom | Setup > RSS | [RSS feeds](../../../guides/rss-feeds.md) |
| PDF | Setup > PDF export | [PDF exports](../../../guides/pdf-exports.md) |
| CSV of form submissions, user lists | Collected information, Check user accounts | [Collected information](../../../guides/collected-information.md) |

## Rich text

The rich text datatype stores XML of its own schema, not HTML. Another system reads it through an export that
renders it (the REST API's `OutputFormat=xhtml` fills the rendered output) or through a conversion you write. Plan
that conversion early in a migration; it is usually the largest single task.

## Encoding

The internal character set is UTF-8 (`settings/i18n.ini [CharacterSettings] Charset=utf-8`). On MySQL and MariaDB a
stock connection uses 3-byte `utf8`; characters above U+FFFF need `utf8mb4` configured by the administrator
([Internationalization standards](../../../standards/internationalization.md#4-byte-characters-on-mysql-and-mariadb)).
SQLite and PostgreSQL store full Unicode.

## Keeping portability

- Upload documents in open or widely documented formats, with a CSV next to every table.
- Avoid storing content only inside templates; keep it in content objects.
- Keep identifiers of classes and attributes meaningful; they become column names in exports.
- Export your main classes once a year and check the files open in common tools.

## Related pages

- [Exporting content and packages](exporting-content-and-packages.md)
- [Database support](../../database-support.md)
- [Internationalization standards](../../../standards/internationalization.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
