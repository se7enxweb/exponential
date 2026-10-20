---
title: Demo mode for hosts
audience: hosting, admin
category: hosting
order: 150
summary: How a host runs a public Exponential demo with the environment variable EXP_DEMO_MODE=1 - setting it for PHP-FPM, Apache, Exponential Velocity, cron and containers, installing the published demo password, checking it, and keeping it away from every production site.
keywords: demo mode, EXP_DEMO_MODE, public demo, demonstration site, environment variable, PHP-FPM env, SetEnv, Velocity, systemd, crontab, container, exp:install, kickstarter, security.log, system.demo.mode, provisioning
type: help
---
# Demo mode for hosts

This page is for the system administrators of a host that runs a public demonstration site of Exponential, one whose
administrator sign-in is published. Demo mode is switched on by the environment variable **`EXP_DEMO_MODE=1`**. While
it is on, nobody can change or reset the password of an administrator (so the next visitor can still sign in), the
installers accept the published demo password, and the administration shows a **Demo mode** banner.

> **Warning:** Demo mode tells the world that the administrator password is known. Set it only for a site whose
> content, users and settings are all throwaway, on its own system user, pool and database, and never for a shared
> pool, a server-wide environment or a template that other sites are built from.

## Contents

1. [The rules of the variable](#the-rules-of-the-variable)
2. [Setting it for each server](#setting-it-for-each-server)
3. [Installing a demo](#installing-a-demo)
4. [Checking it](#checking-it)
5. [Keeping it away from production](#keeping-it-away-from-production)
6. [Risks to tell the demo's owner](#risks-to-tell-the-demos-owner)
7. [Related pages](#related-pages)

## The rules of the variable

- **Environment only.** No INI setting, no request parameter and no administration page can switch it on, so a
  settings file or a database copied from a demo does not carry it to another site.
- **Only the exact value `1`.** `0`, `true`, `yes`, `on` and an empty value leave it off.
- **Per process.** PHP reads it with `getenv()` first, then `$_SERVER`, on every call. Every process that serves or
  maintains the demo needs it: the web server's PHP, Exponential Velocity if it serves the site, cron jobs and console
  commands. A process without it runs with demo mode off.

## Setting it for each server

| Where PHP runs | How to set it, for the demo only |
|---|---|
| PHP-FPM | in the demo's own pool file: `env[EXP_DEMO_MODE] = 1`, then reload the pool's master |
| Apache (mod_php, or PHP-FPM through mod_proxy_fcgi) | in the demo's virtual host: `SetEnv EXP_DEMO_MODE 1` |
| LiteSpeed / OpenLiteSpeed (lsphp) | the demo's virtual host or external application environment: `EXP_DEMO_MODE=1` |
| Exponential Velocity | the environment of the process that starts the server: `Environment=EXP_DEMO_MODE=1` in its systemd unit or a drop-in, `/etc/default/exponential-velocity` for the packaged unit, or `export EXP_DEMO_MODE=1` before `./console exp:velocity start`. The workers inherit it |
| FrankenPHP or another application server | the environment of the server process |
| Cron | a line `EXP_DEMO_MODE=1` at the top of the demo user's crontab |
| Console | `export EXP_DEMO_MODE=1` in the demo user's shell profile, or before each command |
| Containers | `ENV EXP_DEMO_MODE=1` in the demo image, or `-e EXP_DEMO_MODE=1` / `environment:` for the demo service only |

**Velocity restarts.** `exp:velocity restart` and `exp:velocity deploy` start the new server with the environment of
the shell they run in. Set the variable where the service manager keeps it (the unit), or a restart from another shell
silently drops demo mode. Velocity's own `/etc/vc/envvars` file is read for the server's own settings, not exported to
the PHP workers, so it is not the place for `EXP_DEMO_MODE`.

## Installing a demo

Set the variable for the installation too. The installers (setup wizard, kickstarter, `exp:install`) then accept the
published administrator password even though it is a well-known word and shorter than `MinPasswordLength`, instead
of replacing it with a random one:

```sh
EXP_DEMO_MODE=1 ./console exp:install --db=sqlite --password='<the published demo password>' ...
```

Any non-empty password works, so both `publish` (seven characters) and `publishing` do; an empty one is still
refused. Only the administrator password of the new installation is relaxed: every other account keeps
`[UserSettings] MinPasswordLength` (10 as shipped) and the well-known list. Do not reset the password
after the installation with `exp:resetuserpassword`: in demo mode that is refused, and it is no longer needed.

A demo that is rebuilt on a schedule (every few hours, from the current code) does this in its build script: install
with the variable, swap the new site in, keep the variable in the running site's PHP environment and crontab. The
script must not write the variable into `config.php`, `settings/` or anything that is copied to other sites.

## Checking it

- The administration shows the **Demo mode** banner on every page.
- `grep 'demo/mode' var/log/security.log` lists every process that started with it (one line per process, with its
  pid and SAPI); **Setup > Log files** groups them as `demo/mode`.
- The audit has a `system.demo.mode` event per process (always recorded).
- A console command prints `Demo mode is on (EXP_DEMO_MODE=1) ...` on standard error.

## Keeping it away from production

- Never set it server-wide (`/etc/environment`, a global `SetEnv`, the default pool, a base image) or in a
  provisioning template.
- Alert on it: a `demo/mode` line in the `security.log` of a production site, or a `system.demo.mode` audit event,
  means the variable reached a process it should not have.
- Add it to the security review: `grep -r EXP_DEMO_MODE` over the PHP-FPM pools, the virtual hosts, the service units
  and the crontabs of production machines must find nothing.

## Risks to tell the demo's owner

- Demo mode only protects administrator passwords. A visitor signed in as an administrator can still create accounts,
  change roles and settings, install packages and upload files: rebuild the demo on a schedule.
- Keep real data, real mail addresses and secrets out of the demo, and send its mail to a file or a sink.
- Other published sign-ins (an editor, a member) are not protected.
- If the variable is lost (a restart from a shell without it), demo mode is off without a sign other than the missing
  banner.

## Related pages

- [Demo mode](../../demo-mode.md) (for administrators)
- [Demo mode: the upgrade note](../../../bc/6.0/demo-mode.md)
- [First sign-in and default passwords](first-sign-in-and-default-passwords.md)
- [Hardening for hosts](hardening-for-hosts.md), [Security for hosts: overview](README.md)
- [Setting up a demo installation](../../press/setting-up-a-demo-installation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
