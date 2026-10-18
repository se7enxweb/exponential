---
title: "Transferring the repository and the Packagist package"
audience: designer, developer
category: theme-lifecycle
order: 290
summary: Move a theme's GitHub repository and Packagist package to a new owner without breaking installs - GitHub's transfer and redirect, the package name question, Packagist maintainers and the update hook.
keywords: transfer repository, gh repo transfer, Packagist maintainer, package name, redirect, webhook, ownership
type: help
---
# Transferring the repository and the Packagist package

This page is for designers handing a theme over ([Handing a theme over](handover-to-a-new-maintainer.md)).

## The repository

GitHub can transfer a repository to another user or organization (**Settings > Transfer ownership**). GitHub then
redirects the old URL to the new one, but tell users to update their links anyway; a redirect can break if someone
later creates a repository with the old name.

After the transfer:

1. Check the update hook to Packagist still works ([Keeping Packagist up to date automatically](../distribution/packagist-auto-update.md)).
2. Update `homepage`, `support` and `Info_url` in the theme's files in the next release.

## The Composer package

The package name (`yourvendor/mytheme`) is part of every user's `composer.json`. Two options:

| Option | Users | Note |
|---|---|---|
| Keep the name | Change nothing | Add the new maintainer as maintainer on Packagist; update the repository URL on the package page |
| A new name under the new vendor | Change their `require` | Mark the old package abandoned with the new one as successor ([Archiving a repository](archiving-a-repository.md)) |

Keeping the name is easiest for users. A new name is cleaner if the new owner's vendor must appear.

## Step by step (keeping the name)

1. On Packagist, add the new owner as a maintainer of the package.
2. Transfer the GitHub repository.
3. On the package page, update the repository URL if it does not follow automatically.
4. Make a release from the new home and check that Packagist shows it.

## Related pages

- [Submitting a theme to Packagist](../distribution/submitting-to-packagist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
