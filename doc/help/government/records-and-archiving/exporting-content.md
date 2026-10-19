---
title: Exporting content
audience: government, admin, project-manager, developer
category: public-sector
order: 120
summary: For public bodies that hand content to a records system, an archive or a new platform - the export routes Exponential offers (content object packages, PDF export, the REST API, RSS, form and account CSVs, audit exports) with what each carries and what it leaves out.
keywords: export, content export, packages, ezpkg, content object export, PDF export, REST API, RSS feeds, CSV, audit export, migration, records transfer, archive transfer, exit plan, public sector
type: help
---
# Exporting content

This page is for records managers, project managers and developers. Records must leave the website at some point:
into a records system at publication, into an archive at the end of retention, or to a new platform at the end of a
contract. Exponential has several export routes; none is a records transfer format by itself, so choose by purpose.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The routes](#the-routes)
2. [Choosing a route](#choosing-a-route)
3. [Exit planning](#exit-planning)
4. [Related pages](#related-pages)

## The routes

| Route | Carries | Leaves out | Page |
|---|---|---|---|
| Packages, content object export | objects with their fields and files, classes, in a `.ezpkg` (classic XML or JSON) | versions history, audit, other content not selected | "Packages for export" |
| PDF export | one PDF of a node or a tree, its attributes; generated once or on the fly | structure for reuse; tagging | "The PDF export" |
| REST API | nodes, objects, fields, children as JSON, with permissions | versions history | "Exporting with the REST API" |
| RSS feeds | the newest items of a list | everything older | [RSS feeds](../../../guides/rss-feeds.md) |
| Collected information CSV | the submissions of one form | other forms | [Collected information](../../../guides/collected-information.md) |
| Check user accounts CSV | the account list | account content | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Audit export | records as CSV, JSON lines, JSON or a signed bundle | | "Audit records as evidence" |
| Database dump | everything | the files (exported separately: `var/storage`) | [Backups](../../../install/10-after-installing.md#109-backups-and-restore) |

## Choosing a route

| Purpose | Route |
|---|---|
| A human-readable copy of a notice at publication | PDF (export generated once of that node) or a print to PDF from the browser; plus the metadata in your records system |
| Structured content for another system | REST API or packages |
| A full copy for an archive or exit | database dump plus files, and documentation of the classes |
| Evidence of who changed what | audit export as a signed bundle |

For each export to a records system, record which version was exported (version number and date from the versions
page).

## Exit planning

A public body should be able to leave any supplier. With Exponential: the software is free under the GPL, the data is in
your database in a documented schema, files are in `var/storage`, and the REST API and packages export structured
content. Keep in your exit plan: the database dump procedure, the list of classes and their fields
(Setup > Classes), and a test export every year.

## Related pages

- [Packages](../../../guides/packages.md)
- [PDF exports](../../../guides/pdf-exports.md)
- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
