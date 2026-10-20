---
title: Velocity for hosts
audience: hosting, admin
category: hosting
order: 80
summary: How a hosting company installs, configures, runs and supports Exponential Velocity, the recommended engine: requirements, install, ports and worker user, a boot service, the watchdog, deploys, logs, the dashboard and panel, and one Velocity per customer.
keywords: Velocity, Exponential Velocity, qbix, exp:velocity, velocity.ini, workers, persistent workers, systemd, watchdog, deploy, graceful, status, dashboard, control panel, /Q/health, /Q/metrics, ports, worker user, hosting
featured: true
type: help
---
# Velocity for hosts

This page is for system administrators and support staff at hosting companies who run Exponential Velocity for
customer sites. It answers: what Velocity needs from a server, how to install and configure it, how to keep it running
across reboots and crashes, how a change reaches it, where it logs, how to watch it, and how to give each customer
their own instance. The sources are [Chapter 8](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)
and [Velocity engines](../../../bc/6.0/velocity-engines.md); every command below is run from the installation root.

## Contents

1. [What Velocity is, for a host](#what-velocity-is-for-a-host)
2. [Requirements](#requirements)
3. [Install and first start](#install-and-first-start)
4. [Settings and how to change them](#settings-and-how-to-change-them)
5. [Ports, address and the worker user](#ports-address-and-the-worker-user)
6. [Keeping it running: boot service and watchdog](#keeping-it-running-boot-service-and-watchdog)
7. [Deploying a change](#deploying-a-change)
8. [Logs, status and monitoring](#logs-status-and-monitoring)
9. [The dashboard and the control panel](#the-dashboard-and-the-control-panel)
10. [One Velocity per customer](#one-velocity-per-customer)
11. [Checklist](#checklist)

## What Velocity is, for a host

Velocity is an application server written for Exponential. A parent process loads the application once and forks
**workers** that answer one request after another; between requests each worker is reset to the state it had when it
was forked. The parent accepts every connection, answers static files, response-cache hits and its own pages without
a worker, and terminates TLS, so a Velocity site needs no Apache, nginx, PHP-FPM or separate TLS terminator
([Chapter 8, section 8.3](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)).

What that means for your operations:

| | Under Velocity | Under Apache/nginx with PHP-FPM |
|---|---|---|
| Process to keep alive | one long-running server per site | the web server and the FPM master |
| A changed class or INI file | reaches the workers only after a restart (`exp:velocity deploy`) | reaches PHP after OPcache revalidates or an FPM reload |
| TLS | Velocity itself, including ACME | `mod_ssl` or nginx, plus certbot or the panel |
| Repeat anonymous pages | answered by the parent from the response cache | rendered or served from the kernel's caches |
| Memory to judge | PSS of parent and workers, never RSS | the pool's processes |

The command is `exp:velocity` (also `exp:vc`, or `php bin/php/velocity.php`). It drives three engines with the same
verbs: `qbix` (Velocity's own server, the subject of this page), `frankenphp` ([FrankenPHP](frankenphp.md)) and `php`
(PHP's built-in development server).

## Requirements

| Need | Detail |
|---|---|
| PHP | 8.0 or later; the engine package requires it (`"php": ">=8.0"` from release 0.0.4.54, `>=8.1` before); RHEL 9's stock PHP 8.0 runs it ([chapter 2](../../../install/02-requirements.md#php-80-and-red-hat-enterprise-linux)) |
| PHP extensions | `sockets` (required by the package), `pcntl` and `posix` (fork workers, change their user), `openssl` (HTTPS) |
| A long-running process | allowed by the plan; shared hosting usually does not allow one |
| Root, or high ports | ports below 1024 need root; started as root, only the parent keeps it |
| Operating system | Linux and the BSDs for everything; on Windows workers are `php-cgi` subprocesses ([Operating system support](../../os-support.md#windows)) |

At start-up the engine refuses to run without process isolation (`pcntl`, or a php-cgi binary) and without `openssl`
when HTTPS is configured; for the rest it warns once. Ask the machine what it lacks:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint <extension>    # the install command for this operating system
```

On Red Hat Enterprise Linux 9 and its rebuilds the stock PHP is 8.0: enable a newer module stream first
([Chapter 2, section 2.9](../../../install/02-requirements.md#29-operating-system-notes)).

## Install and first start

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.44
php bin/php/console exp:velocity config set ServerSettings Engine qbix
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
curl -sI http://127.0.0.1:8088/ | head -1
```

- The package lands in `vendor/se7enxweb/exponential-velocity`. The tilde constraint accepts later `0.0.4.x`
  releases; 0.0.4.44 is the version Exponential's `composer.json` suggests and the first that reads forwarded
  protocol headers only from trusted proxies ([Chapter 8, section 8.8](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).
- `settings/velocity.ini` ships `Engine=php` because PHP's built-in server always works; every real site sets `qbix`.
- `start` writes the configuration, starts the server detached, waits until the port answers and prints the status.
- The engine also exists as deb and rpm packages and Docker images
  ([Velocity packages and binaries](../../../features/6.0/velocity-packages-and-binaries.md)), but for an Exponential
  installation the Composer package is what `exp:velocity` drives.

What can go wrong on a first start (chapter 8): no server script (package not installed), the port already in use
(`exp:velocity status --all`), no answer from another machine (`Host` is still `127.0.0.1`), a warning that workers
run as root (no worker user and a root-owned document root), or a maintenance page left on by an installation run
(`php bin/php/maintenance.php status`, then `off`).

## Settings and how to change them

| File | What it is |
|---|---|
| `settings/velocity.ini` | the shipped defaults, every setting commented; never edit it |
| `settings/override/velocity.ini.append.php` | this installation's values, written by `exp:velocity config set` |
| `/etc/vc/...` (or `var/vc/qbix/etc/` without root) | the engine's configuration tree, generated from the two files above on every start |

```bash
php bin/php/console exp:velocity config list                    # every setting; * marks the ones you set
php bin/php/console exp:velocity config get ServerSettings Workers
php bin/php/console exp:velocity config set ServerSettings Workers 16
php bin/php/console exp:velocity config unset ServerSettings Workers
php bin/php/console exp:velocity config paths
```

A changed setting reaches the server at the next `restart` or `graceful`. The `/etc/vc` tree is laid out like Debian's
`/etc/apache2` (`vc.conf`, `ports.conf`, `envvars`, `conf-`, `mods-` and `sites-available` with `-enabled`
symlinks), with `exp:velocity site|conf|mod enable|disable <name>` as the `a2ensite` family
([Velocity on-disk layout](../../../bc/6.0/velocity-ondisk-layout.md)). Generated files carry
`"_generated": "exp:velocity"`; remove that marker from a file and it is never overwritten again.

## Ports, address and the worker user

| Setting (`[ServerSettings]`) | Shipped | Meaning |
|---|---|---|
| `Host` | `127.0.0.1` | bind address; reachable only locally, through a tunnel or a proxy |
| `Port` | `8088` | plain HTTP |
| `HTTPSPort` | `8080` | HTTPS, when HTTPS is on |
| `User`, `Group` | empty | who the workers run as when the server is started as root |
| `AllowRootWorkers` | `disabled` | `root` as worker user is refused unless `enabled` |
| `FollowSymlinks` | `disabled` | a symbolic link leading out of the document root is refused |
| `Nice` | `5` | the priority of the server and its workers, whatever the caller runs at |

To face visitors directly on the standard ports:

```bash
php bin/php/console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Port 80 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings HTTPSPort 443 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings User <customer> --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group <customer> --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

Started as root, the parent keeps root (ports, certificate, reloads) and every worker gives it up right after the
fork, before any application code runs. The worker user is decided in this order: `[ServerSettings] User` and `Group`;
then `VC_RUN_USER` and `VC_RUN_GROUP` from the environment or `/etc/vc/envvars`; then the owner of the document root.
A user that does not exist stops the start. Check the start-up line `Workers as: ...` in
`var/vc/qbix/run/console.log`. HTTPS is on [Velocity HTTPS certificates](velocity-https-certificates.md); pool size on
[Velocity workers and pools](velocity-workers-and-pools.md).

## Keeping it running: boot service and watchdog

`exp:velocity` has no command that installs a **boot service**. Two routes
([Chapter 8, section 8.3.11](../../../install/08-serving-the-site.md#8311-running-velocity-as-a-service)):

- the engine's own units (`vendor/se7enxweb/exponential-velocity/service/qbixserver.service` and the operating-system
  package's `exponential-velocity.service`), which start the engine directly and therefore bypass what
  `exp:velocity` adds (the generated site file, `KeepGlobals`, `[PHPSettings] IniOptions[]`, the worker user,
  instances);
- a unit of your own that runs `exp:velocity start`, `stop` and `graceful` (`Type=forking`, `PIDFile=` the
  installation's `var/vc/qbix/run/server.pid`). Chapter 8 gives a derived example; it is not shipped.

The **watchdog** is a second safety net for a crash, an out-of-memory kill or a stop by another tool. It is off until
installed (root, systemd), and it starts the engine only when no process of it runs, nobody stopped it on purpose,
no restart is in progress and it is not backing off. It never stops or restarts a running server
([Velocity engines, The watchdog](../../../bc/6.0/velocity-engines.md#the-watchdog-expvelocity-watchdog)):

```bash
php bin/php/console exp:velocity watchdog install --allow-root-user   # vc-watchdog.service + .timer
php bin/php/console exp:velocity watchdog status --allow-root-user
php bin/php/console exp:velocity watchdog remove --allow-root-user
```

| Setting (`[WatchdogSettings]`) | Shipped | Meaning |
|---|---|---|
| `Interval` | `45` | seconds between checks (15 to 3600) |
| `RestartTimeout` | `300` | how long a restart marker or a held lock may hold it back |
| `Backoff` | `600` | the wait after three failed starts in a row |
| `LockFile` | empty | a lock that means "deploying, wait" |
| `UnitName` | `vc-watchdog` | give each installation on a host its own |

Every check that does more than find the server running writes a line to `var/log/velocity-watchdog.log`, and a
start or back-off is recorded in the audit trail as `system.velocity.watchdog.*`.

## Deploying a change

Workers keep classes, INI values and compiled templates in memory. One command brings a site up to date in the order
that works ([Chapter 8, section 8.3.14](../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)):

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do
php bin/php/console exp:velocity deploy --allow-root-user             # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

It regenerates autoloads, clears the INI and template caches, rebuilds the engine archive when one is used, reloads
PHP-FPM when needed, restarts Velocity, clears the rendered-output caches and the response cache, and requests the
`[DeploySettings] WarmUpURL[]` pages. Each step prints `PASS`, `FAIL` or `SKIP`; the first failure stops the run. Set
`[DeploySettings] PhpFpmService=disabled` when Velocity serves the site alone. For a template or stylesheet change no
restart is needed: clear the template caches and run `exp:velocity cache clear`. `exp:velocity graceful` re-executes
the server while keeping the listening socket, so it drops no request.

## Logs, status and monitoring

| What | Where |
|---|---|
| Access and error logs | `var/vc/qbix/log/<siteaccess>-access.log`, `-error.log` (`[LogSettings] Dir`), combined format plus response time |
| Process output and start-up lines | `var/vc/qbix/run/console.log` |
| pid file | `var/vc/qbix/run/server.pid` (`server.1.pid` ... with `Instances` above 1) |
| The kernel's own logs | `var/<site>/log/`, as under any server |

`[LogSettings] FileMode` and `DirMode` ship as `0640` and `0750`: an access log names visitors. `exp:velocity status`
says `running` only for a server that answers an HTTP request, and exits non-zero otherwise; `status --json` is meant
for monitoring. `/Q/health` answers "ok" to everyone; full figures, `/Q/stats` and Prometheus `/Q/metrics` need this
machine, the dashboard token or a panel session. See [Monitoring overview](../operations/monitoring-overview.md).

## The dashboard and the control panel

| View | Who may open it |
|---|---|
| `/Q/dashboard` (live requests, workers, memory) | this machine; elsewhere with `[DashboardSettings] Token` |
| `/Q/phpinfo` | this machine, or with the token or a panel session; never through `Remote=enabled` |
| `/Q/panel` (domains, certificates, cache, logs, workers, settings) | this machine, then the panel password |

The first visit of `/Q/panel` sets its password (at least 16 characters). On a machine reachable from the internet
set it at once, and reach a remote panel through an SSH tunnel to the server's own port. A token is any long random
string, for example from `openssl rand -hex 24`. Leave `[DashboardSettings] Remote` at `disabled` unless a firewall
already restricts the port ([Velocity control panel](../../../features/6.0/velocity-control-panel.md)).

The panel's Domains tab can set a domain to `suspended` (answers `503` with `Retry-After`) or `disabled` (answers
`404`); the server's own `/Q/` and `/.well-known/` paths keep working, so certificate renewals continue.

## One Velocity per customer

A single server serving several sites "is a routing convenience, not a security boundary" (`README.md`). For several
customers on one machine, run one installation and one Velocity per customer:

- each as the customer's own user (`[ServerSettings] User` and `Group`), never as another customer's;
- each on its own ports (`Port`, `HTTPSPort`), with `Host=127.0.0.1` behind a proxy, or on its own address;
- each with its own boot unit and its own watchdog `UnitName`;
- the proxy trusted by Velocity ([Velocity beside Apache or nginx](velocity-beside-apache-or-nginx.md)).

See [Multi-tenant isolation](../security/multi-tenant-isolation.md).

## Checklist

From [Chapter 8, Checklist](../../../install/08-serving-the-site.md#89-checklist), for each customer site:

- [ ] `Engine=qbix` set, `exp:velocity status` shows the server running, the site answers.
- [ ] `Host`, `Port`, `HTTPSPort` as intended; firewall open where Velocity faces visitors.
- [ ] Workers run as the site's user (`Workers as: ...` in `console.log`), never root.
- [ ] HTTPS: a CA certificate is served, not the self-signed stand-in; renewal planned.
- [ ] A boot service starts Velocity after a reboot; the watchdog installed if you want it.
- [ ] `[DeploySettings] PhpFpmService=disabled` when Velocity serves the site alone; `deploy --dry-run` shows the
      expected steps.
- [ ] The panel password set, `Remote` left disabled, a dashboard token stored in your secrets store.
- [ ] `var/` and `settings/` writable by the one user that runs PHP; no root-owned files in `var/`.

**Demo mode under Velocity.** Workers inherit the server's environment, and the server exports what `/etc/vc/envvars` sets into it at every start: add `export EXP_DEMO_MODE=1` there, and `exp:velocity restart` or `deploy` from any shell keeps it. The unit's environment ([`velocity-demo-mode.conf`](../../../examples/demo-mode/velocity-demo-mode.conf)) works too and wins over the file. The file cannot set `PATH`, `LD_*`, `PHPRC` and the other loader and shell variables ([on-disk layout](../../../bc/6.0/velocity-ondisk-layout.md)). [Demo mode for hosts](../security/demo-mode-for-hosts.md).

## Related pages

- [Velocity workers and pools](velocity-workers-and-pools.md)
- [Velocity HTTPS certificates](velocity-https-certificates.md)
- [Velocity response cache](../performance/velocity-response-cache.md)
- [Velocity will not start](../runbooks/velocity-will-not-start.md)
- [Velocity: running Exponential in a persistent-worker web server](../../../features/6.0/velocity-persistent-worker-server.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
