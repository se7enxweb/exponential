# ezmbpaex: password expiry and validation

`ezmbpaex` ("eZ MB Password Expiry") defines how long a password is valid. When a password has expired, the user is asked to create a new
one at the next login; before it expires the user can be warned by mail. It can also check new passwords against a regular expression.
Its module is `userpaex` (`password`, `forgotpassword` and `editpaex`), with two cronjob parts.

## Settings (`mbpaex.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| mbpaexSettings | `PasswordValidationRegexp` | empty | Regular expression a new password must match; empty accepts any |
| mbpaexSettings | `DefaultPasswordLifeTime` | `3` | Days a password is valid unless the user group or user says otherwise; `0` means passwords never expire |
| mbpaexSettings | `ExpirationNotification` | `172800` | How long before expiry the notification mail is sent; the shipped 172800 is two days expressed in seconds (check `eZPaEx::fetchExpiryNotificationPendingList()` before changing it) |
| mbpaexSettings | `ForgotPasswordHashLifeTime` | `86400` | Seconds a forgot-password link is valid (one day) |
| mbpaexSettings | `UpdateChildrenUser` | `admin` | Login that runs the child-update cronjob |

Cronjob parts (add them to your cron):

```bash
php runcronjobs.php ezmbpaex_send_expiry_notifications
php runcronjobs.php ezmbpaex_updatechildren
```

The extension also writes to the audit log (`audit.ini`); see [Audit trail](../../../bc/6.0/audit.md).

## Security fixes in the Exponential 6 releases

* **6.0.2 (27 September 2026)**: the `userpaex/forgotpassword` view no longer tells whether an address has an account. It answered an unknown
  address with "There is no registered user with that email address", so anyone could test addresses one by one. It now works like the kernel's
  `user/forgotpassword`: an unknown but valid address gets the same page as a known one ("if an account is registered with the address, a mail has
  been sent to it"), input that is not an email address gets "Please enter a valid email address.", and an address posted as an array is treated as
  not given instead of raising an error. The failed attempt is still written to the audit log. The template uses the kernel's strings and context
  `design/standard/user/forgotpassword`, so existing translations apply, and escapes every address it prints and the hash key it carries.
* **6.0.3 (29 September)**: the key in a forgot-password link carries **128 random bits** from the system's secure source (32 hex characters, so stored
  keys and links keep their shape). Before it was an `md5` of the current time and `mt_rand()`, and since the time is known to anyone who asked for the
  link, the key could be found by trying.
* 6.0.4: commands and cronjob parts list a description; copyright notices name 1998 - 2026 7x & Exponential Foundation first.

Links issued before the update keep working until they expire.

## Related

* [ezwebin and ezdemo](ezwebin.md): the designs received the same forgot-password fix
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md#2-forgot-password-pages-no-longer-tell-whether-an-address-has-an-account)
* [Chronicle](../../../history/extensions/ezmbpaex.md) and [release notes](../../../changelogs/extensions/ezmbpaex.md)
