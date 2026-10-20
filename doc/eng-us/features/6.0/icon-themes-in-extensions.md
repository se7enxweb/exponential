# Icon themes that live in your extension

This page is for developers and designers who want their own class icons or file type icons in the admin. Exponential
draws these small icons from an *icon theme*, a folder of images. Before July 2026 the only place for a theme was
`share/icons/`, so a custom icon meant copying files into the core or overriding a kernel class. Since 20 July 2026
(commit `3a40db1988`) an extension can ship its own icon theme: the functions of the former `bciconextensions` and
`bciconextensions_share_icons` extensions are part of the kernel.

**Upgrading?** If you used those two extensions, remove them from `ActiveExtensions[]`. No kernel class override and
no `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` setup are needed any more.

## Put a theme in an extension

1. Create the folders. One folder per size, then per group:

   ```
   extension/mycompany_icons/
     icons/
       crystal-admin/              the theme name
         32x32/apps/job.png
         16x16_indexed/apps/job.png
         icon.ini                  sizes and the class to icon map of this theme
     settings/icon.ini.append.php
   ```

2. Write the theme's own `icons/crystal-admin/icon.ini`:

   ```ini
   #?ini charset="utf-8"?
   [IconSettings]
   Sizes[]
   Sizes[normal]=32x32
   Sizes[small]=16x16_indexed

   [ClassIcons]
   Default=mimetypes/empty.png
   ClassMap[job]=apps/job.png
   ```

3. Name the extension and the theme in the extension's `settings/icon.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   IconExtensions[]=mycompany_icons

   [IconSettings]
   Theme=crystal-admin
   StandardTheme=crystal
   AdditionalThemeList[]=crystal-admin

   */ ?>
   ```

4. Activate the extension (`ActiveExtensions[]=mycompany_icons` in `settings/override/site.ini.append.php`), then:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

5. Open a folder of `job` items in the admin: the list shows your `apps/job.png`.

Extensions listed in `IconExtensions[]` are searched before `share/icons/`, in the order listed.

## How an icon is found

1. The current theme (`Theme`), in the extension folders, then in `Repository` (default `share/icons`).
2. Each theme in `AdditionalThemeList[]`, in the same order of places.
3. `StandardTheme`.
4. The theme's or override's `Default` icon, so a missing file never produces a broken image tag.

Icons in extensions are plain static files: the web server serves them with no extra PHP work per image.

The `crystal-admin` theme now ships an icon for the content class identifier `job`.

## Settings

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/icon.ini` | `IconSettings` | `Repository` | `share/icons` | installation | Default icon repository, relative to the document root |
| `settings/icon.ini` | `IconSettings` | `Theme` | `crystal` | installation | Theme tried first |
| `settings/icon.ini` | `IconSettings` | `StandardTheme` | `crystal` | installation | Last fallback theme |
| `settings/icon.ini` | `IconSettings` | `AdditionalThemeList[]` | empty | installation | Themes searched before `StandardTheme` |
| `settings/icon.ini` | `ExtensionSettings` | `IconExtensions[]` | empty | installation | Extensions whose `icons/` folder is searched; order matters |

The full guide, with the class and the upgrade notes, is [Icon support](../../bc/6.0/ICONS.md).

## Related pages

- [Icon support (guide)](../../bc/6.0/ICONS.md)
- [Additional extension directories](additional-extension-directories.md), [the admin4 design](admin4-design.md)
- [Chronicle: July 2026](../../history/2026/2026-07.md)
