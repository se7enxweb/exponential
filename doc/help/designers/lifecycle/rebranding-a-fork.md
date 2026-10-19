---
title: "Rebranding a fork, fairly"
audience: designer, sales-marketing
category: theme-lifecycle
order: 320
summary: Give a forked theme its own identity without erasing its origin - a new name, package and design name, kept notices and license, a visible "based on" credit, and no implied endorsement - with what to change in which file.
keywords: rebrand, rebranding, fork, rename, attribution, based on, ethics, notices, endorsement, package name
type: help
---
# Rebranding a fork, fairly

This page is for designers who publish a fork under their own name. Rebranding is allowed under the GPL; erasing the
people who made the original is not, and is unfair even where the law is unclear.

## Change

| What | Where |
|---|---|
| The extension and design names | Folder, `DesignExtensions[]`, `SiteDesign` documentation ([Naming your theme extension](../extensions/naming-your-theme-extension.md)) |
| The human name | `ezinfo.php` `Name`, `extension.xml` `<name>`, README |
| The package name | `composer.json` `name`, `extra.ezpublish-legacy-extension-name` |
| Your website and support | `Info_url`, `homepage`, `support` |
| Your own new work | Add your copyright line for it |

## Keep

| What | Why |
|---|---|
| `LICENSE` and the license of the code | The GPL requires it |
| Every existing copyright notice, in files and metadata | The GPL requires it; it is also the truth |
| Third-party notices | Their licenses require it |
| A "based on <original> by <author>" line in the README | Fairness; helps users find the origin |

## Do not

- Imply that the original author or the Exponential project endorses your fork.
- Use the original's name as your product's name ([Names and trademarks](../sales/names-and-trademarks.md)).
- Rewrite history to remove the original authors from commits.

## Step by step

1. Make the changes of the first table in one commit: `Renamed: <old> to <new>`.
2. Add the "based on" line and your copyright line.
3. Search for the old name: `git grep -n -i "<old name>"`; change only identity, never copyright notices.
4. Release under your package name; tell users of the original how to switch, if your fork continues it.

## Related pages

- [Credits and attribution](credits-and-attribution.md)
- [Contributing back upstream](contributing-back-upstream.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
