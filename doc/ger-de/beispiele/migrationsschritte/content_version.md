---
title: content_version (Migrationsschritt)
audience: developer, admin
category: examples
summary: Stellt eine frühere Version eines Inhaltsobjekts wieder her oder entfernt seine alten Versionen.
keywords: content_version, migration, YAML, step, expMigrationStepContentVersion
type: generated
generated: exp:help:examples
---
# content_version

Stellt eine frühere Version eines Inhaltsobjekts wieder her oder entfernt seine alten Versionen.

Ein Schritt einer YAML-Migration mit `type: content_version`. Die Klasse `expMigrationStepContentVersion` (`kernel/classes/migration/steps/expmigrationstepcontentversion.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[content_version]` registriert.

## Modi

`mode:` ist einer von `restore`, `cleanup`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `object` | `identifier` (object) | alle |  | das Objekt (oder node:) |
| `node` | `identifier` (node) | alle |  | ein Knoten des Objekts (oder object:) |
| `version` | `int` | restore |  | die Nummer der Version, die wiederhergestellt wird (restore) |
| `language` | `string` | restore |  | nur diese Sprache der Version wiederherstellen (Standard: jede Sprache, die sie hat) |
| `keep` | `int` | cleanup | `0` | wie viele archivierte Versionen bleiben, die neuesten (cleanup) |
| `drafts` | `bool` | cleanup | `false` | true: auch die Entwürfe des Objekts werden entfernt (cleanup) |
| `workflows` | `bool` | restore | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |
| `notify` | `bool` | restore | `false` | true: das Veröffentlichen erzeugt ein Benachrichtigungsereignis (Standard: ohne Benachrichtigung veröffentlicht) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `object_id` | die ID des Objekts |
| `version` | seine aktuelle Version nach dem Schritt |
| `removed` | wie viele Versionen cleanup entfernt hat |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`.

## Beispiel

```yaml
- type: content_version
  mode: cleanup
  object: remote:news-welcome
  keep: 5
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcontentversion.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
