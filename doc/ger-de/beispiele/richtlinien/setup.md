---
title: Richtlinien des Moduls setup
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls setup (10) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, setup, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls setup

Die Richtlinienfunktionen von `setup` und ihre Einschränkungen, aus `$FunctionList` in `kernel/setup/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `setup/*` erlaubt jede Funktion.

## administrate

Geprüft von den Ansichten: [maintenance](../modulansichten/setup/maintenance.md), [menu](../modulansichten/setup/menu.md), [session](../modulansichten/setup/session.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'administrate' ) )}
    ...
{/if}
```

## install

Geprüft von den Ansichten: [init](../modulansichten/setup/init.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'install' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'install' ) )}
    ...
{/if}
```

## logs

Geprüft von den Ansichten: [logs](../modulansichten/setup/logs.md), [logsaction](../modulansichten/setup/logsaction.md), [logsdigest](../modulansichten/setup/logsdigest.md), [logsfeed](../modulansichten/setup/logsfeed.md), [logshealth](../modulansichten/setup/logshealth.md), [menu](../modulansichten/setup/menu.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'logs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'logs' ) )}
    ...
{/if}
```

## managecache

Geprüft von den Ansichten: [cache](../modulansichten/setup/cache.md), [cachetoolbar](../modulansichten/setup/cachetoolbar.md), [menu](../modulansichten/setup/menu.md), [staticcachestream](../modulansichten/setup/staticcachestream.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'managecache' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managecache' ) )}
    ...
{/if}
```

## managecronjobs

Geprüft von den Ansichten: [cronjobs](../modulansichten/setup/cronjobs.md), [cronjobsstream](../modulansichten/setup/cronjobsstream.md), [menu](../modulansichten/setup/menu.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'managecronjobs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managecronjobs' ) )}
    ...
{/if}
```

## managelogs

Geprüft von den Ansichten: [logsmanage](../modulansichten/setup/logsmanage.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'managelogs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managelogs' ) )}
    ...
{/if}
```

## preload

Geprüft von den Ansichten: [menu](../modulansichten/setup/menu.md), [preload](../modulansichten/setup/preload.md), [preloadjob](../modulansichten/setup/preloadjob.md), [preloadstream](../modulansichten/setup/preloadstream.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'preload' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'preload' ) )}
    ...
{/if}
```

## setup

Geprüft von den Ansichten: [contentextension](../modulansichten/setup/contentextension.md), [datatype](../modulansichten/setup/datatype.md), [designextension](../modulansichten/setup/designextension.md), [extensions](../modulansichten/setup/extensions.md), [handlerextension](../modulansichten/setup/handlerextension.md), [kerneloverride](../modulansichten/setup/kerneloverride.md), [menu](../modulansichten/setup/menu.md), [menuconfig](../modulansichten/setup/menuconfig.md), [moduleextension](../modulansichten/setup/moduleextension.md), [modulewizard](../modulansichten/setup/modulewizard.md), [rad](../modulansichten/setup/rad.md), [radsurvey](../modulansichten/setup/radsurvey.md), [settingsextension](../modulansichten/setup/settingsextension.md), [settingstoolbar](../modulansichten/setup/settingstoolbar.md), [systemupgrade](../modulansichten/setup/systemupgrade.md), [templatecreate](../modulansichten/setup/templatecreate.md), [templateedit](../modulansichten/setup/templateedit.md), [templatelist](../modulansichten/setup/templatelist.md), [templateoperator](../modulansichten/setup/templateoperator.md), [templateview](../modulansichten/setup/templateview.md), [toolbar](../modulansichten/setup/toolbar.md), [toolbarlist](../modulansichten/setup/toolbarlist.md), [workflowevent](../modulansichten/setup/workflowevent.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'setup' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'setup' ) )}
    ...
{/if}
```

## system_info

Geprüft von den Ansichten: [info](../modulansichten/setup/info.md), [languages](../modulansichten/setup/languages.md), [menu](../modulansichten/setup/menu.md), [sitedata](../modulansichten/setup/sitedata.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'system_info' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'system_info' ) )}
    ...
{/if}
```

## unmasklogs

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'setup', 'unmasklogs' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'unmasklogs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'unmasklogs' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von setup](../modulansichten/setup/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
