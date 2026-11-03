# User management from the console

This guide is for administrators and developers of an Exponential 6.0.15+ installation who manage user accounts from
a shell, a deploy script or a CI job: list and inspect accounts, create and change them, disable, enable and remove
them, reset passwords and approve registrations. The `exp:user` commands do each of these the way the administration
does, so workflows, the approval of new accounts, the audit and the user audit behave exactly as when you click.

[Guides](README.md) · Related: [The help manual User management from the console](../help/user-management-from-the-console/README.md),
[Security and audit](security-and-audit.md),
[The user audit log](../help/the-user-audit-log.md), [Setting up editor and administrator accounts](../help/setting-up-editor-and-admin-accounts.md),
[Managing members](../help/managing-members.md), [Console commands](../examples/commands/README.md)

## What you'll have at the end

- Any account found and inspected: its groups, roles, state, registration and sign-ins.
- A new account in the right groups, with a password nobody had to type on a command line.
- An account disabled at once when someone leaves, or removed for good, without locking yourself out.
- A forgotten password reset, and a waiting registration approved or rejected.
- The same in a script, with `--format=json` and dependable exit codes.

## The commands

| Command | What it does | Changes something |
|---|---|---|
| `exp:user:list` | Lists accounts: by state, group and search, with paging | no |
| `exp:user:show <user>` | One account: groups, roles, state, registration, sign-ins, two-step sign-in, sessions | no |
| `exp:user:create` | Creates an account through the content publish | yes |
| `exp:user:edit <user>` | E-mail address, login, names, groups, main group | yes |
| `exp:user:disable <user>` | Disables the account and ends its sessions | yes |
| `exp:user:enable <user>` | Enables a disabled account again | yes |
| `exp:user:delete <user>` | Removes the account for good | yes |
| `exp:user:reset-password <user>` | Sends the reset link, or sets a generated password or one from standard input | yes |
| `exp:user:pending` | Lists the registrations that wait | no |
| `exp:user:approve <user>` | Approves a waiting registration, or rejects it with `--reject` | yes |

`<user>` is a login, an e-mail address (when only one account has it) or the account's id (its content object id).
Every command prints `--help` with all its options; the generated pages of the
[console commands reference](../examples/commands/README.md) list them too.

## Three rules every command follows

**A dry run unless `--apply`.** A command that changes something first lists what it would do and which checks fail,
and changes nothing. Add `--apply` to make the change. `--dry-run` is accepted and means the same as no flag; both
together are a usage error.

```bash
./console exp:user:disable jane          # Would change: disable the account jane and end its sessions
./console exp:user:disable jane --apply  # Changed: ...
```

**No password on the command line.** A password typed as an argument stays in the shell history and shows in the
process list of the server. The commands therefore never take `--password=...`. Choose one of:

- `--generate-password` (create) or `--generate` (reset-password): a password that passes the password rules,
  printed once and never again;
- `--password-stdin`: one line read from standard input, from a password manager, a file of mode 0600 or a CI secret;
- `--send-activation` (create) or `--send-link` (reset-password): the person chooses the password.

**Who acts is recorded.** The commands act as the user of `site.ini [UserSettings] UserCreatorID` (the administrator
of a new installation), or as the user you name with `--as=<login>`. The audit and the user audit name that user and the
siteaccess, just as for a change in the administration.

## Find and inspect accounts

```bash
./console exp:user:list
./console exp:user:list --state=disabled
./console exp:user:list --group=Editors --sort=last_visit --order=desc
./console exp:user:list --state=inactive --days=365
./console exp:user:list --search=example.org --limit=100 --offset=100
```

`--state` takes the filters of **Users > Hygiene** and two of the console's own:

