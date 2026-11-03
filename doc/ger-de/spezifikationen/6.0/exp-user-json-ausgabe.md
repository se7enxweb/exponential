# JSON-Ausgabe und Exit-Codes von exp:user

Die Referenz der maschinenlesbaren Ausgabe der Befehle `exp:user`: das Schema `exp-user/1`, die Schlüssel jedes
Berichts, die Schlüssel jedes Befehls und die Exit-Codes. Lesen Sie sie, wenn ein Skript, ein CI-Job oder eine
Überwachung die Ausgabe von `--format=json` liest. Für die Aufgaben, denen die Befehle dienen, lesen Sie
[Benutzerverwaltung von der Konsole](../../hilfe/benutzerverwaltung-von-der-konsole/README.md).

## Der Vertrag

- Jeder Befehl nimmt `--format=json` (Vorgabe `text`) und gibt dann genau ein JSON-Objekt auf der Standardausgabe aus,
  eingerückt, mit unmaskierten Schrägstrichen und Unicode.
- Das Schema ist `exp-user/1`. Schlüssel und Optionen sind ein **dauerhafter Vertrag**: neue Schlüssel und Optionen
  können hinzukommen, keiner wird je umbenannt, entfernt oder bekommt eine andere Bedeutung. Ein Leser muss Schlüssel
  übergehen, die er nicht kennt.
- Eine unverträgliche Änderung bräuchte ein neues Schema (`exp-user/2`); keines ist geplant.
- Kein Bericht enthält je einen Passwort-Hash. Ein Passwort erscheint nur als `generated_password`, in dem einen Lauf,
  der es erzeugt hat.
- `tests/tests/kernel/classes/user/expUserCommandContractTest.php` vergleicht die Befehlsnamen, Optionen und
  Argumente mit einem Sicherungspunkt.

## Exit-Codes

| Code | Name | Bedeutung |
|---|---|---|
| 0 | erledigt | der Befehl hat seine Arbeit getan; ein Probelauf, den nichts verweigert hat |
| 1 | fehlgeschlagen | das Konto, die Gruppe oder die Rolle wurde nicht gefunden, eine Prüfung schlug fehl, das Konto ist geschützt, eine Aktion wurde verweigert; `exp:user:check-login`: das Passwort meldet das Konto nicht an |
| 2 | Bedienfehler | eine falsche Option oder ein falscher Wert, `--apply` zusammen mit `--dry-run`, ein fehlendes Argument |
| 3 | nicht erreichbar | die Datenbank ist nicht erreichbar |
| 4 | intern | ein interner Fehler |

Die Codes sind die von `exp:upgrade`. Ein Bedienfehler (2) wird als Zeile `FAIL:` auf der Standardfehlerausgabe
ausgegeben, auch mit `--format=json`; die anderen Codes kommen mit einem JSON-Bericht.

## Schlüssel jedes Berichts

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `schema` | string | `exp-user/1` |
| `command` | string | der Befehl, zum Beispiel `exp:user:disable` |
| `mode` | string | worum es im Bericht geht: die Aktion des Befehls (unten), oder `find` (das Konto wurde nicht gefunden), `actor` (der handelnde Benutzer wurde nicht gefunden), `unreachable` (die Datenbank) |
| `errors` | string[] | warum der Befehl fehlschlug oder verweigerte; leer, wenn nicht |
| `warnings` | string[] | was den Befehl nicht aufhielt, zum Beispiel „the account is disabled already: nothing to do“ |
| `exit` | int | der Exit-Code |

## Schlüssel der ändernden Befehle

Die Befehle, die etwas ändern (`create`, `edit`, `delete`, `disable`, `enable`, `reset-password`, `approve`, und die
Befehle der Phase 2 unten, die `--apply` nehmen), fügen hinzu:

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `dry_run` | bool | true ohne `--apply` |
| `applied` | bool | true, wenn die Änderung vorgenommen wurde |
| `changes` | string[] | was geändert wird (oder würde), je eine Zeile |
| `user` | object oder null | das Konto nach der Änderung: `id` (int), `login`, `email`, `name` (strings), `enabled` (bool); das Konto vor der Änderung bei `delete` und einer abgelehnten Registrierung |

### Je Befehl (Phase 1)

| Befehl | `mode` | Weitere Schlüssel |
|---|---|---|
| `exp:user:create` | `create` | `user_id` (int, das neue Konto), `generated_password` (mit `--generate-password` und `--apply`) |
| `exp:user:edit` | `edit` | keine |
| `exp:user:disable` | `disable` | `refusal`, `sessions_ended` (int, mit `--apply`) |
| `exp:user:enable` | `enable` | keine |
| `exp:user:delete` | `delete` | `refusal` |
| `exp:user:reset-password` | `reset-password` | `reset` (`send-link`, `generate` oder `stdin`), `mail_sent` (bool, `send-link`), `sessions_ended` (int), `generated_password` (`generate` mit `--apply`) |
| `exp:user:approve` | `approve` oder `reject` | `state_before`, `state`: der Zustand der Registrierung davor und danach |

`refusal` nennt, warum ein geschütztes Konto verweigert wurde: `anonymous`, `creator`, `admin`, `self` oder
`last_administrator`. Die Zustände der Registrierung sind `email`, `administrator`, `workflow` (wartend), `pending`,
`active`, `disabled` und `missing`.

## Schlüssel der Listen (Phase 1)

`exp:user:list` (`mode` `list`) und `exp:user:pending` (`mode` `pending`):

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `filters` | object | die verwendeten Filter: `state`, `group`, `search`, `days`, `sort`, `order` (`pending`: `state`, `search`) |
| `total` | int | wie viele Konten die Filter auswählen |
| `offset`, `limit` | int | die Seite |
| `accounts` | object[] | die Konten der Seite |

