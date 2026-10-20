---
title: "Keeping every version field in sync"
audience: designer, developer
category: theme-extensions
order: 70
summary: A theme's version number lives in up to five places; the list, the order in which to change them before a release, and a one-minute check that they agree.
keywords: version, version number, ezinfo.php, extension.xml, composer.json, filelist.md5, tag, release, sync, consistency
type: help
---
# Keeping every version field in sync

This page is for whoever releases a theme. Installations, the About page, Packagist and your users all read the
version from different files. When the files disagree, nobody can tell which release is installed.

## Where the version lives

| Place | Form | Required |
|---|---|---|
| `ezinfo.php` `'Version'` | `1.0.4` | yes |
| `extension.xml` `<version>` | `1.0.4` | yes |
| `composer.json` `version` | `1.0.4` | only if you keep the field (Packagist reads the tag when it is absent) |
| `share/filelist.md5` header | as the file states it | only if the theme ships one ([File checksums](../distribution/filelist-checksums.md)) |
| The git tag | `v1.0.4` (or `1.0.4` if your repository tags without `v`) | yes, for every release |

## The order

1. **Choose the number** from what is already published, never from memory
   ([Checking what is already published](../distribution/checking-what-is-already-published.md)).
2. **Write it into every file** of the table.
3. **Commit** ("Updated: Version 1.0.4", or as part of the release's last commit).
4. **Tag that commit** and push both.

Never tag first and bump afterwards: the tag would carry the old number, and a pushed tag is permanent
([A pushed tag is permanent](../distribution/a-pushed-tag-is-permanent.md)).

## The one-minute check

Run in the theme's folder:

```bash
grep -n "'Version'" ezinfo.php
grep -n '<version>' extension.xml
grep -n '"version"' composer.json
php -l ezinfo.php
xmllint --noout extension.xml
```

Expected: the same number on the first two (and the third if present), `No syntax errors detected in ezinfo.php`,
and no output from `xmllint`.

## When they already disagree

- If nothing has been released with the wrong numbers yet: fix the files, then release.
- If a release went out with mismatched files: do not move its tag. Publish the next version with the files corrected
  and say so in its notes ([When a release goes wrong](../distribution/when-a-release-goes-wrong.md)).

## Related pages

- [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md)
- [Bumping the version before the tag](../distribution/bumping-versions-before-the-tag.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
