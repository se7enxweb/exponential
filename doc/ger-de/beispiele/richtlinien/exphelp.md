---
title: Richtlinien des Moduls exphelp
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls exphelp (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, exphelp, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls exphelp

Die Richtlinienfunktionen von `exphelp` und ihre Einschränkungen, aus `$FunctionList` in `kernel/exphelp/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `exphelp/*` erlaubt jede Funktion.

## context

Geprüft von den Ansichten: [context](../modulansichten/exphelp/context.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'exphelp', 'context' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'exphelp', 'function', 'context' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [asset](../modulansichten/exphelp/asset.md), [audience](../modulansichten/exphelp/audience.md), [contents](../modulansichten/exphelp/contents.md), [dashboard](../modulansichten/exphelp/dashboard.md), [extensions](../modulansichten/exphelp/extensions.md), [manual](../modulansichten/exphelp/manual.md), [pdf](../modulansichten/exphelp/pdf.md), [report](../modulansichten/exphelp/report.md), [search](../modulansichten/exphelp/search.md), [topic](../modulansichten/exphelp/topic.md), [viewer](../modulansichten/exphelp/viewer.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'exphelp', 'read' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'exphelp', 'function', 'read' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von exphelp](../modulansichten/exphelp/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
