---
title: "The share folder: demo images, files and seed data"
audience: designer, developer
category: theme-extensions
order: 220
summary: What a theme's share/ folder is for - demo images and files, database schema and seed data (.dba) - how real themes use it, and the rules that keep it small, legal and free of backups and personal data.
keywords: share, share folder, db_schema.dba, db_data.dba, content-images, content-files, seed data, demo data, installer
type: help
---
# The share folder: demo images, files and seed data

This page is for designers whose theme comes with demo content or needs database tables. `share/` is the place for
data that is installed once rather than read on every request.

## What real themes keep there

| Theme | `share/` holds |
|---|---|
| `cjw_themes_jumper` | `content-images/`, `content-files/` (images and files of its demo content) |
| `sevenx_themes_media` | `db_schema.dba` and `db_data.dba` (the tables and rows of its layouts) |

A `.dba` file is the kernel's database-independent format: an installer turns `db_schema.dba` into tables for
the database in use (SQLite, PostgreSQL, MySQL/MariaDB and others) and loads `db_data.dba` into them.

## Step by step

1. Decide what the theme needs installed: images for demo pages, sample documents, extra tables.
2. Put images and files below `share/content-images/` and `share/content-files/` (or names of your own, documented
   in `INSTALL.md`).
3. For tables, let a RAD wizard write `share/db_schema.dba` when it offers it, or generate it from a database with
   the kernel's schema tools. Keep schema and data in separate files.
4. Explain in `INSTALL.md` how the data is installed and what it creates.
5. Before every release, list what is tracked: `git ls-files share/`.

## Rules

- **Only files you may distribute.** Demo photos need a license that allows redistribution
  ([Creative Commons for images](../distribution/creative-commons-for-images.md)).
- **No personal data.** Seed data never contains real customers, users, email addresses or passwords.
- **No backups.** Copies such as `db_data.dba.bak` belong in your own backup, not in the repository; add them to
  `.gitignore` ([A .gitignore for themes](gitignore-for-themes.md)).
- **Keep it small.** A theme downloaded by every installation should not carry hundreds of megabytes of images.
- **Review data changes row by row**, not by diff: a `.dba` file is an array, and removing one row renumbers all
  rows after it.

## Related pages

- [Demo content for a theme](../distribution/demo-content.md)
- [Bundling content class packages](bundling-content-class-packages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
