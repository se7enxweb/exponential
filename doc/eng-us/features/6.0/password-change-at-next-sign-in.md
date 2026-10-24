# Change password at next sign-in

An administrator can mark a user account so that, after the user's next sign-in, the user must set a new password
before doing anything else. The usual case: you create an account for an editor with a temporary password, give the
password to the editor by a second channel (a phone call, a letter, a chat other than the e-mail with the login), and
the editor replaces it with a password that only they know.

## In short

- The account edit form has a checkbox **Change password at next sign-in**. It is ticked by default when an
  administrator creates an account for someone else. On an existing account it shows whether the account is marked;
  untick it to remove the mark.
- After the next sign-in the user is sent to the password change form (`user/password`) instead of the page they
  asked for, with the note "Choose a new password before you continue: an administrator asked you to replace the
  password you have now." Every other page sends them back there until the password is changed. This holds on every
  siteaccess: the administration (admin, admin4, admin4l), the editor and the public site.
- Changing the password removes the mark. The next sign-in is normal.
- The user can still sign out (`user/logout`).
- Two-step sign-in comes first. When it is required, the user sets it up or enters the code as usual, and is then
  sent to the password change.
- The REST interface answers `403` with the error `password_change_required` until the password is changed. SOAP
  refuses the sign-in. Console commands are not affected.
- In demo mode (`EXP_DEMO_MODE=1`) administrator passwords cannot change, so the mark is ignored for administrators.
  It would otherwise lock them out. Other accounts are still sent to the password change.
- Setting the mark, removing it and every redirect are recorded in the audit trail.

## Setting the mark

### In the administration

Open the user in the administration and edit it, or create a new user. Below the account fields (username, password,
e-mail) is the checkbox **Change password at next sign-in**:

- **New account**: ticked by default. Leave it ticked when you give the person a temporary password.
- **Existing account**: ticked when the account is marked. Tick it to mark the account, untick it to remove the mark.

The mark takes effect when you publish. The checkbox is not shown on your own account (you change your own password
at `user/password`) and not on the public registration form.

The mark applies at the user's **next** sign-in. A user who is signed in when you set it is sent to the password
change after they sign in again.

### On the console

```bash
./console exp:user-password-change-required status --login=editor1
./console exp:user-password-change-required set --login=editor1
./console exp:user-password-change-required clear --user-id=14
./console exp:user-password-change-required status --login=editor1 --json
```

`status` shows whether the account is marked and whether the mark is enforced at sign-in (it is not for an
administrator in demo mode). The exit code is 0 when done, 1 for an unknown user and 2 for a usage error.

## What the user sees

1. The user signs in with the temporary password (and the second step, when two-step sign-in is on).
2. The browser goes to `user/password`. The note above the form says why.
3. The user enters the temporary password as the current password and chooses a new one. The site's password rules
   apply as always ([Changing your password](modern-password-change.md)); with `[PasswordSettings]
   ForbidCurrentPassword=enabled` the new password cannot be the temporary one.
4. After the change the page confirms it, and every page opens as usual.

Until then, any other address (a bookmark, the dashboard, a content page, an Ajax call) answers with a redirect to
`user/password`. The answer is sent with `Cache-Control: private, no-store`, so no cache keeps it.

## Settings

`site.ini [PasswordChangeRequiredSettings]`:

```ini
[PasswordChangeRequiredSettings]
# The views that stay open while an account is marked: module/view, or a module alone for all its views
ExemptViews[]
ExemptViews[]=user/password
ExemptViews[]=user/logout
ExemptViews[]=user2fa
```

`user/password` is always open, whatever the list says. `user2fa` is the two-step sign-in of the
`sevenx_authentication_2fa` extension. An extension with views a marked user must reach adds them in its own
`site.ini.append.php`.

## Where the mark is kept

The mark is a user preference, `exp_password_change_required`, in the `ezpreferences` table. Its value is the time it
was set; an empty value means no mark. No table or column was added, so the feature works on every database engine
without an upgrade script. The page `user/preferences` refuses to write this preference, so a user cannot set or
remove it through the preference links.

## REST and SOAP

A REST request with the credentials of a marked account (basic authentication, an OAuth token or a personal API key)
gets:

```http
HTTP/1.1 403 Forbidden
Content-Type: application/json; charset=UTF-8

{"error":"password_change_required","error_description":"This account must set a new password before it can use the REST interface. Sign in to the site and change the password (user/password)."}
```

The request is not signed in. The user changes the password in the browser; the next REST request then works. SOAP
refuses the sign-in of a marked account in the same way.

## Audit

| Event | When | Details |
|---|---|---|
| `access.user.password.change_required.set` | an account is marked | `after.source`: `edit` or `console` |
| `access.user.password.change_required.clear` | the mark is removed | `after.source`: `password` (the user changed it), `edit` or `console` |
| `access.user.password.change_required.redirect` | a marked account is sent to `user/password`, or its REST or SOAP sign-in is refused | `after.channel`: `web`, `rest` or `soap`; `after.view` for the web |

All three are in the `access` channel and on by default. See the [audit trail](audit-trail.md) and the
[event catalogue](../../bc/6.0/audit.md).

## For developers

- `expPasswordChangeRequired` (`kernel/classes/user/exppasswordchangerequired.php`): `isMarked()`, `isRequired()`
  (the mark, except for an administrator in demo mode), `set()`, `clear()`, `exemptViews()`, `isExempt()`.
- The check runs in `ezpKernelWeb::dispatchLoop()` before every view, after the policy check and the audit guard and
  before the request rules.
- The template attributes `password_change_required` (the account is marked) and `password_change_checked` (the edit
  form ticks the box) of a user account. A design with its own `content/datatype/edit/ezuser.tpl` adds the checkbox
  as `design/standard` does: the hidden field `<base>_data_user_password_change_shown_<id>` with value `1` and the
  checkbox `<base>_data_user_password_change_required_<id>` with value `1`. Without the hidden field nothing changes.
- A design with its own `user/password.tpl` shows the note when the template variable `password_change_required` is
  true.

See also: [Upgrade note](../../bc/6.0/password-change-at-next-sign-in.md), [Changing your password](modern-password-change.md),
[Demo mode](../../bc/6.0/demo-mode.md).
