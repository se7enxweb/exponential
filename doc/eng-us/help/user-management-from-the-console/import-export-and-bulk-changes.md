---
title: "Import, export and bulk changes"
audience: admin, developer, hosting
category: administration
order: 8
summary: exp:user:export writes the accounts the list filters select as CSV or JSON without any password, exp:user:import creates accounts from a CSV or JSON file exactly like exp:user:create, and exp:user:bulk disables, enables, deletes, approves, rejects or unlocks many accounts with a count you confirm.
keywords: exp:user:export, exp:user:import, exp:user:bulk, export users, import users, CSV, JSON, bulk disable, bulk delete, bulk approve, bulk reject, bulk unlock, --confirm, many accounts, mass change, data protection, personal data
type: help
---
# Import, export and bulk changes

These commands work on many accounts at once. Each one selects accounts with the filters of `exp:user:list`
(`--state`, `--group`, `--search`, `--days`).

## What you'll have at the end

A file of accounts, accounts created from a file, or one change made to many accounts.

## Export accounts

```bash
./console exp:user:export --output=var/tmp/editors.csv --group=Editors
./console exp:user:export --type=json --state=inactive --days=365 > var/tmp/inactive.json
```

`--type` is `csv` (default) or `json`. Without `--output` the file goes to standard output.

Each account has these fields: `id`, `login`, `email`, `first_name`, `last_name`, `name`, `enabled`, `states`,
`groups` (node ids separated by `|`), `main_group`, `last_visit`, `login_count`. An export never holds a password or
a password hash.

**Data protection:** The file holds personal data: names and e-mail addresses. Store it where only the people who
need it can read it, and remove it when the work is done.

## Import accounts

The file is CSV or JSON. A CSV file starts with this header row:

```text
login,email,first_name,last_name,groups
jdoe,jane.doe@example.com,Jane,Doe,Members
rroe,richard.roe@example.com,Richard,Roe,Members|1234
```

A JSON file is an array of objects with the same keys. `groups` holds node ids or names separated by `|`.

```bash
./console exp:user:import var/tmp/members.csv --send-activation
./console exp:user:import var/tmp/members.csv --send-activation --apply
./console exp:user:import var/tmp/staff.json --type=json --disabled --group=Editors --apply
```

- Choose exactly one of `--send-activation` and `--disabled`.
  - `--send-activation`: each person gets the activation mail, then chooses a password with **Forgot password**.
  - `--disabled`: the accounts are created disabled; you reset their passwords later.
- Rows without groups go to `--group` (repeat it for more groups), by default `[UserSettings] DefaultUserPlacement`.
- A login that exists already is skipped and listed.
- A file with any password column is refused.
- Each account is created exactly like `exp:user:create`: with its checks, workflows and audit events.

The dry run checks every row and lists what would be created. Each row has a result: `would_create` in the dry run,
`created` with `--apply`, or `skipped` or `refused` with the reasons.

The step-by-step task: [Bulk-import members from CSV](bulk-import-members-from-csv.md).

## Bulk changes

```bash
./console exp:user:bulk disable --state=inactive --days=730
./console exp:user:bulk disable --state=inactive --days=730 --confirm=42 --apply
```

The actions are `disable`, `enable`, `delete`, `approve`, `reject` and `unlock`. They apply to the accounts the
filters select, at most `--limit` (default 500, at most 5000).

1. Run the command without `--apply`.
   The dry run lists the accounts and their count.
2. Check the list.
3. Run the command again with `--apply` and `--confirm=<the count>`.
   The number must match what the filters select at that moment. When it does not, nothing changes.

Protected accounts and the last administrator are skipped and listed, never changed. Each account is changed as the
single command would change it, with its own audit event.

**Attention:** `delete` and `reject` remove accounts for good. Export the accounts first, and prefer `disable`.

## Related pages

- [Approve a backlog of registrations](approve-a-backlog-of-registrations.md): `bulk approve` and `bulk reject`.
- [Audit and hygiene](audit-and-hygiene.md): find the accounts a bulk change should select.
- [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md): the keys of `export`, `import` and `bulk`.
