# exp:user JSON output and exit codes

The reference of the machine output of the `exp:user` commands: the schema `exp-user/1`, the keys every report has,
the keys of each command, and the exit codes. Read it when a script, a CI job or a monitoring check reads the output
of `--format=json`. For the tasks the commands serve, read
[User management from the console](../../help/user-management-from-the-console/README.md).

## The contract

- Every command takes `--format=json` (default `text`) and then prints exactly one JSON object on standard output,
  pretty-printed, with unescaped slashes and Unicode.
- The schema is `exp-user/1`. Keys and options are a **permanent contract**: new keys and options may be added, none
  is ever renamed, removed or given another meaning. A reader must ignore keys it does not know.
- A breaking change would need a new schema (`exp-user/2`); none is planned.
- No report ever holds a password hash. A password appears only as `generated_password`, in the one run that
  generated it.
- `tests/tests/kernel/classes/user/expUserCommandContractTest.php` compares the command names, options and arguments
  against a snapshot.

## Exit codes

| Code | Name | Meaning |
|---|---|---|
| 0 | done | the command did its work; a dry run that nothing refused |
| 1 | failed | the account, group or role was not found, a check failed, the account is protected, an action was refused; `exp:user:check-login`: the password does not sign the account in |
| 2 | usage | a wrong option or value, `--apply` together with `--dry-run`, a missing argument |
| 3 | unreachable | the database is not reachable |
| 4 | internal | an internal error |

The codes are those of `exp:upgrade`. A usage error (2) is printed as a `FAIL:` line on standard error, also with
`--format=json`; the other codes come with a JSON report.

## Keys of every report

| Key | Type | Meaning |
|---|---|---|
| `schema` | string | `exp-user/1` |
| `command` | string | the command, for example `exp:user:disable` |
| `mode` | string | what the report is about: the command's action (below), or `find` (the account was not found), `actor` (the user who acts was not found), `unreachable` (the database) |
| `errors` | string[] | why the command failed or refused; empty when it did not |
| `warnings` | string[] | what did not stop the command, for example "the account is disabled already: nothing to do" |
| `exit` | int | the exit code |

## Keys of the changing commands

The commands that change something (`create`, `edit`, `delete`, `disable`, `enable`, `reset-password`, `approve`, and
the phase 2 commands below that take `--apply`) add:

| Key | Type | Meaning |
|---|---|---|
| `dry_run` | bool | true without `--apply` |
| `applied` | bool | true when the change was made |
| `changes` | string[] | what is (or would be) changed, one line each |
| `user` | object or null | the account after the change: `id` (int), `login`, `email`, `name` (strings), `enabled` (bool); the account before the change for `delete` and a rejected registration |

### Per command (phase 1)

| Command | `mode` | Extra keys |
|---|---|---|
| `exp:user:create` | `create` | `user_id` (int, the new account), `generated_password` (with `--generate-password` and `--apply`) |
| `exp:user:edit` | `edit` | none |
| `exp:user:disable` | `disable` | `refusal`, `sessions_ended` (int, with `--apply`) |
| `exp:user:enable` | `enable` | none |
| `exp:user:delete` | `delete` | `refusal` |
| `exp:user:reset-password` | `reset-password` | `reset` (`send-link`, `generate` or `stdin`), `mail_sent` (bool, `send-link`), `sessions_ended` (int), `generated_password` (`generate` with `--apply`) |
| `exp:user:approve` | `approve` or `reject` | `state_before`, `state`: the registration state before and after |

`refusal` names why a protected account was refused: `anonymous`, `creator`, `admin`, `self` or
`last_administrator`. The registration states are `email`, `administrator`, `workflow` (waiting), `pending`,
`active`, `disabled` and `missing`.

## Keys of the lists (phase 1)

`exp:user:list` (`mode` `list`) and `exp:user:pending` (`mode` `pending`):

| Key | Type | Meaning |
|---|---|---|
| `filters` | object | the filters used: `state`, `group`, `search`, `days`, `sort`, `order` (`pending`: `state`, `search`) |
| `total` | int | how many accounts the filters select |
| `offset`, `limit` | int | the page |
| `accounts` | object[] | the accounts of the page |

An entry of `accounts`:

