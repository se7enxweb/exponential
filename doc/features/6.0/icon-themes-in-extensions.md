# Icon themes that live in your extension

Exponential draws the small icons of the admin (class icons, file type icons)
from an *icon theme*, a folder of images. Before July 2026 the only place for a
theme was `share/icons/`, so a custom icon meant copying files into the core
or overriding a kernel class. Since 20 July 2026 (commit `3a40db1988`) the
functions of the former `bciconextensions` and `bciconextensions_share_icons`
extensions are part of the kernel: an extension can ship its own icon theme.

If you used those two extensions, remove them from `ActiveExtensions[]` after the
upgrade. No kernel class override and no `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE`
setup are needed any more.

## Put a theme in an extension

```
extension/mycompany_icons/
  icons/
    crystal-admin/              the theme name
      32x32/apps/job.png        one folder per size, then per group
      16x16_indexed/apps/job.png
      icon.ini                  sizes and the class to icon map of this theme
  settings/icon.ini.append.php
```

`icons/crystal-admin/icon.ini` (the theme's own file):

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

`settings/icon.ini.append.php` of the extension names the extension and the theme:

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

Activate the extension (`ActiveExtensions[]=mycompany_icons` in
`settings/override/site.ini.append.php`), then:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Extensions listed in `IconExtensions[]` are searched before `share/icons/`, in the
order listed.

## How an icon is found

1. The current theme (`Theme`), in the extension folders, then in `Repository`
   (default `share/icons`).
2. Each theme in `AdditionalThemeList[]`, in the same order of places.
3. `StandardTheme`.
4. The theme's or override's `Default` icon, so a missing file never produces a
   broken image tag.

Icons in extensions are plain static files: the web server serves them with no
extra PHP work per image.

## A default job icon

The `crystal-admin` theme now has an icon for the content class identifier `job`.

## Settings

File `settings/icon.ini`:

| Block | Key | Default | Meaning |
|---|---|---|---|
| `[IconSettings]` | `Repository` | `share/icons` | Default icon repository, relative to the document root. |
| `[IconSettings]` | `Theme` | `crystal` | Theme tried first. |
| `[IconSettings]` | `StandardTheme` | `crystal` | Last fallback theme. |
| `[IconSettings]` | `AdditionalThemeList[]` | empty | Themes searched before `StandardTheme`. |
| `[ExtensionSettings]` | `IconExtensions[]` | empty | Extensions whose `icons/` folder is searched; order matters. |

The full guide, with the class and the upgrade notes, is [Icon support](../../bc/6.0/ICONS.md).

## Related

- Month page: [July 2026](../../history/2026/2026-07.md)
- [Additional extension directories](additional-extension-directories.md)
- [The admin4 design](admin4-design.md)
