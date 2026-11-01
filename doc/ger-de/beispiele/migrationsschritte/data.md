---
title: data (Migrationsschritt)
audience: developer, admin
category: examples
summary: Fügt Zeilen einer Tabelle auf jeder Engine ein, ändert oder löscht sie; die Werte setzt die Verbindung in Anführungszeichen.
keywords: data, migration, YAML, step, expMigrationStepData
type: generated
generated: exp:help:examples
---
# data

Fügt Zeilen einer Tabelle auf jeder Engine ein, ändert oder löscht sie; die Werte setzt die Verbindung in Anführungszeichen.

Ein Schritt einer YAML-Migration mit `type: data`. Die Klasse `expMigrationStepData` (`kernel/classes/migration/steps/expmigrationstepdata.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[data]` registriert.

## Modi

`mode:` ist einer von `insert`, `ensure`, `update`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `table` | `string` | alle | erforderlich | die Tabelle (eine Collection bei MongoDB) |
| `rows` | `list` | insert, ensure | erforderlich | die Zeilen, jede Spalte => Wert |
| `key` | `list` | ensure | erforderlich | die Spalten, die eine Zeile finden |
| `id_column` | `string` | insert, ensure |  | die auto_increment-Spalte; ihr Wert in der zuletzt eingefügten Zeile ist die id des Ergebnisses |
| `where` | `map` | update, delete |  | Spalte => Wert (eine Liste: einer davon; null: ist null) |
| `values` | `map` | update | erforderlich | Spalte => neuer Wert |
| `all` | `bool` | update, delete | `false` | true: jede Zeile, ohne where |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `count` | wie viele Zeilen eingefügt, geändert oder gelöscht wurden |
| `id` | insert, ensure: die id_column der zuletzt eingefügten Zeile (null, wenn keine eingefügt wurde) |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: data
  mode: ensure
  table: ezsite_data
  key: [ name ]
  rows:
    - { name: news_list_limit, value: "20" }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepdata.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
