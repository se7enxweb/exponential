# User management from the console

Read this page if you look after the user accounts of an installation and want to do it from a shell, a deploy
script or a CI job. Exponential 6.0.15 adds the `exp:user` commands: list, inspect, create, change, disable, enable
and remove accounts, reset passwords, approve registrations, manage groups, roles, sessions and two-step sign-in,
import, export, change many accounts at once, and read the user audit log. Each command does what the
administration does, through the same operations and with the same audit events.

## In short

- 24 commands under `exp:user:*`, each with `--help` and the generated
  [console commands reference](../../examples/commands/README.md).
- **A dry run unless `--apply`.** Every command that changes something lists first what it would do and which checks
  fail. `--dry-run` is accepted; both together are a usage error.
- **No password on the command line.** A password comes from standard input (`--password-stdin`), is generated and
  printed once, or is chosen by the person through the activation or reset mail. An import file with a password
  column is refused. No output ever holds a password hash.
- **Who acts is recorded.** The commands act as the user of `site.ini [UserSettings] UserCreatorID`, or as `--as`.
  The audit trail and the user audit log name that user and the siteaccess.
- **Protected accounts** are never disabled or removed: the anonymous user, the `UserCreatorID` user, the
  administrator account (id 14 or the login `admin`), the user who acts and the last enabled administrator.
- **The same as a click.** Accounts are created and changed through the content publish, so workflows and the
  approval of new accounts run; disabling uses `user/setsettings` and ends every session.
- **For scripts**: `--format=json` prints the schema `exp-user/1`; the exit codes are 0 done, 1 failed or refused,
  2 usage, 3 database unreachable, 4 internal error. Options and keys are a permanent contract.

## The commands

| Command | What it does | Changes |
|---|---|---|
| `exp:user:list` | accounts by state, group and search, with paging | no |
| `exp:user:show <user>` | one account: groups, roles, state, registration, sign-ins, two-step sign-in, sessions | no |
| `exp:user:create` | creates an account through the content publish | yes |
| `exp:user:edit <user>` | e-mail address, login, names, groups, main group | yes |
| `exp:user:disable <user>`, `exp:user:enable <user>` | disables (and ends the sessions), enables again | yes |
| `exp:user:delete <user>` | removes the account for good | yes |
| `exp:user:reset-password <user>` | the reset link, a generated password or one from standard input | yes |
| `exp:user:require-password-change <user>` | a new password at the next sign-in (`--clear` removes the mark) | yes |
| `exp:user:check-login <user>` | whether a password from standard input signs the account in; exit 0 yes, 1 no | no |
| `exp:user:pending` | registrations that wait | no |
| `exp:user:approve <user>` | approves a registration, or rejects it with `--reject` | yes |
| `exp:user:resend-activation <user>` | the activation mail again, with a new key | yes |
| `exp:user:groups [action] [group]` | lists, creates, renames, moves and deletes user groups | yes |
| `exp:user:roles [action] [role] [target]` | lists, assigns and unassigns roles, with a subtree or section limitation | yes |
| `exp:user:whoami` | the account the console acts as, its roles, whether it has every right | no |
| `exp:user:sessions <user>` | the tracked sessions; `--end` or `--session=<id>` ends them | yes |
| `exp:user:unlock <user>` | resets the failed sign-in counter of a locked account | yes |
| `exp:user:2fa <user>` | two-step sign-in and passkeys; `--reset`, `--remove-passkeys` | yes |
| `exp:user:export` | the accounts the filters select, as CSV or JSON, without passwords | no |
| `exp:user:import <file>` | accounts from CSV or JSON, each as `exp:user:create` would create it | yes |
| `exp:user:bulk <action>` | disable, enable, delete, approve, reject or unlock many accounts, with `--confirm=<count>` | yes |
| `exp:user:audit <user>` | the user audit log rows about the account or by it | no |
| `exp:user:hygiene` | the accounts per state, each with the `exp:user:list` command that lists them | no |

`<user>` is a login, an e-mail address (when only one account has it) or the account's id. A group is a node id or
a name; a role is a name or an id.

## The audit events

| Command | Events |
|---|---|
| create, edit | the content publish events; `access.user.email.change`, `access.user.login.change` |
| disable, enable, unlock | `access.user.disable`, `access.user.enable`, `access.user.unlock` |
| reset-password | `access.user.password.change`, `access.user.password.reset.request` |
| approve | `access.registration.approve`, `access.registration.reject` |
| roles | `access.role.assign`, `access.role.unassign` |
| sessions | `access.session.revoke` |
| 2fa | `access.user.two_factor.remove`, `access.passkey.*` |

No event ever holds a password.

## What is deliberately not offered

- **Merging two accounts.** The kernel has no safe way to move the ownership of content, versions, role assignments
  and history from one account to another.
- **Passwords in import files**, and **a `--password` option**: both would spread passwords where they stay.
- **Deactivate and move.** The console disables; it does not move accounts into the group for deactivated accounts.
  Use the administration for that ([Check user accounts](user-hygiene.md#deactivate-and-move)).

## Settings

The commands add no settings. They read:

| File | Block | Key | Used for |
|---|---|---|---|
| `site.ini` | `[UserSettings]` | `UserCreatorID` | the user who acts by default; protected |
| `site.ini` | `[UserSettings]` | `AnonymousUserID` | protected |
| `site.ini` | `[UserSettings]` | `UserClassID`, `DefaultUserPlacement` | the class and group of a new account |
| `site.ini` | `[UserSettings]` | `RequireUniqueEmail` | the e-mail check on create, edit and import |
| `site.ini` | `[UserSettings]` | `MaxNumberOfFailedLogin` | locked accounts |
| `site.ini` | `[UserSettings]` | `ForgotPasswordKeyLifetime` | how long the reset link works |
| `content.ini` | `[NodeSettings]` | `UserRootNode` | the top of the Users tree for `exp:user:groups` |

## Where to go next

- The tutorial: [User management from the console](../../guides/user-management-from-the-console.md).
- The help manual, with tasks and troubleshooting:
  [User management from the console](../../help/user-management-from-the-console/README.md).
- The JSON output and exit codes: [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md).

## Related pages

- [Check user accounts](user-hygiene.md)
- [User audit log](user-audit.md)
- [Your devices](user-sessions.md)
- [Change password at next sign-in](password-change-at-next-sign-in.md)
- [Reset a user password from the command line](reset-user-password.md)
