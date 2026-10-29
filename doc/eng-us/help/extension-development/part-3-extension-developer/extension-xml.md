---
title: "extension.xml: metadata and dependencies"
audience: developer, app-creator
category: extension-development
order: 30
summary: The extension.xml file: the metadata read before ezinfo.php, and the requires, uses and extends dependencies that the Extensions page shows and that decide the loading order.
keywords: extension.xml, metadata, dependencies, requires, uses, extends, loading order, ExtensionOrdering, xmllint, software, info_url
type: help
---
# extension.xml: metadata and dependencies

Part 3, chapter 3. `extension.xml` does two jobs: it carries the same metadata as `ezinfo.php` (and is read first),
and it declares **dependencies** on other extensions, which the Extensions page shows and which can decide the loading
order.

## A complete file

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>My Site Hello</name>
        <version>1.0.0</version>
        <copyright>Copyright (C) 2026 Your Organization</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/your-organization/mysite_hello</info_url>
    </metadata>
    <dependencies>
        <requires>
            <extension name="ezjscore" />
        </requires>
        <uses>
            <extension name="eztags" />
        </uses>
        <extends>
            <extension name="ezwebin" />
        </extends>
    </dependencies>
</software>
```

Check it:

```bash
xmllint --noout extension/mysite_hello/extension.xml
```

No output means it is well formed. An `extension.xml` that does not parse is ignored (its errors go to the debug
output) and `ezinfo.php` is used instead.

## The metadata block

`<name>`, `<version>`, `<copyright>`, `<license>`, `<info_url>`, and optionally `<description>`, `<summary>` and
`<author>`. The rules are those of [ezinfo.php](ezinfo-php.md): readable name, version without `v`, full license name,
the project page as `info_url`. Never `//autogentag//` or `dev-master` as a version.

An older flat form, `<extension><name>..</name><version>..</version></extension>` without a `<metadata>` block, is
read the same way from the top level.

## Dependencies

| Element | Means | Shown on the Extensions page as | Loading order with `ExtensionOrdering=enabled` |
|---|---|---|---|
| `<requires>` | yours does not work without it | Requires; a problem if it is not active | the required extension is loaded **before** yours |
| `<uses>` | yours uses it when it is there | Uses | the used extension is loaded **before** yours |
| `<extends>` | yours builds on it and changes it | Extends | yours is loaded **before** the extended one |

The order comes from `ezpExtension::getLoadingOrder()` (`kernel/private/classes/ezpextension.php`), which turns
`requires` and `uses` into "after" and `extends` into "before", and `eZExtension::extensionOrdering()`, which sorts
the active extensions topologically. A test with the kernel's sorter shows the direction: an extension `a` that
requires `b` comes out as `b,a`.

Why it matters: an extension **earlier** in the loading order wins for settings and design templates. So:

- declare `<extends>` when your extension overrides another's templates or settings: yours loads first and wins;
- declare `<requires>` for a library you need: it loads first, and the Extensions page warns when it is missing.

`site.ini [ExtensionSettings] ExtensionOrdering=enabled` is the default. The Extensions page shows, per card, where
an extension really loads when that differs from its written position ([The Extensions page](../../../guides/extensions-page.md)).

## Reading dependencies from code

`expExtensionCatalogue::dependenciesFromXml( $xml )` (`kernel/classes/expextensioncatalogue.php`) parses an
`extension.xml` string into `requires`, `uses` and `extends` lists, the same way the loading order reads them:

```php
<?php
$xml = file_get_contents( 'extension/ezwebin/extension.xml' );
print_r( expExtensionCatalogue::dependenciesFromXml( $xml ) );
```

## At every release

`<version>` must equal the release version and the `Version` of `ezinfo.php`, in the commit the tag points at. See
[Bumping ezinfo.php and extension.xml](../part-7-releasing/bumping-ezinfo-and-extension-xml.md).

## Related pages

- Previous: [ezinfo.php: who made it and which version](ezinfo-php.md)
- Next: [Activating an extension](activating-an-extension.md)
- [Extension metadata (specification)](../../../specifications/6.0/extension-metadata.md)
- [Extension load order](extension-load-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](../../copyright-and-copyleft.md).
