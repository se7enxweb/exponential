# Passkeys and "Verify it's you": a table, two views and a question before sensitive actions

Read this page if you upgrade an installation to 6.0.15, if you override `user/login.tpl` or `user/edit.tpl`, or if
your extension has views that change accounts, settings or export data. The features are taught in the guides
[Passkeys](../../guides/passkeys.md) and [Verify it's you](../../guides/verify-its-you.md).

## In short

| | |
|---|---|
| What changed | New table `exppasskey`; new views `user/passkeys` and `setup/reauth`; new blocks `site.ini [PasskeySettings]`, `[ReauthSettings]` and `[ReauthGroup_*]`; the sign-in forms of `design/standard`, `design/admin` and admin4 sign in with a passkey too; sensitive actions on the web pages ask "Verify it's you" first; six audit events (`access.passkey.add`, `.rename`, `.remove`, `.use`, `.use.failed`, `access.session.reauth.ask`). |
| Who is affected | Every installation that upgrades (the table). Every administrator and user: changing their own password or e-mail address, managing users and roles, setup actions and exports now ask for the password (or a passkey or code) once per ten minutes. Overrides of `user/login.tpl` (no passkey button without the include below). Scripts that POST to the listed admin views in a browser session (they get 403 `reauth_required`). |
| How to check | `php update/common/scripts/6.0/createpasskeytable.php --dry-run --allow-root-user` says `table exppasskey: exists`; `/user/passkeys` answers 200; `/setup/reauth` lists five groups |
| How to fix | Create the table (below); regenerate the kernel autoloads; clear the INI, template and template-override caches; reload PHP-FPM and restart Velocity. |
| How to turn off | Passkeys: `[PasskeySettings] Passkeys=disabled`. The question: `[ReauthSettings] Reauth=disabled`, or single groups on `setup/reauth`. |

## The table

`exppasskey`: `aaguid`, `algorithm`, `backup_eligible`, `backup_state`, `created`, `credential_hash` (unique),
`credential_id`, `id`, `last_ip`, `last_used`, `name`, `public_key`, `sign_count`, `transports`, `user_handle`,
`user_id`. Indexes `exppasskey_credential` (unique) and `exppasskey_user`.

- New installations: from `share/db_schema.dba` (every engine) or the kernel schemas of MySQL, PostgreSQL and SQLite.
- Upgrades: the end of `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (`CREATE TABLE IF NOT EXISTS`; a
  guarded `DO` block on PostgreSQL), or on any engine, Oracle and MongoDB included:

```bash
php update/common/scripts/6.0/createpasskeytable.php --allow-root-user
```

Without the table the passkey pages offer nothing and the sign-in form shows no passkey; nothing fails.

## Templates

- `user/login.tpl` of `design/standard`, `design/admin` and `design/admin4` get the passkey button, the options in
  `data-exp-passkey-*` attributes, `autocomplete="username webauthn"` and `design/standard/javascript/exp_passkey.js`.
  A design with its own `user/login.tpl` adds `{include uri='design:user/parts/passkey_login.tpl'}` after its form.
- New templates: `user/passkeys.tpl`, `user/reauth.tpl` (all three designs), `setup/reauth.tpl` (admin, admin4),
  `user/parts/reauth_replay.tpl` (standard). The administration templates use only shared components and tokens.
- `user/edit.tpl` (admin, admin4): a Passkeys card from `expUserAccountOverview` (action key `passkeys`).

## The question before sensitive actions

The check is `expReauth::moduleResult()` in `ezpKernelWeb`, in this order: the policies allow the view, the audit
guard (`expAuditGuard`), "change password at next sign-in" (`expPasswordChangeRequired`), then "Verify it's you",
then the request rules, then the view. REST (`ezpKernelRest`), SOAP, WebDAV and console commands do not run through
it. A view of an extension asks only when a rule names it; see the guide for adding rules.

For extensions:

- `expReauthCodeHandler` (`hasReauthCode( eZUser )`, `checkReauthCode( eZUser, $code )`): a class that checks the
  codes of a second sign-in step, listed in `[ReauthSettings] CodeHandlers[]`. `expReauthTotpCode` does it for
  `sevenx_authentication_2fa`.
- `expReauth::isFresh( $user )` tells whether the user confirmed within the minutes; `expAuditReauth` (the audit
  console's password re-entry) accepts it.
- A POST that is asked is replayed with its own fields after the answer; files are not (a browser cannot send them
  twice), so a view with an upload should be named with `*`.
- `expReauth::addressedUser( $module, $view, $params, $post )` takes the POST as a fourth argument (optional): a POST
  to `collaboration/action` that decides an item of the workflow event *Approve user accounts* addresses the pending
  account, so the users group asks before Approve and Deny of a new user account
  ([Approval of new user accounts](user-account-approval.md)). Content approvals address no account and are not asked.

## Audit

Six new names (the catalogue has 186): `access.passkey.add`, `access.passkey.rename`, `access.passkey.remove`,
`access.passkey.use`, `access.passkey.use.failed`, `access.session.reauth.ask`. `access.session.reauth` and
`access.session.reauth.failed` are raised by "Verify it's you" as well. A passkey sign-in is
`access.session.login` with the handler `expPasskeyLogin`.
