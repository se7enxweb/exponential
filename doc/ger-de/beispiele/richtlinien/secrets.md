---
title: Richtlinien des Moduls secrets
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls secrets (1) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, secrets, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls secrets

Die Richtlinienfunktionen von `secrets` und ihre Einschränkungen, aus `$FunctionList` in `kernel/secrets/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `secrets/*` erlaubt jede Funktion.

## manage

Geprüft von den Ansichten: [list](../modulansichten/secrets/list.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'secrets', 'manage' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'secrets', 'function', 'manage' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von secrets](../modulansichten/secrets/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/secrets/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
