---
title: "User management from the console"
audience: admin, developer, hosting
category: administration
order: 1
summary: The start of everything about the exp:user commands in Exponential 6.0.15 - list, create, change, disable and remove user accounts, reset passwords, approve registrations, manage groups, roles, sessions and two-step sign-in, import, export and audit accounts from a shell, a deploy script or a CI job, exactly as the administration does.
keywords: user management, console, command line, CLI, exp:user, exp:user:list, exp:user:create, exp:user:edit, exp:user:disable, exp:user:delete, exp:user:reset-password, exp:user:approve, exp:user:groups, exp:user:roles, exp:user:import, exp:user:export, exp:user:bulk, user accounts, shell, script, CI, onboarding, offboarding, dry run, --apply
featured: true
type: help
---
# User management from the console

The `exp:user` commands manage the user accounts of an installation from a shell, a deploy script or a CI job. Each
command does what the administration does when you click: the same operations, workflows, checks, mails and audit
events. Nothing changes until you add `--apply`.

Use them when you look after many accounts, when you work on the server without a browser, when a script sets up an
installation, or when an administrator is locked out.

## Contents

1. [Start here](#start-here)
2. [The commands](#the-commands)
3. [Three rules every command follows](#three-rules-every-command-follows)
4. [Tasks](#tasks)
5. [More](#more)

## Start here

- **First time**: read the tutorial [User management from the console](../../guides/user-management-from-the-console.md).
  It walks through finding, creating, changing and removing accounts.
- **By role**: [For site administrators](for-site-administrators.md),
  [For developers: scripts and CI](for-developers-scripts-and-ci.md) and
  [For agencies and hosting](for-agencies-and-hosting.md).
- **A quick answer**: the [FAQ](faq.md). **An error**: [When a command fails](when-a-command-fails.md).
- What the feature adds: [User management from the console](../../features/6.0/user-management-from-the-console.md).

## The commands

Every command prints all its options with `--help`. `<user>` is a login, an e-mail address (when only one account
has it) or the account's id.

| Command | What it does | Page |
|---|---|---|
| `exp:user:list`, `exp:user:show` | find and inspect accounts | [Find, create and change accounts](find-create-and-change-accounts.md) |
| `exp:user:create`, `exp:user:edit` | create an account, change its address, login, names and groups | [Find, create and change accounts](find-create-and-change-accounts.md) |
| `exp:user:disable`, `exp:user:enable`, `exp:user:delete` | stop an account, start it again, remove it for good | [Disable, enable and remove accounts](disable-enable-and-remove-accounts.md) |
| `exp:user:reset-password`, `exp:user:require-password-change`, `exp:user:check-login` | passwords and sign-in checks | [Passwords and sign-in checks](passwords-and-sign-in-checks.md) |
| `exp:user:pending`, `exp:user:approve`, `exp:user:resend-activation` | registrations that wait | [Approve registrations](approve-registrations.md) |
| `exp:user:groups`, `exp:user:roles`, `exp:user:whoami` | user groups, role assignments, who the console acts as | [Groups and roles](groups-and-roles.md) |
| `exp:user:sessions`, `exp:user:unlock`, `exp:user:2fa` | sessions, locked accounts, two-step sign-in and passkeys | [Sessions and sign-in security](sessions-and-sign-in-security.md) |
| `exp:user:export`, `exp:user:import`, `exp:user:bulk` | many accounts at once | [Import, export and bulk changes](import-export-and-bulk-changes.md) |
| `exp:user:audit`, `exp:user:hygiene` | the history of an account, the state of all accounts | [Audit and hygiene](audit-and-hygiene.md) |

## Three rules every command follows

**A dry run unless `--apply`.** A command that changes something first lists what it would do and which checks fail.
Add `--apply` to make the change. `--dry-run` is accepted and means the same as no flag; both together are a usage
error (exit code 2).

**No password on the command line.** No command takes `--password=...`, because the shell history and the process
list would keep it. A password comes from standard input (`--password-stdin`), is generated and printed once, or is
chosen by the person through a mail.

**Who acts is recorded.** The commands act as the user of `site.ini [UserSettings] UserCreatorID`, or as the user
you name with `--as=<login>`. The audit trail and the user audit log record that user and the siteaccess.

**Protected accounts** are never disabled or removed: the anonymous user, the `UserCreatorID` user, the
administrator account (id 14 or the login `admin`), the user who acts and the last enabled administrator.

## Tasks

| Task | Page |
|---|---|
| Set up an account for a new editor | [Onboard a new editor](onboard-a-new-editor.md) |
| Close the access of someone who leaves | [Offboard someone who leaves](offboard-someone-who-leaves.md) |
| Work through many waiting registrations | [Approve a backlog of registrations](approve-a-backlog-of-registrations.md) |
| Get an administrator back in | [Recover a locked-out administrator](recover-a-locked-out-administrator.md) |
| Help someone who lost the phone with the authenticator app | [Reset two-step sign-in after a lost phone](reset-two-step-sign-in-after-a-lost-phone.md) |
| Create many member accounts from a spreadsheet | [Bulk-import members from CSV](bulk-import-members-from-csv.md) |

## More

- The JSON output and exit codes, key by key: [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md).
- The same tasks in the administration: [Setting up accounts for editors and administrators](../setting-up-editor-and-admin-accounts.md),
  [Managing members](../managing-members.md), [The user audit log](../the-user-audit-log.md) and
  [Check user accounts](../../features/6.0/user-hygiene.md).
- Every command with all its options: [Console commands](../../examples/commands/README.md).

## Next step

[Find, create and change accounts](find-create-and-change-accounts.md).
