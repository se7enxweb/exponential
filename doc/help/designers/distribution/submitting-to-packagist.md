---
title: "Submitting a theme to Packagist"
audience: designer, developer
category: theme-distribution
order: 190
summary: Publish a theme as a Composer package on Packagist - prerequisites, the Submit page, checking the package page, and testing composer require from a clean installation.
keywords: Packagist, submit, composer package, packagist.org, composer require, vendor name, package page
type: help
---
# Submitting a theme to Packagist

This page is for designers whose theme is in a public Git repository with a valid `composer.json`. Packagist is the
main index Composer searches; once your theme is there, anyone can run `composer require yourvendor/mytheme`.

## Before you start

- A public repository with `composer.json` at its root ([composer.json for a theme](../extensions/composer-json-for-a-theme.md)).
- `composer validate` passes.
- At least one release tag pushed ([GitHub releases](github-releases.md)). Without a tag, Packagist lists only the
  branch (`dev-main`).
- A Packagist account. Signing in with GitHub makes the automatic updates simplest ([Keeping Packagist up to date
  automatically](packagist-auto-update.md)).
- The package name is free: `https://packagist.org/packages/yourvendor/mytheme` shows "not found".

## Step by step

1. Sign in on [packagist.org](https://packagist.org/).
2. Open **Submit** (`https://packagist.org/packages/submit`).
3. Enter the repository URL (`https://github.com/yourvendor/mytheme`) and press **Check**. Packagist reads
   `composer.json` and shows the package name it found.
4. Confirm with **Submit**. The package page opens and the versions appear after Packagist has read the repository.
5. Check the package page: name, description, license, the README, and the versions (`v1.0.0` shown as `1.0.0`, plus
   `dev-main`).
6. Set up the automatic update right away ([Keeping Packagist up to date automatically](packagist-auto-update.md)).
7. Test from a clean installation:

   ```bash
   composer require yourvendor/mytheme
   ```

   Expected: Composer installs the newest tagged version into `extension/<name>`.

## What Packagist reads

Packagist reads `composer.json` from every tag and branch. The version comes from the tag name; a `version` field in
`composer.json`, if present, must agree with it. A theme that also uses the 7x theme repositories as a model can
compare: `https://repo.packagist.org/p2/se7enxweb/cjw_themes_jumper.json` lists every version with its license and
source.

## Related pages

- [Testing a Composer install before publishing](../extensions/testing-a-composer-install-locally.md)
- [Packagist shows an old version](stale-packagist-version.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
