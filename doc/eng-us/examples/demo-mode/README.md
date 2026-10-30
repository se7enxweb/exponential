---
title: Demo mode examples
audience: hosting, admin, developer
category: examples
order: 95
summary: Example files that set EXP_DEMO_MODE=1 for a public demo of Exponential - .htaccess and Apache virtual host SetEnv, a PHP-FPM pool, nginx, a systemd drop-in for Velocity, a crontab, a container environment file - and the generic shape of a demo rebuild script.
keywords: demo mode, demo.exponential.earth, EXP_DEMO_MODE, public demo, example, htaccess, SetEnv, Apache, LiteSpeed, lsphp, PHP-FPM, env, nginx, fastcgi_param, systemd, drop-in, Velocity, crontab, docker, env_file, install-demo-sqlite.sh, exp:install
type: example
---
# Demo mode examples

A public demo of Exponential publishes its administrator sign-in and runs with the environment variable
**`EXP_DEMO_MODE=1`**: administrator passwords cannot be changed or reset, the installers accept the published demo
password (`publish` or `publishing`), and the administration shows a **Demo mode** banner. The variable must reach
every PHP process of the demo, and no process of any other site. The files here show how, one per place PHP runs.
What demo mode does, and its risks: [Demo mode](../../bc/6.0/demo-mode.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../web-servers/nginx.md), [LiteSpeed](../web-servers/litespeed.md),
> [Caddy](../web-servers/caddy.md), [IIS](../web-servers/iis.md), [Velocity](../web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

> **Warning:** Copy these files only into the configuration of a demo whose content, users and settings are all
> throwaway. Never into a shared pool, a server-wide configuration, a base image or a template other sites are built
> from.

## Contents

1. [The files](#the-files)
2. [Which one you need](#which-one-you-need)
3. [Checking it](#checking-it)
4. [Related pages](#related-pages)

## The files

| File | Where it goes |
|---|---|
| [`htaccess-setenv.conf`](htaccess-setenv.conf) | appended to the demo's `.htaccess`: Apache (mod_php, or PHP-FPM through `mod_proxy_fcgi`) and LiteSpeed with `lsphp` |
| [`apache-vhost-demo.conf`](apache-vhost-demo.conf) | `SetEnv` in the demo's Apache virtual host (also a Plesk "Additional Apache directives" box) |
| [`php-fpm-pool-demo.conf`](php-fpm-pool-demo.conf) | `env[EXP_DEMO_MODE] = 1` in the demo's own PHP-FPM pool |
| [`nginx-fastcgi-demo.conf`](nginx-fastcgi-demo.conf) | `fastcgi_param EXP_DEMO_MODE 1` where nginx passes requests to PHP-FPM |
| `/etc/vc/sites-available/<site>.envvars` (no file here: one line) | **recommended for Exponential Velocity**: `export EXP_DEMO_MODE=1` in the demo's own site envvars; `exp:velocity start` reads it for this installation's server only, so restarts and deploys from any shell keep it. Never the shared `/etc/vc/envvars`: every installation's server reads that, and a worker refuses demo mode from it ([demo mode](../../bc/6.0/demo-mode.md#velocity-one-installation-only)) |
| [`velocity-demo-mode.conf`](velocity-demo-mode.conf) | a systemd drop-in with `Environment=EXP_DEMO_MODE=1` for the unit that starts the demo's own Exponential Velocity |
| [`crontab-demo`](crontab-demo) | the demo user's crontab: `EXP_DEMO_MODE=1` for the cronjobs and the scheduled rebuild |
| [`docker-demo.env`](docker-demo.env) | an environment file for the demo container only (`--env-file`, `env_file:`) |
| [`install-demo-sqlite.sh`](install-demo-sqlite.sh) | the generic shape of a rebuild script: clone, `composer install`, `exp:install` on SQLite in demo mode, check that the published password went in as given, `SetEnv` in `.htaccess`, swap, warm |
| [`install-demo-sqlite-demo-exponential-earth.sh`](install-demo-sqlite-demo-exponential-earth.sh) | the real script of `https://demo.exponential.earth/` (daily from cron at 04:20, sign-in `admin` / `publish` at `edit.demo.exponential.earth`): the generic shape plus siteaccess matching written after the install (`MatchOrder=host_uri;host;uri`: `HostUriMatchMapItems[]` for each siteaccess the installer made under the demo's host, `HostMatchMapItems[]` for the admin on its own host and for the site, so `/admin` on the site's host is a 404), a check that the settings name only the demo's hosts, and `.htaccess` rules that redirect the vhost's old names to the new ones |

## Which one you need

- **Web requests:** one of `.htaccess`, the virtual host, the PHP-FPM pool, nginx or (for Velocity) the site envvars `/etc/vc/sites-available/<site>.envvars` or the systemd
  drop-in, depending on what serves PHP. PHP reads the variable with `getenv()` first, then `$_SERVER`; both the
  FastCGI parameters of PHP-FPM and Apache's `SetEnv` arrive that way.
- **Cron and the console:** the crontab line, and `export EXP_DEMO_MODE=1` in the shell of anyone who runs commands
  on the demo. A process without the variable runs with demo mode off.
- **The installation:** `EXP_DEMO_MODE=1` in the environment of `exp:install`, the kickstarter or the setup wizard's
  PHP. Without it a well-known or short password is replaced by a generated one, which the rebuild script treats as a
  failed build.
- **Containers:** the environment file, for the demo service only.
- **Two-step sign-in:** a new installation requires it of Administrator users and Editors
  (`settings/override/sevenxauthentication2fa.ini.append.php`, `Enforce2FA=enabled`). The visitors of a public demo
  share one administrator account, so the first one to set up an authenticator would lock out every other one. Both
  rebuild scripts here switch it off after the installation (`Enforce2FA=disabled`); `DEMO_ENFORCE_2FA=1` keeps the
  default, for a demo that shows the second step and is not shared.
- **Queue and imports:** `crontab-demo` runs the cron parts `expqueue` and `sqliimport` every minute with the admin
  siteaccess (`-s admin`), where sqliimport is switched on.

## Checking it

- The administration shows the **Demo mode** banner on every page; the forgot-password page tells every visitor that
  administrator passwords cannot be reset.
- `grep 'demo/mode' var/log/security.log` lists one line per process that started with demo mode, with its SAPI
  (`fpm-fcgi`, `litespeed`, `cli` and so on). **Setup > Log files** groups them as `demo/mode`.
- The audit has a `system.demo.mode` event per process.
- When something does not work: [Demo mode problems](../../help/hosting/runbooks/demo-mode-problems.md).

## Related pages

- [Demo mode: the upgrade note](../../bc/6.0/demo-mode.md)
- [Run a public demo on LiteSpeed and Plesk](../../help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md)
- [Demo mode for hosts](../../help/hosting/security/demo-mode-for-hosts.md), [Demo mode FAQ](../../help/demo-mode-faq.md)
- [Environment variables](../../guides/environment-variables.md)
- [Web server examples](../web-servers/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
