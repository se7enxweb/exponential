---
title: "Recipe: run a public demo on LiteSpeed and Plesk"
audience: hosting, admin
category: hosting
order: 130
summary: A step-by-step recipe for a public Exponential demo on a Plesk server with LiteSpeed and lsphp, as the project's own always-current demo runs - its own system user, a SQLite build every two hours from the current code, exp:install in demo mode with the published password, SetEnv EXP_DEMO_MODE 1 in .htaccess, a swap of the document root, and the checks that prove demo mode is on.
keywords: public demo, demo mode, EXP_DEMO_MODE, LiteSpeed, lsphp, LSAPI, Plesk, htaccess, SetEnv, SQLite, exp:install, cron, rebuild, swap, preload, security.log, demo/mode, sapi=litespeed, publishing
type: help
---
# Recipe: run a public demo on LiteSpeed and Plesk

This recipe is for a host or a project member who runs a **public demo**: an Exponential site anyone may sign in to
with a published administrator password, rebuilt from the current code on a schedule. It follows the shape of the
project's own always-current demo, which runs on a Plesk server with LiteSpeed and `lsphp`. The same steps work on
Apache with PHP-FPM; the differences are noted.

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
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

The demo runs in **demo mode** (the environment variable `EXP_DEMO_MODE=1`): nobody can change or reset an
administrator password, so the next visitor can still sign in, and the installers accept the published password.
What demo mode does and does not do: [Demo mode](../../../bc/6.0/demo-mode.md).

> **Warning:** A public demo hands its administration to strangers. Give it its own domain, system user, PHP handler
> and database file, no mail delivery to real addresses, no credentials that work anywhere else, and rebuild it
> often. Demo mode protects only the administrator password.

## Contents

