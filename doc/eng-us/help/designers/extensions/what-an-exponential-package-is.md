---
title: "What an Exponential package (.ezpkg) is"
audience: designer
category: theme-extensions
order: 340
summary: An Exponential package is a directory with a package.xml, exported as one .ezpkg file (a gzip compressed tar); what it can carry, where packages live, how they are installed, and how that differs from a Composer package.
keywords: package, ezpkg, package.xml, package.json, repository, var/storage/packages, install, import, export, content classes, site style
type: help
---
# What an Exponential package (.ezpkg) is

This page is for designers who hear "package" and are not sure whether it means a Composer package or an Exponential
package. Both exist; they do different jobs.

## In short

- An **Exponential package** is a directory with a `package.xml` (or, in the JSON format, a `package.json`) and the
  files it names: content classes, content objects, extensions, settings files, stylesheets, images.
- Exported, it is one file `<name>-<version>.ezpkg`: a gzip compressed tar.
- Packages are kept in repositories below `var/storage/packages/` (`local` for packages made or imported here, one
  directory per vendor).
- A package is **imported** (kept), **installed** (its items are created on the site) or **exported** (written as an
  `.ezpkg`).

## What it can carry, by creator

| Wizard (creator) | Carries |
|---|---|
| Content class export (`ezcontentclass`) | Class definitions, no content |
| Content object export (`ezcontentobject`) | Nodes or subtrees with their content, optionally their classes and templates |
| Extension export (`ezextension`) | The files of extensions |
| Site style (`ezstyle`) | Two stylesheets, their images and a thumbnail |

## Composer package or Exponential package

| | Composer package | Exponential package |
|---|---|---|
| Installs | Files (the theme folder) | Files and **database content** (classes, objects) |
| Installed with | `composer require` | **Setup > Packages** or `php ezpm.php` |
| Updates | `composer update` | A new package version installed again |
| Found on | Packagist | Wherever you publish the file |

Many themes use both: a Composer package for the extension, and `.ezpkg` files inside it for content classes
([Bundling content class packages](bundling-content-class-packages.md)). The choice is discussed in
[Composer package or .ezpkg](composer-or-ezpkg.md).

## Safety you can rely on

An uploaded archive is inspected before anything is written: size and entry limits, no paths outside the package,
no links or devices, a well-formed `package.xml`, and no existing package of the same name. Removing a package deletes
its directory but does not uninstall what it installed. Details: [Packages](../../../guides/packages.md).

## Related pages

- [The package creator wizard, step by step](package-creator-wizard-step-by-step.md)
- [Classic XML and JSON packages](classic-and-json-package-formats.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
