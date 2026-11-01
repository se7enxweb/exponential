---
title: content_class_group (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Gruppe von Inhaltsklassen an, benennt sie um, sichert oder löscht sie.
keywords: content_class_group, migration, YAML, step, expMigrationStepContentClassGroup
type: generated
generated: exp:help:examples
---
# content_class_group

Legt eine Gruppe von Inhaltsklassen an, benennt sie um, sichert oder löscht sie.

Ein Schritt einer YAML-Migration mit `type: content_class_group`. Die Klasse `expMigrationStepContentClassGroup` (`kernel/classes/migration/steps/expmigrationstepcontentclassgroup.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[content_class_group]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `group` | `identifier` (class_group) | update, delete |  | die umzubenennende oder zu löschende Gruppe: ihr Name oder ihre ID |
| `name` | `string` | create, update, ensure |  | der Name der Gruppe (update: ihr neuer Name) |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn die Gruppe nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `group_id` | die ID der Gruppe |
| `name` | sein Name |
| `created` | true, wenn der Schritt die Gruppe angelegt hat |
| `changed` | true, wenn der Schritt sie geändert hat (angelegt, umbenannt oder gelöscht) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `classid`.

## Beispiel

```yaml
- type: content_class_group
  mode: ensure
  name: News
  references: { news_group: group_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcontentclassgroup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
