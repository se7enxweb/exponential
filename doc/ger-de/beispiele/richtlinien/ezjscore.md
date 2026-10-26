---
title: Richtlinien des Moduls ezjscore
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls ezjscore (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, ezjscore, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls ezjscore

Die Richtlinienfunktionen von `ezjscore` und ihre Einschränkungen, aus `$FunctionList` in `extension/ezjscore/modules/ezjscore/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `ezjscore/*` erlaubt jede Funktion.

## call

Geprüft von den Ansichten: [call](../modulansichten/ezjscore/call.md).

| Einschränkung | Werte stammen aus |
|---|---|
| FunctionList | fest: ezjsctemplate (ezjsctemplate), ezjscnode (ezjscnode), ezajaxuploader (ezajaxuploader) |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezjscore', 'call', array( 'FunctionList' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezjscore', 'function', 'call' ) )}
    ...
{/if}
```

## run

Geprüft von den Ansichten: [run](../modulansichten/ezjscore/run.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezjscore', 'run' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezjscore', 'function', 'run' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von ezjscore](../modulansichten/ezjscore/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/modules/ezjscore/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
