# Migrations: deploying to remote installations

This page is the guide to deploying the migrations of one installation (for example beta) to one or many other
installations (dev, stage, production, a group of production nodes) and running them there, all at once and in sync:
every selected remote ends in the same migration state, or none of them changes.

[Guides](README.md) · [Migrations](migrations.md) · Related: [Packages, deploys and long runs](migrations-packages-deploy-and-long-runs.md),
[Rollback and captions](migrations-rollback-and-captions.md)

## What a deploy does

`./console exp:migration:deploy --remotes=<ids or groups>` works in phases. Every phase runs on all selected remotes
in parallel, and a phase only starts when the one before it passed on every remote:

| Phase | On every remote | When it fails |
|---|---|---|
| prepare | The remote's settings and its credential are checked (no connection yet). | Nothing was contacted. |
| preflight | `exp:migration:status --format=json` is read there: reachable, the run lock free, no failed migration, the same kernel version as here, no migration changed after it was applied there. The files to send are planned. | Nothing was changed anywhere. |
| transfer | The planned migration files are sent. | No remote was switched; the files sent stay pending there. |
| verify | A dry run of `exp:migration:migrate` there must see every sent file with this installation's checksum, and captions must be on. FTP: every file is read back and compared. | No remote was switched. |
| switch | `exp:migration:migrate --apply --run-id=<deploy id>` on all remotes at once, with one shared run id. | See "All or nothing". |

Without `--apply` the deploy is a dry run: it ends after the preflight and lists, per remote, the files it would send
and the migrations that would run. Nothing is sent or changed.

```
./console exp:migration:deploy --remotes=stage,production
./console exp:migration:deploy --remotes=stage,production --apply
```

Only the site's migrations (`[MigrationSettings] DirectoryList[]`) and those of the active extensions are sent.
Migrations of packages reach a remote with their package, and kernel steps with the kernel. A migration the remote
already has at the same path with the same checksum is not sent again.

## All or nothing

When one remote fails in the switch, the deploy takes every remote that applied back to the caption it took right
before the run (`exp:migration:rollback <deploy id> --apply` there). The remote that failed has already restored its
own caption, as every failed run does. The deploy then reports `rolled-back`: every remote is where it was before.

That is why the verify phase refuses a remote whose captions are off (`[CaptionSettings]`): without the caption taken
before the run it could not be taken back.

A remote that cannot be taken back (its rollback fails, or the connection dropped during the run so its state is
unknown) makes the deploy `out-of-sync`. The report names the state of every remote; check them with
`./console exp:migration:remote:status` and resolve the one in question by hand before the next deploy.

The switch and the rollback are never retried automatically: a run that may have started is not started a second time.

## Remotes

Remotes are named in `migration.ini` (in `settings/override/migration.ini.append.php`), one section each:

```ini
[RemoteSettings]
RemoteList[]
RemoteList[]=stage
RemoteList[]=production1
RemoteList[]=production2

[Remote_stage]
Environment=stage
Transport=rsync
Host=stage.example.com
User=deploy
Path=/var/www/site
Credential=sshkey:deploy_stage

[Remote_production1]
Environment=production
Transport=ssh
Host=node1.example.com
User=deploy
Path=/var/www/site
Php=/usr/bin/php8.4
Credential=sshkey:deploy_production
Groups[]=production
HostKeyFingerprint=SHA256:+DiY3wvvV6TuJJhbpZisF/zLDA0zPMSvHdkr4UvCOqU

[Remote_production2]
Environment=production
Transport=sftp
Host=node2.example.com
User=deploy
Path=/var/www/site
Credential=sshkey:deploy_production
Groups[]=production
```

