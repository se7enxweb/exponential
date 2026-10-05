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
  sessions. That works only with a handler that can do it (see below).
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
| `$sessions_ended` | int or null | how many other sessions were ended; null when none could be ended |
| `$notification_sent` | bool | whether the mail was handed to the transport |
| `$redirect_uri` | string | where Cancel goes, for a "Continue" link |
| `$password_js_config`, `$password_js_config_json` | hash, string | the configuration for the shared script; print it as `data-exp-password-config="{$password_js_config_json|wash}"` |

The script reads these hooks: `data-exp-password-form` (the form), `data-exp-password="current|new|confirm"`,
`data-exp-password-toggle`, `data-exp-password-rules` / `data-exp-password-rule`, `data-exp-password-meter`,
`data-exp-password-match`, `data-exp-password-generate` and `data-exp-password-summary`.

## Session handlers

`ezpSessionHandler` has a new method, `deleteOtherSessionsOfUser( $userID, $keepSessionKey )`, which returns the
number of sessions it ended, or null when the handler cannot end them. The base class returns null, so a handler
that does not override it behaves exactly as before. `ezpSessionHandlerDB` overrides it and ends each session
through `destroy()`, so the `destroy_pre` and `destroy_post` callbacks run.

## Translations

The new translation contexts are `kernel/user/password`, `kernel/user/password/js`, `kernel/user/password/mail` and
`design/standard/user/password_changed_mail` (eng-US and ger-DE).
