---
title: LiteSpeed and OpenLiteSpeed
audience: hosting
category: hosting
order: 60
summary: What the Exponential sources say about LiteSpeed and OpenLiteSpeed hosting: the shipped Apache rules, the LSAPI SAPI, open_basedir, what to test before selling a plan, and where Velocity fits.
keywords: LiteSpeed, OpenLiteSpeed, LSAPI, litespeed SAPI, htaccess, .htaccess_root, shared hosting, open_basedir, rewrite, tier 3
type: help
---
# LiteSpeed and OpenLiteSpeed

This page is for hosting companies whose shared or managed hosting runs on LiteSpeed or OpenLiteSpeed. It answers:
whether Exponential runs there, which rules to use, what the code knows about LiteSpeed, and what to test before a
plan goes on sale. LiteSpeed's own menus and settings are not described here; check your LiteSpeed documentation for
them.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [Setting a site up](#setting-a-site-up)
3. [PHP under LiteSpeed](#php-under-litespeed)
4. [What to test before selling a plan](#what-to-test-before-selling-a-plan)
5. [Velocity on a LiteSpeed host](#velocity-on-a-litespeed-host)
6. [Checklist](#checklist)

## What the sources say

| Source | Statement |
|---|---|
| `README.md`, "The traditional options" | LiteSpeed and OpenLiteSpeed have an "`.htaccess`-compatible rewrite engine" that "reads Apache rules directly in most cases" |
| `README.md`, "When a traditional server is still the right answer" | where the hosting does not allow a long-lived process, "Apache or LiteSpeed with `.htaccess` is the answer" |
| [Operating system support, Web servers](../../os-support.md#web-servers) | LiteSpeed and OpenLiteSpeed: **tier 3**, expected to work; "reads Apache's rewrite rules in most cases" |
| [Operating system support, PHP SAPIs](../../os-support.md#php-sapis) | LiteSpeed (`litespeed`, LSAPI): "LiteSpeed's own SAPI" |
| `lib/ezutils/classes/ezexecution.php` | under LiteSpeed (lsapi) the shutdown function runs in the PHP binary's directory, which `open_basedir` does not allow; the kernel copes with that |

Tier 3 means "a reasoned expectation, not a test result" ([The four tiers](../../os-support.md#the-four-tiers)): the
project has not watched Exponential run on LiteSpeed in its own tests. "In most cases" is the honest wording; test
your own LiteSpeed version.

## Setting a site up

1. **Document root** is the installation root, the directory with `index.php`.
2. **Rewrite rules:** copy the shipped rules, as for Apache:

   ```bash
   cp .htaccess_root .htaccess
   ```

   or, as the site's user, `./console exp:setup:htaccess --apply`
   ([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)).
   LiteSpeed Enterprise reads it on the next request. OpenLiteSpeed reads only the rewrite rules, only with
   `autoLoadHtaccess` on and only after a restart; put the rules in its virtual host instead
   ([LiteSpeed example](../../../examples/web-servers/litespeed.md)).
   Use `.htaccess_root_static` instead (`--static`) only when the site serves a generated static cache
   ([Static cache](../performance/static-cache.md)).
3. **PHP:** 8.0 or later with the required extensions ([Chapter 2](../../../install/02-requirements.md#23-php-extensions)),
   `memory_limit` at least 64M (the setup wizard's floor; give production more), `date.timezone` set, `file_uploads`
   on ([Chapter 2, php.ini settings](../../../install/02-requirements.md#25-phpini-settings)).
4. **Writable directories:** `var/` and the `settings/` directories, written by one user
   ([Writable directories and permissions](../basics/writable-directories-and-permissions.md)).
5. **Install** with the setup wizard in the browser when the customer has no shell, or with `exp:install` or the
   kickstarter where a shell is available ([One-click installers](../provisioning/one-click-installers.md)).

## PHP under LiteSpeed

- The SAPI is `litespeed` (LSAPI). The web PHP and the command-line PHP are configured separately and are often
  different builds: give both the same version and extensions
  ([Chapter 2, Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php)).
- The setup wizard's optional test wants `open_basedir` empty. Shared hosting often sets it; the kernel already
  handles the LSAPI shutdown directory under `open_basedir`, but other features (for example the background preload
  run, which needs `proc_open`, `setsid` and the PHP binary) may be refused by it
  ([Preloading caches, section 7](../../../guides/preloading-caches.md#7-when-something-does-not-behave-as-expected)).
- Exponential identifies the server it runs under: the system report (`expSystemReport::serverKind()`) classifies a
  LiteSpeed SAPI as `other`, and the debug bar says "Served by" the server, the SAPI and the port, "not by Velocity",
  so support staff can see at a glance what answered.

## What to test before selling a plan

Run the four routing checks of [Web servers and engines](README.md#what-every-setup-has-in-common) first. Then:

| Test | Why |
|---|---|
| The front page, a content page and `/user/login` answer | the rewrite to `index.php` works |
| `/api/` reaches `index_rest.php` and `/content/treemenu` reaches `index_treemenu.php` | the two other front controllers of `.htaccess_root` |
| An uploaded image is shown, an uploaded file downloads | storage paths and `content/download` |
| `.php` below the root answers 404 | "No PHP file runs because its path was asked for" ([Chapter 8](../../../install/08-serving-the-site.md#82-how-a-request-is-served)) |
| A cronjob runs from the customer's crontab | `runcronjobs.php` under the CLI PHP ([Cron jobs](../operations/cron-jobs.md)) |
| A cache clear in Setup > Caches works | `var/` is writable by the web PHP |

## Velocity on a LiteSpeed host

Velocity is a long-running process; on shared LiteSpeed hosting it is usually not allowed. On a LiteSpeed server
that you control, Velocity can run beside it on its own ports and the LiteSpeed server can act as a reverse proxy, the
same shape as with Apache or nginx ([Velocity beside Apache or nginx](velocity-beside-apache-or-nginx.md)). The proxy
must be trusted and must set `X-Forwarded-Proto` ([Chapter 8, Behind a reverse proxy](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).

## Checklist

- [ ] `.htaccess` copied from `.htaccess_root`; document root is the installation root.
- [ ] Web and CLI PHP are the same version with the same extensions.
- [ ] The routing checks and the tests above pass on a staging account.
- [ ] `open_basedir` effects on preload and other background runs are known and documented for support.
- [ ] After each upgrade, `.htaccess` is refreshed from the new `.htaccess_root` (`./console exp:setup:htaccess` shows the diff; `--force --apply` copies after a backup).

**Demo mode on LiteSpeed.** LiteSpeed reads `SetEnv` in `.htaccess` like Apache and passes it to `lsphp`: `SetEnv EXP_DEMO_MODE 1` in a public demo's `.htaccess` switches demo mode on for its web requests, and `security.log` then shows `demo/mode on ... sapi=litespeed`. [Recipe: run a public demo on LiteSpeed and Plesk](run-a-public-demo-on-litespeed-and-plesk.md).

## Related pages

- [Apache with PHP-FPM](apache-with-php-fpm.md)
- [Shared hosting](../models/shared-hosting.md)
- [Choosing a web server](choosing-a-web-server.md)
- [Operating system support](../../os-support.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
