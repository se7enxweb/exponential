# user/password: what changed for templates, settings and session handlers (6.0.15)

The user's guide is [Changing your password](../../features/6.0/modern-password-change.md). This page covers what
a site, a design or an extension may notice after the upgrade.

## Unchanged

- POST fields `oldPassword`, `newPassword` and `confirmPassword`; buttons `OKButton` and `CancelButton`;
  `RedirectURI` and `RedirectOnCancel`; the form action `user/password/<UserID>`.
- The order of the checks: the current password first. When it is wrong, nothing else is reported.
- The redirects, both on Cancel and after a change (there is none after a change: the page is shown again).
- The template variables `$userID`, `$userAccount`, `$oldPasswordNotValid`, `$newPasswordNotMatch`,
  `$newPasswordTooShort` and `$message`.
- The rules a site had: the new `[PasswordSettings]` rules are all off by default.

## Changed

- **`$oldPassword`, `$newPassword` and `$confirmPassword` are always empty.** Old templates printed them into the
  fields' `value` attributes, which wrote typed passwords into the page source. The fields now start empty after a
  failed attempt.
- **When the current password is right, every problem with the new password is reported at once.** Before, a
  mismatch hid the length error. Now `$newPasswordNotMatch` and `$newPasswordTooShort` can both be 1.
- **`$message` is 0 when only one of the new optional rules failed.** A template that knows only the three old flags
  shows no message in that case, instead of its "successfully changed" text. Templates should use
  `$password_changed` and `$has_errors`.
- **The session id is renewed after a change**, and `[PasswordSettings] EndOtherSessions` ends the user's other
  sessions: they are signed out on their next request by the password stamp (below), with every session handler;
  the database handler also deletes them at once.
- **A mail is sent after every change** (`[PasswordSettings] ChangeNotificationMail=enabled`), in the essential
  category `security`. Turn it off with `ChangeNotificationMail=disabled`.
- **The `access.user.password.change.failed` audit event** names the first rule that failed in `after.rule` (before,
  it named only `length`).

## New template variables

| Variable | Type | Meaning |
|---|---|---|
| `$password_changed` | bool | the change went through on this request |
| `$min_length` | int | `[UserSettings] MinPasswordLength` (3 when not set) |
| `$password_rules` | list | `{id, text, min, failed}` for each rule, in display order; `text` is translated |
| `$failed_rules` | list | ids of the rules that failed on this submit |
| `$field_errors` | hash | always the keys `oldPassword`, `newPassword` and `confirmPassword`, each a list of translated sentences |
| `$field_ids` | hash | `password-old`, `password-new` and `password-confirm`, keyed by POST name |
| `$error_summary` | list | `{field, field_id, text}` for each error, in field order (`field` is `''` for an error of the whole form) |
| `$has_errors` | bool | whether the summary has any entries |
| `$sessions_ended` | int or null | how many other sessions the session handler deleted at once; null when it cannot (the PHP handler) |
| `$other_sessions_signed_out` | bool | the other sessions are signed out on their next request (the password stamp, `EndOtherSessions`) |
| `$notification_sent` | bool | whether the mail was handed to the transport |
| `$redirect_uri` | string | where Cancel goes, for a "Continue" link |
| `$password_js_config`, `$password_js_config_json` | hash, string | the configuration for the shared script; print it as `data-exp-password-config="{$password_js_config_json|wash}"` |

The script reads these hooks: `data-exp-password-form` (the form), `data-exp-password="current|new|confirm"`,
`data-exp-password-toggle`, `data-exp-password-rules` / `data-exp-password-rule`, `data-exp-password-meter`,
`data-exp-password-match`, `data-exp-password-generate` and `data-exp-password-summary`.

## Sign-in and currentUser(): the password stamp

- `eZUser::setCurrentlyLoggedInUser()` (a real sign-in, not a `NO_SESSION_REGENERATE` switch) writes the session
  variable `eZUserPasswordStamp`. `eZUser::logoutCurrent()` removes it.
- `eZUser::currentUser()` signs a session out when its stamp belongs to a password that has changed since. That
  includes a change made by an administrator, by content/edit of the account, by "Forgot your password?" or by the
  console. Code that changes a user's password and expects that user's other sessions to stay signed in will see
  them signed out; set `[PasswordSettings] EndOtherSessions=disabled` to keep the old behaviour.
- `eZUser::store()` gives the stored user's own session (when it is the current session's user) the new stamp, and
  updates the user objects the request keeps (`$GLOBALS['eZUserGlobalInstance_…']`), so the session that changes its
  own password stays signed in.
- A session without a stamp (signed in before the upgrade), the anonymous user and accounts with an empty password
  hash (LDAP, text-file and SSO users) are not checked.
- The `UpdateHash` re-hash at sign-in now returns the user with the new hash, and purges that user's cache, which
  before kept the old hash until it expired. The user's other sessions are signed out once after such a re-hash.
- On a mismatch, `currentUser()` reads the stored row once before signing out, because the user cache can be
  older than the password. `eZUser::purgeUserCacheByUserId()` invalidates the cache file in OPcache
  (`opcache_invalidate()`) before deleting it, which also gives the other workers the new roles at once.
- Login handlers in extensions keep working: they sign in through `setCurrentlyLoggedInUser()`. One that keeps
  users without a password should store an empty `password_hash` (type 0), as the LDAP and text-file handlers do.

## The user account datatype and the console

- `eZUserType::validateObjectAttributeHTTPInput()` (registration, content/edit, the administration's user edit)
  checks the password with `expPasswordPolicy::accountErrors()`. With the defaults this is the same length check as
  before, with the same sentence. When `[PasswordSettings]` rules are on, their sentences are joined into the one
  validation error of the field.
- The edit form's "unchanged" placeholder `_ezpassword` is no longer length-checked. Before, a site with
  `MinPasswordLength` above 11 refused every edit of a user that left the password unchanged.
- `bin/php/resetuserpassword.php` refuses what the rules refuse. A generated password (`-g`) is generated again
  until it meets the rules.
- `expPasswordPolicy::validate()` takes an optional third parameter, the login typed in the form (for `not_login`).

## Session handlers

`ezpSessionHandler` has a new method, `deleteOtherSessionsOfUser( $userID, $keepSessionKey )`, which returns the
number of sessions it ended, or null when the handler cannot end them. The base class returns null, so a handler
that does not override it behaves exactly as before. `ezpSessionHandlerDB` overrides it and ends each session
through `destroy()`, so the `destroy_pre` and `destroy_post` callbacks run.

## Translations

The new translation contexts are `kernel/user/password`, `kernel/user/password/js`, `kernel/user/password/mail` and
`design/standard/user/password_changed_mail` (eng-US and ger-DE).
