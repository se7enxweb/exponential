# Change password at next sign-in: what an upgrade brings (6.0.15)

The user's guide is [Change password at next sign-in](../../features/6.0/password-change-at-next-sign-in.md). This
page covers what a site, a design or an extension may notice after the upgrade.

## Nothing changes until an account is marked

- No table and no column is added. The mark is a row in `ezpreferences` (name `exp_password_change_required`), so
  there is no update script to run, on any database engine.
- Existing accounts carry no mark. Signing in works as before until an administrator marks an account.
- New accounts that an administrator creates in the administration are marked **by default** (the checkbox is ticked
  on the form). Untick it when the person already has a password of their own. Accounts created by self-registration,
  by the console, by imports or by code are not marked.

## For designs and extensions

- **`content/datatype/edit/ezuser.tpl`**: the checkbox is in `design/standard` and `design/admin4` (admin4l and the
  editor siteaccess use the admin4 one; admin and admin3 the standard one). A design or extension with its own copy
  of the template shows no checkbox until it adds the two fields described in the guide; without them, publishing
  the form leaves the mark as it is.
- **`user/password.tpl`**: the new template variable `$password_change_required` is true while the user is there
  because of the mark. `design/standard`, `design/admin` and `design/admin4` show a note; another design's copy of the
  template works unchanged but shows no note.
- **Views a marked user must reach**: every view except those in `site.ini [PasswordChangeRequiredSettings]
  ExemptViews[]` (`user/password`, `user/logout`, `user2fa`) answers with a redirect to `user/password` for a marked,
  signed-in user, Ajax calls (`ezjscore/call`) included. An extension whose views must stay open adds them to the
  list.
- **`user/preferences`** refuses to write the preference `exp_password_change_required`.
- **REST**: a marked account gets `403` with `{"error":"password_change_required"}` from every route that needs
  authentication, also with an OAuth token or a personal API key. A client that treats every 403 as "no permission"
  should show the `error_description` instead.
- **WebDAV and SOAP** refuse the sign-in of a marked account.
- **Demo mode** (`EXP_DEMO_MODE=1`): the mark of an administrator is kept but ignored.

## New

- Class `expPasswordChangeRequired` (`kernel/classes/user/exppasswordchangerequired.php`).
- Class `expRestPasswordChangeRequiredStatus` (`kernel/private/rest/classes/status/password_change_required.php`).
- `eZUser` attributes `password_change_required` and `password_change_checked`, and the property
  `PasswordChangeRequiredDraft` (the form's choice while the object is a draft; kept in the draft as
  `password_change_required`).
- `eZUserType::postedPasswordChangeRequired()`.
- `site.ini [PasswordChangeRequiredSettings] ExemptViews[]`.
- Console command `exp:user-password-change-required` (`bin/php/userpasswordchangerequired.php`).
- Audit events `access.user.password.change_required.set`, `.clear` and `.redirect` ([catalogue](audit.md)).

## Checking an installation

```bash
./console exp:user-password-change-required status --login=<login>
./console exp:audit tail --channel=access --name='access.user.password.change_required.*' --lines=20
```
