---
title: Richtlinien des Moduls rss
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls rss (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, rss, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls rss

Die Richtlinienfunktionen von `rss` und ihre Einschränkungen, aus `$FunctionList` in `kernel/rss/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `rss/*` erlaubt jede Funktion.

## edit

Geprüft von den Ansichten: [edit_export](../modulansichten/rss/edit_export.md), [edit_import](../modulansichten/rss/edit_import.md), [list](../modulansichten/rss/list.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'rss', 'edit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'rss', 'function', 'edit' ) )}
    ...
{/if}
```

## feed

Geprüft von den Ansichten: [feed](../modulansichten/rss/feed.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'rss', 'feed' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'rss', 'function', 'feed' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von rss](../modulansichten/rss/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/rss/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
