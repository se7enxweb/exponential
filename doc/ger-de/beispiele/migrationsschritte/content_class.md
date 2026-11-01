---
title: content_class (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Inhaltsklasse mit ihren Attributen, deren Datentyp-Einstellungen und den Übersetzungen ihrer Namen an, ändert, sichert oder löscht sie.
keywords: content_class, migration, YAML, step, expMigrationStepContentClass
type: generated
generated: exp:help:examples
---
# content_class

Legt eine Inhaltsklasse mit ihren Attributen, deren Datentyp-Einstellungen und den Übersetzungen ihrer Namen an, ändert, sichert oder löscht sie.

Ein Schritt einer YAML-Migration mit `type: content_class`. Die Klasse `expMigrationStepContentClass` (`kernel/classes/migration/steps/expmigrationstepcontentclass.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[content_class]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `class` | `identifier` (class) | update, delete |  | die zu ändernde oder zu löschende Klasse: ihr Bezeichner, ihre ID oder remote:<Remote-ID> |
| `identifier` | `string` | create, update, ensure |  | der Bezeichner der Klasse; ensure findet die Klasse über ihn (update: ein neuer Bezeichner) |
| `remote_id` | `string` | create, ensure |  | die Remote-ID einer neuen Klasse (Standard: eine neue) |
| `language` | `string` | create, update, ensure |  | die Sprache von name: (Standard: die Standardsprache der Inhalte, ContentObjectLocale) |
| `name` | `string` | create, update, ensure |  | der Name der Klasse in der Sprache von language: |
| `names` | `map` | create, update, ensure |  | Sprache => der Name der Klasse in ihr: ihre Übersetzungen |
| `description` | `string` | create, update, ensure |  | die Beschreibung der Klasse in der Sprache von language: |
| `descriptions` | `map` | create, update, ensure |  | Sprache => die Beschreibung der Klasse in ihr |
| `name_pattern` | `string` | create, update, ensure |  | wie ein Objekt aus seinen Attributen benannt wird (<title>; Standard: das erste Attribut) |
| `url_alias_pattern` | `string` | create, update, ensure |  | wie der URL-Alias eines Objekts gebildet wird (Standard: wie sein Name) |
| `container` | `bool` | create, update, ensure |  | ob Objekte der Klasse Kinder haben können |
| `always_available` | `bool` | create, update, ensure |  | ob ein neues Objekt der Klasse in einer Sprache gezeigt wird, die es nicht hat |
| `sort_field` | `string`: `path`, `published`, `modified`, `section`, `depth`, `class_identifier`, `class_name`, `priority`, `name` | create, update, ensure |  | wie die Kinder eines neuen Objekts sortiert werden |
| `sort_order` | `string`: `asc`, `desc` | create, update, ensure |  | asc oder desc |
| `groups` | `list` | create, update, ensure |  | die Klassengruppen, in denen die Klasse ist: Namen oder IDs (create, ensure: Standard Content; update und ensure fügen die Klasse denen hinzu, in denen sie nicht ist) |
| `attributes` | `list` | create, ensure |  | die Attribute, jedes eine Zuordnung: identifier, type (der Datentyp), name, names, description, descriptions, required, searchable, translatable, information_collector, category, placement, settings; ensure ergänzt die fehlenden und ändert, was abweicht |
| `add_attributes` | `list` | update |  | hinzuzufügende Attribute, wie in attributes:; jedes Objekt der Klasse bekommt sie |
| `update_attributes` | `map` | update |  | Attributbezeichner => was sich ändert (die Schlüssel von attributes: außer identifier und type) |
| `remove_attributes` | `list` | update |  | die Bezeichner der zu entfernenden Attribute, mit ihren Daten in jedem Objekt |
| `with_objects` | `bool` | delete | `false` | true: eine Klasse mit Objekten wird mit ihnen gelöscht; false (Standard): sie wird nicht gelöscht |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn die Klasse nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `class_id` | die ID der Klasse |
| `identifier` | ihr Bezeichner |
| `remote_id` | seine Remote-ID |
| `name` | sein Name |
| `created` | true, wenn der Schritt die Klasse angelegt hat |
| `changed` | true, wenn der Schritt es geändert hat (angelegt, geändert oder gelöscht) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `classid`, `content`, `sortkey`.

## Beispiel

```yaml
- type: content_class
  mode: ensure
  identifier: news_article
  name: News article
  names: { ger-DE: Nachrichtenartikel }
  name_pattern: "<title>"
  groups: [ Content ]
  attributes:
    - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, searchable: true, settings: { data_int1: 255 } }
    - { identifier: body, type: ezxmltext, name: Body }
  references: { news_class: class_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcontentclass.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
