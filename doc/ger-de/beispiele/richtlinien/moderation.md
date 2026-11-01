---
title: Richtlinien des Moduls moderation
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls moderation (3) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, moderation, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls moderation

Die Richtlinienfunktionen von `moderation` und ihre Einschränkungen, aus `$FunctionList` in `kernel/moderation/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `moderation/*` erlaubt jede Funktion.

## act

Geprüft von den Ansichten: [action](../modulansichten/moderation/action.md), [report](../modulansichten/moderation/report.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'moderation', 'act' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'moderation', 'function', 'act' ) )}
    ...
{/if}
```

## personaldata

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'moderation', 'personaldata' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'moderation', 'personaldata' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'moderation', 'function', 'personaldata' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [recent](../modulansichten/moderation/recent.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'moderation', 'read' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'moderation', 'function', 'read' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von moderation](../modulansichten/moderation/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/moderation/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
