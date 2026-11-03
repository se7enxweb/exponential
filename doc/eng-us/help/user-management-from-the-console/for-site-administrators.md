---
title: "For site administrators"
audience: admin
category: administration
order: 16
summary: What the exp:user commands give a site administrator who already manages accounts in the administration - when the console is faster, the commands for daily work, the safety rules, and a weekly routine.
keywords: site administrator, exp:user, daily work, console or administration, when to use the console, weekly routine, account review, dry run, protected accounts, audit, hygiene
type: help
---
# For site administrators

Everything here can also be done in the administration. The console is faster when you work on many accounts, when
you are on the server anyway, or when the administration is out of reach.

## What you'll have at the end

A clear idea of when to use the console, and the handful of commands you need every week.

## When the console helps

| Situation | Command |
|---|---|
| Nobody can sign in to the administration | [Recover a locked-out administrator](recover-a-locked-out-administrator.md) |
| Someone leaves today | [Offboard someone who leaves](offboard-someone-who-leaves.md) |
| Hundreds of registrations wait | [Approve a backlog of registrations](approve-a-backlog-of-registrations.md) |
| A list of new members arrives as a spreadsheet | [Bulk-import members from CSV](bulk-import-members-from-csv.md) |
| You need to know who has which role | `exp:user:roles list <user>` ([Groups and roles](groups-and-roles.md)) |
| Someone asks what happened to their account | `exp:user:audit <user>` ([Audit and hygiene](audit-and-hygiene.md)) |

## The rules that keep you safe

- Nothing changes without `--apply`. Run every command once without it, read the output, then add `--apply`.
- Protected accounts are refused: the anonymous user, the `UserCreatorID` user, `admin`, yourself and the last
  enabled administrator.
- A bulk change needs `--confirm` with the count of its dry run.
- Every change is in the user audit log, with your name when you pass `--as=<your login>`.

**Note:** Pass `--as=<your login>` for changes you make yourself. Without it, the audit records the user of
`[UserSettings] UserCreatorID`.

## A weekly routine

1. Run `./console exp:user:hygiene`.
2. Run `./console exp:user:pending` and decide each waiting registration.
3. Run `./console exp:user:list --state=locked` and unlock the people you know.
4. Disable the accounts that have not signed in for a year, after a dry run:
   `./console exp:user:bulk disable --state=inactive --days=365`.

## Related pages

- [User management from the console](README.md)
- [Check user accounts](../../features/6.0/user-hygiene.md): the same review in the administration.
