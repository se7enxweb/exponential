---
title: Integrity checks for a bundle
audience: bundler, hosting, developer
category: partnership
order: 410
summary: How a bundler proves the Exponential files in a bundle are the released ones - the file manifest share/filelist.md5, bin/php/checkmanifest.php and its options, the admin's file consistency check, extension manifests, checksums of packages and downloads, and what to do with intentional changes.
keywords: integrity, manifest, share/filelist.md5, checkmanifest.php, --all, --extensions, --csv, --staged, --fix, file consistency check, upgrade check, SHA256SUMS, checksum, bundle
type: help
---
# Integrity checks for a bundle

This page is for bundlers who want to show, and check, that the Exponential inside their bundle is unchanged, and for
support teams who need to know whether a site in the field still is.

## Contents

1. [The file manifest](#the-file-manifest)
2. [checkmanifest.php](#checkmanifestphp)
3. [The check in the administration](#the-check-in-the-administration)
4. [Extensions with their own manifest](#extensions-with-their-own-manifest)
5. [Checksums of what you download and ship](#checksums-of-what-you-download-and-ship)
6. [Intentional changes](#intentional-changes)

## The file manifest

`share/filelist.md5` lists "`md5  path`" for every file of the distribution; "the system upgrade check of the admin
interface compares the files against it, so a stale entry shows up there as a modified file"
(`bin/php/checkmanifest.php`, header). The built-in help also uses it as its file list in an installation without
`.git` ([Documentation to ship](documentation-to-ship.md)).

## checkmanifest.php

The script "needs no kernel and no database", so it runs in any build step:

| Command | Does |
|---|---|
| `php bin/php/checkmanifest.php --all` | every entry exists and has its checksum; Git files missing from the manifest, lines out of order and malformed lines are warnings |
| `php bin/php/checkmanifest.php --all --extensions` | also checks the `share/filelist.md5` every extension may carry |
| `php bin/php/checkmanifest.php --all --csv` | the findings as CSV, "the same columns as the download of the upgrade check" |
| `php bin/php/checkmanifest.php --staged` | the files staged for the next commit (for development) |
| `php bin/php/checkmanifest.php --fix` | rewrites stale checksums, adds missing files, drops removed ones |

"Exit code 0 when everything matches, 1 otherwise." Run `--all` on the built bundle and record the result in the build
record ([Lock files and reproducible builds](lock-files-and-reproducible-builds.md#the-build-record)).

> **Note**
> `--fix` changes the manifest to match your files. On a bundle that would hide your changes from the recipient's
> upgrade check. Use it only on a repository you maintain, never to make a modified bundle "pass".

## The check in the administration

**Setup > System Upgrade > File consistency check** in the administration compares the installation against the
manifest and lists every line that no longer matches as a modified file
([Extension metadata: File manifest](../../specifications/6.0/extension-metadata.md#file-manifest-sharefilelistmd5)).
Recipients and support staff can run it any time; the CSV download has the same columns as `--csv`.

## Extensions with their own manifest

Some extensions ship their own `share/filelist.md5` (for example `cjw_newsletter`). Such an extension refreshes its
manifest "in the same release as any file change", or the check lists its files as modified. Check them with
`--all --extensions`. If you publish extensions with a manifest, keep it current
([Extension bundles](extension-bundles.md)).

## Checksums of what you download and ship

| Item | Checksum source |
|---|---|
| Velocity release files | `SHA256SUMS` in the release ([Velocity packages](../../features/6.0/velocity-packages-and-binaries.md#pick-a-form)) |
| FrankenPHP binary | `exp:velocity install` checks a pinned SHA-256 ([FrankenPHP](../../bc/6.0/frankenphp.md)) |
| Site packages | your own record of each `.ezpkg`; packages can change in place on the public server |
| Your bundle's artifacts | publish a `SHA256SUMS` of your own with each release |

## Intentional changes

A bundle that changes kernel files will show those files as modified forever. Prefer:

1. a setting or an override template in your own extension;
2. a second extension root for your code ([Additional extension directories](../../bc/6.0/AdditionalExtensionDirectories.md));
3. sending the change upstream ([Contributing fixes back](contributing-bundle-fixes-back.md)).

If a kernel change is unavoidable, list it in your release notes so a recipient's file consistency check makes sense.

## Related pages

- [Verifying a customer installation](../partners/verifying-a-customer-installation.md)
- [Testing a bundle](testing-a-bundle.md)
- [Bundle release checklist](bundle-release-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
