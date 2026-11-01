---
title: layout_rule (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Regel von Exponential Layouts an, die Seiten einem Layout zuordnet, ändert, sichert oder löscht sie.
keywords: layout_rule, migration, YAML, step, expMigrationStepLayoutRule
type: generated
generated: exp:help:examples
---
# layout_rule

Legt eine Regel von Exponential Layouts an, die Seiten einem Layout zuordnet, ändert, sichert oder löscht sie.

Ein Schritt einer YAML-Migration mit `type: layout_rule`. Die Klasse `expMigrationStepLayoutRule` (`kernel/classes/migration/steps/expmigrationsteplayoutrule.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[layout_rule]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `layout` | `string` | alle | erforderlich | der Bezeichner des veröffentlichten Layouts |
| `targets` | `list` | alle |  | wo die Regel gilt: eine Liste von { type, value } (path, path_prefix, path_info_prefix, path_regex, node, subtree, route, module, module_view); die Regel wird über sie gefunden |
| `conditions` | `list` | create, update, ensure |  | wann sie gilt: eine Liste von { type, value } (siteaccess, class, query_parameter, route_parameter, time ...) |
| `priority` | `int` | create, update, ensure |  | je höher, desto früher wird sie versucht (eine neue Regel: über allen anderen) |
| `enabled` | `bool` | create, update, ensure |  | ob die Regel verwendet wird (eine neue Regel: true) |
| `which` | `string`: `all` | delete |  | all: jede Regel des Layouts, was immer ihre Ziele sind |
| `missing` | `string`: `fail`, `skip` | alle | `"fail"` | was geschieht, wenn das Layout, die Regel oder Exponential Layouts nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `rule_id` | die ID der Regel (der ersten bei which: all) |
| `layout_id` | die ID des Layouts |
| `created` | true, wenn der Schritt die Regel angelegt hat |
| `changed` | true, wenn der Schritt etwas geändert hat |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `template-block`.

## Beispiel

```yaml
- type: layout_rule
  mode: ensure
  layout: landing_page
  targets:
    - { type: node, value: "remote:landing" }
  conditions:
    - { type: siteaccess, value: site }
  priority: 50
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteplayoutrule.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
