---
title: Richtlinien des Moduls graphql
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls graphql (3) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, graphql, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls graphql

Die Richtlinienfunktionen von `graphql` und ihre Einschränkungen, aus `$FunctionList` in `kernel/graphql/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `graphql/*` erlaubt jede Funktion.

## administrate

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'graphql', 'administrate' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'graphql', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'graphql', 'function', 'administrate' ) )}
    ...
{/if}
```

## playground

Geprüft von den Ansichten: [playground](../modulansichten/graphql/playground.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'graphql', 'playground' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'graphql', 'function', 'playground' ) )}
    ...
{/if}
```

## query

Geprüft von den Ansichten: [query](../modulansichten/graphql/query.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'graphql', 'query' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'graphql', 'function', 'query' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von graphql](../modulansichten/graphql/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/graphql/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
