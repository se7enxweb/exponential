---
title: "With RAD: a settings-only extension"
audience: developer, app-creator, designer
category: extension-development
order: 70
summary: The Settings extension wizard writes settings and event listeners: its topics, the listener class that is safe by default, and moving existing settings with exp:ini move-all instead.
keywords: RAD, settings extension wizard, setup/settingsextension, settings extension, event listeners, image aliases, viewcache, collect, triggers, siteaccess settings, extension roots, icon theme, exp:ini move-all
type: help
---
# With RAD: a settings-only extension

Tutorial 7. The **Settings extension** wizard (`/setup/settingsextension`) writes extensions that are mostly settings:
image aliases, event listeners, view cache rules, information collection, trigger operations, siteaccess settings,
extension roots, icon themes and more. The output below was produced by the wizard's generator
(`expSettingsExtensionWizard::files()`) with two event listeners chosen; every PHP file passed `php -l`.

## The topics

Each topic writes one settings file (the wizard names it):

| Topic | Writes to | Is for |
|---|---|---|
| Image aliases | `image.ini` | "A named size, with the filters that produce it." |
| Event listeners | `site.ini` | "A callback on something the kernel announces." |
| View cache clearing rules | `viewcache.ini` | "What else has to be rebuilt when one object is published." |
| Information collection | `collect.ini` | "What happens when a visitor fills in a form built out of content." |
| Trigger operations | `workflow.ini` | "Which operations a workflow may be bound to." |
| Siteaccess settings | `site.ini` | "Settings that apply to one siteaccess only, kept with the extension." |
| Additional extension roots | `site.ini` | "Somewhere other than extension/ for extensions to live." |
| Icon theme | `icon.ini` | "A set of icons an extension brings with it." |

Plus the listener class, API examples, README, metadata, Composer file, `.gitignore` and license.

## Event listeners

With the events `content/state/assign` and `response/output` and the class `mysiteSettingsListeners`, the wizard wrote:

```text
.gitignore
LICENSE
README.md
classes/mysitesettingslisteners.php
composer.json
doc/examples.php
extension.xml
ezinfo.php
settings/site.ini.append.php
```

```ini
[Event]
Listeners[]=content/state/assign@mysiteSettingsListeners::contentStateAssign
Listeners[]=response/output@mysiteSettingsListeners::responseOutput
```

The listener class knows which kind each event is and is safe until you write it:

```php
/**
 * response/output
 *
 * The whole page, about to be sent. Whatever is returned is what the
 * browser gets.
 *
 * A filter event. Whatever this returns is what the rest of the
 * system uses, so returning nothing destroys the value. The first
 * argument is the value; anything after it is context.
 */
public static function responseOutput( $value = null )
{
    // Not written yet. Handing the value back unchanged leaves the
    // system working exactly as it did before this listener existed.
    return $value;
}
```

The notify listener (`contentStateAssign()`) returns nothing, with the note "What this returns is ignored, and nothing
waits for it either - anything slow here is felt by whoever caused it." Fill them in as in
[Events, listeners and filters](../events-and-filters.md). Note that `content/state/assign` is notified with
`( $objectID, $selectedStateIDList )`; give your method both parameters.

## Moving existing settings instead

When the settings already exist in `settings/siteaccess/<sa>/` or `settings/override/`, the settings command builds the
extension from them, keeping every value in effect:

```bash
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --dry-run
```

See [Settings that live in an extension](../../part-2-builder/extension-settings-for-builders.md).

## Switch it on

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_settings global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
```

## Related pages

- Previous: [With RAD: a design extension](rad-design-extension.md)
- Next: [With RAD: handlers and kernel overrides](rad-handlers-and-kernel-overrides.md)
- [Settings in an extension](../settings-in-an-extension.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
