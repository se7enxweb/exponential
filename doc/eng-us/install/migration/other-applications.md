---
title: Other applications and databases
audience: developer, admin, project-manager
category: install-upgrade
order: 52
summary: The general method for moving content from any other source into Exponential 6 - spreadsheets, custom PHP or other web applications, intranets, any SQL database, office documents, feeds - with the tools that fit each, a step-by-step plan and the way back.
keywords: spreadsheet, CSV, Excel, custom application, SQL database, intranet, office documents, feeds, RSS, import, migration, xrowextract, ezodf
type: install
---
# Other applications and databases

For every source without a guide of its own, the method is the same: get the content out in a structured form, map
it, import it with remote ids, keep the addresses. This page gives the method and the tool for each kind of source.

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## 1. The tool for each kind of source

| Source | Get it out as | Bring it in with |
|---|---|---|
| A spreadsheet (Excel, LibreOffice, Google Sheets) | CSV, UTF-8, one sheet per content type | xrowextract's importer with `--map` (dry run first); `exp:ezcsvimport` for simple classes (`;` separated, no header row, columns in attribute order) |
| Any SQL database (a custom PHP app, an intranet, an old CMS) | one query per content type into CSV or JSON | xrowextract's importer, or the [script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton) |
| An application with an API only | a script that pages through the API into JSON | the script skeleton |
| Office documents (Writer, Word through conversion) | the documents | `ezodf` (Writer import; Word and PDF need the conversion daemon) |
| A folder of images and files | a ZIP | the upload page's ZIP importer (keeps the folder structure on request) |
| A feed (RSS) | the feed | **Setup > RSS** import, run by the `rssimport` cronjob (newest items only) |
| Another Exponential | packages, xrowextract archives, `exp:layouts` | the same tools on the target |
| A site with no access to its back end | a crawl | [static HTML method](static-html.md) |

## 2. The method

1. **Inventory** ([checklist, 1](checklist.md#1-inventory-know-what-you-have)): types, counts, files, users,
   addresses.
2. **Mapping table** per type ([mapping](content-model-mapping.md)), including what is dropped on purpose.
3. **Export** to a frozen file set (CSV/JSON plus files), with a stable source id per item.
4. **Transform** in a script outside Exponential: clean HTML, normalize dates to Unix timestamps, map categories to
   parent locations, resolve file paths.
5. **Import** in dependency order (media, containers, content, relations, translations, users), with remote ids,
   repeatable.
6. **Addresses**: redirect table from the old routes; aliases, wildcards, web-server rules.
7. **Verify** counts and samples; **rehearse**; **cut over**; keep the old system read-only until sign-off.

## 3. The way back

Every export of [Migrating away from Exponential](leaving-exponential.md) is a plain format (CSV, JSON, XML, SQL),
so any application can load it with its own importer.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