| State | Accounts |
|---|---|
| `all` | every account whose object is not in the trash (the default) |
| `enabled`, `disabled` | enabled, or disabled (an account without its settings row counts as disabled) |
| `unactivated` | disabled with an activation key: the registration was never finished |
| `pending` | registrations that wait (see [Approve registrations](#approve-registrations)) |
| `locked` | more failed sign-ins than `[UserSettings] MaxNumberOfFailedLogin` |
| `never` | never signed in |
| `inactive` | signed in before, but not for `--days` days (default 180) |
| `orphaned` | no object, an object never published, or no location |
| `duplicate_login`, `duplicate_email` | a login or an address another account has too |

`--group` takes a node id or the name of a user group and lists its members at any depth. `--search` looks into the
login, the name and the e-mail address.

```bash
./console exp:user:show jane
./console exp:user:show jane@example.com
./console exp:user:show 1234 --format=json
```

`exp:user:show` adds what the list leaves out: every group with the main one first, every role with where it comes
from (the account or a group) and its limitation, the state of the registration, the last password change the audit
knows, whether a new password is required at the next sign-in, two-step sign-in, the number of active sessions, and
whether the account is protected (see below). It never shows a password hash.

## Create an account

```bash
./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
    --group=Editors --generate-password
./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
    --group=Editors --generate-password --apply
```

The account is a content object of the user class (`[UserSettings] UserClassID`, or `--class`) in the first
`--group`, published with the content/publish operation. Repeat `--group` for more groups; the first is the main
group. Without `--group` the account goes to `[UserSettings] DefaultUserPlacement`.

Because the publish runs its workflows, an installation that asks for approval of new accounts (for example new
editors, see [Approval of new accounts](../bc/6.0/user-account-approval.md)) holds the account until it is approved:
the command says so, and the account shows up in `exp:user:pending`.

The checks are the administration's: a valid login nobody has, a valid e-mail address (with
`[UserSettings] RequireUniqueEmail` one nobody has), and a password that passes the password rules. A failed check is
named and nothing is created, with or without `--apply`.

The three ways to the password:

```bash
# printed once: hand it to the person on a safe channel
./console exp:user:create --login=jane --email=jane@example.com --group=Editors --generate-password --apply

# from a secret: never on the command line
printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:create --login=ci-bot --email=ci@example.com \
    --group=Editors --password-stdin --apply

# the person activates the account from the mail, then chooses a password with "Forgot password"
./console exp:user:create --login=member1 --email=member1@example.com --group=Members --send-activation --apply
```

`--disabled` creates the account disabled; enable it later with `exp:user:enable`.

## Change an account

```bash
./console exp:user:edit jane --email=jane.doe@example.com --apply
./console exp:user:edit jane --login=jdoe --apply
./console exp:user:edit jane --first-name=Janet --apply
./console exp:user:edit jane --add-group=Editors --remove-group=Members --main-group=Editors --apply
```

A new e-mail address and login get the same checks as on create, and the audit records each change
(`access.user.email.change`, `access.user.login.change`). A new name is a new version of the object, published with
its workflows. `--add-group` adds a location in a group, `--remove-group` removes one; an account always keeps at
least one group. When the main group is removed, the next group becomes the main one, or the one you name with
`--main-group`.

## Disable, enable and remove

```bash
./console exp:user:disable jane --apply
./console exp:user:enable jane --apply
./console exp:user:delete jane --apply
```

`exp:user:disable` is what **Edit user settings** does: the account is disabled and all its sessions end, the tracked
ones in **Your devices** too. Nothing is removed; `exp:user:enable` undoes it. Use it when someone leaves, or when an
account may have been taken over.

`exp:user:enable` refuses an account that was never activated (it still has its activation key): approve its
registration with `exp:user:approve` instead, so the registration finishes with the mails and steps that belong to it.

`exp:user:delete` removes the account's locations for good, as the administration does: accounts are never moved to
the trash, so the account, its settings and its sessions go. The content the person created stays.

**Protected accounts.** `exp:user:disable` and `exp:user:delete` refuse:

| Account | Why |
|---|---|
| the anonymous user (`[UserSettings] AnonymousUserID`) | every visitor is this user |
| the user of `[UserSettings] UserCreatorID` | the kernel creates accounts as this user |
| the administrator account (id 14, or the login `admin`) | the account of a new installation |
| the user who acts (`--as`) | you do not lock yourself out |
| the last enabled administrator | an account with every right (a role with a policy on every module) must remain |

`exp:user:show` says whether an account is protected and why.

## Reset a password

```bash
./console exp:user:reset-password jane --send-link --apply
./console exp:user:reset-password jane --generate --require-change --apply
./console exp:user:reset-password ci-bot --password-stdin --apply < /root/secrets/ci-bot-password
```

- `--send-link` sends the mail of **Forgot password** with its one-time link to the account's address. The password
  stays as it is until the person uses the link, and the link works for `[UserSettings] ForgotPasswordKeyLifetime`.
- `--generate` sets a password that passes the password rules and prints it once.
- `--password-stdin` sets the password read from standard input; it must pass the password rules.
- `--require-change` (with `--generate` or `--password-stdin`) makes the person choose a new password at the next
  sign-in.

`--generate` and `--password-stdin` end the account's sessions, as a password change in the administration does. The
audit records `access.user.password.change` (or `access.user.password.reset.request` for the link), never the
password. In demo mode (`EXP_DEMO_MODE=1`) an administrator's password is never changed.

## Approve registrations

A registration waits in one of three ways, which `exp:user:pending` names in its State column:

| State | Waits for |
|---|---|
| `email` | the person to confirm the e-mail address (the account has an activation key) |
| `administrator` | an administrator to activate the account (it is disabled, has no activation key and never signed in) |
| `workflow` | an approver to accept the approval item of the account's publish |

```bash
./console exp:user:pending
./console exp:user:approve member1 --apply
./console exp:user:approve spam-account --reject --apply
```

Approving activates the account as **Users > Unactivated** does (the registration then finishes: the account is
published and the welcome mail sent), or accepts the approval item and continues its workflow at once. `--reject`
denies the approval item and removes the account for good, as the reject link of the registration mail does; an
active account is never removed by `--reject`. The audit records `access.registration.approve` or
`access.registration.reject`.

## Groups, roles, sign-in security and many accounts at once

| Command | What it does | Changes something |
|---|---|---|
| `exp:user:groups [list\|create\|rename\|move\|delete] [group]` | The user group tree with members; a group created (`--name`, `--parent`), renamed, moved (`--to`), or removed to the trash once empty | yes, except `list` |
| `exp:user:roles list <target>` | The roles that reach a user or a group, with where they come from and their limitation | no |
| `exp:user:roles assign\|unassign <role> <target>` | A role assigned or removed, optionally limited by `--subtree=<node>` or `--section=<id>`; `<target>` is a user or `group:<node id or name>` | yes |
| `exp:user:whoami` | The account the console acts as, its roles, whether it has every right | no |
| `exp:user:sessions <user>` | The running sessions (Your devices); `--end` ends all, `--session=<id>` one | with `--end` or `--session` |
| `exp:user:unlock <user>` | The failed sign-ins back to 0 for a locked account | yes |
| `exp:user:2fa <user>` | Two-step sign-in and passkeys; `--reset` for a lost phone, `--remove-passkeys` for a lost device | with `--reset` or `--remove-passkeys` |
| `exp:user:require-password-change <user>` | A new password required at the next sign-in; `--clear` removes the mark | yes |
| `exp:user:resend-activation <user>` | The activation mail again, with a new key | yes |
| `exp:user:check-login <user> --password-stdin` | Whether a password signs the account in; never a failed sign-in | no |
| `exp:user:export` | The accounts the list filters select, as CSV or JSON (`--type`, `--output`); never a password or hash | no |
| `exp:user:import <file>` | Accounts from a CSV or JSON file, each created as `exp:user:create` does; `--send-activation` or `--disabled`; a password column is refused | yes |
| `exp:user:bulk <action>` | `disable`, `enable`, `delete`, `approve`, `reject` or `unlock` for every account the filters select; `--apply` needs `--confirm=<count>` | yes |
| `exp:user:audit <user>` | The user audit log of the account, filtered by `--event`, `--result`, `--from`, `--to` | no |
| `exp:user:hygiene` | The number of accounts in each state, with the command that lists them | no |

```bash
# a group for a partner agency, with the Editor role in one subtree
./console exp:user:groups create --name=Partners --parent=Editors --apply
./console exp:user:roles assign Editor group:Partners --subtree=2 --apply

# someone leaves: disable, end every session, then check the audit
./console exp:user:disable jane --apply
./console exp:user:audit jane --from=2026-10-01

# a locked-out administrator, from the server's shell
./console exp:user:unlock admin --apply
./console exp:user:reset-password admin --generate --apply

# members from a spreadsheet, each with the activation mail
./console exp:user:import members.csv --group=Members --send-activation           # checks every row
./console exp:user:import members.csv --group=Members --send-activation --apply

# accounts unused for two years disabled, after a look at the list
./console exp:user:bulk disable --state=inactive --days=730 --group=Members
./console exp:user:bulk disable --state=inactive --days=730 --group=Members --apply --confirm=42
```

`exp:user:bulk` treats every account as the single command does: protected accounts and the last administrator are
listed as refused and never changed. `--confirm` must be the number the dry run showed; when the selection changed in
between, the run is refused and changes nothing. A bulk `delete` needs a filter: it never removes every account.

An import file has the columns `login`, `email`, `first_name`, `last_name` and `groups` (node ids or names separated by
`|`). It never holds a password: a file with a password column is refused as a whole. A login that exists is skipped,
so a corrected file can be imported again. `exp:user:export` writes the same columns and more; an export holds personal
data, so keep it as such (`--output` creates the file with mode 0600).

Not offered on purpose: merging two accounts (the kernel has no safe way to move the ownership and history of one
account to another), passwords in import files, and a `--password` option.

## In scripts and CI

Every command takes `--format=json` and prints one JSON object with `schema` (`exp-user/1`), `command`, `mode`,
`errors`, `warnings` and `exit`, and the command's own keys: `accounts` and `total` for the lists, `account` for
`show`, and for the changing commands `dry_run`, `applied`, `changes` and `user` (id, login, e-mail, name, enabled).
A generated password is the key `generated_password`, present only in the run that set it.

| Exit code | Meaning |
|---|---|
| 0 | done (or, without `--apply`, nothing refused) |
| 1 | failed or refused: the account was not found, a check failed, the account is protected |
| 2 | usage: a wrong option or value |
| 3 | the database is not reachable |
| 4 | internal error |

```bash
# fail a deploy when a service account is missing or disabled
./console exp:user:show ci-bot --format=json | jq -e '.account.enabled' > /dev/null

# every account that waits, as id and login
./console exp:user:pending --format=json --limit=500 | jq -r '.accounts[] | "\(.id) \(.login)"'
```

Options and keys are a permanent contract: new ones may be added, none is ever renamed or removed
(`tests/tests/kernel/classes/user/expUserCommandContractTest.php` checks it).

## Troubleshooting

| What you see | Why, and what to do |
|---|---|
| `no account has the login ...` | Check the spelling, or use the id from `exp:user:list --search=...`. A login is compared in any case. |
| `2 accounts share the e-mail address ...` | Name the account by its login or id. `exp:user:list --state=duplicate_email` lists such accounts. |
| `2 user groups are named ...` | Name the group by its node id (`exp:user:show <user>` and `exp:user:list` show node ids). |
| `the publish waits` after create | A workflow holds the account, usually the approval of new accounts: `exp:user:pending`, then `exp:user:approve`. |
| `the account was never activated` on enable | Use `exp:user:approve <user> --apply`. |
| `this is the last enabled account with every right` | Enable or create another administrator first. |
| `--password-stdin: standard input holds no password` | Pipe or redirect the password into the command; an interactive terminal waits for one line. |
| The reset link points at the wrong host | The link is built from the siteaccess: run the command with `--siteaccess=<the public siteaccess>`. |
