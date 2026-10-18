---
title: "Upgrade testing, step by step"
audience: designer, developer
category: theme-lifecycle
order: 100
featured: true
summary: The routine for testing a theme against a new Exponential release - update the test installation, clear caches, walk the page list, compare overrides, check the error log, audit, and record the result.
keywords: upgrade test, routine, new release, regression, page list, error log, caches, record results
type: help
---
# Upgrade testing, step by step

This page is for designers checking whether a theme still works after Exponential (or PHP, or a library) changed.
Run it in your [upgrade test environment](upgrade-test-environment.md).

## Before you start

- Read the changelog for items that touch themes ([Reading a changelog as a designer](reading-a-changelog-as-a-designer.md)).
- Keep a page list in the theme's repository: the URLs of the demo pages that show every template.

## Step by step

1. **Update** the test installation to the new release (`composer update` for the packages concerned), following
   [Upgrading](../../../guides/upgrading.md) for anything the release asks for.
2. **Regenerate and clear**:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

   Reload the PHP service if the site runs on PHP-FPM or Exponential Velocity.
3. **Walk the page list** at 1440 px: each page renders, nothing is missing, no layout jumps.
4. **Check the logs**: `var/log/error.log` and the site's debug output show no new errors or warnings from your theme.
5. **Compare overrides** with the new standard templates ([Comparing your overrides](comparing-overrides-with-the-standard.md)).
6. **Audit**: accessibility, CSP, visual and performance checks ([Accessibility](accessibility-audit-per-release.md),
   [CSP](csp-audit-per-release.md), [Visual regression](visual-regression-checks.md), [Performance](performance-audit-per-release.md)).
7. **Edit and publish** a piece of content as an editor; the theme shows it.
8. **Record** the result in the compatibility matrix and open issues for what failed ([A compatibility matrix](compatibility-matrix.md)).
9. **Fix and release** ([Bumping the version before the tag](../distribution/bumping-versions-before-the-tag.md)).

## Related pages

- [Checklist: one maintenance cycle](lifecycle-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
