---
title: Shared hosting or a small server
audience: individual, website-creator, customer
category: personal-sites
order: 1634
summary: Where one person's Exponential site should run - shared hosting with Apache or LiteSpeed and the setup wizard, a small virtual server with Velocity or PHP-FPM, or a machine at home - what works where according to the project's sources, what to ask a host, and the work each choice leaves to you.
keywords: shared hosting, VPS, virtual server, small server, home server, Velocity, Apache, LiteSpeed, nginx, PHP-FPM, .htaccess, cron, mail, shell, FTP, hosting questions, individual
type: help
---
# Shared hosting or a small server

This page is for individuals deciding where their site will live. There are three realistic places; each works,
and each leaves different work to you. The hosting section has the detail for hosts
([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).

## Contents

1. [The three places](#the-three-places)
2. [Shared hosting](#shared-hosting)
3. [A small virtual server](#a-small-virtual-server)
4. [A machine at home](#a-machine-at-home)
5. [Questions to ask any host](#questions-to-ask-any-host)
6. [Moving later](#moving-later)

## The three places

| | Shared hosting | Small virtual server | Machine at home |
|---|---|---|---|
| You manage | The site | The site and the operating system | Everything, including the connection |
| Shell access | Often none | Yes | Yes |
| Velocity | Usually not allowed | Yes | Yes |
| Served by | Apache or LiteSpeed with `.htaccess` | Velocity, or Apache or nginx with PHP-FPM | Same as a server |
| Install with | The setup wizard | `exp:install` or the kickstarter | Same as a server |
| Updates of the system | The host | You | You |
| Suits | Personal sites, small blogs, family sites | Portfolios, clubs, anyone who wants control | Learning; small private family sites, with care |

## Shared hosting

The project's sources say it plainly: "Your hosting will not let you run a long-lived process. Shared hosting usually
will not. Apache or LiteSpeed with `.htaccess` is the answer there." (root `README.md`, quoted in
[Shared hosting](../../hosting/models/shared-hosting.md#what-the-sources-say)). What you need to know:

- **Install with the setup wizard**, by FTP and browser ([Choosing an install method](choosing-an-install-method.md)).
- **SQLite** removes the need for a database account; or use the panel's database.
- **Cron**: schedule `runcronjobs.php` in the panel's cron page with the same PHP version as the site; newsletters and
  scheduled publishing depend on it ([Shared hosting, Cron jobs](../../hosting/models/shared-hosting.md#cron-jobs)).
- **PHP settings** per account: memory, upload sizes, the functions the cronjob page needs
  ([PHP settings for Exponential](../../hosting/basics/php-settings-for-exponential.md)).
- **No Velocity** in most cases; the site is still fast with the 6.0.15 cache fixes.

## A small virtual server

A virtual private server gives you a whole (small) machine:

- **Velocity** serves the site without Apache or nginx, and handles HTTPS certificates itself
  ([Serving the site, Velocity](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)).
- **Or** Apache or nginx with PHP-FPM, if you know them.
- **You** keep the operating system updated, the firewall closed, backups running and mail working
  ([VPS hosting](../../hosting/models/vps-hosting.md)).
- Size: a small site needs little; see [Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md).

If keeping an operating system patched sounds like a chore you will skip, choose shared or managed hosting instead.
An unpatched server is worse than a slower one.

## A machine at home

Possible, and good for learning. For a public site it brings problems that hosting solves for you: a changing
address, an internet provider that may block incoming connections or outgoing mail, power and hardware failures, and
your home network exposed to the internet. A family site that only relatives use is the most reasonable case; still
keep backups away from that machine.

## Questions to ask any host

| Question | Why |
|---|---|
| Which PHP versions, and can I choose 8.x per site? | PHP 7.4 or later is required |
| Are `intl`, `mbstring`, `xsl`, `gd` or ImageMagick, `sqlite3` available? | Required or needed extensions ([PHP versions and extensions](../../hosting/basics/php-versions-and-extensions.md)) |
| Can I run cron every few minutes? | Newsletters, scheduled publishing |
| Can the site send mail, and from my domain? | Registration, password reset, forms, newsletters |
| Is there shell access? | Composer, console install, maintenance |
| Are long-running processes allowed? | Velocity |
| How are backups done, and can I download them? | Your own copy |
| Is HTTPS included? | Signing in must be encrypted |

More in [What to ask your host](../../customers/healthy/what-to-ask-your-host.md).

## Moving later

Starting on shared hosting and moving to a server later is normal. A move is: the code, the database (with SQLite,
one file), `var/storage` and your settings, then the new address in the settings and DNS
([Backups for one person](../running-it/backups-for-one-person.md), [Domain names and HTTPS](domain-names-and-https.md)).
Rehearse it once with a copy.

## Related pages

- [Setup for individuals](README.md)
- [Shared hosting](../../hosting/models/shared-hosting.md)
- [VPS hosting](../../hosting/models/vps-hosting.md)
- [Hosting for a website](../../creators/website-creators/hosting-for-a-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
