---
title: "INSTALL.md and CHANGELOG.md"
audience: designer
category: theme-extensions
order: 110
summary: When a theme needs an INSTALL.md beside its README, what a CHANGELOG.md entry looks like (with the Added, Updated, Removed, Renamed and Fixed prefixes), and a real example from a published theme.
keywords: INSTALL.md, CHANGELOG.md, changelog, release notes, install steps, Added, Updated, Fixed, history
type: help
---
# INSTALL.md and CHANGELOG.md

This page is for designers whose theme has grown beyond a one-step install, or that has been released more than once.

## INSTALL.md: when the README is not enough

Write an `INSTALL.md` when installing takes more than switching the extension on, for example when the theme needs:

- a siteaccess of its own and its settings;
- content classes (from packages it ships) or demo content;
- a database table (`share/db_schema.dba`);
- other extensions in a particular loading order.

Structure it as numbered steps, each with the command and what the reader should see afterwards. `cjw_themes_jumper`
and `sevenx_themes_media` both ship an `INSTALL.md`; read one before you write yours.

## CHANGELOG.md: one entry per release

`cjw_themes_jumper` keeps its history like this (shortened):

```markdown
# Changelog

## 0.1.5

- Fixed: The starter design passes the contrast, target size and link name checks (axe, WCAG 2.2 AA). ...

## 0.1.4

- Fixed: The single-location map escapes the address and the title for HTML before it escapes them
  for JavaScript ...
```

The rules:

1. Newest release first, one heading per version, exactly the number of the tag without `v`.
2. One line per change, starting with a prefix: `Added:`, `Updated:`, `Removed:`, `Renamed:` or `Fixed:`
   ([Changelog entries](../distribution/changelog-prefixes.md)).
3. Say what changed for the person using the theme, and why: "The login page shows the register link only to a
   visitor who may register", not "changed login.tpl".
4. Credit contributions: "Pull request #1 by <their public handle>".
5. Mention what the user must do after upgrading (a cache to clear, a setting to add, an upgrade script to run).

## Step by step for a release

1. Before you bump the version, add the new heading and the entries to `CHANGELOG.md`.
2. Commit the changelog together with the version fields
   ([Keeping every version field in sync](keeping-version-fields-in-sync.md)).
3. Reuse the entries for the GitHub release notes ([Writing release notes](../distribution/writing-release-notes.md)).

## Related pages

- [Keeping a CHANGELOG.md](../distribution/keeping-a-changelog-file.md)
- [Writing the README of a theme](readme-for-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