| Key | Type | Meaning |
|---|---|---|
| `id` | int | the account's id (its content object id) |
| `login`, `email`, `name` | string | |
| `enabled` | bool | |
| `unactivated` | bool | disabled with an activation key |
| `has_key` | bool | has an activation key |
| `locked` | bool | more failed sign-ins than `MaxNumberOfFailedLogin` |
| `failed_attempts`, `login_count` | int | |
| `last_visit` | int or false | Unix time of the last visit; false when never |
| `idle_days` | int or false | days since the last visit |
| `object` | string | `published`, `draft` or `none` |
| `orphaned` | bool | no object, never published, or no location |
| `node_id` | int | the main node |
| `groups` | object[] | `node_id`, `name` of each group |
| `roles` | string[] | the names of the roles, its own and those of its groups |
| `shared_login`, `shared_email` | int | how many listed accounts share the login or address (1: none) |
| `states` | string[] | `disabled` or `unactivated`, `locked`, `no_object`, `draft_only`, `no_location`, `never`; `active` when none applies |
| `registration` | string | only in `pending`: `email`, `administrator` or `workflow` |

## Keys of exp:user:show

`mode` `show`, and `account`:

| Key | Type | Meaning |
|---|---|---|
| `id`, `login`, `email`, `name` | | as in the list |
| `class` | string | the identifier of the user class |
| `node_id` | int | the main node |
| `object` | string | `published`, `draft` or `none` |
| `enabled`, `unactivated`, `locked` | bool | |
| `max_login` | int | the setting of the account |
| `failed_attempts`, `login_count` | int | |
| `last_visit` | int or null | Unix time |
| `password_changed` | int or null | Unix time of the last password change the audit knows |
| `password_change_required` | bool | a new password is required at the next sign-in |
| `registration` | string | the registration state |
| `states` | string[] | as in the list |
| `groups` | object[] | `node_id`, `name`, `main` (bool); the main group first |
| `roles` | object[] | `role`, `role_id`, `via` (the object id the role is assigned to), `via_name`, `limitation` (`''`, or for example `Subtree:/1/2/`, `Section:3`) |
| `two_factor` | object or null | `active` (bool), `method`; null when two-step sign-in is not available |
| `sessions` | int or null | active tracked sessions; null when not tracked |
| `protected` | string | `''`, or the reason as in `refusal` |
| `administrator` | bool | the account has every right |

## Keys of the phase 2 commands

| Command | Keys |
|---|---|
| `exp:user:groups` | `groups[]`: `node_id`, `name`, `parent_node_id`, `members`, `subgroups` |
| `exp:user:roles` | `assignments[]`: `role`, `role_id`, `via`, `via_name`, `limitation` |
| `exp:user:sessions` | `sessions[]`: `id`, `method`, `device`, `browser`, `ip`, `last_active`, `current` |
| `exp:user:export` | `count`, `output` |
| `exp:user:import` | `rows[]`: `line`, `login`, `result`, `errors[]`; `created`, `skipped`, `refused` |
| `exp:user:bulk` | `action`, `matched`, `accounts[]`: `id`, `login`, `result`, `reason` |
| `exp:user:audit` | `total`, `rows[]`: `time`, `event`, `result`, `actor_login`, `target_login`, `siteaccess` |
| `exp:user:hygiene` | `counts`: an object `{ state: number }` |
| `exp:user:whoami` | `account`, `roles`, `administrator` |
| `exp:user:check-login` | `valid` (bool), `reason` |

The `result` of an import row is `would_create` in a dry run and `created` with `--apply`; `skipped` (the login
exists) and `refused` (with `errors`) are the same in both. `exp:user:export` writes the accounts themselves to
`--output` or standard output. Without `--output` the data is the whole output: no report is printed and the exit code
says whether it worked. With `--output` the command prints a report, as JSON with `--format=json`, holding `count` and
`output`.

## Example

```json
{
    "schema": "exp-user/1",
    "command": "exp:user:delete",
    "mode": "delete",
    "dry_run": true,
    "user": { "id": 1234, "login": "jane", "email": "jane@example.com", "name": "Jane Doe", "enabled": false },
    "changes": [],
    "applied": false,
    "errors": [ "the user who acts (--as) cannot do this to its own account" ],
    "warnings": [],
    "refusal": "self",
    "exit": 1
}
```

## Related pages

- [User management from the console](../../features/6.0/user-management-from-the-console.md)
- [For developers: scripts and CI](../../help/user-management-from-the-console/for-developers-scripts-and-ci.md)
- [Commands, cronjob parts and module views as classes](runnable-commands-cronjobs-views.md)
