---
title: "Validating a package before you share it"
audience: designer, admin
category: theme-extensions
order: 420
summary: Check a package the way an installation will check it before you hand it out - the Validate button, ezpm validate, the upload rules (gzip, sizes, entries, safe paths, well-formed package.xml) and a manual look inside the archive.
keywords: validate, package validation, ezpm validate, upload inspector, package.xml, xmllint, tar, checksums, safe paths
type: help
---
# Validating a package before you share it

This page is for designers about to publish an `.ezpkg`. The receiving installation inspects every upload before
writing anything; a package that fails there fails for your users. Check it first.

## What an installation checks on upload

1. The name ends in an allowed suffix (`ezpkg`, `tar.gz`, `tgz`) and the file starts with the gzip signature.
2. It is within `MaxArchiveSize` (256 MiB by default), `MaxEntries` (50,000) and `MaxUnpackedSize` (2 GiB).
3. Every entry is a plain file or directory with a relative path: no `..`, no absolute path, no back slash, no
   control character, no link, no device.
4. A well-formed `package.xml` (or `package.json`) is at the top, with `<package>` as root, a valid name, version
   and packaging information, and a vendor that gives a repository name.
5. No package of that name exists in any repository yet.

Then each document must be well formed and, for a JSON package, match the schema and its checksums.

## Step by step

1. On your development installation, open the package's page and press **Validate**. It shows what was checked or
   every problem.
2. On the command line, validate the exported file itself:

   ```bash
   php ezpm.php validate var/tmp/mytheme_classes-1.0.0-1.ezpkg
   ```

3. Look inside:

   ```bash
   tar tzvf var/tmp/mytheme_classes-1.0.0-1.ezpkg | head -30
   ```

   Every path is relative and below the package; no `l` (link) entries in the first column.
4. Extract the definition and check it:

   ```bash
   mkdir -p var/tmp/pkgcheck && tar xzf var/tmp/mytheme_classes-1.0.0-1.ezpkg -C var/tmp/pkgcheck
   xmllint --noout var/tmp/pkgcheck/package.xml
   ```

   Expected: no output from `xmllint`.
5. Import it into a throwaway installation ([Installing your package on a test site](installing-your-package-on-a-test-site.md)).

## When validation fails

| Message names | Fix |
|---|---|
| A path outside the package, or a link | Export again with `ezpm export` from the package directory |
| `package.xml` not well formed | Run `xmllint` on it and correct the line it names |
| "already exists" (on the receiving side) | The user removes the old one first, or you give the package a new name |

## Related pages

- [Packages, section 6: uploading](../../../guides/packages.md#6-uploading-a-package)
- [Classic XML and JSON packages](classic-and-json-package-formats.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
