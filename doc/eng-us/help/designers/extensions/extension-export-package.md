---
title: "Exporting an extension as a package"
audience: designer, admin
category: theme-extensions
order: 360
summary: Pack a theme extension's files into an Exponential package with the Extension export wizard or ezpm - switching exporting on in package.ini [ExtensionExportSettings], the secret check, and the steps.
keywords: extension export, ezextension, package.ini, ExtensionExportSettings, ezpm add, extension package, secret check, export
type: help
---
# Exporting an extension as a package

This page is for designers who want to hand a theme to someone as one `.ezpkg` file, for example to an installation
that does not use Composer.

## Before you start: exporting is off by default

`settings/package.ini [ExtensionExportSettings]` says: "Exporting an extension as a package (Setup > Extensions, the
package wizard, ezpm.php add) is switched off by default." Until an administrator switches it on, the wizard lists
every extension but none can be chosen, each with the reason. How to switch it on is described in
[Extension list and downloads](../../../features/6.0/extension-list-and-downloads.md); do it on a development
installation.

Every way of exporting also refuses an extension that holds secret values or key material
([The secret check](package-secret-check.md)).

## Step by step in the browser

1. Open **Setup > Packages > Create new package** and choose **Extension export**.
2. **Extensions to include**: tick your theme. The table shows its version, last change and details; an extension
   that may not be exported is disabled with its reason.
3. **Package information**: name (lower case letters, digits and `_`), summary, version, license, format (Classic XML
   or JSON).
4. **Package maintainer** and **Package changelog**.
5. Review and press **Create package**. The package is written into the `local` repository and its page opens.
6. **Download** gives you `<name>-<version>.ezpkg`.

## On the command line

```bash
php ezpm.php create mytheme_pkg 'My Theme as a package' 1.0.0 install
php ezpm.php add mytheme_pkg ezextension mytheme
php ezpm.php export mytheme_pkg -d var/tmp
```

`php ezpm.php help add` lists the item types and options. See [Exporting and importing with ezpm](exporting-with-ezpm.md).

## Check the result

```bash
tar tzf var/tmp/mytheme_pkg-1.0.0.ezpkg | head -20
```

Expected: `package.xml` and the theme's files below the package's extension folder; no `.bak`, `.env` or key files.

## Related pages

- [The package creator wizard, step by step](package-creator-wizard-step-by-step.md)
- [Composer package or .ezpkg](composer-or-ezpkg.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
