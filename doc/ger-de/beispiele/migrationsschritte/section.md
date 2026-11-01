---
title: section (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Sektion an, ändert, sichert oder löscht sie, oder weist einem Teil des Inhaltsbaums eine Sektion zu.
keywords: section, migration, YAML, step, expMigrationStepSection
type: generated
generated: exp:help:examples
---
# section

Legt eine Sektion an, ändert, sichert oder löscht sie, oder weist einem Teil des Inhaltsbaums eine Sektion zu.

Ein Schritt einer YAML-Migration mit `type: section`. Die Klasse `expMigrationStepSection` (`kernel/classes/migration/steps/expmigrationstepsection.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[section]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`, `assign`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `section` | `identifier` (section) | update, delete, assign |  | die Sektion: ihr Bezeichner oder ihre ID |
| `identifier` | `string` | create, update, ensure |  | der Bezeichner der Sektion; ensure findet die Sektion über ihn (update: ein neuer Bezeichner) |
| `name` | `string` | create, update, ensure |  | der Name der Sektion |
| `navigation_part` | `string` | create, update, ensure |  | der Navigationsbereich der Administration, zu dem die Sektion gehört (Standard: ezcontentnavigationpart) |
| `node` | `identifier` (node) | assign |  | der Knoten, dessen Objekt (und die Objekte darunter, nach scope:) die Sektion bekommt |
| `object` | `identifier` (object) | assign |  | das Objekt, das die Sektion bekommt (oder node:), an seinem Hauptknoten |
| `scope` | `string`: `subtree`, `inherited`, `object` | assign | `"subtree"` | subtree (Standard): auch die Objekte darunter; inherited: die darunter, die dieselbe Sektion hatten; object: nur das Objekt |
| `missing` | `string`: `fail`, `skip` | update, delete, assign | `"fail"` | was geschieht, wenn die Sektion nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `section_id` | die ID der Sektion |
| `identifier` | ihr Bezeichner |
| `name` | sein Name |
| `created` | true, wenn der Schritt die Sektion angelegt hat |
| `changed` | true, wenn der Schritt sie geändert hat (angelegt, geändert, gelöscht oder zugewiesen) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `user_info_cache`.

## Beispiel

```yaml
- type: section
  mode: ensure
  identifier: news
  name: News
- type: section
  mode: assign
  section: news
  node: "path:news"
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepsection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
