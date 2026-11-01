---
title: image (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt ein Bild aus dem Migrationsordner in ein Bildattribut eines Objekts, mit Alternativtext, Fokuspunkt und Zuschnitt.
keywords: image, migration, YAML, step, expMigrationStepImage
type: generated
generated: exp:help:examples
---
# image

Legt ein Bild aus dem Migrationsordner in ein Bildattribut eines Objekts, mit Alternativtext, Fokuspunkt und Zuschnitt.

Ein Schritt einer YAML-Migration mit `type: image`. Die Klasse `expMigrationStepImage` (`kernel/classes/migration/steps/expmigrationstepimage.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[image]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `object` | `identifier` (object) |  | das Objekt (oder node:) |
| `node` | `identifier` (node) |  | ein Knoten des Objekts (oder object:) |
| `field` | `string` | erforderlich | der Bezeichner des Attributs (ezimage) |
| `file` | `string` |  | das Bild: ein Pfad unterhalb des Ordners der Migrationsdatei (weggelassen: das Bild des Attributs bleibt) |
| `alt` | `string` |  | der Alternativtext des Bildes |
| `focal` | `any` |  | der Fokuspunkt: { x, y } als Anteile (0 bis 1) von Breite und Höhe; null entfernt ihn |
| `crop` | `any` |  | der Zuschnitt: { x, y, width, height } als Anteile des Originals; null entfernt ihn |
| `language` | `string` |  | die Übersetzung, die das Bild bekommt (Standard: die Standardsprache der Inhalte) |
| `in_place` | `bool` | `false` | true: die veröffentlichte Version wird an Ort und Stelle geändert, ohne neue Version |
| `missing` | `string`: `fail`, `skip` | `"fail"` | was geschieht, wenn das Objekt nicht da ist: fail (Standard, scheitern) oder skip (überspringen) |
| `workflows` | `bool` | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |
| `notify` | `bool` | `false` | true: das Veröffentlichen erzeugt ein Benachrichtigungsereignis (Standard: ohne Benachrichtigung veröffentlicht) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `object_id` | die ID des Objekts |
| `node_id` | die ID seines Hauptknotens |
| `remote_id` | seine Remote-ID |
| `version` | seine aktuelle Version |
| `name` | sein Name |
| `path` | der URL-Pfad seines Hauptknotens |
| `changed` | true, wenn der Schritt das Objekt geändert hat |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`.

## Beispiel

```yaml
- type: image
  object: remote:news-welcome
  field: image
  file: images/welcome.jpg
  alt: The team at the opening
  focal: { x: 0.3, y: 0.4 }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepimage.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
