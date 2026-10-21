---
title: Richtlinien des Moduls section
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls section (3) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, section, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls section

Die Richtlinienfunktionen von `section` und ihre Einschränkungen, aus `$FunctionList` in `kernel/section/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `section/*` erlaubt jede Funktion.

## assign

Geprüft von den Ansichten: [assign](../modulansichten/section/assign.md), [list](../modulansichten/section/list.md), [view](../modulansichten/section/view.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| NewSection | `eZSection::fetchList()` |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'section', 'assign', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'section', 'function', 'assign' ) )}
    ...
{/if}
```

## edit

Geprüft von den Ansichten: [edit](../modulansichten/section/edit.md), [list](../modulansichten/section/list.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'section', 'edit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'section', 'function', 'edit' ) )}
    ...
{/if}
```

## view

Geprüft von den Ansichten: [list](../modulansichten/section/list.md), [view](../modulansichten/section/view.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'section', 'view' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'section', 'function', 'view' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von section](../modulansichten/section/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