Ein Eintrag von `accounts`:

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `id` | int | die ID des Kontos (die ID seines Inhaltsobjekts) |
| `login`, `email`, `name` | string | |
| `enabled` | bool | |
| `unactivated` | bool | deaktiviert mit einem Aktivierungsschlüssel |
| `has_key` | bool | hat einen Aktivierungsschlüssel |
| `locked` | bool | mehr fehlgeschlagene Anmeldungen als `MaxNumberOfFailedLogin` |
| `failed_attempts`, `login_count` | int | |
| `last_visit` | int oder false | Unix-Zeit des letzten Besuchs; false, wenn nie |
| `idle_days` | int oder false | Tage seit dem letzten Besuch |
| `object` | string | `published`, `draft` oder `none` |
| `orphaned` | bool | kein Objekt, nie veröffentlicht oder keine Platzierung |
| `node_id` | int | der Hauptknoten |
| `groups` | object[] | `node_id`, `name` jeder Gruppe |
| `roles` | string[] | die Namen der Rollen, die eigenen und die seiner Gruppen |
| `shared_login`, `shared_email` | int | wie viele aufgelistete Konten den Anmeldenamen oder die Adresse teilen (1: keines) |
| `states` | string[] | `disabled` oder `unactivated`, `locked`, `no_object`, `draft_only`, `no_location`, `never`; `active`, wenn nichts davon zutrifft |
| `registration` | string | nur in `pending`: `email`, `administrator` oder `workflow` |

## Schlüssel von exp:user:show

`mode` `show`, und `account`:

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `id`, `login`, `email`, `name` | | wie in der Liste |
| `class` | string | der Bezeichner der Benutzerklasse |
| `node_id` | int | der Hauptknoten |
| `object` | string | `published`, `draft` oder `none` |
| `enabled`, `unactivated`, `locked` | bool | |
| `max_login` | int | die Einstellung des Kontos |
| `failed_attempts`, `login_count` | int | |
| `last_visit` | int oder null | Unix-Zeit |
| `password_changed` | int oder null | Unix-Zeit der letzten Passwortänderung, die das Audit kennt |
| `password_change_required` | bool | bei der nächsten Anmeldung wird ein neues Passwort verlangt |
| `registration` | string | der Zustand der Registrierung |
| `states` | string[] | wie in der Liste |
| `groups` | object[] | `node_id`, `name`, `main` (bool); die Hauptgruppe zuerst |
| `roles` | object[] | `role`, `role_id`, `via` (die Objekt-ID, der die Rolle zugewiesen ist), `via_name`, `limitation` (`''`, oder zum Beispiel `Subtree:/1/2/`, `Section:3`) |
| `two_factor` | object oder null | `active` (bool), `method`; null, wenn die zweistufige Anmeldung nicht verfügbar ist |
| `sessions` | int oder null | aktive erfasste Sitzungen; null, wenn nicht erfasst |
| `protected` | string | `''`, oder der Grund wie in `refusal` |
| `administrator` | bool | das Konto hat alle Rechte |

## Schlüssel der Befehle der Phase 2

| Befehl | Schlüssel |
|---|---|
| `exp:user:groups` | `groups[]`: `node_id`, `name`, `parent_node_id`, `members`, `subgroups` |
| `exp:user:roles` | `assignments[]`: `role`, `role_id`, `via`, `via_name`, `limitation` |
| `exp:user:sessions` | `sessions[]`: `id`, `method`, `device`, `browser`, `ip`, `last_active`, `current` |
| `exp:user:export` | `count`, `output` |
| `exp:user:import` | `rows[]`: `line`, `login`, `result`, `errors[]`; `created`, `skipped`, `refused` |
| `exp:user:bulk` | `action`, `matched`, `accounts[]`: `id`, `login`, `result`, `reason` |
| `exp:user:audit` | `total`, `rows[]`: `time`, `event`, `result`, `actor_login`, `target_login`, `siteaccess` |
| `exp:user:hygiene` | `counts`: ein Objekt `{ Zustand: Zahl }` |
| `exp:user:whoami` | `account`, `roles`, `administrator` |
| `exp:user:check-login` | `valid` (bool), `reason` |

Das `result` einer Importzeile ist `would_create` im Probelauf und `created` mit `--apply`; `skipped` (den
Anmeldenamen gibt es schon) und `refused` (mit `errors`) sind in beiden gleich. `exp:user:export` schreibt die Konten
selbst nach `--output` oder auf die Standardausgabe. Ohne `--output` sind die Daten die ganze Ausgabe: es wird kein
Bericht ausgegeben, und der Exit-Code sagt, ob es geklappt hat. Mit `--output` gibt der Befehl einen Bericht aus, mit
`--format=json` als JSON, mit `count` und `output`.

## Beispiel

```json
{
    "schema": "exp-user/1",
    "command": "exp:user:delete",
    "mode": "delete",
    "dry_run": true,
    "user": { "id": 1234, "login": "jane", "email": "jane@example.com", "name": "Jane Doe", "enabled": false },
    "changes": [],
    "applied": false,
    "errors": [ "the user who acts (--as) cannot do this to its own account" ],
    "warnings": [],
    "refusal": "self",
    "exit": 1
}
```

## Verwandte Seiten

- [Benutzerverwaltung von der Konsole](../../funktionen/6.0/benutzerverwaltung-von-der-konsole.md)
- [Für Entwickler: Skripte und CI](../../hilfe/benutzerverwaltung-von-der-konsole/fuer-entwickler-skripte-und-ci.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md)
