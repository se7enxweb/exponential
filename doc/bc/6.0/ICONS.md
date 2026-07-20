# Icon support in Exponential 6.0.15-alpha

## What changed

The legacy eZ Publish 4 kernel override extensions
[bciconextensions](https://github.com/brookinsconsulting/bciconextensions) and
[bciconextensions_share_icons](https://github.com/brookinsconsulting/bciconextensions_share_icons)
have been **main-lined** into the Exponential 6.0.15-alpha kernel.

This means:

* `eZWordToImageOperator` now searches for icon themes in **extensions** in
  addition to the default `share/icons/` repository.
* No kernel class override or `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` setup is
  required anymore.
* A new default `job` class icon is shipped in the `crystal-admin` theme.
* The `bciconextensions` and `bciconextensions_share_icons` extensions can be
  removed from `ActiveExtensions[]` once sites are upgraded to 6.0.15-alpha.

## What you get as a developer / user

* **Extension-resident icon themes** — put icon sets in
  `extension/<your_extension>/icons/<theme>/` instead of patching the kernel or
  copying files into `share/icons/`.
* **Theme fallback chain** — Exponential searches the current theme, then any
  `AdditionalThemeList[]` themes, then `StandardTheme`.
* **Repository fallback chain** — for each theme, Exponential searches extension
  icon directories first, then the default `share/icons/` repository.
* **Default icon fallback** — if a requested icon is not found, the theme or
  override `Default` icon is used instead of generating a broken `<img>` tag.
* **Static file serving** — icons in extensions are served as ordinary static
  files, so no extra PHP work is done for each image request.

## INI settings

The new behavior is controlled from `settings/icon.ini` (and override/siteaccess
`.append.php` files).

```ini
[IconSettings]
# Default icon repository, relative to the docroot
Repository=share/icons

# The theme used first when an icon is requested
Theme=crystal

# Theme used as a final fallback
StandardTheme=crystal

# Optional list of themes to search before StandardTheme
#AdditionalThemeList[]
#AdditionalThemeList[]=crystal-admin

[ExtensionSettings]
# Extensions whose extension/<name>/icons/ directory should be searched.
# Order matters: listed extensions are checked before the default repository.
IconExtensions[]
#IconExtensions[]=mythemeextension
```

### Siteaccess / override example

A siteaccess that should use the `crystal-admin` theme with an extension can use:

```ini
# settings/siteaccess/sevenx_site_admin/icon.ini.append.php
[IconSettings]
Theme=crystal-admin
StandardTheme=crystal
AdditionalThemeList[]=crystal-admin

[ExtensionSettings]
IconExtensions[]=mycompany_icons
```

## Creating an icon extension

1. Create the extension directory structure:

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

The `IconExtensions[]` entry tells the icon engine to look inside
`extension/mycompany_icons/icons/` **before** falling back to `share/icons/`.

## Available template operators

The same operators are supported as before, but they now transparently search
extension icon themes:

* `{$mime_type|mimetype_icon}` — MIME type icon.
* `{$class_identifier|class_icon}` — class icon.
* `{$class_group_identifier|classgroup_icon}` — class group icon.
* `{$action_identifier|action_icon}` — action icon.
* `{$icon_identifier|icon}` — generic icon.
* `{$country_code|flag_icon}` — country flag icon.
* `{icon_info('class')}` — metadata about the resolved icon theme.

### Examples

```smarty
{* Class icon for the current content class *}
<img src="{$node.class_identifier|class_icon('normal',,true())}" alt="">

{* Same icon rendered as a full <img> tag *}
{$node.class_identifier|class_icon('normal')}

{* Small job icon from an extension theme *}
{'job'|class_icon('small')}
```

## Default icon shipped from bciconextensions_share_icons

The `job.png` icon from `bciconextensions_share_icons` is now part of the
`crystal-admin` theme:

* `share/icons/crystal-admin/32x32/apps/job.png`
* `share/icons/crystal-admin/16x16_indexed/apps/job.png`
* `share/icons/crystal-admin/icon.ini` contains `ClassMap[job]=apps/job.png`

To use it, make sure the active siteaccess/theme is `crystal-admin` or add
`crystal-admin` to `AdditionalThemeList[]`.

## Web server notes

Icons under `extension/<name>/icons/` are ordinary static files. Make sure your
virtual host allows requests to `/extension/*/icons/*` without redirecting them
to `index.php`. The default Exponential `.htaccess`/nginx rules already permit
static files under `extension/`, but if you use custom vhosts, add:

**Apache:**
```apache
RewriteRule ^/extension/[^/]+/icons/[^/]+/[^/]+/[^/]+/.* - [L]
```

**Nginx:**
```nginx
rewrite "^/extension/([^/]+)/icons/([^/]+)/([^/]+)/([^/]+)/(.*)" "/extension/$1/icons/$2/$3/$4" break;
```

## Upgrading from bciconextensions / bciconextensions_share_icons

1. Remove `ActiveExtensions[]=bciconextensions` and
   `ActiveExtensions[]=bciconextensions_share_icons` from your overrides.
2. Remove any `kernel/common/ezwordtoimageoperator.php` kernel override if you
   had one.
3. If you copied `job.png` into `share/icons/crystal-admin/` manually, the
   kernel now ships it by default; keep your override only if you customized it.
4. Regenerate autoloads and clear caches.

## How to test the icons solution in 2min

Run the icon operators from the command line with a temporary template.

1. Create a quick test template that renders the shipped `job` icon URL:

```bash
cat > design/standard/templates/test_icon.tpl <<'EOF'
{'job'|class_icon('normal',,true())}
EOF
```

2. Run the CLI test snippet from the Exponential docroot:

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

If you see that path, the `class_icon` operator is resolving the shipped `job`
icon through the `crystal-admin` theme. To render the full `<img>` tag instead,
change the template to:

```bash
cat > design/standard/templates/test_icon.tpl <<'EOF'
{'job'|class_icon('normal')}
EOF
```

Then rerun the same PHP snippet. Expected output:

```html
<img class="transparent-png-icon" src="/share/icons/crystal-admin/32x32/apps/job.png" width="32" height="32" alt="job" title="job" />
```

3. Clean up the temporary template:

```bash
rm design/standard/templates/test_icon.tpl
```

The test forces the `crystal-admin` theme at runtime because that theme ships
`job.png`; the default `crystal` theme does not include it. If a `job` object
exists in your site, you can also verify the same icon by viewing that object
in the `sevenx_site_admin` siteaccess.

## Files changed

* `kernel/common/ezwordtoimageoperator.php` — extension/theme search, fallback
  handling and `IconExtensions[]` support.
* `settings/icon.ini` — added `StandardTheme`, `AdditionalThemeList[]` and
  `[ExtensionSettings] IconExtensions[]` defaults.
* `share/icons/crystal-admin/icon.ini` — added `ClassMap[job]=apps/job.png`.
* `share/icons/crystal-admin/16x16_indexed/apps/job.png` — new icon.
* `share/icons/crystal-admin/32x32/apps/job.png` — new icon.
