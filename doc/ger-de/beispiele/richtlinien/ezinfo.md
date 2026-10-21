---
title: Richtlinien des Moduls ezinfo
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls ezinfo (1) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, ezinfo, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls ezinfo

Die Richtlinienfunktionen von `ezinfo` und ihre Einschränkungen, aus `$FunctionList` in `kernel/ezinfo/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `ezinfo/*` erlaubt jede Funktion.

## read

Geprüft von den Ansichten: [about](../modulansichten/ezinfo/about.md), [copyright](../modulansichten/ezinfo/copyright.md), [is_alive](../modulansichten/ezinfo/is_alive.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezinfo', 'read' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezinfo', 'function', 'read' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von ezinfo](../modulansichten/ezinfo/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/ezinfo/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
