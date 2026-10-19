---
title: "Registering the design: DesignExtensions[]"
audience: designer, developer
category: theme-extensions
order: 120
summary: The one setting that makes the template system look into a theme extension's design folder - design.ini [ExtensionSettings] DesignExtensions[] - how to write it, how to check that it works, and what goes wrong without it.
keywords: DesignExtensions, design.ini, ExtensionSettings, design folder, register design, template lookup, design extension
type: help
---
# Registering the design: DesignExtensions[]

This page is for designers whose templates are not found although the files are in place. An active extension is
not automatically searched for designs: it has to **register itself as a design extension**.

## The setting

`settings/design.ini` explains it in its own comment: add the extension name to `DesignExtensions[]` to "automatically
get design from the extension when it's turned on". In your theme:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mytheme

*/ ?>
```

saved as `extension/mytheme/settings/design.ini.append.php`. The value is the **extension (folder) name**, not the
design name. Every `design/<name>/` folder below the extension is then available as a design.

`cjw_themes_jumper` does exactly this, with a comment naming its design:

```ini
[ExtensionSettings]
# The design "starter" (design/starter/) of this extension
DesignExtensions[]=cjw_themes_jumper
```

## Step by step

1. Create the file above in your theme.
2. Activate the extension ([Activating a theme](activating-a-theme-extension.md)).
3. Choose the design for a siteaccess ([The design settings a theme ships](design-settings-a-theme-ships.md)).
4. Clear the INI and template caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-id=template,template-override --allow-root-user
   ```

5. Check what is in effect:

   ```bash
   ./console exp:ini where design.ini/ExtensionSettings/DesignExtensions --allow-root-user
   ```

   Expected: your extension's `settings/design.ini.append.php` among the files, and your extension name in the
   value in effect.

6. Open a page of the siteaccess. With template debugging on, the template path comments show files from
   `extension/mytheme/design/...` ([Debugging a design](../theme-design/debugging-a-design.md)).

## Symptoms when it is missing

| You see | Why |
|---|---|
| The site shows the standard design although `SiteDesign=mytheme` | The design folder is not searched |
| Some templates of the theme work, others not | Another extension registers a design of the same name |
| A new template file is ignored | The template override cache still has the old list; clear `template-override` |

## Related pages

- [The design cascade](../theme-design/the-design-cascade.md)
- [Design extensions](../theme-design/design-extensions.md)
- [Loading order and which theme wins](loading-order-for-themes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