1. [What you build](#what-you-build)
2. [Step 1: the subscription](#step-1-the-subscription)
3. [Step 2: the build script](#step-2-the-build-script)
4. [Step 3: demo mode for web requests](#step-3-demo-mode-for-web-requests)
5. [Step 4: the schedule](#step-4-the-schedule)
6. [Step 5: check it](#step-5-check-it)
7. [Variations](#variations)
8. [Related pages](#related-pages)

## What you build

- One Plesk subscription for the demo's domain, with its own system user and PHP served by LiteSpeed (`lsphp`, the
  LSAPI handler).
- A build script, run by that user from cron every two hours, which:
  1. clones the current code into a new build directory next to the live site and runs `composer install`;
  2. installs it with `exp:install` on SQLite, with `EXP_DEMO_MODE=1` exported for every step and the published
     password (`publishing`) given as it is;
  3. stops if the installer generated a password instead (that means the variable did not reach it);
  4. appends `SetEnv EXP_DEMO_MODE 1` to the new build's `.htaccess`;
  5. swaps the new build in as the document root and removes the old one;
  6. warms the new site with the kernel's preloader.
- A failed build is kept as `failed-<date>` next to the site, with its log; the live site stays as it was.

The generic script is [`install-demo-sqlite.sh`](../../../examples/demo-mode/install-demo-sqlite.sh); the crontab is
[`crontab-demo`](../../../examples/demo-mode/crontab-demo).

## Step 1: the subscription

1. Create the subscription for the demo's domain. Note its system user (for example `demo`) and its directory.
2. **PHP:** choose the LiteSpeed PHP handler (`lsphp`) of a supported PHP version for the domain. With PHP-FPM
   instead, see [Variations](#variations).
3. **Shell and cron:** the system user needs a shell for the scheduled task only if the build runs as that user's
   cron job; Plesk's **Scheduled Tasks** can run it without giving the user an interactive shell.
4. **Mail:** let the demo send nothing to real addresses. Its users are invented, with addresses in
   `example.invalid`, and the install's administrator address is one too.
5. Point the document root at a directory the script swaps (for example `httpdocs`), and keep builds in a sibling
   directory (for example `builds/`) that is not served.

## Step 2: the build script

Copy [`install-demo-sqlite.sh`](../../../examples/demo-mode/install-demo-sqlite.sh) to the builds directory, make it
executable for the site user only, and set its variables: the site directory, the document root name, the URL, the
branch, the published password. The essential lines are:

```sh
export EXP_DEMO_MODE=1
php bin/php/install.php --apply --db=sqlite --url="$URL" --password="$DEMO_PASSWORD" --email=demo@example.invalid
[ -e var/log/initial-admin-password ] && fail "the installer generated an administrator password"
```

- `export` gives demo mode to `exp:install` and every command after it. In demo mode the installer installs a
  well-known or short administrator password (`publish`, `publishing`) **as given**; without it, the password would be
  replaced by a generated one and written to `var/log/initial-admin-password`. The script treats that file as a failed
  build, because the published sign-in would not work.
- No reset of the password after the installation: it is not needed, and `exp:resetuserpassword` refuses to change an
  administrator password in demo mode anyway.
- The script holds no secret: the demo password is public by design. Keep real credentials (an API key, a mail
  relay) out of it and out of the demo.

## Step 3: demo mode for web requests

LiteSpeed reads `.htaccess` like Apache, and `SetEnv` reaches `lsphp` as an environment variable that PHP reads with
`getenv()`. The build appends to the new build's `.htaccess`:

```apache
<IfModule mod_env.c>
    SetEnv EXP_DEMO_MODE 1
</IfModule>
```

The file is [`htaccess-setenv.conf`](../../../examples/demo-mode/htaccess-setenv.conf). Because every build starts
from a fresh `.htaccess_root` copy, the script appends it on every build. Putting it in the subscription's
**Additional Apache directives** instead keeps it outside the build, if your LiteSpeed reads those directives for the
domain.

## Step 4: the schedule

The site user's crontab carries `EXP_DEMO_MODE=1` on its first line, so the kernel's cronjobs and the rebuild run in
demo mode, then the cronjob lines of `exponential.cron` and the rebuild every two hours:

```cron
EXP_DEMO_MODE=1
15 */2 * * * /var/www/demo.example.com/builds/install-demo-sqlite.sh >> /var/www/demo.example.com/builds/install.log 2>&1
```

Plesk's **Scheduled Tasks** writes the same crontab for the subscription's user; add the variable line there too, or
export it in the script (it does).

## Step 5: check it

After the first build:

- **The sign-in:** `admin` with the published password signs in to `/admin`. The administration shows the **Demo
  mode** banner on every page.
- **The password pages:** **Change password** for `admin` says that the password cannot be changed; the
  forgot-password page tells every visitor that administrator passwords cannot be reset (in the kernel's designs, and
  in a site design that overrides the page once its release with the notice is installed).
- **The log:** `grep 'demo/mode' var/log/security.log` shows a line per process, with `sapi=litespeed` for the web
  requests and `sapi=cli` for cron and the build:

  ```
  [ Oct 19 2026 18:00:00 ] [203.0.x.x] demo/mode on pid=4711 sapi=litespeed EXP_DEMO_MODE=1: administrator passwords cannot be changed or reset, ...
  ```

- **The audit:** **Audit** in the administration lists `system.demo.mode` events.
- **The build log:** the last line of a good build is `PASS: demo rebuilt (<date>)`; a failed build is kept as
  `builds/failed-<date>` with its install summary.

If one of these is missing: [Demo mode problems](../runbooks/demo-mode-problems.md).

## Variations

| Server | Where the variable goes |
|---|---|
| Apache with PHP-FPM (Plesk's FPM handler) | the same `.htaccess` lines, or `env[EXP_DEMO_MODE] = 1` in the domain's pool ([`php-fpm-pool-demo.conf`](../../../examples/demo-mode/php-fpm-pool-demo.conf)); Plesk writes the pool file, so use its PHP settings for the domain where it offers them |
| nginx with PHP-FPM | the pool, or `fastcgi_param EXP_DEMO_MODE 1` ([`nginx-fastcgi-demo.conf`](../../../examples/demo-mode/nginx-fastcgi-demo.conf)) |
| Exponential Velocity | `export EXP_DEMO_MODE=1` in the demo's site envvars `/etc/vc/sites-available/<site>.envvars`: `exp:velocity start` passes it to this installation's server at every start, so a restart or deploy from any shell keeps it (the demo's own unit environment, [`velocity-demo-mode.conf`](../../../examples/demo-mode/velocity-demo-mode.conf), works too). Never the shared `/etc/vc/envvars`, which every installation's server reads and the workers refuse for demo mode |
| cPanel | the domain's `.htaccess` (Apache or LiteSpeed), and the variable line in the account's cron jobs |
| A container | [`docker-demo.env`](../../../examples/demo-mode/docker-demo.env) for the demo service only |

## Related pages

- [Demo mode: the upgrade note](../../../bc/6.0/demo-mode.md)
- [Demo mode for hosts](../security/demo-mode-for-hosts.md), [Demo mode problems](../runbooks/demo-mode-problems.md),
  [Demo mode FAQ](../../demo-mode-faq.md)
- [Demo mode examples](../../../examples/demo-mode/README.md)
- [LiteSpeed and OpenLiteSpeed](litespeed.md), [Plesk](../panels/plesk.md)
- [exp:install for hosts](../provisioning/exp-install.md), [Cron jobs](../operations/cron-jobs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
