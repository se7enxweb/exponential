---
title: Richtlinien des Moduls audit
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls audit (2) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, audit, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls audit

Die Richtlinienfunktionen von `audit` und ihre Einschränkungen, aus `$FunctionList` in `kernel/audit/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `audit/*` erlaubt jede Funktion.

## manage

Geprüft von den Ansichten: [archives](../modulansichten/audit/archives.md), [settings](../modulansichten/audit/settings.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'audit', 'manage' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'audit', 'function', 'manage' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [alerts](../modulansichten/audit/alerts.md), [charts](../modulansichten/audit/charts.md), [console](../modulansichten/audit/console.md), [dashboard](../modulansichten/audit/dashboard.md), [event](../modulansichten/audit/event.md), [export](../modulansichten/audit/export.md), [recent](../modulansichten/audit/recent.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Channel | fest: Content (content), Access (access), System (system), Commerce (commerce), Read (read) |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'audit', 'read', array( 'Channel' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'audit', 'function', 'read' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von audit](../modulansichten/audit/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
