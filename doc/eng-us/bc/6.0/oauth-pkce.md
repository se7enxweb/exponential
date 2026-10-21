# Upgrade note: PKCE and state for the OAuth authorization server

From 6.0.15 on, the OAuth authorization server of the REST API (`oauth/authorize` and the token endpoint
`POST /api/auth/v1/oauth/token`) supports PKCE (RFC 7636) and sends the `state` of a request back to the client.

## What changes

- **state**: `oauth/authorize` sends the `state` parameter of the request back unchanged with the code, the token or
  the error (RFC 6749, section 4.1.2). Before, it was dropped, so a client could not protect its redirect against
  cross-site request forgery. A request without `state` gets none back.
- **PKCE**: `oauth/authorize` takes `code_challenge` and `code_challenge_method` and stores them with the
  authorization code. The token endpoint issues a token for that code only with the matching `code_verifier`.
  - `S256` is accepted. `plain` is refused unless `AllowPlainPkce=enabled`; a challenge without a method counts as
    `plain` (RFC 7636, section 4.3). A malformed challenge is refused with `error=invalid_request`.
  - A wrong or missing `code_verifier` for a code with a challenge is refused (`invalid_grant`), and the code is used
    up, so a verifier cannot be guessed over many requests.
  - A `code_verifier` for a code issued without a challenge is refused too.
- **Public clients**: a client stored without a secret (a mobile or browser app, which cannot keep one) is a public
  client. It sends no `client_secret` to the token endpoint and must use PKCE (`RequirePkceForPublicClients`,
  enabled by default); `oauth/authorize` answers `error=invalid_request` without a `code_challenge`. Before, such a
  client could not redeem a code at all. Clients made in **Setup > oAuth admin** always have a secret, so they are
  confidential and **nothing changes for them**: PKCE is optional for them unless `RequirePkceForConfidentialClients`
  is enabled. A confidential client still has to send its secret.
- A code or refresh token is redeemed only by the client it was issued to (it was enough to know it before).
- An unknown `response_type` gets `error=unsupported_response_type` instead of an empty page.

## Settings

`rest.ini [OAuthSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `RequirePkceForPublicClients` | `enabled` | a client without a secret must use PKCE |
| `RequirePkceForConfidentialClients` | `disabled` | every client must use PKCE |
| `AllowPlainPkce` | `disabled` | accept the method `plain` |

## What to do when you upgrade

The codes need two new columns. Run the database update of the line,
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, or only its statements for `ezprest_authcode`:

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

Until the columns exist, `oauth/authorize` cannot store a code (`response_type=code` fails); tokens issued before stay
valid. New installations have the columns (kernel schemas and `share/db_schema.dba`, which also makes the Oracle and
MongoDB tables).

## A client using PKCE

```text
code_verifier  = 43 to 128 characters of A-Z a-z 0-9 - . _ ~ (random, kept by the app)
code_challenge = BASE64URL(SHA256(code_verifier)), without padding

GET /oauth/authorize?client_id=...&redirect_uri=...&response_type=code
    &state=<random>&code_challenge=<challenge>&code_challenge_method=S256
-> 302 <redirect_uri>?code=<code>&expires_in=3600&state=<random>     (the app checks state)

POST /api/auth/v1/oauth/token
     grant_type=authorization_code&client_id=...&code=<code>&redirect_uri=...&code_verifier=<verifier>
     (+ client_secret=... for a confidential client)
```

## Code and tests

- `expOAuthPkce` (`kernel/private/rest/classes/oauth/pkce.php`), used by `kernel/private/modules/oauth/authorize.php`
  and `ezpOauthUtility` (`kernel/private/rest/classes/oauth/utility.php`).
- `tests/tests/kernel/private/oauth/expOAuthPkceTest.php`, `expOAuthPkceSchemaTest.php` and
  `ezpOauthAuthorizeRedirectTest.php` (no database): S256 accepted, wrong and missing verifiers refused, PKCE
  required for public clients, `plain` refused, `state` returned, the columns in every schema.
