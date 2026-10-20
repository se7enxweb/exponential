---
title: "Installing your package on a test site"
audience: designer, admin
category: theme-extensions
order: 430
summary: Try your package exactly as a user will - upload, review what the install meets, install (or install as drafts), check the site, and uninstall - on a throwaway installation, never on a live site.
keywords: install package, test site, upload package, install as drafts, uninstall, compare, check, throwaway installation
type: help
---
# Installing your package on a test site

This page is for designers who want to see their package from the user's side. The package installer writes classes
and content, so always use a throwaway installation.

## Before you start

- A test installation you can reset (a copy of a demo site, or a fresh install).
- An administrator account on it with `package/import` and `package/install`.

## Step by step

1. **Upload**: **Setup > Packages > Import new package**, choose the `.ezpkg`, press **Import package**. The page
   names the first rule that failed, if any ([Validating a package](validating-a-package.md)).
2. **Read the package's page**: details, license, what it carries, files. Press **Compare** to see the package's
   content against the site.
3. **Install**: press **Install**. The install page lists what will be installed and **what the install meets
   here**: each content object by remote id (new, existing, a conflict, or without its class).
4. Choose:
   - **Install package** for a normal install. A class that exists asks how to handle the conflict.
   - **Install as drafts** to write content objects as drafts only (nothing published; classes, settings and design
     files are not installed), and review each draft.
5. **Check the site**: clear the caches, open the pages that use the new classes or content, look at them in light
   and dark mode and at a narrow width.
6. **Uninstall**: press **Uninstall** on the package's page. It lists what is removed, content included, and cannot
   be undone. Uninstall to check that your package leaves the site clean.
7. Write down every surprise and fix it in the next version of the package.

![Setup, Packages: the package repositories, where an imported package appears before it is installed](../../images/designers/packages-repositories.webp)

## On the command line

```bash
php ezpm.php import /path/to/mytheme_classes-1.0.0-1.ezpkg
php ezpm.php check mytheme_classes
php ezpm.php install mytheme_classes
```

## Related pages

- [Packages, section 7](../../../guides/packages.md#7-installing-and-uninstalling)
- [Exporting and importing with ezpm](exporting-with-ezpm.md)
- [An upgrade test environment](../lifecycle/upgrade-test-environment.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
