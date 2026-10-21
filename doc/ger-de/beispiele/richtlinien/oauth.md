---
title: Richtlinien des Moduls oauth
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls oauth (1) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, oauth, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls oauth

Die Richtlinienfunktionen von `oauth` und ihre Einschränkungen, aus `$FunctionList` in `kernel/private/modules/oauth/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `oauth/*` erlaubt jede Funktion.

## authorize

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'oauth', 'authorize' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| SiteAccess | `eZSiteAccess::siteAccessList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'oauth', 'authorize', array( 'SiteAccess' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'oauth', 'function', 'authorize' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von oauth](../modulansichten/oauth/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauth/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
