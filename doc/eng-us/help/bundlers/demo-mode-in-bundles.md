---
title: Demo mode in bundles
audience: bundler, hosting
category: bundling
order: 340
summary: When a bundle may use demo mode (EXP_DEMO_MODE=1) and when it must never - what it changes, how a demo bundle sets it for every process, installing with a published password, rebuilding on a schedule, and how to keep it out of every bundle meant for real sites.
keywords: demo mode, EXP_DEMO_MODE, public demo, published password, demo bundle, environment variable, PHP-FPM, Velocity envvars, container, cron, rebuild, banner
type: help
---
# Demo mode in bundles

This page is for bundlers who build public demonstrations, and for every other bundler, so they keep demo mode
out of their bundles. Demo mode is switched on only by the environment variable `EXP_DEMO_MODE=1`
([Demo mode](../demo-mode.md)).

## Contents

1. [What demo mode does](#what-demo-mode-does)
2. [The rule for bundles](#the-rule-for-bundles)
3. [Setting it in a demo bundle](#setting-it-in-a-demo-bundle)
4. [Installing with a published password](#installing-with-a-published-password)
5. [Rebuilding on a schedule](#rebuilding-on-a-schedule)
6. [Checks](#checks)

## What demo mode does

- Administrator passwords "cannot be changed or reset": the **Change password** form, **Forgot your password?**, user
  edit, `exp:resetuserpassword`, web services and imports refuse it for administrators.
- Every administration page shows a **Demo mode** banner.
- The installers accept a published, well-known or short administrator password (`publish`, `publishing`); an empty
  one is still refused.
- It "does not make the site read-only: a visitor can edit content, create users, change roles and settings. That is
  why a demo is rebuilt from scratch on a schedule."

Source: [Demo mode](../demo-mode.md), [Demo mode: the upgrade note](../../bc/6.0/demo-mode.md).

## The rule for bundles

| Bundle | Demo mode |
|---|---|
| Public demo with a published sign-in | on, for that demo's processes only |
| Trade-show or sales laptop showing a published sign-in | on, if the sign-in is published |
| Classroom where every student knows the password | on, only if the image never becomes a real site |
| **Every bundle meant for a real site** | **never** |

"Demo mode means that the administrator password is known to everyone." Because it is environment only ("No INI
setting, no request parameter and no administration page can switch it on"), a settings file or database copied from
a demo does not carry it. Your images, units and panel templates for real sites must not set it either.

## Setting it in a demo bundle

Every process that serves or maintains the demo needs it ([Demo mode for hosts](../hosting/security/demo-mode-for-hosts.md#setting-it-for-each-server)):

| Where PHP runs | Setting |
|---|---|
| PHP-FPM | `env[EXP_DEMO_MODE] = 1` in the demo's own pool |
| Apache | `SetEnv EXP_DEMO_MODE 1` in the demo's virtual host |
| Velocity | `export EXP_DEMO_MODE=1` in `/etc/vc/envvars`, or the environment of the process that starts the server |
| Cron | `EXP_DEMO_MODE=1` at the top of the demo user's crontab |
| Containers | `ENV EXP_DEMO_MODE=1` in the demo image, or `-e EXP_DEMO_MODE=1` for the demo service only |

"Only the exact value `1`" turns it on. `config.env.php` explicitly says demo mode "IS NOT A SETTING OF THIS FILE": a
constant there does nothing (`config.env.php.example`).

## Installing with a published password

```bash
EXP_DEMO_MODE=1 php bin/php/console exp:install --db=sqlite --password='<the published demo password>'
```

The same works for the kickstarter (`[site_admin] Password`) and the setup wizard. "Only the administrator password
of the new installation is relaxed."

## Rebuilding on a schedule

"A demo that is rebuilt on a schedule (every few hours, from the current code) does this in its build script: install
with the variable, swap the new site in, keep the variable in the running site's PHP environment and crontab. The
script must not write the variable into `config.php`, `settings/` or anything that is copied to other sites."
`exp:install` on SQLite is "the usual choice" for such demos ([4.7](../../install/04-choosing-an-install-method.md)).

## Checks

- [ ] The administration of the demo shows the **Demo mode** banner.
- [ ] Bundles for real sites do not show it, and none of their files mention `EXP_DEMO_MODE`.
- [ ] The demo's page says plainly that it is public and reset regularly ("Do not enter anything private here").

## Related pages

- [Education and demo bundles](education-and-demo-bundles.md)
- [Demo mode FAQ](../demo-mode-faq.md), [Demo mode problems](../hosting/runbooks/demo-mode-problems.md)
- [Live demo safety](../advocates/live-demo-safety.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
