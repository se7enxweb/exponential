---
title: search (Migrationsschritt)
audience: developer, admin
category: examples
summary: Indiziert Objekte in der Suchmaschine neu (die genannten oder die, die der Lauf geändert hat) oder alle Objekte.
keywords: search, migration, YAML, step, expMigrationStepSearch
type: generated
generated: exp:help:examples
---
# search

Indiziert Objekte in der Suchmaschine neu (die genannten oder die, die der Lauf geändert hat) oder alle Objekte.

Ein Schritt einer YAML-Migration mit `type: search`. Die Klasse `expMigrationStepSearch` (`kernel/classes/migration/steps/expmigrationstepsearch.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[search]` registriert.

## Modi

`mode:` ist einer von `reindex`, `full`; ohne es `reindex`.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `objects` | `list` | reindex |  | die Objekte (ID, remote:, path:); ohne: die Objekte, die der Lauf bis hierher angelegt oder geändert hat |
| `classes` | `list` | full |  | nur die Objekte dieser Klassen (Bezeichner) |
| `queue` | `bool` | alle | `true` | true: für den Cronjob indexcontent vorgemerkt; false: sofort indiziert |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `count` | wie viele Objekte vorgemerkt oder indiziert wurden |

## Zurücknehmen

Jeder Schritt dieses Typs nimmt sich selbst zurück, wenn seine Migration ohne `down:` zurückgenommen wird.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: search
  mode: reindex
  objects: [ "remote:home" ]
  queue: false
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepsearch.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
