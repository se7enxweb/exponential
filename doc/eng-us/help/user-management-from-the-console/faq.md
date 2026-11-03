---
title: "User management from the console: FAQ"
audience: admin, developer, hosting
category: administration
order: 20
summary: Short answers about the exp:user commands - permissions, passwords, dry runs, protected accounts, removing versus disabling, the audit, MongoDB, and what is deliberately not offered - merging accounts, passwords in import files and a --password option.
keywords: exp:user, FAQ, questions, merge accounts, --password, password in CSV, dry run, --apply, protected account, who may run it, audit, disable or delete, deactivate and move, resetuserpassword, MongoDB, siteaccess
type: help
---
# User management from the console: FAQ

## What you'll have at the end

An answer to your question, or a link to the page that has it.

## Using the commands

**Does a command change anything without `--apply`?** No. Without `--apply` it lists what it would do and which
checks fail. `--dry-run` means the same; both together are a usage error.

**Who may run the commands?** Whoever may run `./console` on the server. The commands do not check the policies of
the account they act as, so keep shell access to the people who administer the site.

**Whose name does the audit record?** The user of `[UserSettings] UserCreatorID`, or the one you name with `--as`.
`exp:user:whoami` shows it.

**Do workflows and approval run?** Yes. The commands use the same operations as the administration, so workflows,
the approval of new accounts, mails and audit events behave as when you click.

**How do I name an account?** By its login, its e-mail address (when only one account has it) or its id.

**Does it work on every database?** The lists are read with SQL. On a MongoDB installation `exp:user:list` and the
commands built on it report that they cannot list.

## Passwords

**Why is there no `--password` option?** A password on the command line stays in the shell history and shows in the
process list of the server. Use `--password-stdin`, a generated password, or a mail that lets the person choose one.

**Why does the import refuse a file with a password column?** Such a file would spread passwords in clear text
through mails, shared folders and backups. Import with `--send-activation` or `--disabled` and let each person
choose a password.

**Is a generated password stored anywhere?** Only its hash, like any password. It is printed once and never again,
also not in the audit.

**What about `bin/php/resetuserpassword.php`?** It still works. `exp:user:reset-password` adds the reset link, the
dry run, the audit and the JSON output.

## Disabling and removing

**Disable or remove?** Disable. A disabled account keeps the name in the history of everything the person did, and
`exp:user:enable` undoes it. Remove an account only when nothing refers to it.

**Can I bring back a removed account?** No. User accounts never go to the trash.

**Why can I not disable the `admin` account?** It is a protected account, like the anonymous user, the
`UserCreatorID` user, yourself and the last enabled administrator.
[Protected accounts](disable-enable-and-remove-accounts.md#protected-accounts).

**Does the console do "Deactivate and move"?** No. It only disables. Use the administration to move an account into
the group for deactivated accounts.

## Deliberately not offered

**Can I merge two accounts?** No. Merging would move the ownership of content, versions, role assignments and the
history from one account to another. The kernel has no safe way to do that, so no command offers it. Disable the
account that is not needed.

**Passwords in import files** and **a `--password` option** are not offered, for the reasons above.

## Related pages

- [When a command fails](when-a-command-fails.md)
- [User management from the console](README.md)
