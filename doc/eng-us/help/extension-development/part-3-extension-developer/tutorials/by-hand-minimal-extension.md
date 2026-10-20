---
title: "By hand: the smallest extension in five minutes"
audience: developer, app-creator, designer
category: extension-development
order: 100
summary: Three files make a complete extension - ezinfo.php, extension.xml and one settings file. Create them, check them, switch the extension on, see it in Setup > Extensions and see its setting win, then put it under Git.
keywords: tutorial, by hand, minimal extension, first extension, ezinfo.php, extension.xml, settings, activate, setup extensions, git init
type: help
---
# By hand: the smallest extension in five minutes

Tutorial 9. This is the smallest useful extension: it has metadata, so the administration knows what it is, and one
setting, so it changes something you can see. Every later tutorial adds to the same folder, `extension/mysite_hello`.
The files below passed `php -l` and `xmllint`.

## 1. The folder

```bash
mkdir -p extension/mysite_hello/settings
cd extension/mysite_hello
git init
```

## 2. ezinfo.php

```php
<?php
class mysite_helloInfo
{
    public static function info()
    {
        return array(
            'Name'      => "My Site Hello",
            'Version'   => "0.1.0",
            'Copyright' => "Copyright (C) 2026 Your Organization",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/your-organization/mysite_hello"
        );
    }
}
```

The class is the folder name plus `Info`; the keys are quoted; `info()` is static ([ezinfo.php](../ezinfo-php.md)).

## 3. extension.xml

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>My Site Hello</name>
        <version>0.1.0</version>
        <copyright>Copyright (C) 2026 Your Organization</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/your-organization/mysite_hello</info_url>
    </metadata>
</software>
```

## 4. One setting

`settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=Hello from mysite_hello

*/ ?>
```

## 5. Check

```bash
php -l ezinfo.php
php -l settings/site.ini.append.php
xmllint --noout extension.xml
cd ../..
```

Expected: two `No syntax errors detected` lines and nothing from `xmllint`.

## 6. Switch it on

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezcache.php --clear-tag=ini
```

## 7. See it

- **Setup > Extensions**: a card "My Site Hello", version 0.1.0, your license and website.
- The setting:

  ```bash
  ./console exp:ini where site.ini/SiteSettings/SiteName
  ```

  lists your file. Whether it wins depends on the order: a later file (the global override, or an extension listed
  before yours) may set `SiteName` too; the last line of the output says which value is in effect. See
  [Settings scopes and the order they win in](../../part-2-builder/settings-scopes-and-their-order.md).

## 8. Commit

```bash
git -C extension/mysite_hello add ezinfo.php extension.xml settings/site.ini.append.php
git -C extension/mysite_hello commit -m "Added: The mysite_hello extension with its metadata and a site name"
```

## Undo

```bash
./console exp:ini rem 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezcache.php --clear-tag=ini
```

The next tutorials keep the extension active and add a template operator, a fetch function, a module and more. The
`SiteName` line was only for this exercise: remove it before you continue.

## Related pages

- Previous: [With RAD: handlers and kernel overrides](rad-handlers-and-kernel-overrides.md)
- Next: [By hand: a template operator](by-hand-template-operator.md)
- [The extension directory layout](../extension-directory-layout.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
