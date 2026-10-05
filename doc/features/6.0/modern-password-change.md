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
- After a change you stay signed in, and your session gets a new id. Your other sessions (other browsers, other
  devices) are signed out on their next request, whatever session handler the site uses. This also happens when
  the password is changed anywhere else: by an administrator, in content/edit, through "Forgot your password?" or on
  the console.
- The same rules apply to every form that sets a password: registration, editing a user account in content/edit or
  in the administration, and the console command `bin/php/resetuserpassword.php`.
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
returns the error sentence. `accountErrors( $password, $user, $login )` returns the sentences for a user account form; the user account datatype (registration, content/edit, the administration's user edit) and `bin/php/resetuserpassword.php` use it, so every form refuses the same passwords. The length sentence there is the one of before; an unchanged password on the edit form (`_ezpassword`) is not checked.

## After a change

| Setting (`[PasswordSettings]`) | Default | What it does |
|---|---|---|
| `EndOtherSessions` | enabled | gives the current session a new id and ends the user's other sessions (also switches the password stamp, below, on and off) |
| `ChangeNotificationMail` | enabled | sends "Your password was changed" to the account's address |
| `StrengthMeter` | enabled | tells the page's script to show the strength meter |
| `GenerateButton` | enabled | tells the page's script to offer "Generate a strong password" ([UserSettings] `GeneratePasswordLength` characters, at least 12) |

### Other sessions: the password stamp

At sign-in, the session stores a **password stamp** in `eZUserPasswordStamp`, next to `eZUserLoggedInID`. The stamp
is the user id plus a keyed hash (HMAC-SHA256, with a key derived from the site secret) of the user id and the stored
password hash. It never contains the password or its hash, and it changes whenever the password changes.

On every request, `eZUser::currentUser()` compares the session's stamp with the stamp of the user's current password.
It costs no extra database query: the hash comes from the user cache that `currentUser()` reads anyway, and the
comparison runs in constant time (`hash_equals`). When the stamps differ, the session is signed out
(`eZUser::logoutCurrent()`) and `access.session.revoke` is recorded with reason `password_changed`.

- **The session that made the change stays signed in.** `eZUser::store()` gives the session of the user who was
  stored the new stamp, and refreshes the user object that the request keeps.
- **Sessions from before the upgrade stay valid.** A session without a stamp is not checked; it gets a stamp at its
  next sign-in. A deploy does not sign anybody out.
- **Not checked:** the anonymous user; accounts without a password of their own (an empty hash, as for LDAP,
  text-file and single sign-on users); a temporary switch to another user (`eZUser::NO_SESSION_REGENERATE`, used by
  the preview caches); and every session when `EndOtherSessions=disabled`.
- The user cache is a PHP file that is read with `include`, so a server's OPcache can keep an outdated copy until
  it revalidates. When a stamp does not match, the check reads the stored row once, which costs one query and only
  in that case, so an outdated cache never signs out the session that signed in with the new password.
  `eZUser::purgeUserCacheByUserId()` also invalidates the file in OPcache. A change made through the site therefore
  reaches every worker of that server at once. A change made on the console (`resetuserpassword.php`, a script)
  reaches a web server's other sessions when that server's OPcache revalidates (`opcache.revalidate_freq`; 10
  seconds on alpha's Apache, at once on Velocity).
- When `[UserSettings] UpdateHash` re-hashes a password at sign-in (after a change of `HashType`), the user's other
  sessions are signed out once, because the stored hash has changed.

With the database session handler (`[Session] Handler=ezpSessionHandlerDB`), the other sessions are also deleted at
once, at the time of the change (`ezpSessionHandler::deleteOtherSessionsOfUser()`). A handler in an extension can
support this by overriding that method.

### The mail

The mail uses the template `design:user/password_changed_mail.tpl`, which a design can override. Its variables are
`$user`, `$changed_at` (a timestamp), `$ip`, `$sessions_ended`, `$other_sessions_signed_out` and `$site_name`, and
the template may set `$subject`.
The mail goes through the mail gate in the category `security`. A user without a valid address gets no mail.

## The audit trail

| Event | When |
|---|---|
| `access.user.password.change` | a password is changed (unchanged; recorded by `eZUser::store()`) |
| `access.user.password.change.failed` | a change is refused; `after.rule` is `old_password`, `confirmation`, or the id of the first rule that failed |
| `access.session.revoke` (new) | the user's other sessions were ended: deleted at once with the database handler (reason `password_change`, `after.count` the number), or one session signed out on its next request by the password stamp (reason `password_changed`) |

## For template authors

The page gets new template variables, all optional: `$password_changed`, `$password_rules`, `$field_errors`,
`$error_summary`, `$field_ids`, `$min_length`, `$password_js_config_json` and a few more. Old templates keep
working unchanged. The variables and the `data-exp-password-*` hooks of the shared script are listed in
[the compatibility note](../../bc/6.0/user-password-change.md).
