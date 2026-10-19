---
title: Exporting content and packages
audience: government, admin
category: public-sector
order: 130
summary: The ways to take content out of an Exponential installation, from a full backup to class and content packages and per-class exports, with what each carries and which one to use for an exit, a copy for another body, or an archive.
keywords: export, packages, ezpkg, Setup Packages, package wizard, content object export, class export, xrowextract, database dump, backup, migration, exit
type: help
---
# Exporting content and packages

This page is for administrators who need content out of an installation: for a move to another provider, a copy for
another public body, an archive transfer, or a migration away. Each method carries something different.

## Contents

1. [Which method](#which-method)
2. [A full backup](#a-full-backup)
3. [Packages](#packages)
4. [Per-class exports](#per-class-exports)
5. [The REST API](#the-rest-api)
6. [Checking an export](#checking-an-export)

## Which method

| Goal | Method | Carries |
|---|---|---|
| Move the whole site to another host | database dump plus `var/` storage plus code repository | everything |
| Change the database engine | engine-neutral `.dba` dump and load | the database content |
| Give a part of the site to another installation | content object package | nodes or subtrees with their content, optionally classes and templates |
| Reuse the content model elsewhere | content class package | class definitions, no content |
| Data for another system or an archive | `xrowextract` CSV, JSON or XML | fields of one class or many classes |
| Live access by another system | REST API | nodes and fields as JSON |

## A full backup

The complete state is the code (your repository and `composer.lock`), the database and the storage files below
`var/<site>/storage/`, plus the settings overrides. The hosting help explains each part and the engines' dump tools
([Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md),
[Database backups per engine](../../hosting/operations/database-backups-per-engine.md)). A backup is only proven by a
restore ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)).

## Packages

**Setup > Packages > Create new package** offers wizards ([Package wizard](../../../features/6.0/package-wizard.md)):

| Wizard | Exports |
|---|---|
| Content class export | the definitions of content classes, no content |
| Content object export | nodes or subtrees with their content, optionally their classes and templates |
| Extension export | the files of extensions (needs exporting switched on in `package.ini [ExtensionExportSettings]`) |

A package is a directory with a `package.xml`, exported as one `.ezpkg` file and imported on another installation in
**Setup > Packages** ([Packages](../../../guides/packages.md)). The content object export suits a subtree, such as one
department's pages; for whole sites a full backup is more reliable.

## Per-class exports

`xrowextract` exports one class or every class below chosen nodes as CSV, JSON or XML, with languages as rows, filters
and presets, and can schedule exports to a destination; the kernel's `exp:ezcsvexport` writes one CSV per class
([xrowextract](../../../features/6.0/extensions/xrowextract.md), [exp:ezcsvexport](../../../examples/commands/ezcsvexport.md)).
Check how rich text fields appear in the export (preview it first) before you rely on it.

## The REST API

For a target system that pulls content itself, the REST API serves nodes, children and fields as JSON, with the
permissions of the user it runs as ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).

## Checking an export

- Compare counts: objects per class in the source and in the export.
- Open files in a text editor and a spreadsheet program; check UTF-8 and special characters.
- Check that files referenced by content (images, documents) are included or listed.
- For packages, import into a test installation and look at the result.

## Related pages

- [Avoiding vendor lock-in](avoiding-vendor-lock-in.md)
- [Exit planning](exit-planning.md)
- [Package compare and import](../../../features/6.0/package-compare-and-import.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
