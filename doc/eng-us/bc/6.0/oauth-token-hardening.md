# OAuth tokens: disabled users, client binding, refresh token lifetime and token sources (6.0.15)

From 6.0.15 on, the OAuth layer of the REST API (`/api/...` and the token endpoint `POST /api/auth/v1/oauth/token`)
checks a token's user, ends refresh tokens after a lifetime, and no longer takes an access token from the address.

## What changes

- **Disabled and locked users.** An access token, an authorization code and a refresh token work only while their
  user is enabled and not locked by failed logins (`eZUser::isEnabledAfterFailedLogin()`), the rule personal API keys
  and password sign-in already apply. Before, a user disabled in the administration kept full REST access with a token
  issued earlier, and kept getting fresh access tokens with the refresh token. A resource request with such a token
  gets `401`; the token endpoint answers `invalid_grant` with the description it gives for a code or refresh token
  of another client, so the answer does not tell the token holder that the account is disabled or locked.
- **Client binding.** A code or refresh token is redeemed only by the client it was issued to. The client identifiers
  are compared as strings in constant time, and an empty identifier never matches.
- **Refresh token lifetime.** A refresh token used to be valid for ever. It now lasts `RefreshTokenTTL` (30 days by
  default) from the refresh that issued it; an expired one is deleted and the request answers `invalid_grant`, so
  the application sends the user through `oauth/authorize` again.
  - `ezprest_token` stores one expiry per row, the access token's, and no issue time. The issue time is derived as
    `expirytime - TokenTTL`, so there is no schema change. Changing `TokenTTL` therefore shifts the deadline of
    refresh tokens issued before by the difference.
  - Every refresh issues a new refresh token, so the lifetime slides from the last refresh; an absolute limit from the
    first authorization would need a new column.
  - A row stored without an expiry (`expirytime=0`) is refused while `RefreshTokenTTL` is set.
- **Token sources.** The `Authorization` header (`Bearer <token>` or `OAuth <token>`) is always read. The
  `oauth_token` parameter of a POST form body (RFC 6750, section 2.2) is read unless `AllowTokenInBody=disabled`. The
  `oauth_token` query parameter (`?oauth_token=...`, RFC 6750, section 2.3) is **no longer read** unless
  `AllowTokenInQuery=enabled`: a token in the address ends up in web server and proxy access logs, the browser
  history and `Referer` headers.
- **Missing parameters.** The token endpoint names every missing parameter ("Missing grant_type, client_id
  parameters."). Before, only a missing last parameter was refused, and a request without `client_id` went on to look
  up a client `null` with "Undefined array key" warnings. A parameter posted as a list (`client_id[]=...`) is refused
  as `invalid_request` ("Invalid client_id parameter.") instead of reaching the database layer as an array.
- **Diagnosis.** A request whose token comes only in the address while `AllowTokenInQuery` is off writes a notice to
  the debug output ("An OAuth token in the query string (oauth_token) was ignored ...").
- **Not changed.** The REST API, SOAP, WebDAV and `expservices` are not guarded by two-factor sign-in by default; this
  change adds no second factor to the token endpoint or to resource requests. The second factor is asked where the
  user signs in on the web form, which `oauth/authorize` uses.

## Settings

`rest.ini [OAuthSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `RefreshTokenTTL` | `2592000` | lifetime of a refresh token in seconds, from the refresh that issued it; `0` = no expiry, as before |
| `AllowTokenInQuery` | `disabled` | `enabled` also takes `?oauth_token=` from the address, as before |
| `AllowTokenInBody` | `enabled` | take `oauth_token` from a POST form body when there is no `Authorization` header |

## What to do when you upgrade

- A client that sends the token in the address (`?oauth_token=...`) now gets `401`. Change it to send
  `Authorization: Bearer <token>`. Where that cannot be done at once, set `AllowTokenInQuery=enabled` in
  `settings/override/rest.ini.append.php` for the time it takes. The command line sample client `php_ezrestcall` of
  the `ezprestapi` package sends the token in the address for some calls and needs the header too.
- An application that kept a refresh token for longer than 30 days without using it has to send its user through
  `oauth/authorize` again. Set `RefreshTokenTTL=0` to keep the earlier behaviour.
- Nothing changes for active users of applications that refresh within the lifetime.

## Code and tests

- `ezpOauthUtility` (`kernel/private/rest/classes/oauth/utility.php`): `userMayUseToken()`, `clientMatches()`,
  `refreshTokenExpired()`, `tokenSources()`; `ezpOauthFilter::run()` (`oauth_filter.php`);
  `ezpRestOauthTokenController::missingParams()` and `invalidParams()` (`controllers/oauth_token.php`).
- `tests/tests/kernel/private/oauth/ezpOauthGrantChecksTest.php` and
  `tests/tests/kernel/classes/rest/ezpOauthUtilityTokenTest.php` (no database).
