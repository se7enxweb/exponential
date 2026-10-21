---
title: Richtlinien des Moduls ezoe
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls ezoe (5) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, ezoe, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls ezoe

Die Richtlinienfunktionen von `ezoe` und ihre Einschränkungen, aus `$FunctionList` in `extension/ezoe/modules/ezoe/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `ezoe/*` erlaubt jede Funktion.

## browse

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'ezoe', 'browse' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezoe', 'browse' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezoe', 'function', 'browse' ) )}
    ...
{/if}
```

## disable_editor

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'ezoe', 'disable_editor' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezoe', 'disable_editor' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezoe', 'function', 'disable_editor' ) )}
    ...
{/if}
```

## editor

Geprüft von den Ansichten: [dialog](../modulansichten/ezoe/dialog.md), [embed_view](../modulansichten/ezoe/embed_view.md), [engine](../modulansichten/ezoe/engine.md), [load](../modulansichten/ezoe/load.md), [relations](../modulansichten/ezoe/relations.md), [spellcheck_rpc](../modulansichten/ezoe/spellcheck_rpc.md), [tags](../modulansichten/ezoe/tags.md), [upload](../modulansichten/ezoe/upload.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezoe', 'editor' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezoe', 'function', 'editor' ) )}
    ...
{/if}
```

## relations

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'ezoe', 'relations' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezoe', 'relations' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezoe', 'function', 'relations' ) )}
    ...
{/if}
```

## search

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'ezoe', 'search' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'ezoe', 'search' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezoe', 'function', 'search' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von ezoe](../modulansichten/ezoe/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/modules/ezoe/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
