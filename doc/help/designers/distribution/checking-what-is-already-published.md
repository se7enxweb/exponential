---
title: "Checking what is already published"
audience: designer, developer
category: theme-distribution
order: 100
summary: Before choosing a version number, list the tags on the remote, the GitHub releases and the versions Packagist knows, because each can know something the others do not.
keywords: published versions, tags, releases, Packagist versions, git fetch --tags, gh release list, ls-remote, p2 json
type: help
---
# Checking what is already published

This page is for whoever is about to release a theme. Do this at the start of every release, even if you released
yesterday: someone else, or a script, may have released since.

## Step by step

1. **Tags on the remote**:

   ```bash
   git fetch --tags
   git tag -l 'v*' --sort=version:refname | tail -5
   git ls-remote --tags origin | tail -5
   ```

   `ls-remote` shows what the server has even if your fetch was refused for some reason.
2. **GitHub releases**:

   ```bash
   gh release list --limit 10
   ```

3. **What Packagist knows** (for a Composer package):

   ```bash
   curl -s https://repo.packagist.org/p2/yourvendor/mytheme.json | grep -o '"version":"[^"]*"' | head -5
   ```

   The first entries are the newest versions.
4. **Take the highest number from all three.** That is the number to raise
   ([Choosing a version number](choosing-a-version-number.md)).

## When the lists disagree

| Situation | Meaning | Action |
|---|---|---|
| A tag without a release | A release was not finished | Create the release for that tag; do not reuse its number |
| A release whose tag your clone lacks | Your clone is behind | `git fetch --tags` |
| Packagist behind the tags | The update hook is missing or failed | [Packagist shows an old version](stale-packagist-version.md) |
| Packagist ahead of your tags | A tag was deleted after Packagist read it | Never reuse that number; release the next one |

## Related pages

- [Sorting tags correctly](sorting-tags-correctly.md)
- [A pushed tag is permanent](a-pushed-tag-is-permanent.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
