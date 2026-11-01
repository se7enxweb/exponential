---
title: user_group (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Benutzergruppe mit ihrem Namen und ihrer Beschreibung in einer oder mehreren Sprachen an, ändert, sichert oder löscht sie.
keywords: user_group, migration, YAML, step, expMigrationStepUserGroup
type: generated
generated: exp:help:examples
---
# user_group

Legt eine Benutzergruppe mit ihrem Namen und ihrer Beschreibung in einer oder mehreren Sprachen an, ändert, sichert oder löscht sie.

Ein Schritt einer YAML-Migration mit `type: user_group`. Die Klasse `expMigrationStepUserGroup` (`kernel/classes/migration/steps/expmigrationstepusergroup.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[user_group]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `parent` | `identifier` (node) | create, ensure |  | der Knoten der Gruppe, in der die neue Gruppe ist (create, ensure) |
| `group` | `identifier` (object) | update, delete |  | die zu ändernde oder zu löschende Gruppe: ein Objektbezeichner (remote:<Remote-ID>, ID, path:) |
| `remote_id` | `string` | create, ensure |  | die Remote-ID einer neuen Gruppe; ensure findet die Gruppe über sie |
| `class` | `identifier` (class) | create, ensure | `"user_group"` | die Klasse einer neuen Gruppe (Standard: user_group) |
| `language` | `string` | create, update, ensure |  | die Sprache von name: und description: (Standard: die Standardsprache der Inhalte) |
| `name` | `string` | create, update, ensure |  | der Name der Gruppe |
| `description` | `string` | create, update, ensure |  | die Beschreibung der Gruppe |
| `names` | `map` | create, update, ensure |  | Sprache => der Name der Gruppe in ihr: ihre Übersetzungen |
| `fields` | `map` | create, update, ensure |  | weitere Attribute der Klasse: Attributbezeichner => Wert |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn die Gruppe nicht da ist: fail (Standard) oder skip |
| `workflows` | `bool` | create, update, ensure | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |
| `notify` | `bool` | create, update, ensure | `false` | true: das Veröffentlichen erzeugt ein Benachrichtigungsereignis (Standard: ohne Benachrichtigung veröffentlicht) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `object_id` | die ID des Objekts der Gruppe |
| `node_id` | die ID ihres Hauptknotens (der parent: der Benutzer und Gruppen in ihr) |
| `remote_id` | seine Remote-ID |
| `name` | sein Name |
| `created` | true, wenn der Schritt die Gruppe angelegt hat |
| `changed` | true, wenn der Schritt es geändert hat (angelegt, geändert oder gelöscht) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`.

## Beispiel

```yaml
- type: user_group
  mode: ensure
  parent: 5
  remote_id: news-editors
  name: News editors
  names: { ger-DE: Nachrichtenredaktion }
  references: { editors: node_id, editors_object: object_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepusergroup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
