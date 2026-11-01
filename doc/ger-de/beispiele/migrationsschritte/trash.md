---
title: trash (Migrationsschritt)
audience: developer, admin
category: examples
summary: Verschiebt Inhalte in den Papierkorb, stellt sie aus ihm wieder her oder löscht sie endgültig.
keywords: trash, migration, YAML, step, expMigrationStepTrash
type: generated
generated: exp:help:examples
---
# trash

Verschiebt Inhalte in den Papierkorb, stellt sie aus ihm wieder her oder löscht sie endgültig.

Ein Schritt einer YAML-Migration mit `type: trash`. Die Klasse `expMigrationStepTrash` (`kernel/classes/migration/steps/expmigrationsteptrash.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[trash]` registriert.

## Modi

`mode:` ist einer von `move`, `restore`, `purge`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `object` | `identifier` (object) | alle |  | das Objekt: move legt es in den Papierkorb; restore und purge nehmen es von dort (seine ID oder remote:<Remote-ID>) |
| `node` | `identifier` (node) | move |  | ein Knoten des Objekts, das in den Papierkorb kommt (oder object:) |
| `parent` | `identifier` (node) | restore |  | der Knoten, unter den ein wiederhergestelltes Objekt kommt (Standard: sein früherer übergeordneter Knoten, wenn es ihn noch gibt) |
| `all` | `bool` | purge | `false` | true: der ganze Papierkorb wird geleert (statt object:) |
| `missing` | `string`: `fail`, `skip` | alle | `"fail"` | was geschieht, wenn das Objekt fehlt (move) oder nicht im Papierkorb liegt (restore, purge): fail (Standard) oder skip |
| `workflows` | `bool` | move, restore | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `object_id` | die ID des Objekts |
| `remote_id` | seine Remote-ID |
| `name` | sein Name |
| `node_id` | sein Hauptknoten nach einer Wiederherstellung (sonst null) |
| `purged` | wie viele Objekte ein purge entfernt hat |
| `changed` | true, wenn der Schritt etwas geändert hat |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`.

## Beispiel

```yaml
- type: trash
  mode: move
  node: "path:news/old-article"
- type: trash
  mode: restore
  object: remote:old-article
  parent: "path:archive"
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteptrash.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
