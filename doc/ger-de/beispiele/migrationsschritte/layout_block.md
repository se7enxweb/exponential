---
title: layout_block (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt einen Block in einer Zone eines Layouts von Exponential Layouts mit seinen Parametern an, ändert, sichert oder löscht ihn.
keywords: layout_block, migration, YAML, step, expMigrationStepLayoutBlock
type: generated
generated: exp:help:examples
---
# layout_block

Legt einen Block in einer Zone eines Layouts von Exponential Layouts mit seinen Parametern an, ändert, sichert oder löscht ihn.

Ein Schritt einer YAML-Migration mit `type: layout_block`. Die Klasse `expMigrationStepLayoutBlock` (`kernel/classes/migration/steps/expmigrationsteplayoutblock.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[layout_block]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `layout` | `string` | alle | erforderlich | der Bezeichner des Layouts |
| `zone` | `string` | alle | erforderlich | der Bezeichner der Zone |
| `name` | `string` | alle | erforderlich | der Name des Blocks, über den der Schritt ihn in der Zone findet |
| `definition` | `string` | create, update, ensure |  | die Blockdefinition (explayouts.ini [BlockDefinition_<Bezeichner>]): title, text, list ... |
| `view_type` | `string` | create, update, ensure |  | wie der Block dargestellt wird (ein neuer Block: default) |
| `item_view_type` | `string` | create, update, ensure |  | wie der Block seine Einträge darstellt |
| `position` | `int` | create, update, ensure |  | die Stelle in der Zone, ab 0 (ein neuer Block: nach dem letzten) |
| `parameters` | `map` | create, update, ensure |  | Parameter => Wert; die übrigen bleiben |
| `publish` | `bool` | create, update, ensure, delete | `true` | true: der Entwurf des Layouts wird veröffentlicht; false: die Änderung bleibt im Entwurf |
| `missing` | `string`: `fail`, `skip` | alle | `"fail"` | was geschieht, wenn das Layout, die Zone, der Block oder Exponential Layouts nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `block_id` | die ID des Blocks (im veröffentlichten Layout oder bei publish: false im Entwurf) |
| `layout_id` | die ID des Layouts, in dem er ist |
| `created` | true, wenn der Schritt den Block angelegt hat |
| `changed` | true, wenn der Schritt etwas geändert hat |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `template-block`.

## Beispiel

```yaml
- type: layout_block
  mode: ensure
  layout: landing_page
  zone: main
  name: Welcome
  definition: title
  view_type: title
  parameters: { title: Welcome, tag: h1 }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteplayoutblock.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
