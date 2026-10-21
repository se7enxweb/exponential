---
title: Richtlinien des Moduls state
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls state (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, state, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls state

Die Richtlinienfunktionen von `state` und ihre Einschränkungen, aus `$FunctionList` in `kernel/state/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `state/*` erlaubt jede Funktion.

Manche Funktionen nehmen außerdem eine Einschränkung pro Objektzustandsgruppe der Website, namens `StateGroup_<group identifier>` (`eZContentObjectStateGroup::limitations()`, aus der Datenbank gelesen und daher unten nicht aufgeführt).

## administrate

Geprüft von den Ansichten: [edit](../modulansichten/state/edit.md), [group](../modulansichten/state/group.md), [group_edit](../modulansichten/state/group_edit.md), [groups](../modulansichten/state/groups.md), [view](../modulansichten/state/view.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'state', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'state', 'function', 'administrate' ) )}
    ...
{/if}
```

## assign

Geprüft von den Ansichten: [assign](../modulansichten/state/assign.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fest: Self (1) |
| NewState | `eZContentObjectState::limitationList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'state', 'assign', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'state', 'function', 'assign' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von state](../modulansichten/state/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/state/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
