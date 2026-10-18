---
title: "Declaring dependencies in extension.xml"
audience: designer, developer
category: theme-extensions
order: 190
summary: The dependencies block of extension.xml - extends, requires and uses - what each does to the loading order, a real example, and how it relates to Composer's require.
keywords: dependencies, extension.xml, extends, requires, uses, loading order, ExtensionOrdering, composer require
type: help
---
# Declaring dependencies in extension.xml

This page is for designers whose theme needs or builds on another extension.

## The block

`cjw_newsletter` declares that it extends `ezwt`:

```xml
<software>
    <metadata>...</metadata>

    <dependencies>
        <extends>
            <extension name="ezwt" />
        </extends>
    </dependencies>
</software>
```

## The three kinds

The kernel reads the block in `ezpExtension::getLoadingOrder()` (`kernel/private/classes/ezpextension.php`):

| Element | Loads your extension | Use it for |
|---|---|---|
| `<extends>` | before the named one (yours wins) | A theme that overrides templates or settings of a base theme |
| `<requires>` | after the named one | Something your theme cannot work without, for example a layouts engine |
| `<uses>` | after the named one | Something your theme uses when present |

The order is applied when `site.ini [ExtensionSettings] ExtensionOrdering=enabled`. It only orders extensions that
are active: declaring `<requires>` does not activate the other extension, and the kernel still loads yours if the
other one is missing. Say in your README what has to be installed and active.

## Step by step

1. Decide for each other extension: does your theme override it (`extends`) or rely on it (`requires`, `uses`)?
2. Add the block to `extension.xml` after `<metadata>`.
3. Check the file: `xmllint --noout extension/mytheme/extension.xml` (no output expected).
4. Clear the INI cache and look at **Setup > Extensions**: the card shows the real loading position.
5. Add the same dependencies to `composer.json` (`require` or `suggest`) so Composer installs them
   ([require and suggest](composer-require-and-suggest.md)).

A broken `extension.xml` is reported in the debug output and its loading order is ignored, so step 3 matters.

## Related pages

- [Loading order and which theme wins](loading-order-for-themes.md)
- [extension.xml: the packaged description](extension-xml.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
