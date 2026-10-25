---
title: Richtlinien des Moduls datatable
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls datatable (4) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, datatable, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls datatable

Die Richtlinienfunktionen von `datatable` und ihre Einschränkungen, aus `$FunctionList` in `kernel/datatable/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `datatable/*` erlaubt jede Funktion.

## connections

Geprüft von den Ansichten: [connection](../modulansichten/datatable/connection.md), [connections](../modulansichten/datatable/connections.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'datatable', 'connections' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'datatable', 'function', 'connections' ) )}
    ...
{/if}
```

## manage

Geprüft von den Ansichten: [schema](../modulansichten/datatable/schema.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'datatable', 'manage' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'datatable', 'function', 'manage' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [dashboard](../modulansichten/datatable/dashboard.md), [rows](../modulansichten/datatable/rows.md), [table](../modulansichten/datatable/table.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'datatable', 'read' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'datatable', 'function', 'read' ) )}
    ...
{/if}
```

## sync

Geprüft von den Ansichten: [conflicts](../modulansichten/datatable/conflicts.md), [sync](../modulansichten/datatable/sync.md), [syncs](../modulansichten/datatable/syncs.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'datatable', 'sync' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'datatable', 'function', 'sync' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von datatable](../modulansichten/datatable/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
