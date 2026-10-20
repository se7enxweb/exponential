---
title: Set up environment-specific settings
audience: government, admin, developer
category: public-sector
order: 330
summary: Step by step - name each machine's environment with EXP_ENV in config.env.php and keep the settings that differ (mail transport, debug output, the admin badge) in variant files, so a test copy never mails citizens and production never shows debug output.
keywords: EXP_ENV, config.env.php, environments, dev, test, prod, staging, settings variants, site.ini.test.append.php, mail transport, debug, admin environment badge
type: help
---
# Set up environment-specific settings

This task is for administrators and developers who run development, test and production copies of a public website.
At the end each machine knows its environment and reads the right settings automatically
([Settings per environment](../../../../features/6.0/environment-settings.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- Shell access on each machine.
- A list of what differs per environment (mail, debug, databases, the admin badge).

## Steps

1. On each machine, copy the example and set the name (`dev`, `test`, `prod` or your own: lowercase letters, digits,
   `-` or `_`, starting with a letter, at most 32 characters):

   ```bash
   cp config.env.php.example config.env.php
   ```

   ```php
   <?php
   define( 'EXP_ENV', 'test' );
   ```

   `config.env.php` is kept out of version control by `.gitignore`.
2. Put the differences in variant files next to the standard ones, the environment name right after `.ini`:

   ```ini
   # settings/override/site.ini.test.append.php
   <?php /*
   [MailSettings]
   Transport=file

   [DebugSettings]
   DebugOutput=enabled
   DebugByIP=enabled
   DebugIPList[]
   DebugIPList[]=192.0.2.10/32 ; tester workstation
   */ ?>
   ```

   The variant files go into version control; only `config.env.php` differs per machine.
3. Clear the INI cache once (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`); each environment has its own
   cache files afterwards.
4. Optional: `site.ini [AdminEnvironment] Level=auto` shows the environment as a badge in the admin4 header and sign-in
   page.

## Check it worked

```bash
./console exp:ini get 'site.ini/MailSettings/Transport' --allow-root-user
```

prints `file` on the test machine and your mail transport on production. Templates can test `{exp_environment()}`.

## Pitfalls

- `dev` and `test` turn PHP's `display_errors` on in `index.php`; never set them on production.
- `settings/override` still outranks siteaccess settings; a variant overrides only its own layer.
- Without `EXP_ENV`, exactly the old files are read; an invalid name counts as none (and is noted in the PHP error log).

## Related pages
- [Environments: development, test and production](../../operations/hosting/environments-dev-test-prod.md)
- [Staging and approval](../../operations/upgrades-and-change/staging-and-approval.md)
- [Mail problems](../../operations/incidents/mail-problems.md)
- [Lock configuration with ManagedByDeployment](lock-configuration-with-managed-by-deployment.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
