---
title: "A support policy for your theme"
audience: designer
category: theme-lifecycle
order: 30
summary: Write down which versions of a theme you maintain, with which Exponential and PHP versions, and what an older version gets - modeled on the supported-versions table of Exponential's own security policy.
keywords: support policy, supported versions, maintained versions, version table, SECURITY.md, older versions, upgrade path
type: help
---
# A support policy for your theme

This page is for designers who want users to know which versions of their theme get fixes.

## The model

Exponential's `SECURITY.md` has a supported-versions table: the current 6.0 line on `main` and the next tag cut from it
is supported; released tags are "fixed by upgrading to the newest 6.0.x"; older major lines are not supported. It also
says that a published tag is never changed: a fix arrives as the next release.

## A table for your theme

| Theme version | Exponential | PHP | Status |
|---|---|---|---|
| 1.0.x (newest) | 6.0 | 8.0 to 8.5 | Maintained |
| 0.9.x | 6.0 | 8.0 to 8.4 | Fixed by upgrading to 1.0.x |
| older | - | - | Not supported |

Fill it with what you actually test and fix.

## Step by step

1. Decide how many lines you maintain at once. For one person, one line is realistic.
2. Write the table into `SECURITY.md` and the README.
3. Name the upgrade path for older versions ("upgrade to 1.0.x; see the changelog for changes").
4. Update the table with each release that changes it.

## Good practice

- Maintain the newest line; ask users of older ones to upgrade.
- Never fix an old version by moving its tag; a fix for an old line is a new release of that line
  ([A pushed tag is permanent](../distribution/a-pushed-tag-is-permanent.md)).

## Related pages

- [A compatibility matrix](compatibility-matrix.md)
- [A security policy for a theme](../distribution/security-policy-for-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
