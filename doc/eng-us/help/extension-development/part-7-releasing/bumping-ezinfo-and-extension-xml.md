---
title: Bumping ezinfo.php and extension.xml
audience: developer, designer, app-creator
category: extension-development
order: 40
summary: Every release raises the version inside the extension - ezinfo.php, extension.xml, and composer.json or filelist.md5 where present - to exactly the release's number, in the commit the tag points at, never after.
keywords: version bump, ezinfo.php, extension.xml, composer.json, release commit, tag, about page, metadata, php -l, xmllint
type: help
---
# Bumping ezinfo.php and extension.xml

Part 7, chapter 4. The administration's about page, the upgrade checks and anyone looking at an installed copy read
the version from files inside the extension, not from Git. So every release writes its number into those files, in the
commit the tag points at. This chapter shows the steps.

## The rule

| File | Field | Value for tag `v1.2.4` |
|---|---|---|
| `ezinfo.php` | `'Version'` | `"1.2.4"` |
| `extension.xml` | `<version>` (in `<metadata>` if the file has one) | `1.2.4` |
| `composer.json` | `"version"`, only if the file has one | `"1.2.4"` |
| `share/filelist.md5` | its version header, only if the extension ships one | `1.2.4` |

No `v`, no `dev-master`, no `//autogentag//`. The kernel's about page reads `extension.xml` first, so both files must
agree ([Extension metadata specification](../../../specifications/6.0/extension-metadata.md)).

## The order

1. Choose the number ([Choosing the next version number](choosing-the-next-version.md)).
2. Change the fields.
3. Commit: `Updated: Version 1.2.4`, or include the bump in the release's last commit.
4. Check (below).
5. Tag that commit and push it ([Tags and releases are permanent](tags-are-permanent.md)).

Never tag first and bump afterwards: the tag would carry the old number in its files, forever.

A real one, se7enxweb/expchangeclass commit 32196f0, changed exactly three lines:

```text
composer.json   "php": "^8.1 || ..."     ->  "php": "^8.0 || ^8.1 || ..."
extension.xml   <version>1.0.5</version>  ->  <version>1.0.6</version>
ezinfo.php      'Version' => "1.0.5",     ->  'Version' => "1.0.6",
```

(The `composer.json` line was the change the release was about; that file has no version field of its own.)

## Check

```bash
php -l ezinfo.php
xmllint --noout extension.xml
grep -n "Version\|<version>" ezinfo.php extension.xml
```

Expected: "No syntax errors detected", no output from `xmllint`, the same new number twice.

After installing the release on a test site, open the about page of the administration (the `ezinfo/about` view) and
find your extension's row: the version shown must be the new one.

## The other fields

While you are in the files, check that the rest is still right: the name, the copyright years, the license
(`GNU General Public License v2.0 (or any later version)` or exactly what your license file says) and `Info_url`
pointing at the repository. They are covered in
[Modernizing ezinfo.php and extension.xml](../part-4-upgrading/modernizing-ezinfo-and-extension-xml.md).

## Extensions shipped with the kernel

Extensions that live inside the kernel's repository (ezjscore, ezoe, ezformtoken and others) carry the version of the
Exponential release they ship with, not a numbering of their own.

## Related pages

- Previous: [Tags and releases are permanent](tags-are-permanent.md)
- Next: [Composer and Packagist](composer-and-packagist.md)
- [ezinfo.php](../part-3-extension-developer/ezinfo-php.md), [extension.xml](../part-3-extension-developer/extension-xml.md)
- [filelist.md5 and the upgrade check](filelist-md5-and-the-upgrade-check.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
