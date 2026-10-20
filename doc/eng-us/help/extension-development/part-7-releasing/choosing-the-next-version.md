---
title: Choosing the next version number
audience: developer, designer, app-creator
category: extension-development
order: 20
summary: Look up what is already published before you pick a version - fetch the tags, sort them as versions, compare with GitHub releases and Packagist - so you never reuse a number, with the commands and the sorting trap.
keywords: next version, tags, git fetch --tags, sort -V, version:refname, gh release list, Packagist, published versions, sorting trap
type: help
---
# Choosing the next version number

Part 7, chapter 2. The next version is "the last published version plus one", which sounds simple until a version
turns out to be taken already: by a tag your clone had not fetched, or a release someone else made. This chapter
shows how to find the last published version reliably.

## Step 1: fetch every tag

Your clone knows only the tags it has fetched:

```bash
git fetch --tags
```

## Step 2: sort them as versions

```bash
git tag -l 'v*' --sort=version:refname | tail -5
```

Never use a plain `git tag -l | tail -1`. Sorting by name puts `v1.0.10` before `v1.0.8`:

```bash
git tag -l 'v1.0.*' | tail -1                        # v1.0.9    wrong
git tag -l 'v1.0.*' --sort=version:refname | tail -1 # v1.0.10   right
git tag -l 'v1.0.*' | sort -V | tail -1              # v1.0.10   right
git describe --tags --abbrev=0                       # the newest tag on your branch's history
```

Show five, not one: you see the pattern and notice a gap or an odd tag at once.

## Step 3: compare with the published releases

A tag can exist without a GitHub release, and a release can exist for a tag your clone has not fetched. With the
GitHub command line tool:

```bash
gh release list --limit 10
```

And on Packagist, the package's page lists every version it knows
(`https://packagist.org/packages/<vendor>/<name>`). If Packagist shows an older newest version than your tags, see
[The Packagist webhook](the-packagist-webhook.md).

## Step 4: decide

Take the highest of the three, and raise the position that matches the change ([Versioning rules](versioning-rules.md)):

| Newest published | Change | Next |
|---|---|---|
| v1.0.9 | a fix | v1.0.10 |
| v1.0.10 | a new setting, old behavior unchanged | v1.1.0 |
| v1.1.0 | a template renamed | v2.0.0 |

Decide from what is published, not from memory, not from the version in `ezinfo.php` (it may be ahead or behind) and
not from what the previous release in your notes says.

## In a script

If you automate releases, the same rule applies to the script. A shell script that picks "the newest tag" must sort
with `--sort=version:refname` or `sort -V`:

```bash
#!/usr/bin/env bash
set -euo pipefail
git fetch --tags --quiet
last=$(git tag -l 'v*' --sort=version:refname | tail -1)
echo "Newest tag: ${last:-none}"
gh release list --limit 5
```

The script only shows; the person releasing decides.

## Related pages

- Previous: [Versioning rules](versioning-rules.md)
- Next: [Tags and releases are permanent](tags-are-permanent.md)
- [git tag](https://git-scm.com/docs/git-tag) in the Git documentation
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
