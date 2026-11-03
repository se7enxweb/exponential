---
title: "For developers: scripts and CI"
audience: developer, hosting
category: administration
order: 17
summary: Use the exp:user commands in scripts, deploys and CI - the exp-user/1 JSON output, the exit codes, passwords from CI secrets on standard input, idempotent provisioning, and jq examples for checks and reports.
keywords: scripts, CI, continuous integration, deploy, automation, --format=json, exp-user/1, exit codes, jq, idempotent, provisioning, service account, password-stdin, CI secret, health check, check-login, GitHub Actions, monitoring
type: help
---
# For developers: scripts and CI

Every command takes `--format=json` and ends with a dependable exit code. Options and keys are a permanent
contract: new ones may be added, none is renamed or removed.

## What you'll have at the end

Scripts that create, check and report accounts without screen-scraping and without a password on a command line.

## The output

`--format=json` prints one JSON object with the schema `exp-user/1`:

```json
{
    "schema": "exp-user/1",
    "command": "exp:user:disable",
    "mode": "disable",
    "dry_run": true,
    "user": { "id": 1234, "login": "jane", "email": "jane@example.com", "name": "Jane Doe", "enabled": true },
    "changes": [ "disable the account jane and end its sessions" ],
    "applied": false,
    "errors": [],
    "warnings": [],
    "exit": 0
}
```

Every key, per command: [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md).

| Exit code | Meaning |
|---|---|
| 0 | done, or a dry run that nothing refused |
| 1 | failed or refused |
| 2 | usage: a wrong option or value |
| 3 | the database is not reachable |
| 4 | internal error |

## Examples

Fail a deploy when a service account is missing or disabled:

```bash
./console exp:user:show ci-bot --format=json | jq -e '.account.enabled' > /dev/null
```

Create a service account once, with its password from a CI secret:

```bash
if ! ./console exp:user:show ci-bot --format=json > /dev/null; then
    printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:create --login=ci-bot --email=ci@example.com \
        --group=Editors --password-stdin --apply --format=json
fi
```

Check that the password still works, without counting a failed sign-in:

```bash
printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:check-login ci-bot --password-stdin --format=json \
    | jq -r '.valid, .reason'
```

Report the accounts that wait, as id and login:

```bash
./console exp:user:pending --format=json --limit=500 | jq -r '.accounts[] | "\(.id) \(.login)"'
```

Count the accounts per state for a dashboard:

```bash
./console exp:user:hygiene --format=json | jq '.counts'
```

Stop a pipeline when a dry run would refuse something:

```bash
./console exp:user:bulk disable --state=inactive --days=730 --format=json > var/tmp/plan.json || exit 1
jq -r '.matched' var/tmp/plan.json
```

## Rules for scripts

- Run the dry run first and check `errors` before you run with `--apply`.
- Pass a password only on standard input (`--password-stdin`). Never put it in a variable that is printed.
- `generated_password` is in the output of the run that set it, and never again. Store it at once, or do not
  generate one.
- `--as=<service account>` makes the audit name the automation, not the `UserCreatorID` user.
- Add `--siteaccess=<siteaccess>` when the links in mails must point at a particular site.

## Related pages

- [exp:user JSON output](../../specifications/6.0/exp-user-json-output.md)
- [For agencies and hosting](for-agencies-and-hosting.md)
- [Console commands](../../examples/commands/README.md)
