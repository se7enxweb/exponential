---
title: schema (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Tabelle, eine Spalte oder einen Index auf jeder Engine über die Schema-Handler des Kernels an oder entfernt sie.
keywords: schema, migration, YAML, step, expMigrationStepSchemaChange
type: generated
generated: exp:help:examples
---
# schema

Legt eine Tabelle, eine Spalte oder einen Index auf jeder Engine über die Schema-Handler des Kernels an oder entfernt sie.

Ein Schritt einer YAML-Migration mit `type: schema`. Die Klasse `expMigrationStepSchemaChange` (`kernel/classes/migration/steps/expmigrationstepschemachange.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[schema]` registriert.

## Modi

`mode:` ist einer von `create_table`, `drop_table`, `add_column`, `drop_column`, `add_index`, `drop_index`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `table` | `string` | alle | erforderlich | die Tabelle (eine Collection bei MongoDB) |
| `columns` | `any` | create_table, add_index, drop_index |  | create_table: Name => Definition (type, length, not_null, default); add_index, drop_index: die Spalten des Index |
| `primary_key` | `list` | create_table |  | die Spalten des Primärschlüssels |
| `indexes` | `map` | create_table |  | Name => { columns: [...], unique: bool } |
| `column` | `string` | add_column, drop_column | erforderlich | die Spalte |
| `definition` | `map` | add_column | erforderlich | die Spalte: type, length, not_null, default |
| `index` | `string` | add_index, drop_index | erforderlich | der Name des Index |
| `unique` | `bool` | add_index | `false` | ein eindeutiger Index |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `changed` | true, wenn der Schritt das Schema geändert hat, false, wenn er seine Arbeit schon erledigt fand |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: schema
  mode: add_column
  table: exp_news_rating
  column: created
  definition: { type: int, not_null: true, default: 0 }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepschemachange.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
