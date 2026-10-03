# Icons from extensions (6.0.15)

Read this page if your site uses `bciconextensions` or `bciconextensions_share_icons`, or if you want to ship your
own icon theme in an extension. The two extensions were merged into the Exponential 6.0.15 kernel, so an icon theme
can now live inside any extension, and the extensions themselves can be switched off.

## In short

| | |
|---|---|
| What changed | `eZWordToImageOperator` searches icon themes in extensions (`icon.ini [ExtensionSettings] IconExtensions[]`) before `share/icons/`, follows a theme fallback chain, and falls back to the `Default` icon instead of a broken `<img>`. A `job` class icon ships in the `crystal-admin` theme. |
| Who is affected | Sites that activate `bciconextensions` or `bciconextensions_share_icons`, or keep a kernel override of `kernel/common/ezwordtoimageoperator.php`. Custom vhosts that send `/extension/*/icons/*` to `index.php`. |
| How to check | `grep -n "IconExtensions\|StandardTheme" settings/icon.ini` |
| How to fix | Remove the two extensions from `ActiveExtensions[]` and the kernel override; see [Upgrade from bciconextensions](#upgrade-from-bciconextensions). |

No kernel class override and no `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` setup is needed any more.

## Upgrade from bciconextensions

1. Remove `ActiveExtensions[]=bciconextensions` and `ActiveExtensions[]=bciconextensions_share_icons` from your
   overrides.
2. Remove any kernel override of `kernel/common/ezwordtoimageoperator.php`.
3. If you copied `job.png` into `share/icons/crystal-admin/` by hand, the kernel now ships it. Keep your copy only if
   you changed it.
4. Regenerate autoloads and clear caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

## How icons are found

- **Theme chain**: the current theme (`Theme`), then every `AdditionalThemeList[]` theme, then `StandardTheme`.
- **Repository chain**: for each theme, the `icons/` directory of every extension in `IconExtensions[]` (in the
  listed order), then the default repository `share/icons/`.
- **Default icon**: when nothing matches, the theme's or override's `Default` icon is used.
- **Static files**: icons in extensions are served as ordinary static files; no PHP runs per image.

## Settings

All settings are in `settings/icon.ini`, overridable in `settings/override/` and per siteaccess.

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `icon.ini` | `[IconSettings]` | `Repository` | `share/icons` | global or siteaccess |
| `icon.ini` | `[IconSettings]` | `Theme` | `crystal` | global or siteaccess |
| `icon.ini` | `[IconSettings]` | `StandardTheme` | `crystal` (final fallback) | global or siteaccess |
| `icon.ini` | `[IconSettings]` | `AdditionalThemeList[]` | empty (searched before `StandardTheme`) | global or siteaccess |
| `icon.ini` | `[ExtensionSettings]` | `IconExtensions[]` | empty (searched before `Repository`, order matters) | global or siteaccess |

```ini
[IconSettings]
Repository=share/icons
Theme=crystal
StandardTheme=crystal
#AdditionalThemeList[]
#AdditionalThemeList[]=crystal-admin

[ExtensionSettings]
IconExtensions[]
#IconExtensions[]=mythemeextension
```

A siteaccess that uses the `crystal-admin` theme together with an extension theme:

```ini
# settings/siteaccess/sevenx_site_admin/icon.ini.append.php
[IconSettings]
Theme=crystal-admin
StandardTheme=crystal
AdditionalThemeList[]=crystal-admin

[ExtensionSettings]
IconExtensions[]=mycompany_icons
```

## Example: ship an icon theme in an extension

1. Create the directory structure:

   ```
   extension/mycompany_icons/
     icons/
       crystal-admin/
         32x32/
           apps/
             job.png
         16x16_indexed/
           apps/
             job.png
         icon.ini
   ```

2. Create `extension/mycompany_icons/icons/crystal-admin/icon.ini`:

   ```ini
   #?ini charset="utf-8"?
   [IconSettings]
   Sizes[]
   Sizes[normal]=32x32
   Sizes[small]=16x16_indexed

   [ClassIcons]
   # Default icon if no class identifier matches
   Default=mimetypes/empty.png
   ClassMap[job]=apps/job.png
   ```

3. Create `extension/mycompany_icons/settings/icon.ini.append.php`:

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

4. Activate the extension in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=mycompany_icons
   ```

5. Regenerate autoloads and clear caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

The `IconExtensions[]` entry makes the icon engine look in `extension/mycompany_icons/icons/` before
`share/icons/`.

## Template operators

The operators are the same as before. They now search extension icon themes too.

| Operator | Returns |
|---|---|
| `{$mime_type\|mimetype_icon}` | MIME type icon |
| `{$class_identifier\|class_icon}` | class icon |
| `{$class_group_identifier\|classgroup_icon}` | class group icon |
| `{$action_identifier\|action_icon}` | action icon |
| `{$icon_identifier\|icon}` | generic icon |
| `{$country_code\|flag_icon}` | country flag icon |
| `{icon_info('class')}` | metadata about the resolved icon theme |

```smarty
{* URL of the class icon of the current content class *}
<img src="{$node.class_identifier|class_icon('normal',,true())}" alt="">

{* The same icon as a full <img> tag *}
{$node.class_identifier|class_icon('normal')}

{* Small job icon from an extension theme *}
{'job'|class_icon('small')}
```

## The shipped job icon

The `job.png` icon of `bciconextensions_share_icons` is now part of the `crystal-admin` theme:

- `share/icons/crystal-admin/32x32/apps/job.png`
- `share/icons/crystal-admin/16x16_indexed/apps/job.png`
- `share/icons/crystal-admin/icon.ini` contains `ClassMap[job]=apps/job.png`

To use it, make `crystal-admin` the siteaccess theme or add it to `AdditionalThemeList[]`. The default `crystal`
theme does not include it.

## Web server rules

Icons under `extension/<name>/icons/` are static files. The default Exponential `.htaccess` and nginx rules already
serve static files under `extension/`. A custom vhost must not send `/extension/*/icons/*` to `index.php`; add:

Apache:

```apache
RewriteRule ^/extension/[^/]+/icons/[^/]+/[^/]+/[^/]+/.* - [L]
```

Nginx:

```nginx
rewrite "^/extension/([^/]+)/icons/([^/]+)/([^/]+)/([^/]+)/(.*)" "/extension/$1/icons/$2/$3/$4" break;
```

## Check it in two minutes

This renders the shipped `job` icon from the command line with a temporary template.

1. Create the template:

   ```bash
   cat > design/standard/templates/test_icon.tpl <<'TPL'
   {'job'|class_icon('normal',,true())}
   TPL
   ```

2. Render it from the Exponential document root. The snippet forces the `crystal-admin` theme, because that theme
   ships `job.png`:

   ```bash
   php -r '
   $GLOBALS["eZCurrentAccess"] = array( "name" => "sevenx_site_admin" );
   require "autoload.php";

   $ini = eZINI::instance( "icon.ini" );
   $ini->setVariable( "IconSettings", "Theme", "crystal-admin" );
   $ini->setVariable( "IconSettings", "AdditionalThemeList", array( "crystal-admin" ) );

   $tpl = eZTemplate::factory();
   echo $tpl->fetch( "design:test_icon.tpl" ) . PHP_EOL;
   '
   ```

   Expected output:

   ```text
   /share/icons/crystal-admin/32x32/apps/job.png
   ```

3. For the full `<img>` tag, change the template to `{'job'|class_icon('normal')}` and run the snippet again.
   Expected output:

   ```html
   <img class="transparent-png-icon" src="/share/icons/crystal-admin/32x32/apps/job.png" width="32" height="32" alt="job" title="job" />
   ```

4. Remove the temporary template:

   ```bash
   rm design/standard/templates/test_icon.tpl
   ```

If your site has a `job` object, you can also see the icon by viewing that object in the `sevenx_site_admin`
siteaccess.

## Files

| File | Change |
|---|---|
| `kernel/common/ezwordtoimageoperator.php` | extension and theme search, fallback handling, `IconExtensions[]` |
| `settings/icon.ini` | `StandardTheme`, `AdditionalThemeList[]`, `[ExtensionSettings] IconExtensions[]` |
| `share/icons/crystal-admin/icon.ini` | `ClassMap[job]=apps/job.png` |
| `share/icons/crystal-admin/16x16_indexed/apps/job.png` | new icon |
| `share/icons/crystal-admin/32x32/apps/job.png` | new icon |

Upstream projects that were merged: [bciconextensions](https://github.com/brookinsconsulting/bciconextensions) and
[bciconextensions_share_icons](https://github.com/brookinsconsulting/bciconextensions_share_icons).

## Related pages

- [Icon themes in extensions](../../features/6.0/icon-themes-in-extensions.md)
- [Extensions: behaviour changes](extensions-behaviour-changes.md)
- [Upgrading guide](../../guides/upgrading.md)
