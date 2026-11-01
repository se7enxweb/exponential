---
title: sql (Migrationsschritt)
audience: developer, admin
category: examples
summary: Führt Anweisungen der Engine aus, auf der die Installation läuft, im Schritt geschrieben oder in Dateien neben der Migration.
keywords: sql, migration, YAML, step, expMigrationStepSql
type: generated
generated: exp:help:examples
---
# sql

Führt Anweisungen der Engine aus, auf der die Installation läuft, im Schritt geschrieben oder in Dateien neben der Migration.

Ein Schritt einer YAML-Migration mit `type: sql`. Die Klasse `expMigrationStepSql` (`kernel/classes/migration/steps/expmigrationstepsql.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[sql]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `sql` | `string` |  | Anweisungen für jede SQL-Engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle) |
| `mysql` | `string` |  | Anweisungen für MySQL/MariaDB (gehen sql vor) |
| `postgresql` | `string` |  | Anweisungen für PostgreSQL (gehen sql vor) |
| `sqlite` | `string` |  | Anweisungen für SQLite (gehen sql vor) |
| `oracle` | `string` |  | Anweisungen für Oracle (gehen sql vor) |
| `mongodb` | `list` |  | Befehle für MongoDB: insert, update, delete, create_collection, drop_collection, create_index, drop_index |
| `file` | `string` |  | eine Datei neben der Migration; die eigene Datei der Engine (<name>.<engine>.sql, <name>.mongodb.json) geht <name>.sql vor |
| `part` | `string`: `up`, `down` | `"up"` | der Teil der Datei: up, oder down (nach einer Zeile "-- down") |
| `missing` | `string`: `fail`, `skip` | `"fail"` | was auf einer Engine ohne Anweisungen geschieht: fail oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `statements` | wie viele Anweisungen oder Befehle liefen |
| `engine` | die Engine, auf der sie liefen |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: sql
  sql: "UPDATE ezcontentclass SET always_available = 1 WHERE identifier = 'news';"
  mongodb:
    - { update: ezcontentclass, filter: { identifier: news }, set: { always_available: 1 } }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepsql.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
