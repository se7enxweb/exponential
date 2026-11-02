---
title: Richtlinien des Moduls migration
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls migration (6) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, migration, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls migration

Die Richtlinienfunktionen von `migration` und ihre Einschränkungen, aus `$FunctionList` in `kernel/migration/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `migration/*` erlaubt jede Funktion.

## deploy

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'migration', 'deploy' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'migration', 'deploy' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'deploy' ) )}
    ...
{/if}
```

## edit

Geprüft von den Ansichten: [editor](../modulansichten/migration/editor.md), [recording](../modulansichten/migration/recording.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'migration', 'edit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'edit' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [deploy](../modulansichten/migration/deploy.md), [deployment](../modulansichten/migration/deployment.md), [dryrun](../modulansichten/migration/dryrun.md), [history](../modulansichten/migration/history.md), [log](../modulansichten/migration/log.md), [remotes](../modulansichten/migration/remotes.md), [status](../modulansichten/migration/status.md), [stream](../modulansichten/migration/stream.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'migration', 'read' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'read' ) )}
    ...
{/if}
```

## remotes

Geprüft von den Ansichten: [remote](../modulansichten/migration/remote.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'migration', 'remotes' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'remotes' ) )}
    ...
{/if}
```

## rollback

Geprüft von den Ansichten: [rollback](../modulansichten/migration/rollback.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'migration', 'rollback' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'rollback' ) )}
    ...
{/if}
```

## run

Geprüft von den Ansichten: [run](../modulansichten/migration/run.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'migration', 'run' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'run' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von migration](../modulansichten/migration/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
