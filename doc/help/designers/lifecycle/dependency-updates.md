---
title: "Keeping dependencies up to date"
audience: designer, developer
category: theme-lifecycle
order: 210
summary: Keep the libraries, fonts, icon sets and extensions a theme depends on current - an inventory, where updates are announced, testing an update, and checking licenses again.
keywords: dependencies, updates, libraries, fonts, icons, extensions, inventory, composer outdated, license check
type: help
---
# Keeping dependencies up to date

This page is for designers maintaining a theme that bundles or requires other work.

## The inventory

Keep a list (for example in `THIRD-PARTY.md`) with, for each dependency: name, version, where it comes from, license,
and how it is loaded.

| Kind | Example | How to update |
|---|---|---|
| Bundled library | A slider script | Replace the file with the new release |
| Bundled font or icon set | An open-source font | Replace the files; check the license again |
| Library from Exponential | jQuery through ezjscore | Comes with Exponential; adapt your code ([JavaScript library upgrades](javascript-library-upgrades.md)) |
| Required extension | A layouts engine | `composer outdated` on the test installation |

## Step by step, each maintenance cycle

1. On the test installation run `composer outdated` and read the list for your theme's dependencies.
2. Check the release notes of each bundled library for security fixes.
3. Update one dependency at a time, test, commit with `Updated: <name> <version>`.
4. Check the license of the new version; licenses change sometimes ([Third-party fonts, icons and libraries](../distribution/third-party-fonts-icons-and-libraries.md)).
5. Update the inventory.

## Remove what you no longer use

A library nobody loads still ships with every install and may carry known vulnerabilities: Exponential removed the old
jQuery UI 1.10.3 for exactly that reason. Remove unused files in a release with a `Removed:` entry.

## Related pages

- [Maintenance calendar](maintenance-calendar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
