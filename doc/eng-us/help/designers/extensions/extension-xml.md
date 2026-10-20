---
title: "extension.xml: the packaged description"
audience: designer, developer
category: theme-extensions
order: 60
summary: Write the extension.xml of a theme with its metadata block (name, version, copyright, license, website) and optional dependencies, check it with xmllint, and keep it equal to ezinfo.php.
keywords: extension.xml, metadata, software, version, license, info_url, copyright, dependencies, xmllint, About page
type: help
---
# extension.xml: the packaged description

This page is for designers who prepare a theme for release. `extension.xml` is the second metadata file of an
extension. **The About page reads it first** when it has a `<metadata>` block, so a wrong value here wins over a
correct one in [`ezinfo.php`](ezinfo-php.md).

## A complete example

This is the file of `cjw_themes_jumper` 0.1.6, as published:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>CJW Themes Jumper</name>
        <version>0.1.6</version>
        <copyright>Copyright (C) 2007 - 2026 CJW Network, JAC Systeme GmbH and 7x</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/se7enxweb/cjw_themes_jumper</info_url>
    </metadata>
</software>
```

## Step by step

1. Create `extension/mytheme/extension.xml` with the root `<software>` and a `<metadata>` block.
2. Fill the elements:

   | Element | Value | Must equal |
   |---|---|---|
   | `<name>` | Human name | `Name` in `ezinfo.php` |
   | `<version>` | Release version without `v` | `Version` in `ezinfo.php`, the tag, `composer.json` `version` if you keep one |
   | `<copyright>` | Years and holders | `Copyright` in `ezinfo.php` |
   | `<license>` | The license's full name | `License` in `ezinfo.php` and the `LICENSE` file |
   | `<info_url>` | Project page | `Info_url` in `ezinfo.php` |

3. If the theme needs another extension, add a `<dependencies>` block
   ([Declaring dependencies in extension.xml](dependencies-in-extension-xml.md)).
4. Check that the XML is well formed:

   ```bash
   xmllint --noout extension/mytheme/extension.xml
   ```

   Expected: no output. Any output names the line that is broken.
5. Reload **Setup > About** and **Setup > Extensions**: the row shows the values of this file.

## Values never to write

- `//autogentag//` or `dev-master` as the version: they were placeholders of build scripts long ago and tell a
  reader nothing.
- A license name that differs from the `LICENSE` file.
- A website that is not yours, for example the product's site for a theme the product does not maintain.

## Related pages

- [ezinfo.php: what the About page reads](ezinfo-php.md)
- [Keeping every version field in sync](keeping-version-fields-in-sync.md)
- [Specification: extension metadata](../../../specifications/6.0/extension-metadata.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
