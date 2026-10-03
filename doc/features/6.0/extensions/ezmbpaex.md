# ezmbpaex: password expiry and validation

This page is for administrators who need passwords to expire or to follow a rule. `ezmbpaex` ("eZ MB Password
Expiry") does three things:

- It sets how long a password is valid. When it has expired, the user must create a new one at the next login.
- It can warn the user by mail before the password expires.
- It can check new passwords against a regular expression.

Its module is `userpaex` (views `password`, `forgotpassword` and `editpaex`), and it has two cronjob parts.

## Set it up

1. Activate the extension.
2. Set the rules in `settings/override/mbpaex.ini.append.php` (see the table).
3. Add the two cronjob parts to cron:

```bash
php runcronjobs.php ezmbpaex_send_expiry_notifications
php runcronjobs.php ezmbpaex_updatechildren
```

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `mbpaex.ini` | `mbpaexSettings` | `PasswordValidationRegexp` | empty | Regular expression a new password must match; empty accepts any |
| `mbpaex.ini` | `mbpaexSettings` | `DefaultPasswordLifeTime` | `3` | Days a password is valid unless the user group or user says otherwise; `0` means passwords never expire |
| `mbpaex.ini` | `mbpaexSettings` | `ExpirationNotification` | `172800` | How long before expiry the notification mail is sent; the shipped value is two days in seconds (check `eZPaEx::fetchExpiryNotificationPendingList()` before changing it) |
| `mbpaex.ini` | `mbpaexSettings` | `ForgotPasswordHashLifeTime` | `86400` | Seconds a forgot-password link is valid (one day) |
| `mbpaex.ini` | `mbpaexSettings` | `UpdateChildrenUser` | `admin` | Login that runs the child-update cronjob |

The extension also writes to the audit log (`audit.ini`); see [Audit trail](../../../bc/6.0/audit.md).

## Security fixes in the Exponential 6 releases

**6.0.2 (27 September 2026): the forgot-password page no longer tells whether an address has an account.** The
`userpaex/forgotpassword` view answered an unknown address with "There is no registered user with that email
address", so anyone could test addresses one by one. It now works like the kernel's `user/forgotpassword`:

- an unknown but valid address gets the same page as a known one ("if an account is registered with the address, a
  mail has been sent to it");
- input that is not an email address gets "Please enter a valid email address.";
- an address posted as an array is treated as not given, instead of raising an error;
- the failed attempt is still written to the audit log;
- the template uses the kernel's strings and context `design/standard/user/forgotpassword`, so existing translations
  apply, and it escapes every address it prints and the hash key it carries.

**6.0.3 (29 September 2026): the key in a forgot-password link carries 128 random bits** from the system's secure
source (32 hex characters, so stored keys and links keep their shape). Before, it was an `md5` of the current time and
`mt_rand()`; since the time is known to anyone who asked for the link, the key could be found by trying.

**6.0.4:** commands and cronjob parts list a description; copyright notices name 1998 - 2026 7x & Exponential
Foundation first.

Links issued before the update keep working until they expire.

## Related pages

- [ezwebin and ezdemo](ezwebin.md): the designs received the same forgot-password fix
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md#2-forgot-password-pages-no-longer-tell-whether-an-address-has-an-account)
- [Reset a user's password](../reset-user-password.md)
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronicle](../../../history/extensions/ezmbpaex.md) and [release notes](../../../changelogs/extensions/ezmbpaex.md)
- [Change ledger](../../../history/ledger/ezmbpaex.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
