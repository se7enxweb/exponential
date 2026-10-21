---
title: Richtlinien des Moduls pdf
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls pdf (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, pdf, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls pdf

Die Richtlinienfunktionen von `pdf` und ihre Einschränkungen, aus `$FunctionList` in `kernel/pdf/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `pdf/*` erlaubt jede Funktion.

## create

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'pdf', 'create' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'pdf', 'create' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'pdf', 'function', 'create' ) )}
    ...
{/if}
```

## edit

Geprüft von den Ansichten: [edit](../modulansichten/pdf/edit.md), [list](../modulansichten/pdf/list.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'pdf', 'edit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'pdf', 'function', 'edit' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von pdf](../modulansichten/pdf/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/pdf/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
