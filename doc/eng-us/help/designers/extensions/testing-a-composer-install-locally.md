---
title: "Testing a Composer install before publishing"
audience: designer, developer
category: theme-extensions
order: 330
summary: Install your theme into a throwaway Exponential installation through a Composer path or vcs repository before you submit it to Packagist, and check the folder name, the files and the rendered page.
keywords: composer test, path repository, vcs repository, local install, before Packagist, test installation, composer require dev-main
type: help
---
# Testing a Composer install before publishing

This page is for designers who want to be sure `composer require yourvendor/mytheme` works before anyone else tries
it. Use a throwaway installation, never a live site.

## Before you start

- A test installation of Exponential installed with Composer, which you may change and throw away.
- Your theme in its own Git repository with a valid `composer.json`.

## Step by step with a path repository

1. In the test installation's `composer.json`, add a repository that points at your theme's folder:

   ```bash
   composer config repositories.mytheme '{"type": "path", "url": "/home/you/src/mytheme", "options": {"symlink": false}}'
   ```

2. Require it:

   ```bash
   composer require yourvendor/mytheme:@dev
   ```

   Expected: Composer reports installing `yourvendor/mytheme` from the path repository.

3. Check the folder: `ls extension/mytheme/ezinfo.php extension/mytheme/extension.xml`. If the folder has another
   name, fix `extra.ezpublish-legacy-extension-name`
   ([The Composer installer and the folder name](composer-installer-and-extension-name.md)).
4. Activate the theme, regenerate the autoloads, clear the caches, and open a page of a siteaccess that uses it
   ([Activating a theme](activating-a-theme-extension.md)).
5. Check that nothing you did not mean to ship arrived: `find extension/mytheme -name '*.bak' -o -name '.env'`
   prints nothing.

## Step by step with your pushed repository

To test exactly what users will get once it is public:

```bash
composer config repositories.mytheme vcs https://github.com/yourvendor/mytheme
composer require yourvendor/mytheme:dev-main
```

After you tag a release, test the tag the same way: `composer require yourvendor/mytheme:1.0.0`.

## Clean up

Remove the test repository entry (`composer config --unset repositories.mytheme`) or throw the installation away. Do
not leave path repositories in an installation that others use.

## Related pages

- [composer.json for a theme](composer-json-for-a-theme.md)
- [Submitting a theme to Packagist](../distribution/submitting-to-packagist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
