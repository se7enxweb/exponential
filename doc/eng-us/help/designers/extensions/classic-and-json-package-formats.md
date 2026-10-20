---
title: "Classic XML and JSON packages"
audience: designer, developer
category: theme-extensions
order: 400
summary: The two formats an Exponential package can be stored in - classic package.xml and JSON package.json - what differs (checksums, file names, schema), when JSON helps a designer, and how to convert between them.
keywords: package format, JSON package, package.json, package.xml, classic format, convert, SHA-256, JsonML, ezpm convert, json.ezpkg
type: help
---
# Classic XML and JSON packages

This page is for designers who are asked "Classic XML or JSON?" in the package wizard. Both formats hold the same
package, element for element, and install the same way. The classic format stays the default.

## The differences

| | Classic | JSON |
|---|---|---|
| Definition | `package.xml` | `package.json` |
| Documents | `ezcontentclass/class-article.xml` ... | the same names with `.json` |
| Other files (images, templates, extension files) | as they are | as they are, byte for byte |
| Checksums | MD5 of design files only | SHA-256 of every file |
| Archive name | `<name>-<version>-<release>.ezpkg` | `<name>-<version>-<release>.json.ezpkg` |
| Schema | the kernel's reader | `share/package/schema/package-v1.json` |

The format is detected from the content (which definition file is at the top), never from the file name.

## When JSON helps a designer

- You review packages in pull requests: JSON diffs read better for many people and tools.
- You want every file covered by a checksum, so a changed or missing file is found before anything is installed.
- Your own scripts read or write packages.

Stay with classic when the people installing your package use older tools that only know `package.xml`.

## Step by step: choose or convert

1. In the wizard's **Package information** step, choose **Classic XML** or **JSON**.
2. To download the other format later, use **Download as JSON** (or **Download**) on the package's page.
3. On the command line, convert an archive:

   ```bash
   php ezpm.php convert mytheme_classes-1.0-1.ezpkg var/tmp/mytheme_classes-1.0-1.json.ezpkg
   ```

   `php ezpm.php help convert` explains the target (an archive or a new directory) and `--to`.
4. Validate the result: `php ezpm.php validate var/tmp/mytheme_classes-1.0-1.json.ezpkg`.

## Related pages

- [The JSON package format](../../../bc/6.0/package-json-format.md)
- [Validating a package](validating-a-package.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
