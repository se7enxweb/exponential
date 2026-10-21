---
title: Richtlinien des Moduls notification
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls notification (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, notification, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls notification

Die Richtlinienfunktionen von `notification` und ihre Einschränkungen, aus `$FunctionList` in `kernel/notification/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `notification/*` erlaubt jede Funktion.

## administrate

Geprüft von den Ansichten: [job](../modulansichten/notification/job.md), [runfilter](../modulansichten/notification/runfilter.md), [status](../modulansichten/notification/status.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'notification', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'notification', 'function', 'administrate' ) )}
    ...
{/if}
```

## use

Geprüft von den Ansichten: [addtonotification](../modulansichten/notification/addtonotification.md), [settings](../modulansichten/notification/settings.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'notification', 'use' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'notification', 'function', 'use' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von notification](../modulansichten/notification/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
