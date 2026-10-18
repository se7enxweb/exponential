---
title: "Putting a theme under Git"
audience: designer
category: theme-extensions
order: 280
summary: Keep a theme in its own Git repository, rooted at the extension folder - why one repository per theme, how to start it, how to work on it inside an installation, and which commit message prefixes to use.
keywords: git, repository, git init, version control, commit messages, Added, Updated, one repository per theme, clone, extension folder
type: help
---
# Putting a theme under Git

This page is for designers who have not yet put their theme under version control, or who keep it inside a site's
repository. The themes 7x publishes each live in a repository of their own whose root is the extension folder.

## Why one repository per theme

| One repository per theme | The theme inside a site's repository |
|---|---|
| Its own history, tags and releases | Releases mixed with the site's changes |
| Installable with Composer by any site | Copied by hand into other sites |
| Contributors see only the theme | Contributors see the customer's site |
| Can be public while the site stays private | Publishing it means publishing the site |

## Step by step: start the repository

1. Go to the theme's folder: `cd extension/mytheme`.
2. Create the repository with `main` as its branch: `git init -b main`.
3. Check what will be committed: `git status --short`. Add a `.gitignore` first
   ([A .gitignore for themes](gitignore-for-themes.md)).
4. Commit: `git add . && git commit -m "Added: The mytheme theme extension"`.
5. Create the remote repository and push ([Creating the GitHub repository](../distribution/creating-the-github-repository.md)).

## Working on it inside an installation

- If the installation requires the theme through Composer, let Composer make the clone so its history is kept:
  `composer config --global 'preferred-install.yourvendor/*' source`, then `composer require` (or `composer
  reinstall`) the package. A clone made by hand in place of an archive Composer installed is replaced at the next
  update. See [Develop with Git](../../develop-with-git.md).
- Commit in `extension/mytheme`, not in the installation's repository.

## Commit messages

Write what changed for the user and why, with one of these prefixes, so a changelog can be assembled from the
history:

| Prefix | For |
|---|---|
| `Added:` | A new template, view mode, setting, translation |
| `Updated:` | Changed behavior or look |
| `Removed:` | A deleted template, setting or file |
| `Renamed:` | A renamed file, setting or class |
| `Fixed:` | A bug fix |

Example: `Fixed: The map popup escapes the address and the title`.

## Related pages

- [From the wizard's output to the first commit](from-wizard-output-to-first-commit.md)
- [Running a team on Git](../../leadership/running-a-team-on-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
