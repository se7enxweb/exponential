---
title: user (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt ein Benutzerkonto mit seinen Gruppen, seinem Passwort und seinen Feldern an, ändert, sichert oder löscht es.
keywords: user, migration, YAML, step, expMigrationStepUser
type: generated
generated: exp:help:examples
---
# user

Legt ein Benutzerkonto mit seinen Gruppen, seinem Passwort und seinen Feldern an, ändert, sichert oder löscht es.

Ein Schritt einer YAML-Migration mit `type: user`. Die Klasse `expMigrationStepUser` (`kernel/classes/migration/steps/expmigrationstepuser.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[user]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `login` | `string` | create, ensure |  | das Login eines neuen Benutzers; ensure findet den Benutzer über es |
| `user` | `identifier` (user) | update, delete |  | der zu ändernde oder zu löschende Benutzer: Login, ID oder remote:<Remote-ID> |
| `email` | `string` | create, update, ensure |  | die E-Mail-Adresse des Kontos |
| `password` | `string` | create, update, ensure |  | das Passwort, gehasht, wie eZUser es hasht (ensure: nur für einen neuen Benutzer); nehmen Sie einen Platzhalter, nie das Passwort in der Datei |
| `password_hash` | `string` | create, update, ensure |  | ein Passwort-Hash, der so gespeichert wird, wie er ist (statt password:) |
| `password_hash_type` | `string` | create, update, ensure |  | der Typ von password_hash: (md5_password, md5_user, md5_site, plaintext, password_hash ...) |
| `secrets` | `map` | create, update, ensure |  | login, email oder password => der Name eines Geheimnisses (Setup > Geheimnisse oder env:<Variable>), aus dem der Wert gelesen wird, wenn der Schritt läuft, statt login:, email: oder password: |
| `enabled` | `bool` | create, update, ensure |  | ob sich das Konto anmelden kann (Standard für einen neuen Benutzer: true) |
| `groups` | `list` | create, update, ensure |  | die Knoten der Benutzergruppen, in denen der Benutzer ist (die erste ist sein Hauptort); update und ensure fügen ihn denen hinzu, in denen er nicht ist |
| `remove_groups` | `list` | update |  | die Knoten der Benutzergruppen, die der Benutzer verlässt (nicht die Hauptgruppe) |
| `class` | `identifier` (class) | create, ensure | `"user"` | die Klasse eines neuen Benutzers (Standard: user) |
| `remote_id` | `string` | create, ensure |  | die Remote-ID des Objekts eines neuen Benutzers |
| `language` | `string` | create, update, ensure |  | die Sprache von fields: (Standard: die Standardsprache der Inhalte) |
| `fields` | `map` | create, update, ensure |  | Attributbezeichner => Wert (first_name, last_name ...); das Konto setzen login:, email: und password: |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn der Benutzer nicht da ist: fail (Standard) oder skip |
| `workflows` | `bool` | create, update, ensure | `false` | true: die Workflows der Auslöser der Operation laufen, wie in der Administration (Standard: sie laufen nicht) |
| `notify` | `bool` | create, update, ensure | `false` | true: das Veröffentlichen erzeugt ein Benachrichtigungsereignis (Standard: ohne Benachrichtigung veröffentlicht) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `object_id` | die ID des Objekts des Benutzers (die Benutzer-ID) |
| `node_id` | die ID seines Hauptknotens |
| `remote_id` | seine Remote-ID |
| `login` | das Login |
| `email` | die E-Mail-Adresse |
| `created` | true, wenn der Schritt den Benutzer angelegt hat |
| `changed` | true, wenn der Schritt es geändert hat (angelegt, geändert oder gelöscht) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `user_info_cache`.

## Beispiel

```yaml
- type: user
  mode: ensure
  login: editor
  email: editor@example.com
  password: "${ref.editor_password}"
  groups: [ "remote:news-editors" ]
  fields: { first_name: News, last_name: Editor }
  references: { editor: object_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepuser.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