| Setting | Meaning |
|---|---|
| `Environment` | The label the reports show: alpha, dev, stage, beta, production, post, arch ... |
| `Transport` | `ssh` (commands over ssh, files with scp), `sftp`, `rsync` (over ssh: only what differs is sent, a broken transfer resumes), `ftp`, `ftps` (files only), `local` (a folder of this host) |
| `Host`, `Port`, `User` | Where the remote is; the port defaults to 22 (21 for FTP) |
| `Path` | The installation's root on the remote (for FTP: the path the FTP account sees) |
| `Php` | The PHP CLI on the remote (default `php`) |
| `Credential` | `sshkey:<secret name>`, `password:<secret name>`, either with `env:VARIABLE` in place of the name, or `none` (the host's SSH agent and keys) |
| `Groups[]` | Groups the remote belongs to: `--remotes=production` selects every remote of the group |
| `HostKeyFingerprint` | Optional: the host key must have this fingerprint (`ssh-keygen -l -E sha256`); without it a host key is learnt on first contact and a changed one is refused |
| `AllowRootUser` | `enabled` when the console scripts run as root on the remote (`--allow-root-user`) |

`--remotes=all` selects every remote. `[RemoteSettings]` also sets `Parallel` (how many remotes at a time), `Timeout`
(seconds per remote and phase), `Retries` (how often a connection failure is tried again) and `StoreReports`.

```
./console exp:migration:remote:list
```

lists the remotes and every problem of their settings without connecting to any.

## Credentials

A remote section never holds a secret: a section with a `Password`, `Key`, `Token` or `Secret` setting is refused.
`Credential` names a secret of the secret store (Setup > Secrets) or an environment variable (`env:VARIABLE`, for CI).

- **SSH key** (`sshkey:`): the private key is written to a file only the deploy can read, for the time of the deploy,
  and overwritten and removed afterwards. Put the public key into the remote user's `~/.ssh/authorized_keys`.
- **Password** (`password:`): for ssh, ssh asks the helper `bin/shell/expstaticaskpass.sh`, which answers from the
  environment of the ssh process; for FTP it goes into a netrc file only the deploy can read. It is never on a command
  line.

Every output kept in a report or shown is redacted: the secret's value is replaced by `[secret <kind>:<name>]`.

## FTP remotes

FTP cannot run commands, so a deploy to an FTP remote only sends and verifies the files:

```
./console exp:migration:deploy --remotes=webspace --transfer-only --apply
```

Every selected migration is sent (the remote's state cannot be read over FTP) and read back to check it arrived
unchanged. Run the migrations there with the remote's own means.

## Status and history

```
./console exp:migration:remote:status
./console exp:migration:remote:status --remotes=production --format=json
```

reads every remote's migration status in parallel and lists, per remote, its kernel version, the run lock and how
many migrations are done, pending and failed, then whether the remotes are **in sync** (every migration done on all
of them or on none, nothing failed) and the latest deploys of this installation.

Every deploy that sends or runs something keeps its report (`exp-migration-deploy/1`) in
`var/<site>/migration/deploy/reports/<deploy id>.json`, and the audit log records it (`system.migration.deploy`;
`system.migration.deploy.rollback` when remotes were taken back).

## Staged deploys, gates and rollback

`--staged` (with `--apply`) puts the selected remotes in stages by their `Environment`, in the order of
`[RemoteSettings] StageOrder[]` (alpha, dev, test, stage, beta, production, post, arch; an environment that is not
listed comes last). The preflight still runs on every remote first. Then the first stage is transferred, verified and
switched, all or nothing within the stage, and the deploy ends in the state `gated`: the later remotes are `waiting`.

```bash
./console exp:migration:deploy --remotes=all --staged --apply
./console exp:migration:deploy --continue=20261102-120000-a1b2c3 --apply     # approve the gate: the next stage
./console exp:migration:deploy --rollback=20261102-120000-a1b2c3 --apply     # take back every remote that applied it
```

`--continue=<deploy id>` approves the gate: the next stage is prepared and preflighted again (time has passed) and
switched the same way; the report records who approved each gate. When a later stage fails, it is taken back and the
stages before it stay deployed (the report says the remotes are not in sync). `--rollback=<deploy id>` takes back
every remote that applied a deploy (`exp:migration:rollback <deploy id> --apply` on each, all at once), staged or not.

With the upgrade step `6.0.15-migration-deploy-tables`, every stored report is also written to the tables
`exp_migration_deploy` (one row per deploy) and `exp_migration_deploy_host` (one row per remote). A deploy that fails
and a staged deploy that waits at a gate send a plain-text notice by e-mail to `[RemoteSettings] NotifyEmail` (empty:
site.ini `[MailSettings] AdminEmail`); `Notify[]` names the kinds (`failure`, `gate`, `success`).

All of this is also in the administration: [Setup > Migrations > Remotes and Deploys](../help/migrations/remotes-and-deploys.md)
(the wizard of a remote, Test connection, a deploy with live progress per remote, the history, the gate and the
rollback).

## Recipes

**Deploy beta's migrations to production.** On beta: `./console exp:migration:deploy --remotes=production` (the dry
run), read the plan, then the same with `--apply`.

**Keep dev, stage, beta and production in sync.** Put all of them in `RemoteList[]`, deploy with `--remotes=all`, and
check `./console exp:migration:remote:status` afterwards: "In sync: yes".

**Only one migration.** `--only=20261102090000_add_news_class` sends and runs only that one on every remote.

## Troubleshooting

| Symptom | Cause and remedy |
|---|---|
| `not reachable or no migration status (exit 255)` | ssh could not log in or connect: the host, port, user, key or the remote's `authorized_keys`. Try `ssh -p <port> <user>@<host>` by hand. |
| `Permission denied (publickey)` | The public key of the secret is not in the remote user's `authorized_keys`. Not retried. |
| `the host key ... does not have the fingerprint` | `HostKeyFingerprint` does not match the remote's key. Check it with `ssh-keygen -l -E sha256 -f /etc/ssh/ssh_host_ed25519_key.pub` on the remote. |
| `a migration run holds the lock there` | A run is going on there, or a crashed run left its lock; see `exp:migration:status` on the remote. |
| `the kernel version there is ...` | Upgrade the remote first, so the migrations run on the same code. |
| `was applied there from a file that differs` | The migration was changed after it was applied there. Make the files equal, or accept it there with `exp:migration:migrate --force --reason`. |
| `captions are off there` | Turn captions on in the remote's `[CaptionSettings]`; a deploy needs them to take a remote back. |
| State `out-of-sync` | Read every remote's state in the report and resolve the one that could not be taken back by hand. |

## Not yet available

The signed trigger endpoint that would run migrations on FTP-only hosts, and the automatic repair of a stale lock,
file modes or caches (self-healing) are planned; until then, run the migrations of an FTP-only host there by hand
after a `--transfer-only` deploy.
