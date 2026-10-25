---
title: Richtlinien des Moduls expimageeditor
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls expimageeditor (1) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, expimageeditor, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls expimageeditor

Die Richtlinienfunktionen von `expimageeditor` und ihre Einschränkungen, aus `$FunctionList` in `extension/expimageeditor/modules/expimageeditor/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `expimageeditor/*` erlaubt jede Funktion.

## edit

Geprüft von den Ansichten: [save](../modulansichten/expimageeditor/save.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'expimageeditor', 'edit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'expimageeditor', 'function', 'edit' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von expimageeditor](../modulansichten/expimageeditor/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expimageeditor/modules/expimageeditor/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
