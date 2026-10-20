---
title: Environments for development, test and production
audience: government, hosting, admin, developer
category: public-sector
order: 80
summary: For public-sector IT teams that keep separate development, test (acceptance) and production copies of an Exponential site. How EXP_ENV gives each machine its own settings, how to keep test copies from sending mail or running jobs, and how the admin shows which environment you are in.
keywords: environments, development, test, acceptance, staging, production, EXP_ENV, config.env.php, settings variants, file transport, test copy, environment badge, OTAP, DTAP
type: help
---
# Environments for development, test and production

This page is for IT teams in public bodies that run the same site on several machines: development, test or
acceptance, and production (often called DTAP or OTAP in public-sector IT). It answers how Exponential lets each
machine have its own settings without editing files after every copy, how to keep a test copy from acting like the
live site, and how editors can see which environment they are working in. The feature page is
[Settings per environment](../../../../features/6.0/environment-settings.md).

## Contents

1. [Why separate environments](#why-separate-environments)
2. [Naming the environment](#naming-the-environment)
3. [Settings that differ per environment](#settings-that-differ-per-environment)
4. [Keeping test copies quiet](#keeping-test-copies-quiet)
5. [Showing the environment in the administration](#showing-the-environment-in-the-administration)
6. [Data in test environments](#data-in-test-environments)
7. [Checklist](#checklist)

## Why separate environments

Change boards in the public sector usually ask that a change is seen working on a test copy before it reaches
production. Exponential supports this with the same code and settings files on every machine, plus a small per-machine
variant for what must differ (database, mail, debug output). The approval steps are on
[Staging and approval](../upgrades-and-change/staging-and-approval.md).

## Naming the environment

Copy `config.env.php.example` to `config.env.php` in the installation root and set the name:

```php
<?php
define( 'EXP_ENV', 'test' );
```

`config.env.php` is read before `config.php` by the web entry points and the command line scripts, and `.gitignore`
keeps it out of version control. A name is lowercase letters, digits, `-` or `_`, starts with a letter and has at most
32 characters. With `dev` or `test`, `index.php` shows PHP errors in the browser; with `prod`, any other name or none,
`display_errors` stays as `php.ini` sets it ([Settings per environment](../../../../features/6.0/environment-settings.md)).

## Settings that differ per environment

Every settings file can have a variant with the environment name right after `.ini`:

| Standard file | Variant for `test` |
|---|---|
| `settings/override/site.ini.append.php` | `settings/override/site.ini.test.append.php` |
| `settings/siteaccess/admin/site.ini.append.php` | `settings/siteaccess/admin/site.ini.test.append.php` |

A variant is read right after its standard file, in every layer, so `settings/override` still wins over everything.
Each environment has its own INI cache files; after creating variants, clear the INI cache once:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Typical differences:

| Setting | Development | Test | Production |
|---|---|---|---|
| `[DatabaseSettings]` connection | local | test database | production database |
| `[MailSettings] Transport` | `file` | `file` | your mail transport |
| `[DebugSettings] DebugOutput` | enabled for listed addresses | as needed | disabled |
| Velocity ports and host names | local | test names | production names |

Keep the variants in version control except where they hold secrets (see
[Configuration under version control](configuration-under-version-control.md)).

## Keeping test copies quiet

A test copy made from a production backup has real accounts and real e-mail addresses. Keep it from acting like the
live site ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)):

- **Mail:** `Transport=file` writes every mail to `var/log/mail/` instead of sending it. Or keep the transport and set
  `DebugSending=enabled` with `DebugReceiverEmail` to send everything to one test address.
- **Cron:** do not install the crontab on a test machine unless you test cronjobs; otherwise notifications, newsletters
  and content jobs run against real addresses.
- **Search engines:** keep test host names off the public internet, or behind your network restriction.

## Showing the environment in the administration

The admin4 and admin4l designs can show the environment as a badge in the header, on the sign-in page and before the
page title. `site.ini [AdminEnvironment] Level=auto` takes the level from `EXP_ENV`
([The admin4 design](../../../../features/6.0/admin4-design.md)). Templates read the name with
`{exp_environment()}`, so a public test site can show a "Test site" banner:

```
{if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
```

Editors who see a clear badge are less likely to publish on the wrong copy.

## Data in test environments

Copying production data to test brings personal data with it. Whether that is allowed, and with which safeguards, is
decided by your data protection rules. Options the software supports: the file mail transport above; restricting the
test host to your network; and removing or replacing data on the copy before testers get access. When test data must
not contain personal data at all, build test content separately instead of copying production.

## Checklist

- [ ] Each machine has `config.env.php` with its environment name; production uses `prod` or none.
- [ ] Database, mail and debug settings differ through variant files, not hand edits.
- [ ] Test copies write mail to files and run no unintended cronjobs.
- [ ] The admin shows the environment badge on non-production copies.
- [ ] The use of production data on test copies is approved by your data protection rules.

## Related pages

- [Hosting](README.md) index of this folder
- [Restoring to a test server](../backups-and-recovery/restoring-to-a-test-server.md)
- [Staging and approval](../upgrades-and-change/staging-and-approval.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
