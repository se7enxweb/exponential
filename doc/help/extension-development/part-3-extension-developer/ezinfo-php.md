---
title: "ezinfo.php: who made it and which version"
audience: developer, app-creator
category: extension-development
order: 20
summary: The ezinfo.php metadata file - class name, the static info() method, the keys the administration reads, how it is read after extension.xml, the PHP 8 bare-key trap with a real fix, and how to check it.
keywords: ezinfo.php, metadata, Info class, info(), Name, Version, Copyright, License, Info_url, expInfo, about page, PHP 8, bare keys
type: help
---
# ezinfo.php: who made it and which version

Part 3, chapter 2. `ezinfo.php` tells the system and its users what an extension is: its name, version, copyright,
license and website. **Setup > About**, **Setup > Extensions** and the upgrade checks read it. The reference is
[Extension metadata](../../../specifications/6.0/extension-metadata.md); this chapter shows how to write one and the
mistakes to avoid.

## The file

`extension/mysite_hello/ezinfo.php`:

```php
<?php
class mysite_helloInfo
{
    public static function info()
    {
        return array(
            'Name'      => "My Site Hello",
            'Version'   => "1.0.0",
            'Copyright' => "Copyright (C) 2026 Your Organization",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/your-organization/mysite_hello"
        );
    }
}
```

| Rule | Why |
|---|---|
| The class is the folder name plus `Info`: `mysite_helloInfo` | that is the name the reader looks for |
| `info()` is `public static` | it is called statically |
| Keys are quoted strings | a bare `Name` is a constant lookup, an error in PHP 8 (below) |
| `Name` is a readable name | the about page shows it; a folder name looks broken there |
| `Version` has no `v` | the release tag `v1.0.0` gives `'Version' => "1.0.0"` |
| `License` is the full name | for example `GNU General Public License v2.0 (or any later version)` |
| `Info_url` is the project page | shown as the extension's website |

Keys are matched case-insensitively, so older extensions that write `info_url` still work; new ones write `Info_url`.

## Who reads it, and in which order

The reader is `expInfo` (`lib/ezutils/classes/expinfo.php`): `expInfo::extensionInfo( 'mysite_hello' )` returns the
normalized fields `name`, `description`, `summary`, `version`, `copyright`, `author`, `license` and `info_url`. It reads:

1. `extension.xml` first; non-empty values win,
2. then `ezinfo.php`, for every field `extension.xml` did not give,
3. then `composer.json` and the Git origin, for what is still missing.

So both files should exist and agree. A quick check with the kernel loaded:

```php
<?php
// php bin/php/ezexec.php extension/mysite_hello/scripts/info.php
$info = expInfo::extensionInfo( 'mysite_hello' );
eZCLI::instance()->output( $info === false ? 'not found' : $info['name'] . ' ' . $info['version'] );
```

## The PHP 8 trap: bare keys

Older extensions wrote the keys without quotes. In PHP 5 an undefined constant became its own name with a notice; in
PHP 8 it is an `Error`, and the page that reads the metadata stops. A real fix, from se7enxweb/expsite_installer
(commit `0c771fe`, "Fixed ezinfo.php, whose keys were bare words that PHP 8 reads as undefined constants and stops
on"):

```php
// before
return array( Name => "expsite_installer",
              Version => "1.0.1",
              info_url => "https://github.com/se7enxweb/expsite_installer" );

// after
return array( 'Name' => "expsite_installer",
              'Version' => "1.0.1",
              'Info_url' => "https://github.com/se7enxweb/expsite_installer" );
```

`php -l` does **not** catch this (the file is valid syntax); only running it does. Part 4 has more of these:
[Modernizing ezinfo.php and extension.xml](../part-4-upgrading/modernizing-ezinfo-and-extension-xml.md).

## HTML in the values

Some older extensions put HTML links into `Name` or `Copyright` (`<a href='...'>`). `expInfo` removes the tags
(`strip_tags()`) when it normalizes the fields, so only the text is shown and the link is lost. Write plain text and
use `Info_url` for the link.

## Several copyright holders

Keep every earlier holder when you take over or fork an extension, and add yourself:

```php
'Copyright' => "Copyright (C) 2011 - 2014 Original Author Organization, 2026 Your Organization",
```

See [Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md).

## Check

```bash
php -l extension/mysite_hello/ezinfo.php
php bin/php/ezexec.php extension/mysite_hello/scripts/info.php
```

Then open **Setup > Extensions**: the card shows your name, version, license and website.

## At every release

The `Version` in `ezinfo.php` and in `extension.xml` equals the release version, changed in the commit the tag points
at; see [Bumping ezinfo.php and extension.xml](../part-7-releasing/bumping-ezinfo-and-extension-xml.md).

## Related pages

- Previous: [The extension directory layout](extension-directory-layout.md)
- Next: [extension.xml: metadata and dependencies](extension-xml.md)
- [Extension metadata (specification)](../../../specifications/6.0/extension-metadata.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
