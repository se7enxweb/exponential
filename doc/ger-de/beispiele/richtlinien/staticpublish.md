---
title: Richtlinien des Moduls staticpublish
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls staticpublish (4) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, staticpublish, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls staticpublish

Die Richtlinienfunktionen von `staticpublish` und ihre Einschränkungen, aus `$FunctionList` in `kernel/staticpublish/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `staticpublish/*` erlaubt jede Funktion.

## build

Geprüft von den Ansichten: [build](../modulansichten/staticpublish/build.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Target | `expStaticAdmin::limitationList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'staticpublish', 'build', array( 'Target' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'staticpublish', 'function', 'build' ) )}
    ...
{/if}
```

## publish

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'staticpublish', 'publish' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Target | `expStaticAdmin::limitationList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'staticpublish', 'publish', array( 'Target' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'staticpublish', 'function', 'publish' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [dashboard](../modulansichten/staticpublish/dashboard.md), [history](../modulansichten/staticpublish/history.md), [log](../modulansichten/staticpublish/log.md), [preview](../modulansichten/staticpublish/preview.md), [serve](../modulansichten/staticpublish/serve.md), [stream](../modulansichten/staticpublish/stream.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Target | `expStaticAdmin::limitationList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'staticpublish', 'read', array( 'Target' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'staticpublish', 'function', 'read' ) )}
    ...
{/if}
```

## setup

Geprüft von den Ansichten: [keys](../modulansichten/staticpublish/keys.md), [target](../modulansichten/staticpublish/target.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Target | `expStaticAdmin::limitationList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'staticpublish', 'setup', array( 'Target' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'staticpublish', 'function', 'setup' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von staticpublish](../modulansichten/staticpublish/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/staticpublish/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
