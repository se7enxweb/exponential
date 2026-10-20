---
title: "Exporting and importing with ezpm"
audience: designer, admin
category: theme-extensions
order: 410
summary: Use ezpm.php, the package manager on the command line, to create a package, add classes, content or an extension, export it as .ezpkg, and import, check and install it elsewhere.
keywords: ezpm, ezpm.php, command line, create, add, export, import, install, list, info, check, validate, convert, ezcontentclass, ezextension
type: help
---
# Exporting and importing with ezpm

This page is for designers who prefer the shell, or who build packages in a script. `ezpm.php` is the command-line
front end of the package manager; all commands start in the installation root. `php ezpm.php help` lists the
commands, `php ezpm.php help <command>` explains one.

## The commands you need

| Command | Does |
|---|---|
| `create NAME [SUMMARY [VERSION [INSTALLTYPE]]]` | An empty package (`install` by default, or `import`) |
| `add PACKAGE ITEM [PARAMETERS]` | Adds classes (`ezcontentclass` with a comma-separated list of class identifiers), content (`ezcontentobject`), extensions (`ezextension`) |
| `export PACKAGE -d DIR` | Writes the `.ezpkg` into a directory (`--format=json` for JSON) |
| `import FILE` | Imports an archive, with the same checks as the upload page |
| `check PACKAGE` | What installing would meet here, by remote id; nothing is written |
| `install PACKAGE` | Installs it (`--as-drafts` for content as drafts) |
| `validate SOURCE` | Checks an archive, directory or repository package without importing it |
| `list`, `info PACKAGE` | What is there |

## Step by step: a class package for your theme

```bash
php ezpm.php create mytheme_classes 'Content classes of My Theme' 1.0.0 install
php ezpm.php add mytheme_classes ezcontentclass article,event
php ezpm.php export mytheme_classes -d var/tmp
```

Expected: `var/tmp/mytheme_classes-1.0.0-1.ezpkg` (the file name includes the release number the package opened).

## Step by step: the theme itself as a package

```bash
php ezpm.php create mytheme_pkg 'My Theme' 1.0.0 install
php ezpm.php add mytheme_pkg ezextension mytheme
php ezpm.php export mytheme_pkg -d var/tmp
```

This needs extension exporting switched on and passes the secret check first
([Exporting an extension as a package](extension-export-package.md)).

## Step by step: on the receiving installation

```bash
php ezpm.php validate /path/to/mytheme_classes-1.0.0-1.ezpkg
php ezpm.php import /path/to/mytheme_classes-1.0.0-1.ezpkg
php ezpm.php check mytheme_classes
php ezpm.php install mytheme_classes
```

Read the output of `check` before `install`: it lists conflicts (an existing class, for instance).

## Related pages

- [ezpm: the package manager on the command line](../../../features/6.0/ezpm-package-manager-cli.md)
- [Installing your package on a test site](installing-your-package-on-a-test-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
