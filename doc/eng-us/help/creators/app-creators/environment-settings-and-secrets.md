---
title: Environment settings and secrets
audience: app-creator, developer, admin
category: app-creation
order: 550
summary: Keep settings that differ per machine and secrets out of code - EXP_ENV and settings variants, settings/override for the installation, file modes, what never goes into Git, and how an application reads its keys.
keywords: environment, EXP_ENV, config.env.php, settings variants, secrets, API keys, passwords, settings/override, gitignore, file modes, app creator
type: help
---
# Environment settings and secrets

This page is for application creators who handle settings that differ per machine and secrets such as API keys and
passwords. Both must stay out of the code repository.

## Contents

1. [Settings per environment](#settings-per-environment)
2. [Where secrets live](#where-secrets-live)
3. [Reading secrets in code](#reading-secrets-in-code)
4. [Checklist](#checklist)

## Settings per environment

From [Settings per environment (EXP_ENV)](../../../features/6.0/environment-settings.md):

1. Copy `config.env.php.example` to `config.env.php` and set `define( 'EXP_ENV', 'test' );`. It is read before
   `config.php` by web and command line, and `.gitignore` keeps it out of version control.
2. Put the environment's settings in a variant file: the environment name right after `.ini`
   (`settings/override/site.ini.test.append.php`, `extension/<ext>/settings/site.ini.test.append.php`).
3. Clear the INI cache once; each environment has its own cache files.

Typical variants: the mail transport (`file` outside production), debug output, hosts, the Solr address.

## Where secrets live

| Secret | Where |
|---|---|
| Database password | `settings/override/site.ini.append.php` of the installation |
| Your application's API keys | `settings/override/<yourfile>.ini.append.php`, mode 0640 |
| Provider keys of expai | `settings/override/expai.ini.append.php` only |
| Form token secret | `[HTMLForms] Secret` in `settings/override/` |

Never in: your extension's repository, a `.ini` file the web server might show (use `.ini.append.php`, which wraps the
content in a PHP comment), templates, job payloads, logs, URLs or commit messages.

## Reading secrets in code

```php
$key = eZINI::instance( 'expdemo.ini' )->variable( 'ArchiveSettings', 'ApiKey' );
if ( $key === '' )
    throw new RuntimeException( 'expdemo.ini [ArchiveSettings] ApiKey is not set; put it in settings/override/expdemo.ini.append.php' );
```

Ship the key in your extension's `expdemo.ini` as an empty value with a comment, so installations see where to set it.

## Checklist

- [ ] `config.env.php` stays out of Git (the shipped `.gitignore` lists it); in a project repository of your own, keep
      `settings/override/` out of version control too.
- [ ] Secrets have mode 0640 and belong to the web server's group.
- [ ] Each environment has its own keys; production keys are never on development machines.
- [ ] A leaked key can be revoked without a code change.

**Demo mode.** The environment variable `EXP_DEMO_MODE=1` belongs to public demos only: an app that talks to a demo gets HTTP 403 when it tries to change or reset an administrator's password. [Environment variables](../../../guides/environment-variables.md), [Demo mode](../../../bc/6.0/demo-mode.md).

## Related pages

- [Settings and INI files in your application](settings-and-ini-files.md)
- [Security for applications](security-for-applications.md)
- [Security standard](../../../standards/security.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
