---
title: "Bumping the version before the tag"
audience: designer, developer
category: theme-distribution
order: 110
summary: The release commit - write the new number into ezinfo.php, extension.xml and the other version fields, check them, commit, and only then tag that commit - so the tag carries the right number forever.
keywords: version bump, release commit, ezinfo.php, extension.xml, composer.json, tag after commit, Updated Version
type: help
---
# Bumping the version before the tag

This page is for whoever cuts a release. The rule is short: **raise the version in a commit, then tag that commit**.
Never tag first and bump afterwards: the tag would carry the old number, and a pushed tag cannot be moved.

## Step by step

1. Choose the number ([Choosing a version number](choosing-a-version-number.md)). Example: `1.0.25`.
2. Write it, without `v`, into:
   - `ezinfo.php`: `'Version' => "1.0.25"`;
   - `extension.xml`: `<version>1.0.25</version>`;
   - `composer.json`: `"version": "1.0.25"`, only if the file has the field;
   - the header of `share/filelist.md5`, only if the theme ships one ([File checksums](filelist-checksums.md)).
3. Add the release to `CHANGELOG.md` ([Keeping a CHANGELOG.md](keeping-a-changelog-file.md)).
4. Check:

   ```bash
   php -l ezinfo.php
   xmllint --noout extension.xml
   grep -n "'Version'" ezinfo.php; grep -n '<version>' extension.xml
   git status --short
   ```

   Expected: no syntax errors, no `xmllint` output, the same number twice, and only the files you meant to change.
5. Commit: `git commit -am "Updated: Version 1.0.25"` (or make it part of the release's last commit).
6. Tag that commit ([Annotated tags](annotated-tags.md)):

   ```bash
   git tag -a v1.0.25 -m "v1.0.25: <what this release is about>"
   ```

7. Push the branch and the tag: `git push origin main v1.0.25`.
8. Create the release ([GitHub releases](github-releases.md)).

## Verify before you push, not after

Before step 7 confirm: the working tree is clean, the theme renders on a test installation, and any built file
(compiled CSS) matches its sources. A minute of checking costs less than a version number burned forever.

## Related pages

- [Keeping every version field in sync](../extensions/keeping-version-fields-in-sync.md)
- [A pushed tag is permanent](a-pushed-tag-is-permanent.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
