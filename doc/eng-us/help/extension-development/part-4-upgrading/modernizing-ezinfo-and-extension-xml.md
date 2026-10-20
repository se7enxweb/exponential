---
title: Modernizing ezinfo.php and extension.xml
audience: developer, app-creator
category: extension-development
order: 110
summary: Bring an old extension's ezinfo.php and extension.xml up to date - quoted keys, a readable name, the release version, the license, the website and credit for included software - and check what the about page shows.
keywords: ezinfo.php, extension.xml, metadata, version, license, Info_url, Includes, about page, expInfo, upgrade, credit
type: help
---
# Modernizing ezinfo.php and extension.xml

Part 4, chapter 11. Two small files tell the system, and the people running it, what an extension is: its name,
version, license, copyright and website. Old extensions often have them wrong, incomplete or missing, and the
administration's about page then shows a directory name and empty columns. This chapter upgrades both files. What
each field means is in Part 3 ([ezinfo.php](../part-3-extension-developer/ezinfo-php.md),
[extension.xml](../part-3-extension-developer/extension-xml.md)); here we look at the typical old forms and how
to fix them.

## Who reads these files

The kernel class `expInfo` (`lib/ezutils/classes/expinfo.php`) reads both files and merges them: `extension.xml`
first, then `ezinfo.php` for anything still missing, then `composer.json` and the Git origin. The about page
(the `ezinfo/about` view of the administration) shows the result, one row per extension. Keys of
`ezinfo.php` are matched case-insensitively. The full rules are in the
[extension metadata specification](../../../specifications/6.0/extension-metadata.md).

## The old forms you will meet

### Bare keys (a fatal error on PHP 8)

```php
// before: se7enxweb/expsite_installer, before commit 0c771fe
return array( Name => "expsite_installer",
              Version => "1.0.1",
              Copyright => "Copyright (C) 1998 - 2026 7x. All rights reserved.",
              License => "GNU General Public License v2.0 (or any later version)",
              info_url => "https://github.com/se7enxweb/expsite_installer" );
```

PHP 8 reads `Name` as a constant and stops. Quote every key. See
[PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md).

### A placeholder version

```xml
<version>//autogentag//</version>
```

`//autogentag//` was replaced by a build script in the old release process. That script is not part of an
extension's life any more, so the placeholder stays and the about page shows it. Write the real version.

### A directory name as the name

```php
'Name' => "myoldext",
```

The name is shown to people. Use a readable name ("My Old Extension"). The specification records that three
extensions showed their directory name on the about page until their release of 30 September 2026.

### An old website

`'Info_url' => "http://ez.no/developer/contribs/..."` and similar addresses point at sites that no longer exist.
Use the extension's repository on GitHub, or its page on
[projects.exponential.earth](https://projects.exponential.earth), over https.

### No license

A missing license is not a formality: without one, nobody may legally redistribute the extension. If the old
extension's files carry a license header (most carry the GPL), copy its exact name into both files. If they carry
none, ask the author before you publish anything
([Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)).

## The modern ezinfo.php

```php
<?php
class myoldextInfo
{
    public static function info()
    {
        return array( 'Name' => "My Old Extension",
                      'Version' => "2.0.0",
                      'Copyright' => "Copyright (C) 2009 The Original Author; Copyright (C) 2026 Your Name",
                      'License' => "GNU General Public License v2.0 (or any later version)",
                      'Info_url' => "https://github.com/your-account/myoldext" );
    }
}
```

| Rule | Why |
|---|---|
| Class name: the directory name plus `Info` | that is how the kernel finds it |
| `info()` is `public static` | it is called statically |
| Keys are quoted | PHP 8 |
| `Version` without a `v` | tag `v2.0.0` is version `2.0.0` |
| Keep the original copyright, add yours | you are not the only author; see [Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md) |

## Credit for software you include

If the extension includes someone else's code (a fork of an older extension, a bundled library), say so with a key
that starts with `Includes`. The about page lists each entry under the extension's row. From
se7enxweb/expchangeclass, which is a fork of an older extension:

```php
'Includes' => array(
    array( 'Name' => "eZChangeclass",
           'Copyright' => "Copyright (C) 2007 (the original author)",
           'License' => "GNU General Public License v2.0" ) )
```

(The name of the original author is in the real file; it is shortened here.) Each entry may have `Name`, `Version`,
`License`, `Copyright` and `Info_url`. A single entry may also be given as one hash instead of a list.

## The modern extension.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<software>
    <metadata>
        <name>My Old Extension</name>
        <version>2.0.0</version>
        <copyright>Copyright (C) 2009 The Original Author; Copyright (C) 2026 Your Name</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/your-account/myoldext</info_url>
    </metadata>
    <dependencies>
        <requires>
            <extension name="ezjscore" />
        </requires>
    </dependencies>
</software>
```

`extension.xml` is read first, so the two files must agree. The flat form (`<extension><name>...</name>
<version>...</version></extension>`) that some extensions use, se7enxweb/expchangeclass among them, is read the
same way.

`<requires>` matters for the loading order: an extension that needs ezjscore's packer, for example, should say so
([Extension load order](../part-3-extension-developer/extension-load-order.md)).

## Check your work

```bash
cd extension/myoldext
php -l ezinfo.php
xmllint --noout extension.xml
grep -n "Version\|<version>" ezinfo.php extension.xml
```

Expected: "No syntax errors detected", no output from `xmllint`, and the same version twice. Then open the about page
in the administration and find your extension's row: name, version, license and website should all be filled in.

## When you release

Every release raises the version in both files to exactly the release's number, in the commit the tag points at,
never afterwards. That is part of the release procedure in
[Bumping ezinfo.php and extension.xml](../part-7-releasing/bumping-ezinfo-and-extension-xml.md).

## Related pages

- Previous: [Database code for PHP 8 and every engine](database-code-for-php-8.md)
- Next: [Modernizing composer.json](modernizing-composer-json.md)
- [ezinfo.php](../part-3-extension-developer/ezinfo-php.md) and [extension.xml](../part-3-extension-developer/extension-xml.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
