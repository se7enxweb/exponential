---
title: "Runbook: Velocity will not start"
audience: hosting, admin
category: hosting
order: 90
summary: What to do when the Exponential Velocity engine does not come up or does not answer: read its status and console log, then the usual causes (port in use, missing package or PHP extension, engine archive, certificate, privileges, a restart that hung) with the fix for each.
keywords: runbook, Velocity, will not start, qbix, exp:velocity status, console.log, port in use, already running, engine archive, EnginePhar, sockets, pcntl, certificate, ports 80 443, root, watchdog, restart, kill
type: help
---
# Runbook: Velocity will not start

For on-call staff when the Exponential Velocity engine of a site does not start, or starts and does not answer. The
messages and fixes come from [12.11 Velocity and the web server](../../../install/12-troubleshooting.md#1211-velocity-and-the-web-server).

## Contents

1. [First look](#first-look)
2. [Causes and fixes](#causes-and-fixes)
3. [A restart that hangs](#a-restart-that-hangs)
4. [After the incident](#after-the-incident)

## First look

```bash
php bin/php/console exp:velocity status --allow-root-user
```

`status` reports "running" only when the server answers a request; otherwise "not answering", with the problems it
finds, and exit status 1. Then read what the server process printed:

```bash
tail -n 50 var/vc/qbix/run/console.log
```

`var/vc/qbix/run/console.log` (`[ServerSettings] LogFile`) "holds the start-up lines and the reason" when a `qbix` start
does not come up. The server's error log is in `var/vc/qbix/log/` (`<siteaccess>-error.log`).

Also check which engine the site is set to: `[ServerSettings] Engine` in `settings/velocity.ini` (override in
`settings/override/velocity.ini.append.php`). The shipped default is `php`, PHP's built-in server for development;
production uses `qbix`:

```bash
php bin/php/console exp:velocity config get ServerSettings Engine --allow-root-user
```

## Causes and fixes

| Message or symptom | Cause | Fix |
|---|---|---|
| `velocity: no server script at ...` | the `qbix` engine needs the Velocity package, which is not installed | run `composer install` (the package is required), or install `se7enxweb/exponential-velocity` `~0.0.4.54` (PHP 8.0 or later), as [12.11](../../../install/12-troubleshooting.md#1211-velocity-and-the-web-server) describes |
| `velocity: already running` | a server of that engine runs already | `exp:velocity status`; `restart` to apply changes |
| `velocity: port ... is already in use by another process` | another program or engine holds the port | stop the other engine (`exp:velocity stop --engine=...`), or change `Port`/`HTTPSPort` with `exp:velocity config set` |
| `velocity: EnginePhar names an archive that does not exist` | `[ServerSettings] EnginePhar` points at an archive that was never built or was removed | `php bin/php/console exp:phar build`, or `exp:velocity config set ServerSettings EnginePhar disabled` |
| ports 80 and 443 cannot be bound | ports below 1024 need root | start Velocity as root (the workers drop to `[ServerSettings] User`), or use ports above 1024 behind a proxy |
| a missing PHP extension in `console.log` | the command-line PHP lacks `sockets`, `pcntl` or `openssl` | `php bin/php/console exp:velocity ext check --allow-root-user` shows what is missing, `ext install-hint <ext>` the install command |
| the machine's PHP is 8.0 and Composer refuses the engine | an engine release older than 0.0.4.54, which needed PHP 8.1 | require `~0.0.4.54` or later, which runs on 8.0 ([Choosing a web server](../servers/choosing-a-web-server.md)) |
| HTTPS shows a self-signed certificate | the configured certificate is missing, expired or does not match its key | `exp:velocity ssl show`; fix `[HTTPSSettings] Certificate` and `Key` ([Certificate expiry](certificate-expiry.md)) |
| workers cannot write | workers run as `[ServerSettings] User` and `Group` | set them, or fix ownership; workers never run as root unless `AllowRootWorkers=enabled` ([12.3](../../../install/12-troubleshooting.md#123-permissions-and-ownership)) |
| it stopped and the watchdog does not start it | `exp:velocity stop` or `kill` leaves `stopped.json`; the watchdog then leaves it alone on purpose | `exp:velocity start`; `exp:velocity watchdog status` and `var/log/velocity-watchdog.log` say what the watchdog did |

To see the exact command line the server would run, without starting it:

```bash
php bin/php/console exp:velocity command --allow-root-user
```

## A restart that hangs

`exp:velocity restart` "succeeds only when a new parent answers" and "runs detached ... so a caller killed half-way
does not leave the server down". If a restart does not finish:

1. `exp:velocity status` (is a new parent answering?).
2. If a worker is wedged: `php bin/php/console exp:velocity kill --allow-root-user` ("stop without asking, for a wedged
   worker"), then `exp:velocity start`.
3. Check with `status` again, and with a request to the site.

> **Tip**
> While the engine is down, the same installation can be served by Apache or nginx with PHP-FPM if that is set up on
> the machine; see [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md).

## After the incident

- [ ] Clear Velocity's response cache after the start: `php bin/php/console exp:velocity cache clear --allow-root-user`.
- [ ] If the cause was a crash, consider the watchdog (`exp:velocity watchdog install`, as root)
      ([Velocity for hosts](../servers/velocity-for-hosts.md)).
- [ ] If the cause was a change (a deploy, a settings change), note it and fix the procedure
      ([Deploying changes](../operations/deploying-changes.md)).

## Related pages

- [Velocity for hosts](../servers/velocity-for-hosts.md)
- [Velocity workers and pools](../servers/velocity-workers-and-pools.md)
- [Site down](site-down.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
