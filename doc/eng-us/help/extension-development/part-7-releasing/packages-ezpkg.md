---
title: Packages (.ezpkg) and the package creator
audience: developer, designer, app-creator
category: extension-development
order: 100
summary: The other way to distribute: .ezpkg packages made with the package creator in the administration or ezpm.php - extension exports, content class exports, site styles - and when to use them next to Composer.
keywords: package, ezpkg, package creator, /package/create, ezpm.php, extension export, content class export, site style, import, Composer
type: help
---
# Packages (.ezpkg) and the package creator

Part 7, chapter 10. Before Composer, extensions and content were exchanged as packages: `.ezpkg` archives that the
administration can create, upload and install. Exponential 6 still supports them, and they remain the right tool for
some jobs. This chapter explains when to use them and how to make one. The full guide is
[Packages: what they carry, where they live, and how to handle them safely](../../../guides/packages.md).

## Composer or a package?

| You distribute | Use |
|---|---|
| An extension (PHP, templates, settings) | Composer ([Composer and Packagist](composer-and-packagist.md)) |
| Content classes a theme or an extension expects | packages, shipped inside the extension (as sevenx_themes_simple does in `packages/`) |
| Example or demo content | packages |
| An extension for sites that are not installed with Composer | an extension export package, in addition to Composer |

## Making a package in the administration

**Create new package** on the packages page opens `/package/create`, with one wizard per kind your role may use:
content object export, content class export, extension export and site style. The wizard asks for the parts, then
name, summary, version, license and maintainer, and the format the package is kept in (classic XML or JSON), and
writes the package into the `local` repository. Nothing on the site changes. The policy `package/create` controls who
may use it.

Then download it from the package's page (policy `package/export`): that is the `.ezpkg` file.

## Making and handling packages from the shell

```bash
php ezpm.php list                                   # the packages of the repositories
php ezpm.php export mytheme_classes -d var/tmp      # write an .ezpkg (only reads the package)
php ezpm.php import var/tmp/mytheme_classes.ezpkg   # the same archive checks as the upload page
php ezpm.php help                                   # every command
```

(Add `--allow-root-user` when you run them as root.) `ezpm.php` is at the root of the installation.

## Packages inside your extension

For a theme or an extension whose templates expect content classes:

1. Create the classes on a development site.
2. Export them with the content class export wizard; set the package's version and license to match the extension.
3. Put the `.ezpkg` files into the extension's `packages/` folder.
4. In the README, tell users how to import and install them (the upload page of the packages list, or
   `ezpm.php import`).

Version the package files with the extension: a changed class means a new package version in the next release.

## Safety

The kernel checks every uploaded or imported archive: paths that point outside the package, links and devices are
refused ([the guide](../../../guides/packages.md), sections 6 and 12). Build packages with the wizard or
`ezpm.php export`, never by hand-packing a directory with links in it. Do not put secrets or personal data into
packages: example content must be made up.

## Related pages

- Previous: [filelist.md5 and the upgrade check](filelist-md5-and-the-upgrade-check.md)
- Next: [Testing before a release](testing-before-a-release.md)
- [Packages guide](../../../guides/packages.md)
- [Distributing a theme](../part-6-theme-developers/distributing-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
