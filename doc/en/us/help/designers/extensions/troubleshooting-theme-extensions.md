---
title: "Troubleshooting theme extensions"
audience: designer, admin
category: theme-extensions
order: 460
summary: Symptoms and fixes for theme extensions - the design is not used, a new template is ignored, the About page shows no version, Composer installs into the wrong folder, an operator is unknown, an export is refused.
keywords: troubleshooting, design not used, template ignored, no version, wrong folder, unknown operator, export refused, cache, autoload
type: help
---
# Troubleshooting theme extensions

This page is for designers and administrators when a theme does not behave. Find the symptom, read the cause, apply
the fix. For problems inside templates, see [Debugging a design](../theme-design/debugging-a-design.md) and
[Common template mistakes](../templates/common-template-mistakes.md).

| You see | Likely cause | Fix |
|---|---|---|
| The site shows the standard design | The extension is not active, or does not register its design | [Activating a theme](activating-a-theme-extension.md), [Registering the design](registering-the-design.md); clear the INI cache |
| The administration suddenly uses the theme | The theme sets `SiteDesign` globally | Move `SiteDesign` to the site's siteaccess ([The design settings a theme ships](design-settings-a-theme-ships.md)) |
| A new `.tpl` file is ignored; the page is empty | The template override cache still has the old list | `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` |
| Another theme's template wins | Loading order | [Loading order and which theme wins](loading-order-for-themes.md) |
| The About page shows a dash for the version | `ezinfo.php` or `extension.xml` lacks it, or `extension.xml` has another value | [Keeping every version field in sync](keeping-version-fields-in-sync.md) |
| Composer puts the theme into `extension/my-theme` | `extra.ezpublish-legacy-extension-name` is missing | [The Composer installer and the folder name](composer-installer-and-extension-name.md) |
| "Unknown template operator" for the theme's operator | `autoloads/eztemplateautoload.php` or `ExtensionAutoloadPath[]` is missing | [Template operators in a theme](template-operators-in-a-theme.md) |
| A new PHP class is not found | Autoload array not regenerated, or the PHP service not reloaded | `php bin/php/ezpgenerateautoloads.php -e`, then reload PHP-FPM or Velocity |
| Theme texts stay in English | `TranslationExtensions[]` missing, or the translation cache | [Translations in a theme](translations-in-a-theme.md) |
| A changed stylesheet does not reach the browser | The packed stylesheet is cached | Clear the packer and template-block caches ([Packer cache and deploy](../theme-design/packer-cache-and-deploy.md)); hard-reload the browser |
| Exporting the theme is refused | Exporting is off, or the secret check found something | [Exporting an extension as a package](extension-export-package.md), [The secret check](package-secret-check.md) |
| Upload of your package: "already exists" | A package of that name is on that installation | Remove the old one there, or rename yours |

## A general routine

1. Clear the caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.
2. Regenerate the autoloads: `php bin/php/ezpgenerateautoloads.php -e`.
3. Reload the PHP service that serves the site.
4. Turn on template debugging for your own IP address and read the path comments.
5. Check `var/log/error.log` and the site's debug output.

If the problem remains and you believe it is a bug in Exponential, report it
([When something goes wrong](../../when-something-goes-wrong.md)).

## Related pages

- [Checklist: the theme extension is ready](checklist-theme-extension-ready.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
