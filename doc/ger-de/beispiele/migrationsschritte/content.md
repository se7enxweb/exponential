---
title: content (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt ein Inhaltsobjekt an, ändert, sichert oder löscht es, mit seinen Feldern in einer oder mehreren Sprachen.
keywords: content, migration, YAML, step, expMigrationStepContent
type: generated
generated: exp:help:examples
---
# content

Legt ein Inhaltsobjekt an, ändert, sichert oder löscht es, mit seinen Feldern in einer oder mehreren Sprachen.

Ein Schritt einer YAML-Migration mit `type: content`. Die Klasse `expMigrationStepContent` (`kernel/classes/migration/steps/expmigrationstepcontent.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[content]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `class` | `identifier` (class) | create, ensure |  | die Klasse eines neuen Objekts: ihr Bezeichner, ihre ID oder remote:<Remote-ID> (create, ensure) |
| `parent` | `identifier` (node) | create, ensure |  | der Elternknoten eines neuen Objekts (create, ensure) |
| `object` | `identifier` (object) | update, delete |  | das Objekt, das geändert oder gelöscht wird (oder node:) |
| `node` | `identifier` (node) | update, delete |  | ein Knoten des Objekts, das geändert oder gelöscht wird (oder object:) |
| `remote_id` | `string` | create, ensure |  | die Remote-ID eines neuen Objekts; ensure findet das Objekt über sie |
| `language` | `string` | create, update, ensure |  | die Sprache von fields: (Standard: die Standardsprache für Inhalte, ContentObjectLocale) |
| `fields` | `map` | create, update, ensure |  | Attributbezeichner => Wert, in der Sprache von language:; jeder Wert geht durch fromString() des Datentyps |
| `translations` | `map` | create, update, ensure |  | Sprache => eine Zuordnung von Feldern: ein Block je weiterer Sprache, jeder als eigene Version veröffentlicht |
| `section` | `identifier` (section) | create, ensure |  | die Sektion eines neuen Objekts: ihr Bezeichner oder ihre ID (Standard: die des Elternknotens) |
| `always_available` | `bool` | create, ensure |  | ob ein neues Objekt in einer Sprache gezeigt wird, die es nicht hat (Standard: die Einstellung der Klasse) |
| `in_place` | `bool` | update, ensure | `false` | true: die veröffentlichte Version wird an Ort und Stelle geändert, ohne neue Version |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn das Objekt nicht da ist: fail (Standard, scheitern) oder skip (überspringen) |
| `trash` | `bool` | delete | `false` | true: das Objekt kommt in den Papierkorb; false (Standard): es wird endgültig entfernt |
| `workflows` | `bool` | alle | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |
| `notify` | `bool` | alle | `false` | true: das Veröffentlichen erzeugt ein Benachrichtigungsereignis (Standard: ohne Benachrichtigung veröffentlicht) |

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
| `path` | der URL-Pfad seines Hauptknotens (path_identification_string) |
| `created` | true, wenn der Schritt das Objekt angelegt hat |
| `changed` | true, wenn der Schritt es geändert hat (angelegt, geändert oder gelöscht) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`.

## Beispiel

```yaml
- type: content
  mode: ensure
  class: folder
  parent: 2
  remote_id: news-folder
  fields:
    name: News
  translations:
    ger-DE: { name: Nachrichten }
  references: { news: node_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcontent.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
