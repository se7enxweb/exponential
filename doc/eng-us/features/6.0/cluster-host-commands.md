# Commands on other hosts and across a cluster: exp:host:run and exp:cluster:*

This page is for administrators of an installation that more than one server runs: two web servers behind a load
balancer, a web server and a Velocity node, or a primary and a standby. Clearing the caches or deploying a change used
to mean logging in on each server and running the same commands by hand, as the right user, with the right PHP
binary, in the right order. Steps got missed: the INI caches on one node, the Valkey keys, the PHP-FPM reload, the
Velocity restart. These commands do it from one server, for every host, with PASS or FAIL per host.

| | |
|---|---|
| Commands | `./console exp:host:run`, `exp:cluster:run`, `exp:cluster:cache`, `exp:cluster:deploy`, `exp:cluster:status`, `exp:cluster:hosts` (`bin/php/exphostrun.php`, `bin/php/expclusterrun.php`, `bin/php/expclustercache.php`, `bin/php/expclusterdeploy.php`, `bin/php/expclusterstatus.php`, `bin/php/expclusterhosts.php`) |
| Settings | `settings/cluster.ini`: `[ClusterSettings]` and one `[Host_<name>]` block per host, in `settings/override/cluster.ini.append.php` |
| Transport | the system's `ssh` with `BatchMode=yes`, `-n -T`; `Ssh=local` runs on this machine with `sh -c`. No password and no key is kept in the settings |
| Output | PASS or FAIL per host, exit code 1 when any host failed, `--json` for scripts, `--dry-run` everywhere |
| Log | one line per host in `var/log/cluster.log`, and the audit event `system.cluster.run` ([audit trail](audit-trail.md)) |
| Code | `expClusterHosts`, `expClusterRemote`, `expClusterOperations` (`kernel/classes/cluster/`), the commands in `kernel/private/classes/commands/` |

These commands are independent of the DFS cluster file handler ([exp:cluster:enable](cluster-commands.md)): they work
for any installation that runs on several hosts, whether its files live in a DFS share, on NFS or on each server's own
disk.

## The settings

Copy one `[Host_<name>]` block for each host and list the names in `HostList[]`, in the order the commands visit
them. The server you run the commands on can be one of the hosts, with `Ssh=local`.

```ini
# settings/override/cluster.ini.append.php
[ClusterSettings]
HostList[]
HostList[]=cluster
HostList[]=web
DefaultPhp=/opt/plesk/php/8.5/bin/php
Mode=sequential

[Host_cluster]
Ssh=local
Root=/var/www/vhosts/example.com/doc/example.com
User=example
Engine=velocity

[Host_web]
Ssh=root@web.example.com
Root=/var/www/vhosts/example.com/doc/example.com
User=example
Engine=fpm
```

| Setting | Meaning |
|---|---|
| `[ClusterSettings] HostList[]` | the hosts; a name is letters, digits, dot, dash and underscore |
| `DefaultPhp` | the PHP binary of a host whose block names none |
| `ConnectTimeout` | seconds ssh waits for a host to answer (default 20) |
| `Timeout` | seconds one host may take for everything it is asked; a host that takes longer is stopped and fails (default 1800) |
| `Mode` | `sequential` (the default) or `parallel`; `--parallel` and `--sequential` win |
| `AllowRootUser` | add `--allow-root-user` to every console command, for hosts where the login is root (default enabled) |
| `SshOptions[]` | more `ssh -o` options for every host, e.g. `StrictHostKeyChecking=accept-new` |
| `LogFile` | the log in `var/log` (default `cluster.log`) |
| `[Host_<name>] Ssh` | an alias of `~/.ssh/config`, a host name or `user@host`; `local` runs here without ssh |
| `Port` | the ssh port; empty: ssh's default or the alias's |
| `Root` | the installation's root on that host (required) |
| `Php` | the PHP binary on that host; empty: `DefaultPhp` |
| `User` | run as this user (`sudo -n -u <user> -H`); use the site user, so that caches are not written as root |
| `DeployUser` | the user `exp:cluster:deploy` runs as; empty: the ssh login, because a deploy reloads PHP-FPM and restarts Velocity |
| `Engine` | `fpm` or `velocity`: how the host serves the site (see below) |
| `DeployOptions[]` | options `exp:cluster:deploy` adds for this host to `exp:velocity deploy` |

