---
title: role (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt eine Rolle mit ihren Richtlinien und Einschränkungen an, ändert, sichert oder löscht sie, oder weist sie Benutzern und Benutzergruppen zu oder nimmt sie ihnen.
keywords: Rolle, role, migration, YAML, step, expMigrationStepRole
type: generated
generated: exp:help:examples
---
# role

Legt eine Rolle mit ihren Richtlinien und Einschränkungen an, ändert, sichert oder löscht sie, oder weist sie Benutzern und Benutzergruppen zu oder nimmt sie ihnen.

Ein Schritt einer YAML-Migration mit `type: role`. Die Klasse `expMigrationStepRole` (`kernel/classes/migration/steps/expmigrationsteprole.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[role]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`, `assign`, `unassign`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `role` | `identifier` (role) | update, delete, assign, unassign |  | die Rolle: ihr Name oder ihre ID |
| `name` | `string` | create, update, ensure |  | der Name der Rolle; ensure findet die Rolle über ihn (update: ein neuer Name) |
| `policies` | `list` | create, ensure |  | die Richtlinien, jede eine Zuordnung: module, function (* für alle) und limitations (Bezeichner => Liste von Werten); ensure ergänzt die, die die Rolle nicht hat |
| `add_policies` | `list` | update |  | hinzuzufügende Richtlinien, wie in policies: |
| `remove_policies` | `list` | update |  | zu entfernende Richtlinien, jede eine Zuordnung: module und function (ohne function: jede Richtlinie des Moduls) |
| `users` | `list` | assign, unassign |  | die Benutzer: Logins, IDs oder remote:<Remote-ID> |
| `groups` | `list` | assign, unassign |  | die Benutzergruppen (oder andere Objekte): Objektbezeichner |
| `subtree` | `identifier` (node) | assign, unassign |  | die Zuweisung ist auf den Teilbaum dieses Knotens beschränkt |
| `section` | `identifier` (section) | assign, unassign |  | die Zuweisung ist auf diese Sektion beschränkt |
| `missing` | `string`: `fail`, `skip` | update, delete, assign, unassign | `"fail"` | was geschieht, wenn die Rolle nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `role_id` | die ID der Rolle |
| `name` | sein Name |
| `created` | true, wenn der Schritt die Rolle angelegt hat |
| `changed` | true, wenn der Schritt sie geändert hat (angelegt, geändert, gelöscht, zugewiesen oder entzogen) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `user_info_cache`, `content`.

## Beispiel

```yaml
- type: role
  mode: ensure
  name: News editor
  policies:
    - { module: content, function: read }
    - { module: content, function: edit, limitations: { Class: [ news_article ], Subtree: [ "path:news" ] } }
- type: role
  mode: assign
  role: News editor
  groups: [ "remote:news-editors" ]
  subtree: "path:news"
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteprole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
