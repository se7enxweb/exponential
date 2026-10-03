# Specification: the August 2026 security patches

This page describes the security patches of 16 August 2026 on the 6.0.15 line: six findings fixed, the integrity
manifest refreshed and the bundled jQuery replaced. Read it before you upgrade an installation that has users
created outside the admin (by SQL, packages or migrations), and if you write extensions that create tokens or call
external tools. Earlier hardening is described in [the 6.0.13 hardening](security-hardening-6.0.13.md).

Commits: findings `fa808e5a39`, `dec6c49a68`, `233a6d8c35`, `4a43d5df33`, `b0fdcbb806`, `9d36acf523`; integrity
manifest `d889a8abc6`; jQuery `f7c57376aa`.

## What you need to do

1. Update to the 6.0.15 line. All fixes keep their interfaces.
2. **Check that every account can still sign in** (finding F-06). An account without a row in `ezuser_setting`
   is now treated as disabled at sign-in. Accounts created in the admin always have the row. Accounts created by
   SQL, a package or a migration may not. [Find them with this query](#f-06-find-accounts-that-cannot-sign-in).
3. Existing REST/OAuth tokens and activation links keep working. Only newly issued ones use the new generator.
4. If you load scripts from an external CDN, change those settings to `https://` (the shipped file already is).

## The findings

| ID | Class | CWE | File | Fix |
|---|---|---|---|---|
| F-01 | Predictable REST/OAuth tokens | 330, 338 | `kernel/private/rest/classes/models/ezprest_token.php` | `ezpRestToken::generateToken()` returns `bin2hex( random_bytes( 20 ) )`: 40 hex characters from the system's secure random source. Before, it was an 8-character token from a seeded `mt_rand()`. The `$vary` parameter stays for compatibility and is no longer used |
| F-02 | Guessable account activation link | 330 | `kernel/user/ezuseroperationcollection.php` | The activation hash is `bin2hex( random_bytes( 16 ) )` instead of `md5( mt_rand() . time() . $userID )` |
| F-03 | HTTP header injection (CRLF) | 93 | `extension/ezoe/classes/runnable/views/ezoe/atd_rpc.php` | `\r` and `\n` are removed from the `url` parameter before it becomes part of the hand-built request to the After the Deadline server. The post text is initialised, so a GET no longer reads an undefined variable |
| F-05 | OS command injection | 78 | `kernel/classes/datatypes/ezbinaryfile/plugins/ezpdfparser.php`, `ezwordparser.php` | The extraction tool goes through `escapeshellcmd()`; file names, including the temporary output file, go through `escapeshellarg()`. A file name with shell characters can no longer add a command |
| F-06 | Fail open on login | 636, 697 | `kernel/classes/datatypes/ezuser/ezuser.php`, `eZUser::_loginUser()` | At sign-in an account without an `ezuser_setting` row counts as disabled; it counted as enabled. `eZUser::isEnabled()` itself still returns true for such an account; it is the login that fails closed. Disabling a user keeps the row with `is_enabled = 0`, so only accounts without a row are affected |
| H-06 | Scripts over plain HTTP | 829, 319 | `extension/ezjscore/settings/ezjscore.ini` | `ExternalScripts[...]` URLs use `https://`. Prefer `LoadFromCDN=disabled` (local scripts) or subresource integrity. The Yahoo CDN of YUI is end of life and may not answer |

F-03 note: the view `extension/ezoe/modules/ezoe/atd_rpc.php` is now an entry point that calls the class above.
F-04 (the jQuery 1.10.2 library) was handled by the [jQuery replacement](#jquery-371-and-jquery-migrate-341).

## F-06: find accounts that cannot sign in

Run this read-only query in your database client. The table layout (`user_id`, `is_enabled`, `max_login`) is
in `kernel/sql/mysql/kernel_schema.sql`.

```sql
SELECT u.contentobject_id, u.login
FROM ezuser u
LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id
WHERE s.user_id IS NULL;
```

Each row is an account without settings. An empty result means every account can sign in. To let such an
account sign in, create its row:

```sql
INSERT INTO ezuser_setting ( user_id, is_enabled, max_login ) VALUES ( <contentobject_id>, 1, 1000 );
```

`max_login` is the allowed number of simultaneous logins; use the value your other accounts have. Package and
migration importers should create this row when they create a user.

## jQuery 3.7.1 and jQuery Migrate 3.4.1

On 16 August `ezjscore` replaced the bundled jQuery 1.10.2 with 3.7.1. The server function `ezjsc::jquery` now
loads jQuery Migrate 3.4.1 right after it, for every caller. Migrate restores APIs that jQuery 3 removed (`live`,
`delegate`, `hover`, `$.isArray`, `$.trim`), so TinyMCE, the online editor, ezie, admin templates and third-party
extensions keep working. The minified Migrate build is muted (`jQuery.migrateMute = true`), so the browser
console stays quiet.

Removed files: `jquery-1.10.2.min.js`, `jquery-migrate-1.1.1.min.js`, `jquery-migrate-1.2.1.min.js`, and the
copy `jquery-3.7.0.min.js` in `design/simple`.

The keys as they stood on 16 August:

| File | Block | Key | Value on 16 August |
|---|---|---|---|
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `LocalScripts[jquery]` | the 3.7.1 file |
| same | `eZJSCore` | `LocalScripts[jqueryMigrate]` | `jquery-migrate-3.4.1.min.js` |
| same | `eZJSCore` | `ExternalScripts[jquery]`, `ExternalScripts[jqueryMigrate]` | the `https://` CDN addresses of the same versions |

jQuery UI stayed at 1.10.3 at that time. All three later moved on with the jQuery 4 work: the keys now point to
jQuery 4.0.0, Migrate 4.0.2 and jQuery UI 1.14.2. See
[jQuery 4 and the removal of YUI](../../features/6.0/jquery4-and-yui-removal.md).

## File integrity

Commit `d889a8abc6` refreshed `share/filelist.md5`, so the
[file consistency check](../../features/6.0/file-consistency-check.md) does not list the patched files as
modified.

## For extension developers

- Never build tokens, activation links or reset links from `mt_rand()`, `rand()`, `time()` or `md5()` of them.
  Use `bin2hex( random_bytes( $n ) )`.
- Pass every file name and argument of an external tool through `escapeshellarg()`.
- Strip `\r` and `\n` from any value that becomes part of a header or request line.
- Treat a missing settings row as "no", never as "yes".

## Related pages

- [Security hardening release notes](../../bc/6.0/hardening.md), [Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md), [RAD tools — security](../../bc/6.0/rad-security.md), [Securing content/view/full](../../bc/6.0/view_full_security.md)
- [Specification: the 6.0.13 hardening](security-hardening-6.0.13.md), [Security defaults of September 2026](security-defaults-2026-09.md), [Datatype and input hardening](datatype-input-hardening.md)
- [jQuery 4 and the removal of YUI](../../features/6.0/jquery4-and-yui-removal.md), [Reset a user password](../../features/6.0/reset-user-password.md), [Request rules](../../features/6.0/request-rules.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), month pages [August 2026](../../history/2026/2026-08.md) and [February 2026](../../history/2026/2026-02.md)
