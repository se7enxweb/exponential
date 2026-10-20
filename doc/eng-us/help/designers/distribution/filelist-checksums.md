---
title: "File checksums (share/filelist.md5)"
audience: designer, developer
category: theme-distribution
order: 230
summary: When a theme ships share/filelist.md5, the administration's file consistency check compares installed files against it; how to refresh the checksums in the same release, and what happens when you forget.
keywords: filelist.md5, checksums, file consistency check, upgrade check, modified files, md5, share folder
type: help
---
# File checksums (share/filelist.md5)

This page is for designers whose theme ships a `share/filelist.md5`. Most themes do not; then this page only explains
why you might add one.

## What it is for

The administration's upgrade check (**Setup > Upgrade check > Check file consistency**) compares installed files with
their recorded checksums and lists every file that differs as modified. For an extension that ships
`share/filelist.md5`, its lines are part of that comparison. It lets an administrator see whether someone changed the
theme's files on the server.

The check is described in [File consistency check](../../../features/6.0/file-consistency-check.md).

## The rule

**Refresh the checksums in the same release as the files change.** If you change 21 files and not their checksums, the
check lists all 21 as modified on every installation of that release, and administrators cannot tell your changes
from tampering. A changelog of a newsletter extension recorded exactly that mistake and its correction in the next
release.

## Step by step in a release

1. Make your changes and commit them.
2. Regenerate the checksums of the changed files (or of all files) with the same tool and format the file already
   uses. Every line is `<md5>  <path>`; the paths are as the file already writes them.
3. Update the version header of the file, if it has one, to the release number.
4. Commit together with the version bump ([Bumping the version before the tag](bumping-versions-before-the-tag.md)).
5. On a test installation, run the consistency check: your theme's files are not listed as modified.

The installation's maintainer script `bin/shell/generatefilelist.sh` can refresh extensions that already ship a list
(read `bash bin/shell/generatefilelist.sh --help` first and use `--dry-run`).

## Should your theme ship one?

| Ship one when | Leave it out when |
|---|---|
| Administrators of your users care about tamper detection | You release often and nobody uses the check |
| You can refresh it reliably in every release | A forgotten refresh would cause more alarm than it prevents |

## Related pages

- [Upgrade check](../../../guides/upgrade-check.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
