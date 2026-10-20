---
title: Changelogs
audience: developer, designer, app-creator
category: extension-development
order: 70
summary: Write a changelog users can act on - grouped by Added, Updated, Fixed, Removed and Renamed, assembled from the commit messages since the last tag, upgrade notes first - and keep an old extension's changelog.
keywords: changelog, release notes, Added, Updated, Fixed, Removed, Renamed, git log, upgrade notes, CHANGELOG.md, changelog.txt
type: help
---
# Changelogs

Part 7, chapter 7. A changelog answers the question every user asks before updating: "What changes for me?". This
chapter shows how to write one quickly from good commit messages, and what to put first.

## Where it lives

Keep one file in the repository and add to it with every release:

| File | Seen in |
|---|---|
| `CHANGELOG.md` at the root (the common choice) | GitHub, Packagist's links, editors |
| `doc/changelog.txt` (older extensions) | keep it if the extension has one; add new entries on top |

se7enxweb/expchangeclass kept the `doc/changelog.txt` of the extension it was forked from, with its old entries and
its own legend (`*fixed +added -removed`), and adds each release on top. That keeps the history readable for anyone
who knew the original.

## Assemble it from the commits

With commit subjects that start with a prefix ([Commit messages the project can build a changelog from](../part-5-extend-fork-contribute/commit-messages.md)),
the raw material is one command:

```bash
git log --format='%s' v1.2.3..HEAD | sort
```

The output sorts itself into groups by the first word. Rewrite each line for users: drop internal details, merge
commits that belong together, keep the effect.

## The shape of an entry

```markdown
## 1.2.4 (2026-10-19)

Upgrade notes
- Clear the template override cache after updating: two templates were added.

Added
- German translation of the setup page.

Updated
- The context menu entries carry no inline handlers, so they work under a Content Security Policy.

Fixed
- The parameter file shown after a conversion is escaped.

Removed
- The unused YUI calendar stylesheet.
```

Rules:

1. **Upgrade notes first**: anything a user must do (clear a cache, run a script, change a setting, new requirement).
   Most entries have none; when there is one, it is the most important line.
2. **The groups in a fixed order**, only the ones that have entries.
3. **One line per change, as the user sees it.** "The menu works under a Content Security Policy", not "Refactored
   classcontextmenu.tpl".
4. **A broken previous release says so**: "1.2.3 left out the translation files; this release adds them."
   ([Tags and releases are permanent](tags-are-permanent.md))
5. **The date**, so users can relate it to their own updates.

## When to write it

In the release's last commit, together with the version bump, before the tag. Then the tag contains the changelog
that describes it. Writing the changelog after tagging leaves the tagged version without its own entry.

## The kernel's changelogs

The kernel keeps one page per release in `doc/changelogs/` (for example `doc/changelogs/6.0/6.0.15.md`), with the
upgrade steps at the top and links to the behavior change pages. It is a larger version of the same idea, and a
good model for an extension with many changes.

## Related pages

- Previous: [The Packagist webhook](the-packagist-webhook.md)
- Next: [GitHub releases and release notes](github-releases-and-release-notes.md)
- [git log](https://git-scm.com/docs/git-log) in the Git documentation
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
