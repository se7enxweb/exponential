---
title: Richtlinien des Moduls infocollector
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls infocollector (1) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, infocollector, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls infocollector

Die Richtlinienfunktionen von `infocollector` und ihre Einschränkungen, aus `$FunctionList` in `kernel/infocollector/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `infocollector/*` erlaubt jede Funktion.

## read

Geprüft von den Ansichten: [collectionlist](../modulansichten/infocollector/collectionlist.md), [export](../modulansichten/infocollector/export.md), [overview](../modulansichten/infocollector/overview.md), [view](../modulansichten/infocollector/view.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'infocollector', 'read' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'infocollector', 'function', 'read' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von infocollector](../modulansichten/infocollector/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
