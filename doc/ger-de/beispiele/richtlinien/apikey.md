---
title: Richtlinien des Moduls apikey
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls apikey (1) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, apikey, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls apikey

Die Richtlinienfunktionen von `apikey` und ihre Einschränkungen, aus `$FunctionList` in `kernel/apikey/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `apikey/*` erlaubt jede Funktion.

## create

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'apikey', 'create' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Scope | fest: Read content (read), Create and publish content (publish), Edit content (edit), Remove content (remove) |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'apikey', 'create', array( 'Scope' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'apikey', 'function', 'create' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von apikey](../modulansichten/apikey/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/apikey/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
