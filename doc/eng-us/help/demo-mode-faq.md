---
title: Demo mode FAQ
audience: admin, hosting, developer, press, advocate, trainer, partner, sales-marketing
category: administration
order: 34
summary: Short answers about demo mode (EXP_DEMO_MODE=1) - what it is for, what it blocks and what it does not, how to switch it on and off, why it cannot come from a setting, which passwords the installers accept, how to tell it is on, and why it must never reach a real site.
keywords: demo mode, FAQ, questions, EXP_DEMO_MODE, public demo, administrator password, password reset, forgot password, publish, publishing, installer, banner, security.log, system.demo.mode, production, Velocity, LiteSpeed, PHP-FPM
type: help
---
# Demo mode FAQ

Short answers about **demo mode**, for anyone who runs, shows, sells or teaches with a public demo of Exponential.
The full reference is [Demo mode: the upgrade note](../bc/6.0/demo-mode.md); the administrator's view is
[Demo mode](demo-mode.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../examples/web-servers/nginx.md), [LiteSpeed](../examples/web-servers/litespeed.md),
> [Caddy](../examples/web-servers/caddy.md), [IIS](../examples/web-servers/iis.md), [Velocity](../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](hosting/panels/plesk.md), [cPanel](hosting/panels/cpanel.md), [DirectAdmin](hosting/panels/directadmin.md), [ISPConfig](hosting/panels/ispconfig.md).

## Contents

1. [What is demo mode for?](#what-is-demo-mode-for)
2. [What does it block?](#what-does-it-block)
3. [What does it not block?](#what-does-it-not-block)
4. [How do I switch it on?](#how-do-i-switch-it-on)
5. [Why is there no setting for it?](#why-is-there-no-setting-for-it)
6. [Which passwords do the installers accept in demo mode?](#which-passwords-do-the-installers-accept-in-demo-mode)
7. [How do I know it is on?](#how-do-i-know-it-is-on)
8. [Who counts as an administrator?](#who-counts-as-an-administrator)
9. [Can I use it on a real site?](#can-i-use-it-on-a-real-site)
10. [How do I switch it off?](#how-do-i-switch-it-off)
11. [Related pages](#related-pages)

## What is demo mode for?

For a **public demo**: a site whose administrator sign-in is published so that anyone can try the administration.
Without demo mode the first visitor could change the administrator password and lock everyone else out. With it,
administrator passwords cannot be changed or reset, so the published sign-in keeps working until the next rebuild.

## What does it block?

Every way of setting a new password for an administrator: the **Change password** page, **Forgot your password?**
(no reset mail is sent; the page tells every visitor that administrator passwords cannot be reset), the password field
of the user account in the edit form, `exp:resetuserpassword` (also with `--allow-root-user`), the web services, the
`user/password` and `user/forgotpassword` operations, and any code that stores a user: the kernel keeps an
administrator's stored password. Each refusal says "This installation is a public demo: the passwords of
administrators cannot be changed or reset." and is recorded in the audit with the reason `demo_mode`.

## What does it not block?

Everything else. An administrator on a demo can still edit content, create accounts, change roles and settings,
install packages and upload files, so a demo is rebuilt on a schedule. Other accounts (editors, members) change and
reset their own passwords as usual, with all the usual rules. Signing in is never affected.

## How do I switch it on?

Set the environment variable `EXP_DEMO_MODE=1` for every PHP process of the demo: the web server's PHP (`.htaccess`
or virtual host `SetEnv`, the PHP-FPM pool, nginx's `fastcgi_param`, the environment of Exponential Velocity), the
cron jobs and the console. Ready-made files: [Demo mode examples](../examples/demo-mode/README.md); a complete setup:
[Recipe: run a public demo on LiteSpeed and Plesk](hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md).

## Why is there no setting for it?

So that it cannot be switched on by accident. An INI setting would travel with a copied settings directory, a
restored database or a package; a request parameter could be sent by anyone. The environment belongs to the server of
one site. Only the exact value `1` counts; `0`, `true`, `yes` and `on` leave it off.

## Which passwords do the installers accept in demo mode?

The administrator password of a new installation may be well-known and shorter than `MinPasswordLength`, so
`publish` and `publishing` are installed as given by the setup wizard, the kickstarter and `exp:install`. An empty
password is still refused (the kickstarter and `exp:install` generate one). Every other account, and every password
set after the installation, keeps every rule. Without demo mode the installers replace such a password with a
generated one.

## How do I know it is on?

- The administration shows a yellow **Demo mode** banner on every page.
- `var/log/security.log` has a `demo/mode on pid=... sapi=...` line for every process that started with it.
- The audit has a `system.demo.mode` event per process.
- Console commands print `Demo mode is on (EXP_DEMO_MODE=1) ...` on standard error.

## Who counts as an administrator?

An account whose roles give it unlimited access to every module and function: `admin`, everyone in **Administrator
users**, and anyone with the **Administrator** role. An account whose roles cannot be read counts as one too.

## Can I use it on a real site?

No. Demo mode means the administrator password is meant to be known and cannot be changed. Never set the variable
server-wide, in a shared pool, a base image or a template other sites are built from. If the banner or a `demo/mode`
line ever appears on a real site, remove the variable at once: [Demo mode problems](hosting/runbooks/demo-mode-problems.md).

## How do I switch it off?

Remove the variable from every place it was set and reload or restart that service (PHP-FPM, the web server,
Velocity) and the crontab. There is nothing to undo in the database: once the variable is gone, passwords can be
changed again, and the banner disappears with the next request.

## Related pages

- [Demo mode](demo-mode.md), [Demo mode: the upgrade note](../bc/6.0/demo-mode.md)
- [Demo mode for hosts](hosting/security/demo-mode-for-hosts.md),
  [Demo mode for developers](extension-development/part-8-reference/demo-mode-for-developers.md)
- [Demo mode problems](hosting/runbooks/demo-mode-problems.md)
- [Environment variables](../guides/environment-variables.md)
- [Free documentation](free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
