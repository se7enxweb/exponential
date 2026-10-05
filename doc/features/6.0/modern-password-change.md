# Changing your password

`user/password` is the page where a signed-in user changes their own password, in the administration
(`/admin/user/password`) and on the public site (`/user/password`). Since 6.0.15 the page lists the password's
requirements beside the field, reports every problem next to the field it belongs to, and says clearly when the
change has gone through. After a change the user gets an e-mail saying so. A site can add its own password rules.

## In short

- The form still has three fields: current password, new password, and the new password again. Their names, the
  buttons and the redirects are the same as before, so forms in other designs and extensions keep working.
- The requirements are listed under the new password. The server checks them in every case. With JavaScript, the
  page also ticks them off while you type, shows a strength meter, tells you whether the two new passwords match,
  lets you show or hide each field, and can generate a strong password.
- A failed change names each problem next to its field and in a summary at the top. Each summary entry links to its
  field.
- The page never writes a typed password back into the HTML. Before 6.0.15 it put the typed passwords into the
  fields' `value` attributes.
- After a change you stay signed in, and your session gets a new id. On installations that keep their sessions in
  the database, your other sessions are ended.
- An e-mail, "Your password was changed", goes to the account's address. It never contains the password. It belongs
  to the essential category **Account security**, so nobody can switch it off.
- Every change and every refused change is recorded in the audit trail. The password is never recorded.

## The rules

The length rule has not changed: `site.ini [UserSettings] MinPasswordLength` (10 on a new installation; 3 when the
setting is missing). Length is counted in bytes, as before, so no password that passed before is refused now.

The other rules are in `site.ini [PasswordSettings]`. They are all **off** by default, so an upgrade does not make
the rules any stricter:

| Setting | Rule id | The new password must |
|---|---|---|
| `RequireLowercase=enabled` | `lowercase` | contain a lowercase letter, in any script |
| `RequireUppercase=enabled` | `uppercase` | contain an uppercase letter |
| `RequireDigit=enabled` | `digit` | contain a digit |
| `RequireSymbol=enabled` | `symbol` | contain a character that is neither a letter nor a digit (a space counts) |
| `MinCharacterClasses=N` | `classes` | mix at least N of those four kinds (0 to 4; 0 = off) |
| `ForbidLogin=enabled` | `not_login` | not contain the login name (case is ignored; only logins of 3 or more characters) |
| `ForbidCurrentPassword=enabled` | `not_current` | differ from the current password |

To turn a rule on, use an override, for example `settings/override/site.ini.append.php`:

```ini
[PasswordSettings]
MinCharacterClasses=3
ForbidLogin=enabled
```

The rules live in one class, `expPasswordPolicy` (`kernel/classes/exppasswordpolicy.php`). Its `rules()` method
returns the list, `validate( $password, $user )` returns the ids of the rules a password fails, and `errorText( $id )`
returns the error sentence. A form elsewhere can use the same class to check the same rules.

## After a change

| Setting (`[PasswordSettings]`) | Default | What it does |
|---|---|---|
| `EndOtherSessions` | enabled | gives the current session a new id and ends the user's other sessions |
| `ChangeNotificationMail` | enabled | sends "Your password was changed" to the account's address |
| `StrengthMeter` | enabled | tells the page's script to show the strength meter |
| `GenerateButton` | enabled | tells the page's script to offer "Generate a strong password" ([UserSettings] `GeneratePasswordLength` characters, at least 12) |

Only a session handler that can find all the sessions of one user can end them. The database handler
(`[Session] Handler=ezpSessionHandlerDB`) can. The default PHP handler (`Handler=` empty) and the Symfony handler
cannot. With those handlers the current session still gets a new id, but the other sessions stay open until they
expire. A handler in an extension can add support by overriding `ezpSessionHandler::deleteOtherSessionsOfUser()`.

The mail uses the template `design:user/password_changed_mail.tpl`, which a design can override. Its variables are
`$user`, `$changed_at` (a timestamp), `$ip`, `$sessions_ended` and `$site_name`, and the template may set `$subject`.
The mail goes through the mail gate in the category `security`. A user without a valid address gets no mail.

## The audit trail

| Event | When |
|---|---|
| `access.user.password.change` | a password is changed (unchanged; recorded by `eZUser::store()`) |
| `access.user.password.change.failed` | a change is refused; `after.rule` is `old_password`, `confirmation`, or the id of the first rule that failed |
| `access.session.revoke` (new) | the user's other sessions were ended; `after.count` holds the number |

## For template authors

The page gets new template variables, all optional: `$password_changed`, `$password_rules`, `$field_errors`,
`$error_summary`, `$field_ids`, `$min_length`, `$password_js_config_json` and a few more. Old templates keep
working unchanged. The variables and the `data-exp-password-*` hooks of the shared script are listed in
[the compatibility note](../../bc/6.0/user-password-change.md).
