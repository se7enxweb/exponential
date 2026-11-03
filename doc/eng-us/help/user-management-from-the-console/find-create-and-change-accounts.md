---
title: "Find, create and change accounts"
audience: admin, developer, hosting
category: administration
order: 2
summary: exp:user:list and exp:user:show find and inspect accounts by state, group and search; exp:user:create creates an account through the content publish with a password nobody types on a command line; exp:user:edit changes the e-mail address, login, names and groups.
keywords: exp:user:list, exp:user:show, exp:user:create, exp:user:edit, list users, find user, show user, create user, new account, change e-mail address, change login, add to group, remove from group, main group, state, filter, search, paging, --generate-password, --password-stdin, --send-activation
type: help
---
# Find, create and change accounts

`exp:user:list` and `exp:user:show` read; `exp:user:create` and `exp:user:edit` change. The changing commands are a
dry run until you add `--apply`.

## What you'll have at the end

Any account found and inspected, a new account in the right groups, and an existing account with a new address,
login, name or group.

## Find accounts

```bash
./console exp:user:list
./console exp:user:list --state=disabled --group=Editors
./console exp:user:list --state=inactive --days=365 --sort=last_visit --order=desc
./console exp:user:list --search=example.com --limit=100 --offset=100
```

| Option | Meaning |
|---|---|
| `--state` | `all` (default), `enabled`, `disabled`, `unactivated`, `pending`, `locked`, `never`, `inactive`, `orphaned`, `duplicate_login`, `duplicate_email` |
| `--group` | a user group by node id or name: its members at any depth |
| `--search` | a text in the login, the name or the e-mail address |
| `--days` | with `--state=inactive`: not signed in for this many days (default 180) |
| `--sort`, `--order` | `name` (default), `login`, `email`, `last_visit`, `login_count`, `id`; `asc` (default) or `desc` |
| `--limit`, `--offset` | page size (default 25, at most 500) and the accounts to skip |

The states are the filters of **Users > Check user accounts**. The tutorial explains each one:
[User management from the console](../../guides/user-management-from-the-console.md#find-and-inspect-accounts).

## Inspect one account

```bash
./console exp:user:show jane
./console exp:user:show jane@example.com
./console exp:user:show 1234 --format=json
```

The output names the groups (the main group first), every role with where it comes from and its limitation, the
state, the registration, the sign-ins and failed attempts, the last password change, two-step sign-in, the active
sessions and whether the account is protected. It never shows a password hash.

## Create an account

1. Run the command without `--apply`.

   ```bash
   ./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
       --group=Editors --generate-password
   ```

   The command lists what it would create and which checks fail.
2. Fix any check that fails, for example a login that is taken.
3. Run the same command with `--apply`.
   The account is published. A generated password is printed once.

`--login` and `--email` are required. Repeat `--group` for more groups; the first is the main group. Without
`--group` the account goes to `[UserSettings] DefaultUserPlacement`. `--class` picks another user class than
`[UserSettings] UserClassID`.

Choose one way to the password:

| Option | What happens |
|---|---|
| `--generate-password` | a password that passes the password rules, printed once |
| `--password-stdin` | one line read from standard input, from a password manager, a file of mode 0600 or a CI secret |
| `--send-activation` | the account starts disabled and gets the activation mail; the person then chooses a password with **Forgot password** |

`--disabled` creates the account disabled; enable it later with `exp:user:enable`.

**Note:** The account is published with its workflows. Where new accounts need approval, the account waits and
shows up in `exp:user:pending` ([Approve registrations](approve-registrations.md)).

## Change an account

```bash
./console exp:user:edit jane --email=jane.doe@example.com --apply
./console exp:user:edit jane --login=jdoe --apply
./console exp:user:edit jane --first-name=Janet --last-name=Doe --apply
./console exp:user:edit jane --add-group=Editors --remove-group=Members --main-group=Editors --apply
```

- A new e-mail address and login get the same checks as on create. The audit records
  `access.user.email.change` and `access.user.login.change`.
- A new name is a new version of the user object, published with its workflows.
- `--add-group` adds a location in a group, `--remove-group` removes one. Both can be repeated.
- An account always keeps at least one group. When the main group goes, the next group becomes the main one, or the
  one you name with `--main-group`.

To move an account between groups, add the new group and remove the old one in one command.

## Remove an account

`exp:user:delete` removes an account for good. Read
[Disable, enable and remove accounts](disable-enable-and-remove-accounts.md) first: in most cases, disabling is the
better choice.

## Related pages

- [Groups and roles](groups-and-roles.md): create groups and assign roles.
- [Setting up accounts for editors and administrators](../setting-up-editor-and-admin-accounts.md): the same in the
  administration.
- [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md): the keys of `list`, `show` and the
  changing commands.
