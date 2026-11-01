---
title: object_state_group (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Objektzustandsgruppe mit ihren Zuständen und den Übersetzungen ihrer Namen an, ändert, sichert oder löscht sie.
keywords: object_state_group, migration, YAML, step, expMigrationStepObjectStateGroup
type: generated
generated: exp:help:examples
---
# object_state_group

Legt eine Objektzustandsgruppe mit ihren Zuständen und den Übersetzungen ihrer Namen an, ändert, sichert oder löscht sie.

Ein Schritt einer YAML-Migration mit `type: object_state_group`. Die Klasse `expMigrationStepObjectStateGroup` (`kernel/classes/migration/steps/expmigrationstepobjectstategroup.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[object_state_group]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `group` | `identifier` (state_group) | update, delete |  | die zu ändernde oder zu löschende Gruppe: ihr Bezeichner oder ihre ID |
| `identifier` | `string` | create, update, ensure |  | der Bezeichner der Gruppe; ensure findet die Gruppe über ihn (update: ein neuer Bezeichner) |
| `language` | `string` | create, update, ensure |  | die Sprache von name: (Standard: die Standardsprache der Inhalte, ContentObjectLocale) |
| `name` | `string` | create, update, ensure |  | der Name der Gruppe in der Sprache von language: |
| `names` | `map` | create, update, ensure |  | Sprache => der Name der Gruppe in ihr: ihre Übersetzungen |
| `description` | `string` | create, update, ensure |  | die Beschreibung der Gruppe in der Sprache von language: |
| `descriptions` | `map` | create, update, ensure |  | Sprache => die Beschreibung der Gruppe in ihr |
| `states` | `list` | create, ensure |  | die Zustände, jeder eine Zuordnung: identifier, name, names, description, descriptions; ensure ergänzt die fehlenden und ändert, was abweicht |
| `add_states` | `list` | update |  | hinzuzufügende Zustände, wie in states: |
| `update_states` | `map` | update |  | Zustandsbezeichner => was sich ändert (name, names, description, descriptions, identifier für einen neuen) |
| `remove_states` | `list` | update |  | die Bezeichner der zu entfernenden Zustände; ihre Objekte bekommen den ersten verbleibenden Zustand |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn die Gruppe nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `group_id` | die ID der Gruppe |
| `identifier` | ihr Bezeichner |
| `state_ids` | Zustandsbezeichner => ID, für jeden Zustand der Gruppe |
| `created` | true, wenn der Schritt die Gruppe angelegt hat |
| `changed` | true, wenn der Schritt es geändert hat (angelegt, geändert oder gelöscht) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `state_limitations`, `user_info_cache`, `content`.

## Beispiel

```yaml
- type: object_state_group
  mode: ensure
  identifier: review
  name: Review
  names: { ger-DE: Prüfung }
  states:
    - { identifier: draft, name: Draft, names: { ger-DE: Entwurf } }
    - { identifier: approved, name: Approved }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepobjectstategroup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
