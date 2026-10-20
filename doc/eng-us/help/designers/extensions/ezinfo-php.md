---
title: "ezinfo.php: what the About page reads"
audience: designer, developer
category: theme-extensions
order: 50
summary: Write the ezinfo.php file of a theme extension step by step, with the keys the About page and the upgrade checks read, a complete example from a real theme, and the checks to run before a release.
keywords: ezinfo.php, extension metadata, Version, License, Copyright, Info_url, About page, setup/about, expInfo, theme extension, design extension
type: help
---
# ezinfo.php: what the About page reads

Every theme extension tells the system who it is in two small files: `ezinfo.php` and
[`extension.xml`](extension-xml.md). This page is about the first. When you finish it you have an `ezinfo.php`
that shows your theme's name, version, license and website on **Setup > About** (`/ezinfo/about`) and on
**Setup > Extensions**.

## Before you start

- You have a theme extension folder, for example `extension/mytheme/`
  ([The folder structure of a theme extension](extension-folder-structure.md)).
- You know the version you are about to release ([Choosing a version number](../distribution/choosing-a-version-number.md)).

## Step by step

1. Create `extension/mytheme/ezinfo.php`. The class name is the folder name followed by `Info`, and `info()` is
   `static`:

   ```php
   <?php
   class mythemeInfo
   {
       public static function info()
       {
           return array(
               'Name'      => "My Theme",
               'Version'   => "1.0.0",
               'Copyright' => "Copyright (C) 2026 Your Name",
               'License'   => "GNU General Public License v2.0 (or any later version)",
               'Info_url'  => "https://github.com/<you>/mytheme",
           );
       }
   }
   ```

2. Fill each key:

   | Key | What to write | Example from a real theme |
   |---|---|---|
   | `Name` | The human name of the theme | `CJW Themes Jumper` |
   | `Version` | The release version, without a `v` (tag `v0.1.6` gives `0.1.6`) | `0.1.6` |
   | `Copyright` | The years and every holder whose work is in the theme | `2007 - 2026 CJW Network, JAC Systeme GmbH and 7x` |
   | `License` | Exactly what your `LICENSE` file says | `GNU General Public License v2.0 (or any later version)` |
   | `Info_url` | The theme's project page, usually its repository | `https://github.com/se7enxweb/cjw_themes_jumper` |
   | `Author` (optional) | Who wrote it | `7x` |

   The examples are copied from `extension/cjw_themes_jumper/ezinfo.php` as it ships with release 0.1.6.

3. Write the same version, license and website into [`extension.xml`](extension-xml.md). The About page reads
   `extension.xml` first when it has a `<metadata>` block, so the two files must agree
   ([Keeping every version field in sync](keeping-version-fields-in-sync.md)).

4. Check the file:

   ```bash
   php -l extension/mytheme/ezinfo.php
   ```

   Expected: `No syntax errors detected in extension/mytheme/ezinfo.php`.

5. Open **Setup > About** in the administration. Your theme's row shows the name, version, license and website you
   wrote. A dash means the value is missing.

## Rules worth keeping

- **Quote every key** (`'Version'`, not `Version`) and keep `info()` `static`.
- **Never write a placeholder** such as `//autogentag//` or `dev-master` as the version. The About page and the
  upgrade check cannot tell a placeholder from a real release.
- **Raise the version in the commit the release tag points at**, never after the tag
  ([Bumping the version before the tag](../distribution/bumping-versions-before-the-tag.md)).
- **The license line must match the `LICENSE` file**. If the theme is GPL, write the license's full name as above.

![The About page of the administration, which lists every extension with the version, license and website from its ezinfo.php or extension.xml](../../images/designers/about-page.webp)

## Related pages

- [extension.xml: the packaged description](extension-xml.md)
- [Keeping every version field in sync](keeping-version-fields-in-sync.md)
- [Copyright and author lines](copyright-and-author-lines.md)
- [Specification: extension metadata](../../../specifications/6.0/extension-metadata.md)
- [Theme extensions: start here](theme-extensions-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
