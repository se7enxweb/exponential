---
title: Migrating away from Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 60
summary: Everything Exponential 6 can export when you move a site to another system - xrowextract site archives with manifests, CSV/XML/JSON per class, packages, layouts, database dumps, files, users, URL aliases for redirects, form data and consent records - and a plan for handing the content over.
keywords: export, leave, exit, data portability, xrowextract, site archive, manifest, CSV, JSON, XML, ezpkg, ezsqldumpschema, storage, URL aliases, redirects, users, bccie, consent log
type: install
---
# Migrating away from Exponential

Your content is yours. Exponential stores it in an open database schema and ordinary files, and has tools that write
it out in plain formats. This page lists them and gives a plan; the target system's own import decides the rest.
Guides for specific targets: [WordPress](wordpress.md#5-from-exponential-to-wordpress),
[Drupal](drupal.md#5-from-exponential-to-drupal), [Joomla](joomla.md#5-from-exponential-to-joomla),
[Shopify](shopify.md#4-from-exponential-to-shopify), [Contentful](contentful.md#4-from-exponential-to-contentful),
[site builders](site-builders.md#5-from-exponential-to-a-site-builder), and within the family
[Platform Legacy](exponential-platform-legacy.md), [Nexus](exponential-platform-nexus.md),
[DXP](exponential-platform-dxp.md).

[Migration hub](README.md) · [Checklist](checklist.md) · [Tools](exponential-tools.md#2-export-from-exponential)

## Contents

- [1. What to export](#1-what-to-export)
- [2. The complete export, step by step](#2-the-complete-export-step-by-step)
- [3. Rich text](#3-rich-text)
- [4. Addresses for the redirects](#4-addresses-for-the-redirects)
- [5. Users and personal data](#5-users-and-personal-data)
- [6. Checklist](#6-checklist)

## 1. What to export

| Data | Tool | Format |
|---|---|---|
| All content of a site, self-describing | xrowextract site archive: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>` | per class a CSV (object id, remote id, main node, parent node, URL alias, dates, every attribute), `manifest.json`, `README.txt`; ZIP or TAR.GZ/BZ2/XZ. Password hashes are never exported |
| One class, filtered | `./console ext:xrowextract:csv --class=<identifier> --node=<id> --output=<file>` (also XML and JSON) | with a typed manifest (datatype, format, language per column) |
| Classes and content for another Exponential or Exponential Platform Legacy | packages (`php ezpm.php export`, `ext:xrowextract:package --export`) | `.ezpkg` |
| Layouts | `./console exp:layouts export --all --output=<file.zip>` | ZIP transfer file |
| Images, files, media | the storage directory `var/<site>/storage/` | originals; the paths are in the export |
| The whole database | `./console exp:ezsqldumpschema --output-types=all ...`, or the database's own dump tool | neutral format or SQL |
| Form submissions (information collection) | `./console ext:bccie:export --object=<id> --format=csv` | CSV or SYLK |
| E-mail consent records | `./console exp:mailconsent` (export as CSV) | CSV |
| Audit log | `./console exp:audit` | see [Security and audit](../../guides/security-and-audit.md) |
| Single tables, by an administrator | `sevenx_dse` | SQL, CSV, TSV |
| Content over HTTP | REST v1/v2 | JSON |

## 2. The complete export, step by step

1. **Freeze** editing (or note the time for a later delta export).
2. **Site archive** with xrowextract, every class, the format the target reads best (CSV for spreadsheets and most
   importers, JSON for scripts).
3. **Storage**: copy `var/<site>/storage/` (or the cluster's files with `exp:clusterize -u` first on a database
   cluster).
4. **Database dump**, as an archive you can return to.
5. **Layouts**, if the target can use them (another Exponential or a script).
6. **Address list** ([4](#4-addresses-for-the-redirects)).
7. **Check** the archive: open the manifest, count rows per class against the admin's counts, open a sample of files.

## 3. Rich text

`ezxmltext` attributes are stored as Exponential's XML. The target needs HTML (or Markdown). Two ways:

- **Render**: the REST v2 call with `OutputFormat=xhtml`, or the site's own templates, give the HTML visitors saw.
- **Transform**: convert the XML (paragraph, header, link, embed, table, custom tags) to HTML in a script; embeds
  become image tags or links to the exported files.

## 4. Addresses for the redirects

Every node's main URL alias is in the site archive (one column per object). Other aliases (custom aliases, other
languages) and the wildcards are in the database (`ezurlalias_ml`, `ezurlwildcard`). Build the redirect table from
them: old address to the address of the same item on the new system, matched by remote id or object id. Install it on
the new system before DNS switches.

## 5. Users and personal data

- The site archive never contains password hashes. Users set new passwords on the target (send reset mails), unless
  the target can verify standard PHP `password_hash()` values: then a database export of `ezuser` hashes of type 7 can
  be used. Decide this with your data protection advisor.
- Export only the personal data the target needs; keep the rest in an archive with access controls, or delete it as
  your retention rules say.

## 6. Checklist

- [ ] Freeze or delta plan; site archive with all classes; manifests checked.
- [ ] Storage copied; database dump archived; layouts exported if useful.
- [ ] Rich text rendered or transformed; embeds resolved to files.
- [ ] Redirect table from URL aliases and wildcards; installed on the target.
- [ ] Users and personal data handled by the agreed rules.
- [ ] Old site kept read-only until the new one is signed off.

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
