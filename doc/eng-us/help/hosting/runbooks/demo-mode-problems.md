---
title: "Runbook: demo mode does not show, or the demo build fails with a generated password"
audience: hosting, admin, developer
category: hosting
order: 130
summary: What to check when a public demo runs without demo mode (no banner, an administrator password could be changed, no demo/mode line in security.log), when its build fails because the installer generated an administrator password, and when demo mode shows on a site where it must not.
keywords: runbook, troubleshooting, demo mode, EXP_DEMO_MODE, banner missing, generated password, initial-admin-password, exp:install, SetEnv, htaccess, PHP-FPM env, clear_env, Velocity restart, cron, security.log, demo/mode, system.demo.mode, production
type: help
---
# Runbook: demo mode does not show, or the demo build fails with a generated password

For whoever looks after a public demo of Exponential, and for whoever finds demo mode where it should not be. Demo
mode is on in a process exactly when that process has the environment variable `EXP_DEMO_MODE` with the value `1`
(read with `getenv()`, then `$_SERVER`); there is no setting for it anywhere else. Every problem below is a process
that has the variable, or does not, when it should. Background: [Demo mode](../../../bc/6.0/demo-mode.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).

## Contents

1. [Find out which processes have it](#find-out-which-processes-have-it)
2. [No banner in the administration](#no-banner-in-the-administration)
3. [The build fails: the installer generated a password](#the-build-fails-the-installer-generated-a-password)
4. [Demo mode was on and is gone after a restart](#demo-mode-was-on-and-is-gone-after-a-restart)
5. [The forgot-password page shows no notice](#the-forgot-password-page-shows-no-notice)
6. [Demo mode shows on a site where it must not](#demo-mode-shows-on-a-site-where-it-must-not)
7. [Related pages](#related-pages)

## Find out which processes have it

1. `grep 'demo/mode' var/log/security.log | tail`: one line per process that started with demo mode, with its pid and
   SAPI. `sapi=litespeed`, `sapi=fpm-fcgi` or `sapi=apache2handler` are web requests, `sapi=cli` is cron, the console
   or a build. (Velocity's workers are PHP command-line processes and log `sapi=cli` too.) No line for the web server's SAPI means the web requests
   do not have the variable.
2. **Audit:** `system.demo.mode` events, one per process.
3. A console check in the shell you use: `echo "$EXP_DEMO_MODE"` must print `1`.

Remember that only the exact value `1` counts (spaces around it are trimmed): `true`, `yes`, `on` and `01` leave it
off.

## No banner in the administration

| Cause | Check | Fix |
|---|---|---|
| The variable is set for cron or the console only | no `demo/mode` line with the web SAPI | set it for the web server too: `.htaccess` or virtual host `SetEnv`, the PHP-FPM pool, `fastcgi_param`, or Velocity's site envvars `/etc/vc/sites-available/<site>.envvars` ([examples](../../../examples/demo-mode/README.md)) |
| Velocity: the variable is in the shared `/etc/vc/envvars` | `grep 'demo/refused' var/log/security.log` shows `came from an envvars file other installations share`; `./console exp:info` says `refused (shared envvars)` | move the line to the demo's site envvars `/etc/vc/sites-available/<site>.envvars` (`<site>`: `./console exp:velocity layout`), remove it from the shared file, `./console exp:velocity restart` |
| `SetEnv` in a `.htaccess` the server does not read | the server has `AllowOverride None`, or the line is in another directory's `.htaccess` | put it in the virtual host, or allow overrides for the document root; with LiteSpeed check that `.htaccess` is read for the domain |
| `mod_env` is not loaded | the `<IfModule mod_env.c>` block is skipped | enable `mod_env`, or set the variable in the PHP-FPM pool |
| PHP-FPM drops the environment | the master has it, the pool has `clear_env = yes` (the default) | `env[EXP_DEMO_MODE] = 1` in the pool, then reload that pool's master |
| The wrong pool or master was changed | Plesk and cPanel run several PHP versions, each with its own master | change the pool of the domain's PHP version and reload that one |
| A value other than `1` | `true`, `yes`, `on` | write `1` |
| A stale page | the administration's pages are cached per user | the banner is drawn outside the cache-blocks; reload, and clear the template cache if a design of your own includes it inside one |
| A design of your own | the pagelayout does not include the banner | `{include uri='design:parts/exp_demo_mode_banner.tpl'}` outside every `{cache-block}` |

## The build fails: the installer generated a password

The demo's build stops with "the installer generated an administrator password" (or `var/log/initial-admin-password`
exists after `exp:install`). The installer replaces an administrator password it may not install by a random one: an
empty one always, and a well-known one (such as `publishing`) or one shorter than `MinPasswordLength` (such as
`publish`) unless demo mode is on. So the installer did not have the variable.

| Cause | Fix |
|---|---|
| The script sets the variable after the install command, or without `export` | `export EXP_DEMO_MODE=1` before the first command |
| `EXP_DEMO_MODE=1 cmd1 && cmd2` gives it to `cmd1` only | export it, or put it in front of the install command itself |
| `sudo` or `su` cleared the environment | `sudo --preserve-env=EXP_DEMO_MODE`, or set it inside the command `su -c` runs |
| The value is not exactly `1` | write `1` |
| The password is empty | an empty password is never installed, also in demo mode; pass `--password=` with the published value |
| The kickstarter runs as a child process | it passes its own environment on; set the variable for the kickstarter command |

The setup wizard in a browser takes the variable from the web server's PHP: there, the page **Site administrator**
shows a **Demo mode** note when it is on. Without the note, set the variable for the web requests first.

## Demo mode was on and is gone after a restart

- **Exponential Velocity:** solved by the demo's site envvars, `/etc/vc/sites-available/<site>.envvars` (`<site>` is
  the name `./console exp:velocity layout` prints). Put `export EXP_DEMO_MODE=1` there: `exp:velocity start` reads it
  at every start of this installation's server, so `exp:velocity restart` and `exp:velocity deploy` keep demo mode
  whichever shell runs them, and `exp:velocity layout` lists it under `site envvars`. Not the shared
  `/etc/vc/envvars`: every installation's server reads that file, so since 6.0.15 the workers refuse demo mode from it
  and log `demo/refused` (a demo set up there by an earlier release loses demo mode at the upgrade's restart). The
  file cannot set `PATH`, `LD_*`, `PHPRC` and the other loader and shell variables
  ([on-disk layout](../../../bc/6.0/velocity-ondisk-layout.md)). A restart with the engine's own `qbixctl` does not
  read the site file; restart with `exp:velocity`. The demo's own systemd unit
  ([`velocity-demo-mode.conf`](../../../examples/demo-mode/velocity-demo-mode.conf)) works too.
- **PHP-FPM:** a pool file rewritten by the control panel (Plesk, cPanel) loses lines it does not manage. Set it
  through the panel's PHP settings for the domain, or use `.htaccess` / the virtual host instead.
- **A rebuild:** a fresh `.htaccess` from `.htaccess_root` has no `SetEnv`; the build must append it every time.

## The forgot-password page shows no notice

The kernel passes the notice to `user/forgotpassword.tpl` as `demo_mode_text` (and to `user/password.tpl` as
`demo_mode_locked` and `demo_mode_text`). The kernel's designs show it. A site design that overrides those templates
shows it once its own release with the notice is installed; a design of your own needs:

```smarty
{if first_set( $demo_mode_text, '' )|ne( '' )}<div class="warning"><p>{$demo_mode_text|wash}</p></div>{/if}
```

The refusal itself does not depend on the template: no reset mail goes to an administrator in demo mode either way.

## Demo mode shows on a site where it must not

A **Demo mode** banner, a `demo/mode` line in `security.log` or a `system.demo.mode` audit event on a production site
means a process of that site has `EXP_DEMO_MODE=1`. Treat it as an incident: its administrator password can no longer
be changed, and the installers would accept a well-known one.

1. Find where it comes from: `grep -r EXP_DEMO_MODE` over the PHP-FPM pools, the virtual hosts, the `.htaccess`
   files, the service units and drop-ins, the crontabs, `/etc/environment`, the container definitions and the
   deployment templates.
2. Remove it there, reload or restart that service, and check that new `demo/mode` lines stop.
3. Ask who added it and why; check the audit for administrator changes while it was set.

## Related pages

- [Demo mode: the upgrade note](../../../bc/6.0/demo-mode.md)
- [Demo mode for hosts](../security/demo-mode-for-hosts.md), [Demo mode FAQ](../../demo-mode-faq.md)
- [Recipe: run a public demo on LiteSpeed and Plesk](../servers/run-a-public-demo-on-litespeed-and-plesk.md)
- [Demo mode examples](../../../examples/demo-mode/README.md)
- [Runbooks](README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
