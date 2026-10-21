# Hinweis zum Upgrade: PKCE und state für den OAuth-Autorisierungsserver

Ab 6.0.15 unterstützt der OAuth-Autorisierungsserver der REST-API (`oauth/authorize` und der Token-Endpunkt
`POST /api/auth/v1/oauth/token`) PKCE (RFC 7636) und gibt den `state` einer Anfrage an den Client zurück.

## Was sich ändert

- **state**: `oauth/authorize` gibt den Parameter `state` der Anfrage unverändert mit dem Code, dem Token oder dem
  Fehler zurück (RFC 6749, Abschnitt 4.1.2). Bisher ging er verloren, sodass ein Client seine Weiterleitung nicht gegen
  Cross-Site Request Forgery schützen konnte. Eine Anfrage ohne `state` erhält keinen zurück.
- **PKCE**: `oauth/authorize` nimmt `code_challenge` und `code_challenge_method` an und speichert sie mit dem
  Autorisierungscode. Der Token-Endpunkt gibt für diesen Code nur mit dem passenden `code_verifier` ein Token aus.
  - `S256` wird angenommen. `plain` wird abgelehnt, außer bei `AllowPlainPkce=enabled`; eine Challenge ohne Methode
    gilt als `plain` (RFC 7636, Abschnitt 4.3). Eine fehlerhafte Challenge wird mit `error=invalid_request`
    abgelehnt.
  - Ein falscher oder fehlender `code_verifier` für einen Code mit Challenge wird abgelehnt (`invalid_grant`), und der
    Code ist damit verbraucht, sodass ein Verifier nicht über viele Anfragen erraten werden kann.
  - Ein `code_verifier` für einen Code ohne Challenge wird ebenfalls abgelehnt.
- **Öffentliche Clients**: Ein Client ohne gespeichertes Geheimnis (eine mobile oder Browser-App, die keines bewahren
  kann) ist ein öffentlicher Client. Er sendet kein `client_secret` an den Token-Endpunkt und muss PKCE verwenden
  (`RequirePkceForPublicClients`, standardmäßig eingeschaltet); `oauth/authorize` antwortet ohne `code_challenge`
  mit `error=invalid_request`. Bisher konnte ein solcher Client gar keinen Code einlösen. Clients aus
  **Setup > oAuth admin** haben immer ein Geheimnis, sind also vertraulich, und **für sie ändert sich nichts**: PKCE
  ist für sie freiwillig, außer `RequirePkceForConfidentialClients` ist eingeschaltet. Ein vertraulicher Client muss
  sein Geheimnis weiterhin senden.
- Ein Code oder Refresh-Token wird nur von dem Client eingelöst, für den er ausgestellt wurde (bisher genügte es, ihn
  zu kennen).
- Ein unbekannter `response_type` erhält `error=unsupported_response_type` statt einer leeren Seite.

## Einstellungen

`rest.ini [OAuthSettings]`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `RequirePkceForPublicClients` | `enabled` | ein Client ohne Geheimnis muss PKCE verwenden |
| `RequirePkceForConfidentialClients` | `disabled` | jeder Client muss PKCE verwenden |
| `AllowPlainPkce` | `disabled` | die Methode `plain` annehmen |

## Was beim Upgrade zu tun ist

Die Codes brauchen zwei neue Spalten. Führen Sie die Datenbankaktualisierung der Linie aus,
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, oder nur ihre Anweisungen für `ezprest_authcode`:

```sql
-- MySQL / MariaDB
ALTER TABLE ezprest_authcode ADD COLUMN code_challenge varchar(128) default NULL,
                             ADD COLUMN code_challenge_method varchar(10) default NULL;
-- PostgreSQL
ALTER TABLE ezprest_authcode ADD COLUMN IF NOT EXISTS code_challenge character varying(128);
ALTER TABLE ezprest_authcode ADD COLUMN IF NOT EXISTS code_challenge_method character varying(10);
-- SQLite
ALTER TABLE ezprest_authcode ADD COLUMN code_challenge varchar(128) DEFAULT NULL;
ALTER TABLE ezprest_authcode ADD COLUMN code_challenge_method varchar(10) DEFAULT NULL;
```

Solange die Spalten fehlen, kann `oauth/authorize` keinen Code speichern (`response_type=code` schlägt fehl); bereits
ausgestellte Tokens bleiben gültig. Neue Installationen haben die Spalten (Kernel-Schemas und
`share/db_schema.dba`, aus dem auch die Oracle- und MongoDB-Tabellen entstehen).

## Ein Client mit PKCE

```text
code_verifier  = 43 bis 128 Zeichen aus A-Z a-z 0-9 - . _ ~ (zufällig, bleibt in der App)
code_challenge = BASE64URL(SHA256(code_verifier)), ohne Auffüllung

GET /oauth/authorize?client_id=...&redirect_uri=...&response_type=code
    &state=<zufall>&code_challenge=<challenge>&code_challenge_method=S256
-> 302 <redirect_uri>?code=<code>&expires_in=3600&state=<zufall>     (die App prüft state)

POST /api/auth/v1/oauth/token
     grant_type=authorization_code&client_id=...&code=<code>&redirect_uri=...&code_verifier=<verifier>
     (+ client_secret=... bei einem vertraulichen Client)
```

## Code und Tests

- `expOAuthPkce` (`kernel/private/rest/classes/oauth/pkce.php`), verwendet von
  `kernel/private/modules/oauth/authorize.php` und `ezpOauthUtility` (`kernel/private/rest/classes/oauth/utility.php`).
- `tests/tests/kernel/private/oauth/expOAuthPkceTest.php`, `expOAuthPkceSchemaTest.php` und
  `ezpOauthAuthorizeRedirectTest.php` (ohne Datenbank): S256 angenommen, falsche und fehlende Verifier abgelehnt,
  PKCE für öffentliche Clients verlangt, `plain` abgelehnt, `state` zurückgegeben, die Spalten in jedem Schema.
