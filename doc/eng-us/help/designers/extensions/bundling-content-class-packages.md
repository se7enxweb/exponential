---
title: "Bundling content class packages with a theme"
audience: designer, developer
category: theme-extensions
order: 230
summary: When a theme's templates expect content classes (article, event, blog post), ship them as content class packages in the theme's packages/ folder, as sevenx_themes_simple does, and tell users how to import them.
keywords: packages folder, content class package, ezcontentclass, ezpkg, sevenx_themes_simple, import, ezpm, class identifiers
type: help
---
# Bundling content class packages with a theme

This page is for designers whose templates are written for particular content classes. A full view template for
`event` is useless on a site that has no `event` class; shipping the class with the theme solves that.

## A real example

`sevenx_themes_simple` ships 20 content class packages in `packages/`, one per class, for example
`article-1.0-1.ezpkg`, `blog_post-1.0-1.ezpkg`, `event_calendar-1.1-1.ezpkg`, `frontpage-1.0-1.ezpkg` and
`product-1.0-1.ezpkg`. The file name is `<package name>-<version>-<release>.ezpkg`.

## Step by step: make the packages

1. On a development installation, create or adjust the content classes your templates need.
2. Open **Setup > Packages > Create new package** and choose **Content class export**
   ([The package creator wizard](package-creator-wizard-step-by-step.md)). Tick one class, fill name, version and
   license, and finish. One package per class keeps them independent; one package for all is simpler to install.
3. Download each package from its page (**Download**), or export it on the command line:

   ```bash
   php ezpm.php export article -d extension/mytheme/packages
   ```

4. Check each archive: `tar tzf extension/mytheme/packages/article-1.0-1.ezpkg | head` lists `package.xml` and an
   `ezcontentclass/` folder.
5. Commit the files and list them in `INSTALL.md`.

## Step by step: what your users do

1. Import a package: `php ezpm.php import extension/mytheme/packages/article-1.0-1.ezpkg`, or upload it on
   **Setup > Packages > Import new package**.
2. Install it from its page (**Install**). A class that already exists asks how to handle the conflict.
3. Clear the caches.

Write these steps into your `INSTALL.md` with your real file names.

## Good practice

- Use class identifiers that are unlikely to clash, or reuse the common ones (`article`, `folder`) on purpose and
  say so.
- Raise the package version when the class changes, and record it in the theme's changelog.
- Never export content that holds personal data as part of a theme.

## Related pages

- [What an Exponential package (.ezpkg) is](what-an-exponential-package-is.md)
- [Exporting and importing with ezpm](exporting-with-ezpm.md)
- [Packages guide](../../../guides/packages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
