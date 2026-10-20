---
title: Richtlinien des Moduls mailpreferences
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls mailpreferences (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, mailpreferences, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls mailpreferences

Die Richtlinienfunktionen von `mailpreferences` und ihre Einschränkungen, aus `$FunctionList` in `kernel/mailpreferences/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `mailpreferences/*` erlaubt jede Funktion.

## administrate

Geprüft von den Ansichten: [admin](../modulansichten/mailpreferences/admin.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'mailpreferences', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'mailpreferences', 'function', 'administrate' ) )}
    ...
{/if}
```

## export

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'mailpreferences', 'export' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'mailpreferences', 'export' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'mailpreferences', 'function', 'export' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von mailpreferences](../modulansichten/mailpreferences/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/mailpreferences/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
