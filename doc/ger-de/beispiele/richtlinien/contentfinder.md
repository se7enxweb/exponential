---
title: Richtlinien des Moduls contentfinder
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls contentfinder (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, contentfinder, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls contentfinder

Die Richtlinienfunktionen von `contentfinder` und ihre Einschränkungen, aus `$FunctionList` in `kernel/contentfinder/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `contentfinder/*` erlaubt jede Funktion.

## delete

Geprüft von den Ansichten: [action](../modulansichten/contentfinder/action.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentfinder', 'delete' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentfinder', 'function', 'delete' ) )}
    ...
{/if}
```

## search

Geprüft von den Ansichten: [action](../modulansichten/contentfinder/action.md), [search](../modulansichten/contentfinder/search.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentfinder', 'search' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentfinder', 'function', 'search' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von contentfinder](../modulansichten/contentfinder/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentfinder/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
