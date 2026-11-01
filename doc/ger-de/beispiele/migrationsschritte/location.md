---
title: location (Migrationsschritt)
audience: developer, admin
category: examples
summary: Fügt einen Ort (Knoten) eines Inhaltsobjekts hinzu, ändert, entfernt, verschiebt, kopiert, verbirgt oder zeigt ihn.
keywords: location, migration, YAML, step, expMigrationStepLocation
type: generated
generated: exp:help:examples
---
# location

Fügt einen Ort (Knoten) eines Inhaltsobjekts hinzu, ändert, entfernt, verschiebt, kopiert, verbirgt oder zeigt ihn.

Ein Schritt einer YAML-Migration mit `type: location`. Die Klasse `expMigrationStepLocation` (`kernel/classes/migration/steps/expmigrationsteplocation.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[location]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `delete`, `move`, `copy`, `hide`, `unhide`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `node` | `identifier` (node) | update, delete, move, copy, hide, unhide |  | der Knoten, auf den der Schritt wirkt |
| `object` | `identifier` (object) | create, delete |  | create: das Objekt, das einen neuen Ort bekommt; delete: mit parent: sein Ort unter diesem Elternknoten |
| `parent` | `identifier` (node) | create, delete, move, copy |  | der Elternknoten: des neuen Orts (create), des zu entfernenden Orts (delete mit object:), wohin der Knoten kommt (move, copy) |
| `priority` | `int` | create, update |  | die Priorität des Knotens unter seinen Geschwistern |
| `sort_field` | `string`: `path`, `published`, `modified`, `section`, `depth`, `class_identifier`, `class_name`, `priority`, `name` | create, update |  | wie die Kinder des Knotens sortiert werden |
| `sort_order` | `string`: `asc`, `desc` | create, update |  | die Richtung von sort_field |
| `main` | `bool` | create, update |  | true: der Knoten wird der Hauptort seines Objekts |
| `missing` | `string`: `fail`, `skip` | update, delete, move, hide, unhide | `"fail"` | was geschieht, wenn der Knoten nicht da ist: fail (Standard, scheitern) oder skip (überspringen) |
| `trash` | `bool` | delete | `false` | ist der Knoten der letzte Ort des Objekts, wird das Objekt mit ihm gelöscht: true legt es in den Papierkorb |
| `workflows` | `bool` | alle | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `node_id` | die ID des Knotens (des neuen bei create und copy) |
| `object_id` | die ID seines Objekts (des neuen bei copy) |
| `parent_node_id` | die ID seines Elternknotens |
| `remote_id` | die Remote-ID des Knotens |
| `path` | sein URL-Pfad (path_identification_string) |
| `hidden` | ob er verborgen ist |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `template-block`.

## Beispiel

```yaml
- type: location
  mode: create
  object: remote:news-welcome
  parent: path:frontpage
  priority: 10
  references: { welcome_on_front: node_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteplocation.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