The keys come from the ssh agent or `~/.ssh/config` of the user who runs the command. A host that would ask for a
password, or a `sudo` that would ask for one, fails instead of waiting. Check the settings and the logins first:

```bash
./console exp:cluster:hosts            # the hosts and what is wrong with their settings
./console exp:cluster:hosts --check    # also logs in on each, checks bin/php/console and the PHP binary
```

## exp:host:run and exp:cluster:run

```bash
./console exp:host:run web -- exp:cache status
./console exp:host:run web --shell -- git log -1 --oneline
./console exp:cluster:run -- exp:maintenance status
./console exp:cluster:run --hosts=web,cluster --parallel -- exp:ezcache --clear-tag=ini
./console exp:cluster:run --dry-run -- exp:velocity deploy
```

The words after `--` are a console command: each host runs `bin/php/console <words>` in its `Root`, with its PHP
binary, as its `User` (else the ssh login). `--shell` runs the words as a command line for `sh` instead. `--as`
chooses the user: `user` (the default), `deploy` (`DeployUser`) or `login`. Nothing is interactive.

## exp:cluster:cache clear

```bash
./console exp:cluster:cache clear                         # every cache on every host
./console exp:cluster:cache clear --tag=template,content  # only these
./console exp:cluster:cache clear --id=template-block
./console exp:cluster:cache clear --full --parallel       # with PHP-FPM reload and Velocity restart
```

On each host, in this order:

1. `exp:ezcache --clear-all` (or `--clear-tag` / `--clear-id` with `--tag` / `--id`; `--purge` removes the files for
   real). This also runs the cache clear listeners, which clear the caches extensions keep elsewhere, such as the
   Valkey INI and view keys, and the template-block cache.
2. On a host with `Engine=velocity`, Velocity's response cache (`exp:velocity cache clear --apply`).

`--full` runs `exp:velocity deploy --all-caches --no-autoload` instead, which also reloads PHP-FPM and restarts
Velocity before the caches of rendered output are cleared, so that no page made by the old code is cached again.

## exp:cluster:deploy

```bash
./console exp:cluster:deploy                           # exp:velocity deploy on every host
./console exp:cluster:deploy --kernel --packer
./console exp:cluster:deploy --hosts=web --remote-dry-run   # each host prints its steps
```

Each host runs `exp:velocity deploy` with the options given here and its own `DeployOptions[]`: autoloads, INI and
template caches, PHP-FPM, Velocity and the caches of rendered output, in that order, stopping at the first failure. A
host with `Engine=fpm` gets `--no-velocity`. The code must already be on the hosts (git pull, rsync); this command
does not copy files. `--dry-run` shows the ssh command for each host; `--remote-dry-run` asks each host for its deploy
plan.

## exp:cluster:status

```bash
./console exp:cluster:status
./console exp:cluster:status --parallel --json
```

For each host: the version, the PHP version, the git head, branch and commit date, whether maintenance mode is on,
and on a Velocity host whether Velocity answers. It reads only, and warns when the hosts are not on the same git
head.

## Options every command takes

| Option | Meaning |
|---|---|
| `--hosts=a,b` / `--nodes=a,b` | only these hosts of `HostList[]` |
| `--all` | every host (the default) |
| `--parallel` / `--sequential` | all hosts at once, or one after the other |
| `--timeout=<s>` | seconds one host may take |
| `--dry-run` | print the command each host would run, run nothing, log nothing |
| `--json` | one JSON object with each host's result, output and time |

Exit code: 0 every host passed; 1 a host failed (the others still ran); 2 wrong options or settings.

## Tested

`tests/tests/kernel/classes/cluster/expClusterHostCommandsTest.php`: 20 tests without a database, with a fake ssh
transport for the command lines, quoting, users, timeouts, parallel runs and the JSON report, and real local `sh`
runs. Live on a two-node installation (one Velocity node with `Ssh=local`, one FPM node over ssh, the site user):
`exp:cluster:hosts --check`, `exp:cluster:status`, `exp:cluster:deploy --remote-dry-run --parallel` and
`exp:cluster:cache clear --id=template-block` passed on both hosts.

## Related

- [Clustering in Exponential 6.0](clustering-overview.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [All console commands](../../examples/commands/README.md), [cluster.ini](../../examples/ini-settings/cluster/README.md)
