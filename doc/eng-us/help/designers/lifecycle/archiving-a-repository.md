---
title: "Archiving a repository"
audience: designer
category: theme-lifecycle
order: 270
summary: Archive a theme's GitHub repository so it stays readable and installable but takes no new changes, and mark the Packagist package as abandoned with a successor - without deleting anything.
keywords: archive repository, read-only, gh repo archive, abandoned package, Packagist abandon, successor, keep installable
type: help
---
# Archiving a repository

This page is for designers at the end of a theme's life ([End of life](end-of-life.md)).

## Archive, never delete

An archived GitHub repository is read-only: code, tags and releases remain, so Composer can still install every
version. Deleting it breaks every site that installs from it and every link to it.

## Step by step

1. Publish the last release and the end-of-life notice first ([Announcing end of life](announcing-end-of-life.md)).
2. Archive the repository:

   ```bash
   gh repo archive yourvendor/mytheme --yes
   ```

   or **Settings > Archive this repository** in the browser.
3. Check: `gh repo view yourvendor/mytheme --json isArchived` prints `"isArchived": true`.
4. On Packagist, open the package's page and mark it **Abandoned**, naming the successor package if there is one.
   Composer then warns users who install it and names the successor.
5. Test that it still installs: `composer require yourvendor/mytheme` in a throwaway installation.

## Undoing

An archive can be undone (**Unarchive**) if someone takes the theme over ([Handing a theme over](handover-to-a-new-maintainer.md)).

## Related pages

- [Transferring the repository and the Packagist package](transferring-repository-and-packagist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
