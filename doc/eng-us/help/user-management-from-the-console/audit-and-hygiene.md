---
title: "Audit and hygiene"
audience: admin, developer, hosting
category: administration
order: 9
summary: exp:user:audit shows the user audit log rows about an account or by it, filtered by event, result and dates; exp:user:hygiene counts the accounts per state, each with the exp:user:list command that lists them.
keywords: exp:user:audit, exp:user:hygiene, user audit log, account history, who changed what, events, refused, failed, hygiene, check user accounts, inactive accounts, never signed in, locked, orphaned, duplicate logins, duplicate e-mail addresses, unactivated, review
type: help
---
# Audit and hygiene

`exp:user:audit` answers what happened to one account. `exp:user:hygiene` answers which accounts need attention.
Both only read.

## What you'll have at the end

The history of an account, and a list of the accounts to tidy up.

## The history of an account

```bash
./console exp:user:audit jane
./console exp:user:audit jane --event=access.user.password --from=2026-10-01
./console exp:user:audit jane --result=refused --limit=50
```

The command lists the rows of the user audit log (**Users > User audit log**) about the account or done by it: the
time, the event, the result, the actor and the siteaccess.

| Option | Meaning |
|---|---|
| `--event` | an event name or its beginning, for example `access.user.password` |
| `--result` | `success`, `refused` or `failed` |
| `--from`, `--to` | days as `YYYY-MM-DD` |
| `--limit`, `--offset` | page size and the rows to skip |

Use it when someone asks why their account changed, or when a sign-in looks suspicious.

## The state of all accounts

```bash
./console exp:user:hygiene
./console exp:user:hygiene --days=365
```

The command shows the counts of **Users > Check user accounts**: the accounts per state.

| State | Accounts |
|---|---|
| enabled, disabled | can or cannot sign in |
| unactivated | registered, never activated |
| pending | registrations that wait |
| locked | too many wrong passwords |
| never signed in | created, never used |
| inactive | not signed in for `--days` days (default 180) |
| orphaned | no user object, never published, or no location |
| duplicate logins, duplicate e-mail addresses | shared with another account |

Each count comes with the `exp:user:list` command that lists those accounts. Copy it to see them, then act with the
single commands or with `exp:user:bulk`.

## A regular review

1. Run `./console exp:user:hygiene`.
2. List the accounts of each count that is not 0, with the command shown next to it.
3. Disable the accounts nobody needs ([Bulk changes](import-export-and-bulk-changes.md#bulk-changes)).
4. Unlock or reset the locked ones that belong to active people.
5. Ask the owners of duplicate e-mail addresses which account they use.

## Related pages

- [The user audit log](../the-user-audit-log.md): the same log in the administration.
- [Check user accounts](../../features/6.0/user-hygiene.md): the page and its filters.
- [User audit log](../../features/6.0/user-audit.md): every event and setting.
