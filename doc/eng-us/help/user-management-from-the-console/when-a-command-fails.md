---
title: "When a command fails"
audience: admin, developer, hosting
category: administration
order: 21
summary: Troubleshooting the exp:user commands by symptom - nothing changed, an account or group not found or ambiguous, a protected account, a publish that waits, a password from standard input, the wrong host in a link, a bulk count that does not match, an import that refuses rows, and the exit codes 2, 3 and 4.
keywords: exp:user, troubleshooting, error, FAIL, exit code, nothing changed, no account has the login, accounts share the e-mail address, user groups are named, protected, last enabled account with every right, the publish waits, never activated, standard input holds no password, --confirm, count does not match, import refused, database not reachable
type: help
---
# When a command fails

Every command ends with `PASS`, or with `FAIL (exit <code>)` and the reasons above it. Start with the exit code.

## What you'll have at the end

The cause of the failure and the fix.

## By exit code

| Exit code | Meaning | What to do |
|---|---|---|
| 1 | failed or refused | read the `FAIL:` lines; the tables below explain them |
| 2 | usage | an option or value is wrong; run the command with `--help` |
| 3 | the database is not reachable | check the database server and `site.ini [DatabaseSettings]`, or run with the right `--siteaccess` |
| 4 | internal error | look into `var/log/error.log` and report it with the command you ran |

## Nothing changed

| What you see | Why, and what to do |
|---|---|
| `Would change (dry run, nothing changed; --apply makes the change)` | The command ran as a dry run. Add `--apply`. |
| `--apply and --dry-run exclude each other` | Pass only one of them. |
| `the account is disabled already: nothing to do` | A warning, not an error. The account is in the state you asked for. |

## The account or group is not found

| What you see | Why, and what to do |
|---|---|
| `no account has the login ...` | Check the spelling, or use the id from `exp:user:list --search=...`. A login is compared in any case. |
| `2 accounts share the e-mail address ...` | Name the account by its login or id. `exp:user:list --state=duplicate_email` lists such accounts. |
| `2 user groups are named ...` | Name the group by its node id. `exp:user:groups` shows the node ids. |
| `node ... is not a user group` | The node id belongs to something else. Use `exp:user:groups` to find the group. |
| `--as: ...` or `UserCreatorID ... names no account` | The user who acts does not exist. Name an existing account with `--as`. |

## The command refuses

| What you see | Why, and what to do |
|---|---|
| `this is the last enabled account with every right` | Enable or create another administrator first. |
| `the administrator account ... is protected`, `the anonymous user ...`, `the user who acts ...` | A protected account is never disabled or removed ([Protected accounts](disable-enable-and-remove-accounts.md#protected-accounts)). |
| `the account was never activated` on enable | Use `exp:user:approve <user> --apply`. |
| `the registration does not wait` on approve | The account is active already, or has no registration that waits. Check it with `exp:user:show`. |
| A group cannot be deleted | It still has members or subgroups. Move them first. |
| A bulk change refuses `--confirm` | The count no longer matches what the filters select. Run the dry run again and use the new count. |
| An import refuses the file | It has a password column, or neither or both of `--send-activation` and `--disabled`. |
| An import refuses rows | Each refused row has its line and reasons: a login or address that is not valid, or a group that does not exist. Fix the rows and import again; existing logins are skipped. |
| `exp:user:2fa --reset` cannot reset | The extension `sevenx_authentication_2fa` is not active. |

## Passwords and mails

| What you see | Why, and what to do |
|---|---|
| `--password-stdin: standard input holds no password` | Pipe or redirect the password into the command. An interactive terminal waits for one line. |
| `choose exactly one of --send-link, --generate and --password-stdin` | `exp:user:reset-password` needs one way to the password. |
| A password check fails | The password does not pass the password rules. Use `--generate`, or a longer password. |
| The reset or activation link points at the wrong host | The link is built from the siteaccess. Run the command with `--siteaccess=<the public siteaccess>`. |
| `the publish waits` after create | A workflow holds the account, usually the approval of new accounts: `exp:user:pending`, then `exp:user:approve`. |

## Related pages

- [User management from the console: FAQ](faq.md)
- [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md): `errors` and `warnings` for scripts.
