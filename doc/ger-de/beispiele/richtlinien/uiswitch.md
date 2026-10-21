---
title: Richtlinien des Moduls uiswitch
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls uiswitch (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, uiswitch, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls uiswitch

Die Richtlinienfunktionen von `uiswitch` und ihre Einschränkungen, aus `$FunctionList` in `kernel/uiswitch/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `uiswitch/*` erlaubt jede Funktion.

## design

Geprüft von den Ansichten: [set](../modulansichten/uiswitch/set.md), [state](../modulansichten/uiswitch/state.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'uiswitch', 'design' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'uiswitch', 'function', 'design' ) )}
    ...
{/if}
```

## language

Geprüft von den Ansichten: [set](../modulansichten/uiswitch/set.md), [state](../modulansichten/uiswitch/state.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'uiswitch', 'language' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'uiswitch', 'function', 'language' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von uiswitch](../modulansichten/uiswitch/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/uiswitch/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
