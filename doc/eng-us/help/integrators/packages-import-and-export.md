---
title: Packages for import and export
audience: integrator, developer, admin
category: integration
order: 130
summary: Moving content classes, content subtrees and settings between installations with packages - ezpm.php on the command line, the JSON package format for tools, installing large packages, remote ids in packages, and when packages fit an integration.
keywords: package, ezpkg, ezpm.php, ezpm, export, import, install, add, ezcontentobject, ezcontentsubtree, ezcontentclass, JSON package format, package.json, as-drafts, remote id, migration, deployment
type: help
---
# Packages for import and export

This page is for integrators who move **structure and content** between installations: content classes from a
development to a production site, a sample subtree for a demo, the starting content of a new site, or a migration
batch prepared elsewhere. A package (`.ezpkg`, a gzip compressed tar archive) can hold content classes, content
objects with their files, template overrides and settings. The administration side is in
[Packages](../../guides/packages.md); the command line is `ezpm.php`
([ezpm: the package manager on the command line](../../features/6.0/ezpm-package-manager-cli.md)).

## Contents

1. [What packages are good for in an integration](#what-packages-are-good-for-in-an-integration)
2. [Exporting a subtree](#exporting-a-subtree)
3. [Installing a package](#installing-a-package)
4. [The JSON package format for tools](#the-json-package-format-for-tools)
5. [Remote ids in packages](#remote-ids-in-packages)
6. [Cautions](#cautions)

## What packages are good for in an integration

| Use | Fit |
|---|---|
| Ship the content classes your integration needs together with your extension's release | Good: classes are installed with their identifiers and attributes |
| Move a subtree once from a staging installation to production | Good |
| Seed a new site with starting content | Good |
| Let a tool generate content offline and hand it over as a file | Good with the JSON format |
| A daily sync of changing records | Poor: use an import keyed by `remote_id` instead ([how](idempotent-imports-keyed-by-remote-id.md)) |
| Exchange with a system that is not Exponential | Poor, unless that system writes the package format |

## Exporting a subtree

Run from the installation root (add `--allow-root-user` as root):

```bash
php ezpm.php create mypackage 'Starting content for the product area' 1.0.0 install
php ezpm.php add mypackage ezcontentsubtree /products --exclude-templates --language=eng-US
php ezpm.php export mypackage -d var/tmp
```

`add ezcontentobject` (alias `ezcontentsubtree`) takes a node id or a URL path and the options
`--include-classes`/`--exclude-classes`, `--include-templates`/`--exclude-templates`, `--node-main`/`--node-selected`,
`--siteaccess=`, `--language=`, `--current-version`/`--all-versions` and `--minimal-template-set`. The console knows
the same tool as `php bin/php/console ezpm`.

## Installing a package

```bash
php ezpm.php import var/tmp/mypackage-1.0.0-1.ezpkg        # into the package repository, with the archive checks
php ezpm.php install mypackage --destination-node-id 61    # objects below node 61 (default: node 2)
php bin/php/updatesearchindex.php --allow-root-user        # the installer delays indexing
php bin/php/ezcache.php --clear-all --allow-root-user
```

What to know about the installer:

- **One run at a time**: `install` and `add` take a lock (`ezpm.lock` in the var directory).
- **Batched commits** every 50 objects, so large packages do not hold locks for the whole run; a failed object is
  named by its remote id.
- **No duplicates**: repeated items are installed once.
- **Search indexing is delayed** during the run: rebuild the index afterwards.
- **Dependencies first**: packages listed under `requires` are installed before the package.
- **Non-interactive runs skip destructive class replacement**, so an unattended install never replaces a class.

## The JSON package format for tools

`ezpm.php export --format=json` writes the same package as JSON (`package.json`, documents as JsonML, SHA-256 of
every file), and `install --as-drafts` installs objects as drafts for review. `convert`, `validate` and `check` work on
JSON packages. A tool of yours, or a script on the customer's side, can generate a package in this format and validate
it against `share/package/schema/package-v1.json` before handing it over
([The JSON package format](../../bc/6.0/package-json-format.md)).

## Remote ids in packages

Objects in a package carry their remote ids (a document per object is named `object-<remote id>.xml`). That is what
lets a package installed on several installations produce objects that the same import can find later with
`eZContentObject::fetchByRemoteID()`, even though their object ids differ.

## Cautions

- **Install into a copy first.** The installer writes content and classes.
- **Never put credentials into a package.** A package is a file that travels between installations and people.
- **Upload limits.** The upload page refuses archives with links or paths outside the package; build archives with
  `ezpm export`, not by hand.
- **Classes are the contract of your integration.** Ship class changes as a new package version together with the
  extension version that needs them, and write both into the release notes.

## Related pages

- [Packages](../../guides/packages.md)
- [ezpm: the package manager on the command line](../../features/6.0/ezpm-package-manager-cli.md)
- [Look inside a package, compare it, import single items](../../features/6.0/package-compare-and-import.md)
- [Packages and ezpkg for app creators](../creators/app-creators/packages-and-ezpkg.md)
- [Bulk data exchange with xrowextract](bulk-data-exchange-with-xrowextract.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
