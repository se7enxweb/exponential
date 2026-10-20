---
title: filelist.md5 and the upgrade check
audience: developer
category: extension-development
order: 90
summary: Extensions that ship share/filelist.md5 are read by the admin's file consistency check; every release must refresh the checksums of changed files, or the check lists them as modified. How to keep it right.
keywords: filelist.md5, checksums, file consistency check, upgrade check, share, manifest, release, cjw_newsletter, md5sum
type: help
---
# filelist.md5 and the upgrade check

Part 7, chapter 9. Some extensions ship a list of their files with a checksum for each: `share/filelist.md5`. The
administration's upgrade check compares an installation's files with it and lists every file that differs as
"modified". That is useful for finding local changes, and misleading when the list itself is out of date. This
chapter is for maintainers of such extensions. If yours has no `share/filelist.md5`, you can skip it.

## What the check does

In the administration, the upgrade check (Setup, the system upgrade page, "Check file consistency") reads the
kernel's `share/filelist.md5` and those of extensions that ship one, computes the checksum of each listed file, and
lists the files whose checksum differs. A site administrator reads that list as "files someone changed on this
server".

## Why a release must refresh it

If a release changes files but not their lines in `share/filelist.md5`, every installation of that release reports
those files as modified, although nobody touched them. The extension metadata specification records the case of
cjw_newsletter:

| Release | What happened |
|---|---|
| 4.1.4 | changed 21 files without refreshing the manifest; the check listed all of them |
| 4.1.5 | corrected the manifest |
| 4.1.14 | the manifest left out 33 files under `classes/runnable/` |
| 4.1.15 | the manifest lists all 309 tracked files |

## Keep it right

cjw_newsletter's file starts with a short header and then has one line per file: the MD5 checksum, two spaces, the
path relative to the extension.

```text
name: cjw_newsletter
version: 4.2.12
files_count: 697

a65c763ef0340e232cfe8c274f56fca0  .github/FUNDING.yml
aa9ea80af84e6cba65e0764a80b56e16  .gitignore
```

Before every release, regenerate the lines of every tracked file and compare:

```bash
cd extension/myext
git ls-files -z -- . ':!share/filelist.md5' | xargs -0 -r md5sum > ../myext-filelist.new
diff <( grep -E '^[0-9a-f]{32}  ' share/filelist.md5 | sort -k2 ) <( sort -k2 ../myext-filelist.new )
```

(The `grep -E` keeps only the checksum lines, leaving out the header.) Any difference is a line to update. Write the
new list, keep the header, and update its `version` and `files_count` with the release
([Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md)). Delete the temporary file afterwards;
it is written outside the extension so it cannot be committed by accident.

Include every tracked file the extension installs. Leave out files that are not installed (tests, CI configuration),
if your extension's `.gitattributes` excludes them from the Composer archive.

## The kernel's own manifest

The kernel keeps its manifest current with every commit: `make manifest-fix` rewrites the lines of changed files, and
the pre-commit hook and CI check it ([Quality checks](../../../features/6.0/quality-checks.md)). An extension can do
the same with a small check in its own CI.

## Related pages

- Previous: [GitHub releases and release notes](github-releases-and-release-notes.md)
- Next: [Packages (.ezpkg) and the package creator](packages-ezpkg.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
