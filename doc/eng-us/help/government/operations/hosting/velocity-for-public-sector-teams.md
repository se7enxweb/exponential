---
title: Velocity for public-sector teams
audience: government, hosting, admin
category: public-sector
order: 70
summary: For in-house IT teams that run Exponential Velocity, the recommended engine. How it is started, kept running after a crash, deployed to, watched and secured, with the points an internal security assessment usually asks about.
keywords: Velocity, Exponential Velocity, qbix, exp:velocity, watchdog, boot service, systemd, deploy, graceful, control panel, dashboard, /Q/health, logs, workers, security assessment
type: help
---
# Velocity for public-sector teams

This page is for the operations team of a public body that runs Exponential on Velocity, the PHP application server
that ships with Exponential and is its recommended engine. It answers how Velocity starts, how it comes back after a
crash, how a change reaches it, where it logs, how to watch it and which of its parts a security assessment will
look at. The full operator's page is [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md); the feature
pages are [Velocity web server](../../../../features/6.0/velocity-web-server.md) and
[Velocity persistent-worker server](../../../../features/6.0/velocity-persistent-worker-server.md).

## Contents

1. [What Velocity is](#what-velocity-is)
2. [Starting and stopping](#starting-and-stopping)
3. [Coming back after a crash or reboot](#coming-back-after-a-crash-or-reboot)
4. [Deploying a change](#deploying-a-change)
5. [Logs and status](#logs-and-status)
6. [Points for a security assessment](#points-for-a-security-assessment)
7. [Checklist](#checklist)

## What Velocity is

Velocity is a PHP application server. Workers keep the application in memory between requests, a response cache
answers anonymous visitors before PHP runs, and HTTPS with certificates (including Let's Encrypt and other ACME
authorities) is built in. It needs no Apache, nginx or PHP-FPM ([Deploying](../../../../guides/deploying.md)). It needs
PHP 8.0 or later (from its release 0.0.4.54; until then 8.1), so the stock PHP of RHEL 9 runs it.

## Starting and stopping

```bash
./console exp:velocity config set --apply ServerSettings Engine qbix --allow-root-user
./console exp:velocity start --allow-root-user
./console exp:velocity status --allow-root-user
./console exp:velocity graceful --allow-root-user    # re-exec, listening socket kept, no request dropped
./console exp:velocity restart --allow-root-user
./console exp:velocity stop --allow-root-user
```

`config set` writes `settings/override/velocity.ini.append.php`, the one file for this installation's values; the
shipped defaults stay in `settings/velocity.ini`. Plain HTTP is on `Port` (8088 shipped), HTTPS on `HTTPSPort` (8080
shipped), both bound to `Host` (`127.0.0.1` shipped).

## Coming back after a crash or reboot

`exp:velocity` has no command that installs a boot service. Use a systemd unit of your own that runs
`exp:velocity start`, `stop` and `graceful`, or the engine's own unit files; the routes and their differences are on
[Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md). Put the unit through the same review as any
other service unit.

The optional **watchdog** starts the engine again after a crash, an out-of-memory kill or a stop by another tool. It
never stops or restarts a running server, and it is off until installed:

```bash
./console exp:velocity watchdog install --apply --allow-root-user
./console exp:velocity watchdog status --allow-root-user
```

Every start or back-off is written to `var/log/velocity-watchdog.log` and recorded in the audit trail as
`system.velocity.watchdog.*`. For continuity planning this matters: an automatic restart is logged and can be shown to
an auditor afterwards.

## Deploying a change

Workers keep classes, settings and compiled templates in memory, so a change needs the right steps in the right order.
One command does them, with PASS, FAIL or SKIP for each step and a stop at the first failure:

```bash
./console exp:velocity deploy --dry-run --allow-root-user
./console exp:velocity deploy --allow-root-user
```

Attach the dry-run output to the change request; it lists exactly what the real run will do. Details:
[Deploying](../../../../guides/deploying.md) and the change pages of this section, for example
[Staging and approval](../upgrades-and-change/staging-and-approval.md).

## Logs and status

| What | Where |
|---|---|
| Access and error logs | `var/vc/qbix/log/<siteaccess>-access.log` and `-error.log` |
| Process output | `var/vc/qbix/run/console.log` |
| The kernel's own logs | `var/log/` or `var/<site>/log/`, as under any server |

`exp:velocity status` says `running` only for a server that answers an HTTP request and exits non-zero otherwise;
`status --json` is meant for monitoring. `/Q/health` answers "ok" to everyone and suits an external uptime check
([Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md)).

## Points for a security assessment

| Topic | What the sources say |
|---|---|
| Worker user | `[ServerSettings] User` and `Group`; workers must never run as root ([Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md)) |
| Control panel `/Q/panel` | this machine only, then a password of at least 16 characters; reach it remotely through an SSH tunnel ([Velocity control panel](../../../../features/6.0/velocity-control-panel.md)) |
| Dashboard `/Q/dashboard` | this machine, elsewhere only with `[DashboardSettings] Token`; leave `Remote` disabled unless a firewall restricts the port |
| Log file modes | `0640` and `0750` shipped, because an access log names visitors |
| Response cache | never serves signed-in visitors; needs engine 0.0.4.26 or later ([Velocity response cache](../../../hosting/performance/velocity-response-cache.md)) |
| HTTPS | certificates checked before use; a self-signed stand-in until a real one is usable ([Velocity HTTPS and certificates](../../../../features/6.0/velocity-https-certificates.md)) |
| Separation of sites | one server for several sites "is a routing convenience, not a security boundary"; run one Velocity per separate organization ([Multi-tenant isolation](../../../hosting/security/multi-tenant-isolation.md)) |

## Checklist

- [ ] `exp:velocity status` shows the server running; the site answers.
- [ ] A boot unit starts Velocity after a reboot; the watchdog is installed if wanted.
- [ ] Workers run as the site's user, not root.
- [ ] The panel password is set; `Remote` stays disabled; the dashboard token is in your secrets store.
- [ ] Deploys use `exp:velocity deploy`, and its dry run is attached to the change request.
- [ ] A real certificate is served, not the self-signed stand-in.

## Related pages

- [Hosting](README.md) index of this folder
- [Web server choices](web-server-choices.md)
- [Velocity workers and pools](../../../hosting/servers/velocity-workers-and-pools.md)
- [Velocity will not start](../../../hosting/runbooks/velocity-will-not-start.md) runbook
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
