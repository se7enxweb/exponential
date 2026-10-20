---
title: "Changelog entries: Added, Updated, Removed, Renamed, Fixed"
audience: designer, developer
category: theme-distribution
order: 170
summary: Write changelog entries and commit messages with one of five prefixes so a changelog can be assembled from history - what each prefix means, good and bad examples, and one change per entry.
keywords: changelog, prefixes, Added, Updated, Removed, Renamed, Fixed, commit messages, release notes, conventions
type: help
---
# Changelog entries: Added, Updated, Removed, Renamed, Fixed

This page is for designers writing commit messages and changelog lines. The Exponential project and the 7x themes use
the same five prefixes, so a reader can scan a history and a changelog can be assembled from commits.

## The prefixes

| Prefix | For | Example |
|---|---|---|
| `Added:` | Something new: a template, view mode, setting, translation, screenshot | `Added: Open Graph and Twitter tags on every page of the starter design` |
| `Updated:` | Changed behavior or look of something that exists | `Updated: The login page shows the register link only to a visitor who may register` |
| `Removed:` | Something deleted | `Removed: The unused YUI stylesheet` |
| `Renamed:` | A file, setting, class or design renamed | `Renamed: mytheme.ini [Front] to [FrontPage]` |
| `Fixed:` | A bug fix | `Fixed: The map popup escapes the address and the title` |

The `Added:` and `Updated:` examples are taken from the `cjw_themes_jumper` changelog (0.1.2 and 0.1.3).

## Rules

1. **One change per entry.** A change that adds and removes things gets two entries.
2. **Say what changed for the user**, not which file you touched.
3. **Give the reason** when it is not obvious: "... so that the page works under a Content Security Policy".
4. **Name what the user must do**, if anything.
5. **No private details**: no internal paths, host names or customer names. Credit people by public handle.
6. **Write "Exponential"** for the product.

## Good and bad

| Bad | Good |
|---|---|
| `fix stuff` | `Fixed: Event teasers keep their date in dark mode` |
| `Updated: login.tpl` | `Updated: The login form names its fields for screen readers` |
| `Added: new feature and removed old one` | Two entries: `Added: ...` and `Removed: ...` |

## Related pages

- [Keeping a CHANGELOG.md](keeping-a-changelog-file.md)
- [INSTALL.md and CHANGELOG.md](../extensions/install-and-changelog-files.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
