---
title: exp:audit
audience: developer, admin
category: examples
summary: "Audit log: tail, show one event, verify the hash chains, list the channels."
keywords: exp:audit, audit, console, command, CLI, audit.php
type: generated
generated: exp:help:examples
---
# exp:audit

Audit log: tail, show one event, verify the hash chains, list the channels.

Script `bin/php/audit.php`, code in `kernel/private/classes/commands/audit.php`. Run it from the installation root, as the user the web server runs as:

```bash
./console exp:audit --help
php bin/php/audit.php --help
```

## Help text

```text
Audit log: the records of the audit channels and their hash chains

  exp:audit status|channels
  exp:audit tail [--channel=<c>] [--name=<pattern>] [--lines=20] [--follow] [--json]
  exp:audit show <event id> [--json]
  exp:audit search [filters] [--files] [--limit=50] [--json]
  exp:audit verify [--channel=<c>] [--date=YYYY-MM-DD] [--archives] [--json]
  exp:audit rotate [--channel=<c>] [--apply]
  exp:audit archive [--channel=<c>] [--before=YYYY-MM-DD] [--format=gzip|bzip2|xz|zstd|zip] [--apply]
  exp:audit restore --channel=<c> --date=YYYY-MM-DD [--to=<dir>] [--apply]
  exp:audit purge [--channel=<c>] [--apply]
  exp:audit reindex [--incremental] [--archives] [--channel=<c>] [--apply]
  exp:audit pseudonymise [--apply]
  exp:audit export [filters] [--subject-user=<id>] [--format=jsonl|csv|bundle] --out=<path>
  exp:audit import [--dir=<old log dir>] [--file=login.log] [--keep-originals] [--apply]
  exp:audit key list|rotate [--apply]|fingerprint [--pseudonym]
  exp:audit checkpoint [--apply]
  exp:audit sinks list|test <name>|flush [<name>] [--apply]
  exp:audit alerts list|test <rule> [--replay=YYYY-MM-DD]|recipients [--rule=<rule>]
  exp:audit cron [--daily] [--apply]  one run of the cronjob part (--daily: the daily tasks now)

The manage actions (rotate, archive, restore, purge, reindex, pseudonymise, import, key rotate,
checkpoint, sinks flush, cron) only say what they would do unless --apply is given. --dry-run means
the same as no --apply; --apply together with --dry-run is a usage error (exit 2).

Filters (search, export): --name=<pattern> --user=<id> --login= --object=<type:id> --target=<type:id>
  --result=success|refused|failed --severity=<min> --request= --job= --run= --ip=<network>
  --from=YYYY-MM-DD[THH:MM[:SS]][Z|+HH:MM] --to=... (UTC unless an offset is given; --to includes that day,
  minute or second) --query=<text> --legacy-file=<4.x file name> --channel=
Name patterns: * | <domain>.* | a prefix ending in .* (access.session.*) | a whole name; anything else
  is refused (exit 2).
```

## Options

| Option | Value | Help |
|---|---|---|
| `--channel` | required | Only this channel (content, access, system, commerce, read) |
| `--name` | required | tail, search, export: only names matching this pattern: *, access.*, access.session.*, content.node.move |
| `--lines` | required | tail: how many records (default 20) |
| `--follow` |  | tail: keep printing new records as they are written (Ctrl-C ends) |
| `--json` |  | Print records and results as JSON |
| `--date` | required | verify: only that day's files; restore: the archived day (YYYY-MM-DD, UTC) |
| `--archives` |  | verify: also the archives (manifests, HMAC, checksums, the chain through them); reindex: also the archives |
| `--apply` |  | rotate, archive, restore, purge, reindex, pseudonymise, import, key rotate, checkpoint, sinks flush, cron: do it (the default is a dry run) |
| `--dry-run` |  | The default, kept for scripts that pass it: only say what would be done |
| `--before` | required | archive: the days before this date (default: older than LiveDays) |
| `--format` | required | archive: the format handler; export: jsonl, csv or bundle |
| `--to` | required | restore: the directory (default <LogDir>/restored); search, export: up to and including this time (as --from) |
| `--incremental` |  | reindex: only what is new (default: rebuild) |
| `--user` | required |  |
| `--login` | required |  |
| `--object` | required |  |
| `--target` | required |  |
| `--result` | required |  |
| `--severity` | required |  |
| `--request` | required |  |
| `--job` | required |  |
| `--run` | required |  |
| `--ip` | required |  |
| `--from` | required | search, export: from this time (YYYY-MM-DD[THH:MM[:SS]], UTC unless it ends in Z or +HH:MM) |
| `--query` | required | search, export: full-text search over names, object names and before/after values |
| `--legacy-file` | required |  |
| `--limit` | required | search: how many records (default 50); export: at most MaxExportRecords |
| `--files` |  | search: read the files, not the index |
| `--out` | required | export: the file (jsonl, csv) or directory (bundle) |
| `--subject-user` | required | export: every record by or about this user id (an access request) |
| `--dir` | required | import: the directory of the 4.x audit files (default: the audit LogDir) |
| `--file` | required | import: only this 4.x file (login.log) |
| `--keep-originals` |  | import: leave the 4.x files in place after archiving them |
| `--pseudonym` |  | key rotate: replace the pseudonym key instead of the signing key |
| `--replay` | required | alerts test: run the rule over the records since this date, without firing |
| `--daily` |  | cron: run the daily tasks now |
| `--rule` | required | alerts recipients: only this rule |

Read from the option definitions in the code (`startup()`, `getOptions()`, `options()` or `ezcConsoleOption`). Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Arguments: `[action][id][extra]`.

## Related

- [All console commands](README.md)

---

Generated by `./console exp:help:examples` from `bin/php/audit.php`, `kernel/private/classes/commands/audit.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
