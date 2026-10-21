# Persönliche API-Schlüssel: eine Tabelle, ein Modul und eine Richtlinie

Lesen Sie diese Seite, wenn Sie eine Installation auf 6.0.15 aktualisieren, wenn Sie die REST-Schnittstelle
(`/api/...`) mit HTTP-Basic-Authentifizierung oder OAuth verwenden, wenn Sie die Templates von `oauthadmin`
überschreiben oder wenn Sie eigene REST-Routen bauen. Die Funktion selbst erklärt die Anleitung
[Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Tabelle `expapikey`, neues Modul `apikey` (View `list`, Richtlinie `apikey/create` mit einer Einschränkung `Scope`), neues `rest.ini [ApiKeySettings]`, neue Admin-Views `oauthadmin/keys` und `oauthadmin/keyaction`, vier Audit-Ereignisse `access.apikey.*`. Die REST-Schicht akzeptiert `Authorization: Bearer`. Die Basic-Authentifizierung prüft jeden Typ von Passwort-Hash. |
| Wer betroffen ist | Jede Installation, die aktualisiert wird (die Tabelle). Websites mit `AuthenticationStyle=ezpRestBasicAuthStyle` (Benutzer mit modernen Hashes können sich jetzt anmelden). Overrides von `design:oauthadmin/*.tpl` (die Seiten wurden neu gestaltet). Clients, die sich bei `insufficient_scope` auf 401 verlassen haben (jetzt 403). |
| Wie Sie es prüfen | `php update/common/scripts/6.0/createapikeytable.php --dry-run --allow-root-user` meldet `table expapikey: exists`; `/apikey/list` antwortet mit 200 |
| Wie Sie es beheben | Legen Sie die Tabelle an (siehe unten); erzeugen Sie die Autoloads des Kernels neu; leeren Sie die Caches für INI, template und template-override; laden Sie PHP-FPM neu und starten Sie Velocity neu. Geben Sie `apikey/create` an die Rollen, die Schlüssel haben dürfen. |

## Die Tabelle

`expapikey`: `id`, `user_id`, `name`, `key_prefix` (eindeutig), `secret_hash`, `salt`, `scopes`, `created`,
`created_by`, `expires`, `last_used`, `last_ip`, `revoked`, `revoked_by`. Indizes `expapikey_prefix` (eindeutig) und
`expapikey_user`.

- Neue Installationen: aus `share/db_schema.dba` (jede Engine) oder dem Kernel-Schema von MySQL, PostgreSQL und
  SQLite.
- Upgrades: das Ende von `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (`CREATE TABLE IF NOT EXISTS`; unter
  PostgreSQL ein `DO`-Block, der Sequenz, Tabelle, Schlüssel und Indizes nur anlegt, wenn die Tabelle fehlt, sodass ein
  zweiter Lauf nichts ändert), oder auf jeder Engine, Oracle und MongoDB eingeschlossen:

```bash
php update/common/scripts/6.0/createapikeytable.php --allow-root-user
```

## Die Richtlinie und die Seite

- `apikey/create` darf Schlüssel anlegen. In einer neuen Installation hat sie keine Rolle; für niemanden ändert sich
  etwas, bis sie vergeben wird. Ihre Einschränkung `Scope` führt die Scopes aus `rest.ini [ApiKeySettings] Scopes[]`
  auf.
- `apikey/list` steht in `site.ini [RoleSettings] PolicyOmitList[]`: Die View prüft die Anmeldung und `apikey/create`
  selbst und lässt jeden angemeldeten Benutzer seine eigenen Schlüssel sehen und widerrufen.
- `oauthadmin` hat weiterhin keine eigenen Funktionen: Seine Views, auch die neuen, brauchen `oauthadmin/*` (oder
  `*/*`).

## Verhaltensänderungen der REST-Schnittstelle

| Vorher | Jetzt |
|---|---|
| `ezpRestBasicAuthStyle` verglich `md5("login\npassword")` mit `ezuser.password_hash`: Nur Benutzer mit dem alten Hash `md5_user` konnten sich anmelden | Das Passwort wird mit `eZUser::authenticateHash()` und dem eigenen Hash-Typ des Benutzers geprüft; der Benutzer muss aktiviert, veröffentlicht und nicht gesperrt sein; ein falsches Passwort zählt als fehlgeschlagene Anmeldung |
| Nur `Authorization: OAuth <token>` | `OAuth <token>` und `Bearer <token>`; `Bearer expk_...` ist ein persönlicher API-Schlüssel |
| `insufficient_scope` antwortete mit 401 (PHP machte aus der 403 eine 401, weil `WWW-Authenticate` nach dem Status gesendet wurde) | 403, wie RFC 6750 es vorsieht; die Statuszeile wird nach der Challenge geschrieben |
| Eine neue OAuth-Anwendung erhielt `md5( name . uniqid( name ) )` als Kennung und Geheimnis | `random_bytes()`: 32 Hex-Zeichen Kennung, 64 Geheimnis |
| Das Entfernen einer Anwendung ließ ihre Autorisierungen und Tokens zurück | Sie werden mit entfernt |
| Client-Geheimnisse mit `===` verglichen | `hash_equals()` |
| Ein abgelehntes `POST` oder `DELETE` (kein Token, ein falsches, ein Schlüssel ohne den Scope) antwortete mit `405` | `401` oder `403`: Die Routen für Authentifizierung und Fehler beantworten jede Methode |
| Eine Route, deren Pfad passte, aber nicht die Methode, beendete die Suche mit `405` | Die nächsten Routen werden versucht; `405` nur, wenn keine die Methode annimmt |
| Der Antwort-Cache wurde von allen Benutzern geteilt und hielt auch Schreibzugriffe | Ein Eintrag pro Benutzer, nur `GET` und `HEAD` |
| Die Schreibzugriffe von `ezprestapi` fragten bei Tokens oder Basic-Authentifizierung keine Richtlinie ab | Jeder Lese- und Schreibzugriff prüft die Richtlinien des Benutzers (`expRestContentPermission`), sonst `403 access_denied`; braucht `ezprestapi` 1.2.5 |
| `/api/ezp/v1/...` antwortete bei aktivem `ezprestapi` mit 404 | Lesezugriffe antworten unter v1 und v2 (`ezpRestVersionedRoute` nimmt eine Liste von Versionen) |

Jede REST-Anfrage mit einem Schlüssel wird gegen einen Scope geprüft:
`[ApiKeySettings] RouteScopes[<Controller>_<action>]`, sonst `DefaultReadScope` für GET und HEAD, sonst abgelehnt. Eine
eigene REST-Route, die schreibt, ist für Schlüssel gesperrt, bis Sie ihren Scope in `rest.ini` nennen.

## Templates

`design/standard/templates/oauthadmin/` (list, view, edit, delete_confirmation) wurden neu geschrieben und haben
`keys.tpl`, `key_revoke_confirmation.tpl` sowie `parts/style.tpl`, `parts/tabs.tpl` erhalten. Die Namen der Formulare,
Felder und Schaltflächen und die Aktionen sind unverändert, sodass ein Override der alten Templates weiter
funktioniert; vergleichen Sie es mit dem neuen, um die neuen Angaben und Links zu übernehmen. Neu:
`design/standard/templates/apikey/` (`list.tpl`, `parts/page_start.tpl`, `parts/page_end.tpl`, `parts/style.tpl`,
`parts/account_link.tpl`). Ein Design rahmt die Seite ein, indem es `parts/page_start.tpl` und `parts/page_end.tpl`
überschreibt, wie es das Design media tut. `user/edit.tpl` (standard) und `user/setting.tpl` (admin, admin4)
verlinken auf die Schlüssel.

## Der Filter für das Formular-Token

`ezxFormToken` setzte auf jeder Seite mit einem Formular `Cache-Control: private, no-cache, must-revalidate` und
ersetzte damit den eigenen Header der View. Eine View, die `no-store` gesendet hat, behält es jetzt. Sonst ändert sich
nichts.

## Verwandte Seiten

- [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md), die Anleitung
- [Audit](../../../eng-us/bc/6.0/audit.md#8-event-reference): die Ereignisse `access.apikey.*`
- [ezprestapi](../../../eng-us/features/6.0/extensions/ezprestapi.md): die Inhaltsrouten
- [Vertrauenswürdige Proxys](vertrauenswuerdige-proxys.md): die Adresse, die als letzte Verwendung eines Schlüssels
  festgehalten wird
