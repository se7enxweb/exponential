# Well-known passwords are refused, and a forgot-password link expires (6.0.15)

Three gaps in how Exponential 6 handles passwords are closed in 6.0.15:

1. The **kickstarter** installed a `kickstart.ini [site_admin] Password=` of any length.
2. The **setup wizard** accepted a well-known word as long as it was long enough, and user/password, registration,
   the user edit form, the console and the web services only checked the length (and the optional
   `[PasswordSettings]` rules).
3. A **"Forgot your password?" link** worked until it was used, however old it was.

## What changes

### One list of well-known passwords

`expWeakPasswordList` (`kernel/classes/expweakpasswordlist.php`) holds the list and the check. It refuses:

- the built-in words: the software's own names and historical defaults (`publish`, `exponential`, `ezpublish`,
  `demo`), account names (`admin`, `administrator`, `root`, `user`, `test`), the classics (`password`, `changeme`,
  `secret`, `welcome`, `letmein`, `qwerty`, `123456` and so on);
- simple variants of a word: any case, white space around it, digits and symbols at the end or the start (`Admin2026!`),
  separators (`change me`, `change_me`), letter swaps (`p@ssw0rd`), common endings (`-s`, `-es`, `-ed`, `-er`, `-ers`,
  `-ing`, `-ings`) and the word typed twice;
- patterns: one character or a short pattern repeated (`aaaaaaaaaa`, `abcabcabc`), runs of the keyboard, the alphabet
  or the digits (`qwertyuiop`, `abcdefghij`, `1234567890`), also with digits or symbols around them.

A password is compared as a whole: a long passphrase that merely contains a word (`admin-of-the-blue-forest-42`) passes.

Every place that sets a new password uses it:

| Where | How |
|---|---|
| setup wizard | the administrator step refuses it: "This password is well-known or too common, so anyone could guess it. Choose another one." |
| kickstarter (`kickstart.ini`) | an empty, well-known or short (`MinPasswordLength`) password is replaced by a random 20-character one, printed once and written to `var/log/initial-admin-password` (mode `0600`) |
| `exp:install` | the same rules as before, now with the shared list (it was a list of nine words) |
| `user/password`, registration, the user edit form | the rule `not_common` of `expPasswordPolicy`, listed beside the field: "Is not a well-known or common password" |
| `exp:resetuserpassword` (`bin/php/resetuserpassword.php`) | "Target password does not validate. The password is well-known or too common. Choose another one." |
| web services (`expaccount`, `expuser`) | 422 with the rules the password fails; `expuser::validatePassword` also returns `errors` |

### Settings (`site.ini [UserSettings]`)

```ini
WeakPasswordCheck=enabled          # disabled: only the length and [PasswordSettings] count, as before
#WeakPasswordList[]=ourcompanyname # more words, in their base form; their variants are refused too
WeakPasswordFile=                  # a file with one word per line (# comments), relative to the root or absolute
ForgotPasswordKeyLifetime=3600     # seconds a forgot-password link works; 0: until it is used, as before
```

The installers always check, whatever `WeakPasswordCheck` says: a new site never starts with a well-known administrator
password.

### Forgot-password links expire

The table `ezforgot_password` has always stored the time a key was made (`time`), so **no database change** is needed.

- A link older than `ForgotPasswordKeyLifetime` (one hour as shipped) is refused with its own message: "This link has
  expired." and a link to ask for a new one; the key is removed. Designs that override `user/forgotpassword.tpl`
  without the new `key_expired` variable show their "The key is invalid or has been used." text, as for any key that
  does not work.
- The mail says how long the link works (`key_lifetime_minutes` in `user/forgotpasswordmail.tpl`).
- Expired keys are removed on every visit of `user/forgotpassword` (`eZForgotPassword::removeExpired()`, one `DELETE`
  on a small table), so no cronjob is needed. The web services (`expaccount::forgotKeyValid`, `reset`) use the same
  lifetime; they had a fixed 24 hours.
- The audit trail records a refused expired link as `access.user.password.reset.failed`, reason `expired`.

API: `eZForgotPassword::lifetime()`, `isExpiredAt( $time, $lifetime, $now )`, `isExpired()`, `expiresAt()`,
`removeExpired()`.

## Compatibility

- **Nobody is locked out.** Only a new password is checked. Signing in with an existing password, however weak, works
  as before. A user whose password is on the list is refused only when they choose a new one.
- **Scripts and integrations** that set passwords through the forms, the console or the web services and use a
  well-known password (in tests, for example) get a refusal now. Use a real password, or set
  `WeakPasswordCheck=disabled` in that environment's override.
- **Templates:** `user/password.tpl` lists the rules it is given, so the new rule appears without a template change.
  The setup wizard's `setup/init/site_admin.tpl` has the new flag `password_too_common`.
- **Forgot-password links** already sent before the upgrade expire one hour after they were made. To keep the old
  behavior, set `ForgotPasswordKeyLifetime=0` in an override.
- `eZStepSiteAdmin::isWeakDefaultPassword()` keeps its name and now uses the shared list;
  `eZStepSiteAdmin::kickstartPasswordProblem()` and `minAdminPasswordLength()` are new.
- **Public demos:** with the environment variable `EXP_DEMO_MODE=1` the installers accept a well-known administrator
  password (the length rule stays), so a demo can install the password it publishes; administrator passwords then
  cannot be changed or reset. Only for public demos: [Demo mode](demo-mode.md).

## Tests

- `expWeakPasswordListTest` (no database): every built-in word and its variants, the patterns, strong passwords and
  generated ones that pass, the extra list and file, the switch, the `not_common` rule of `expPasswordPolicy`, the
  installers.
- `eZForgotPasswordExpiryTest` (no database): the lifetime setting, the expiry boundary, the key object, the shipped
  settings and templates.
- `eZSetupStepsTest`, `InstallPasswordTest`, `expPasswordPolicyTest` (the account form and the console refuse a
  well-known password).
