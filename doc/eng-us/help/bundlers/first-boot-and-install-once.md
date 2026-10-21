---
title: First boot and install once
audience: bundler, hosting
category: bundling
order: 230
summary: Why a bundle installs the site at the recipient's first start and never at build time or at every start, the documented "already installed" test, a first-boot sequence with ownership, secrets, the install, the password hand-over and clean-up, and what a bundle must never contain when it leaves your hands.
keywords: first boot, install once, entry point, already installed, DatabaseSettings, settings/override, idempotent, cloud-init, systemd, container, image, secrets, initial-admin-password
type: help
---
# First boot and install once

This page is for bundlers of images, containers and appliances. The installers write secrets, unique keys and the
recipient's addresses into the installation. A bundle should therefore ship the code ready to install and run the
installation **once**, at the recipient's first start.

## Contents

1. [Why not install at build time](#why-not-install-at-build-time)
2. [Why not install at every start](#why-not-install-at-every-start)
3. [The already-installed test](#the-already-installed-test)
4. [A first-boot sequence](#a-first-boot-sequence)
5. [What must not be in the bundle](#what-must-not-be-in-the-bundle)

## Why not install at build time

An installation made on your build machine and shipped to many recipients would give every recipient:

- the same administrator password, or one your build log has seen;
- the same database credentials in `settings/siteaccess/*/site.ini.append.php`;
- the same e-mail preferences' site secret, which `CreateSites` writes to
  `settings/override/mailpreferences.ini.append.php` ([4.4](../../install/04-choosing-an-install-method.md#settings));
- your build's URL and host names instead of theirs.

The exception is a public demo that is rebuilt from scratch on a schedule ([Education and demo bundles](education-and-demo-bundles.md)).

## Why not install at every start

`exp:install` uses `--db-action=remove` by default, which empties the database first. An entry point that installs
at every start destroys the site at every restart. "In a container, run the install once (for example when
`settings/override/site.ini.append.php` has no `[DatabaseSettings]`), not at every start"
([6.11](../../install/06-kickstarter.md#611-in-ci-and-containers)).

## The already-installed test

```bash
grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null
```

"The test ... is the same test `exp:install` itself uses to decide that a directory already holds an installation"
([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)). It must read the
persistent copy of `settings/override/`: in a container that directory lives on a volume
([Container images](container-images.md)).

## A first-boot sequence

1. **Wait for the database** (server engines only) until it accepts connections.
2. **Check ownership** of `var/`, `settings/` and `design/` for the site's user
   ([File permissions and var/ ownership](file-permissions-and-var-ownership.md)).
3. **Skip the rest if already installed** (the test above).
4. **Read secrets** from the platform's secret store into `EXP_INSTALL_DB_PASSWORD` and, if you set one, the
   administrator password.
5. **Install** as the site's user: `exp:install` with the recipient's URL and e-mail, or the kickstarter with a
   rendered template.
6. **Check the exit status**; on `1`, keep `var/log/setup.log` and stop. Maintenance mode stays on, so visitors see a
   503 page, not a half-built site.
7. **Hand over the password**: show `var/log/initial-admin-password` once through your platform's channel (a
   console message, a panel page, a secure e-mail), then delete the file.
8. **Remove install files**: a rendered `kickstart.ini`, temporary package copies.
9. **Start serving** ([Web server and engine choices](web-server-and-engine-choices.md)) and the cron schedule
   ([Cron jobs](../hosting/operations/cron-jobs.md)).

A container entry point that does steps 3, 5 and 9 is on [Container images](container-images.md#an-entry-point).

## What must not be in the bundle

- [ ] No `settings/override/*.ini.append.php` with database credentials, mail passwords or secrets of your build.
- [ ] No `settings/siteaccess/` written by an installer run.
- [ ] No `kickstart.ini` (the templates `kickstart.ini-dist` and `kickstart.ini--example` are fine).
- [ ] No `var/log/initial-admin-password`, no logs, no caches of your build.
- [ ] No SQLite database file with content, unless it is a demo bundle by design.
- [ ] No `config.env.php` of your build machine; it names the machine's environment (`EXP_ENV`) and is kept out of
      version control by design (`config.env.php.example`).
- [ ] No `.git` directories you did not mean to ship.

## Related pages

- [Unattended install with exp:install](unattended-install-with-exp-install.md)
- [Automating provisioning](../hosting/provisioning/automating-provisioning.md)
- [Security defaults of a bundle](security-defaults-of-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
