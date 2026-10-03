# Reset a user password from the command line

This page is for administrators who have lost the administrator password, have taken over an installation, or have
imported users. `bin/php/resetuserpassword.php` sets a new password for any user. It was added on 11 July 2026
(`ea77927c23`).

The new password is hashed by the same code as the user edit form (`eZUser::setInformation`), and it must satisfy
`site.ini [UserSettings] MinPasswordLength`.

## Reset the admin password now

As the operating system root user, generate a new 20-character password for `admin`:

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -g -l 20
```

The generated password is printed once. Note it, sign in, and change it in the admin if you like.

## Two ways to authorise

**As the operating system root user** (no Exponential login needed):

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -p 'NEW_PASSWORD'
```

`--allow-root-user` (short `-r`) requires that you really are the OS user `root`; otherwise the script stops with an
error.

**As an administrator of the site**:

```bash
php bin/php/resetuserpassword.php -a admin -ap 'YOUR_ADMIN_PASSWORD' -u editor1 -p 'NEW_PASSWORD'
```

The login given with `-a` and `-ap` must authenticate and must hold the `Administrator` role.

## Options

| Option | Meaning | Default |
|---|---|---|
| `-a <login>` | Administrator login that authorises the reset | none |
| `-ap <password>` | Password of that administrator | none |
| `-r`, `--allow-root-user` | Skip administrator authentication; OS root only | off |
| `-u <login>` | Login of the user to change | `admin` |
| `-p <password>` | The new password | none |
| `-g`, `--generate` | Generate a random password instead of `-p` | off |
| `-l <length>` | Length of a generated password | `16` |
| `-s`, `--siteaccess` | Siteaccess (and so database) to use | default access |
| `-q`, `--quiet` | Only errors are printed | off |
| `-h`, `--help` | Help | |

## Errors you may meet

| Message | Cause |
|---|---|
| `Admin authentication required` | Neither `-a`/`-ap` nor `--allow-root-user` was given. |
| `Admin authentication failed.` | Wrong login or password after `-a`. |
| `... does not have the Administrator role.` | The authorising user is not an administrator. |
| `Target user not found: <login>` | `-u` names a login that does not exist. |
| `Target password does not validate. It must be at least N characters long.` | Shorter than `MinPasswordLength`. |

## Good practice

A password given on the command line is visible in the process list and in your shell history. Prefer `-g` and change
the password in the admin after the first sign-in, or clear the history afterwards.

## Related pages

- [The August 2026 security patches (sign-in checks)](../../specifications/6.0/security-hardening-2026-08.md)
- [Security defaults of September 2026](../../specifications/6.0/security-defaults-2026-09.md)
- [Exponential Console](../../bc/6.0/console.md)
- [Audit trail](audit-trail.md)
- [Chronicle: July 2026](../../history/2026/2026-07.md)
