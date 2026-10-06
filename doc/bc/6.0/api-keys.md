# Personal API keys: a table, a module and a policy

Read this page if you upgrade an installation to 6.0.15, if you use the REST interface (`/api/...`) with HTTP basic
authentication or OAuth, if you override the `oauthadmin` templates, or if you build REST routes of your own. The
feature itself is taught in the guide [Personal API keys](../../guides/api-keys.md).

## In short

| | |
|---|---|
| What changed | New table `expapikey`, new module `apikey` (view `list`, policy `apikey/create` with a `Scope` limitation), new `rest.ini [ApiKeySettings]`, new admin views `oauthadmin/keys` and `oauthadmin/keyaction`, four audit events `access.apikey.*`. The REST layer accepts `Authorization: Bearer`. Basic authentication checks every password hash type. |
| Who is affected | Every installation that upgrades (the table). Sites with `AuthenticationStyle=ezpRestBasicAuthStyle` (users on modern hashes can now sign in). Overrides of `design:oauthadmin/*.tpl` (the pages were redesigned). Clients that relied on 401 for `insufficient_scope` (it is 403 now). |
| How to check | `php update/common/scripts/6.0/createapikeytable.php --dry-run --allow-root-user` says `table expapikey: exists`; `/apikey/list` answers 200 |
| How to fix | Create the table (below); regenerate the kernel autoloads; clear the INI, template and template-override caches; reload PHP-FPM and restart Velocity. Give `apikey/create` to the roles that may have keys. |

## The table

`expapikey`: `id`, `user_id`, `name`, `key_prefix` (unique), `secret_hash`, `salt`, `scopes`, `created`,
`created_by`, `expires`, `last_used`, `last_ip`, `revoked`, `revoked_by`. Indexes `expapikey_prefix` (unique) and
`expapikey_user`.

- New installations: from `share/db_schema.dba` (every engine) or the kernel schema of MySQL, PostgreSQL and SQLite.
- Upgrades: the end of `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (`CREATE TABLE IF NOT EXISTS`; on
  PostgreSQL a `DO` block that creates the sequence, table, key and indexes only when the table is missing, so a second
  run changes nothing), or on any engine, Oracle and MongoDB included:

```bash
php update/common/scripts/6.0/createapikeytable.php --allow-root-user
```

## The policy and the page

- `apikey/create` may make keys. No role has it in a new installation; nothing changes for anybody until it is
  given. Its `Scope` limitation lists the scopes of `rest.ini [ApiKeySettings] Scopes[]`.
- `apikey/list` is in `site.ini [RoleSettings] PolicyOmitList[]`: it checks the sign-in and `apikey/create` itself,
  and lets every signed-in user see and revoke their own keys.
- `oauthadmin` still has no functions of its own: its views, the new ones included, need `oauthadmin/*` (or
  `*/*`).

## Behaviour changes of the REST interface

| Before | Now |
|---|---|
| `ezpRestBasicAuthStyle` compared `md5("login\npassword")` with `ezuser.password_hash`: only users on the legacy `md5_user` hash could sign in | The password is checked with `eZUser::authenticateHash()` and the user's own hash type; the user must be enabled, published and not locked; a wrong password counts as a failed login |
| Only `Authorization: OAuth <token>` | `OAuth <token>` and `Bearer <token>`; `Bearer expk_...` is a personal API key |
| `insufficient_scope` answered 401 (PHP turned the 403 into 401 because `WWW-Authenticate` was sent after the status) | 403, as RFC 6750 says; the status line is written after the challenge |
| A new OAuth application got `md5( name . uniqid( name ) )` as identifier and secret | `random_bytes()`: 32 hex characters of identifier, 64 of secret |
| Removing an application left its authorizations and tokens | They are removed with it |
| Client secrets compared with `===` | `hash_equals()` |
| A refused `POST` or `DELETE` (no token, a wrong one, a key without the scope) answered `405` | `401` or `403`: the authentication and error routes answer every method |
| A route that matched the path but not the method ended the search with `405` | The next routes are tried; `405` only when none takes the method |
| The answer cache was shared by every user and kept writes too | One entry per user, `GET` and `HEAD` only |
| The `ezprestapi` writes asked no policy for tokens or basic authentication | Every read and write checks the user's policies (`expRestContentPermission`), `403 access_denied` otherwise; needs `ezprestapi` 1.2.5 |
| `/api/ezp/v1/...` answered 404 with `ezprestapi` active | Reads answer at v1 and v2 (`ezpRestVersionedRoute` takes a list of versions) |

Every REST request with a key is checked against a scope: `[ApiKeySettings] RouteScopes[<Controller>_<action>]`, else
`DefaultReadScope` for GET and HEAD, else refused. A REST route of your own that writes is closed to keys until you
name its scope in `rest.ini`.

## Templates

`design/standard/templates/oauthadmin/` (list, view, edit, delete_confirmation) were rewritten and gained `keys.tpl`,
`key_revoke_confirmation.tpl` and `parts/style.tpl`, `parts/tabs.tpl`. The form names, field names, button names and
actions are unchanged, so an override of the old templates keeps working; compare it with the new one to take the new
figures and links. New: `design/standard/templates/apikey/` (`list.tpl`, `parts/page_start.tpl`, `parts/page_end.tpl`,
`parts/style.tpl`, `parts/account_link.tpl`). A design frames the page by overriding `parts/page_start.tpl` and
`parts/page_end.tpl`, as the media design does. `user/edit.tpl` (standard) and `user/setting.tpl` (admin, admin4) link
to the keys.

## The form token filter

`ezxFormToken` set `Cache-Control: private, no-cache, must-revalidate` on every page with a form, replacing the
view's own header. A view that sent `no-store` keeps it now. Nothing else changes.

## Related pages

- [Personal API keys](../../guides/api-keys.md), the guide
- [Audit](audit.md#8-event-reference): the `access.apikey.*` events
- [ezprestapi](../../features/6.0/extensions/ezprestapi.md): the content routes
- [Trusted proxies](trusted-proxies.md): the address noted as a key's last use
