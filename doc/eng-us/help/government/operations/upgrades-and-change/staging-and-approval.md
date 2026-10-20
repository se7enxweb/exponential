---
title: Staging and approval
audience: government, admin, project-manager, developer
category: public-sector
order: 50
summary: How a public body moves a change from development through test (staging) to production with Exponential - one code base per environment, settings variants with EXP_ENV, a copy of the data for testing, the checks to run on staging, and the sign-off before production.
keywords: staging, test environment, acceptance, user acceptance testing, UAT, approval, sign-off, EXP_ENV, environments, deployment pipeline, release, test copy, production
type: help
---
# Staging and approval

This page is for project managers, administrators and developers who must show that a change was tested before it
reached the public site. It describes a three-environment path (development, test or staging, production) with the
features Exponential has for it, and the sign-off points a change board usually asks for.

## Contents
1. [The path of a change](#the-path-of-a-change)
2. [Making environments differ only where they should](#making-environments-differ-only-where-they-should)
3. [Data on staging](#data-on-staging)
4. [What to test on staging](#what-to-test-on-staging)
5. [The sign-off](#the-sign-off)
6. [Related pages](#related-pages)

## The path of a change

| Step | Where | Output |
|---|---|---|
| Develop | developer machine, `EXP_ENV=dev` | a commit in version control |
| Review | your code review | an approved merge |
| Deploy to staging | test server, `EXP_ENV=test` | the change running on a copy of the site |
| Test and accept | staging | test notes, upgrade check report, acceptance by the service owner |
| Approve | change board | an approved change record ([Change management and advisory boards](change-management-and-advisory-boards.md)) |
| Deploy to production | live server, `EXP_ENV=prod` | the same commit deployed; evidence collected |

Deploy the same commit to staging and production, never a hand-edited copy; with
[ManagedByDeployment](managed-by-deployment.md) on production, there is no other way.

## Making environments differ only where they should

Since October 2026 the constant `EXP_ENV` in `config.env.php` names the environment of a machine, and every settings
file can have a variant for it, for example `settings/override/site.ini.test.append.php`
([Settings per environment](../../../../features/6.0/environment-settings.md)). Typical differences:

| Setting | Staging | Production |
|---|---|---|
| `[MailSettings] Transport` | `file` (no real mail to citizens) | your mail server |
| `[DebugSettings] DebugOutput` | enabled for testers' addresses | disabled, or for listed addresses only |
| Admin environment badge | `site.ini [AdminEnvironment] Level=auto` shows the environment in admin4 | the same, showing production |
| Database | the staging database | the production database |

`dev` and `test` also show PHP errors in the browser; `prod` leaves `display_errors` as `php.ini` sets it.
Templates can show a banner with `{exp_environment()}` so nobody confuses staging with the live site.

## Data on staging

Testing needs realistic content, and production content may include personal data (user accounts, form
submissions, newsletter subscribers). Decide, with your data protection officer, whether staging may hold a full copy,
a copy with personal data removed, or test content only. The `git_manager` backup manager can produce an "AGPL
compatible database dump" with passwords, e-mail addresses, keys and paths removed (MySQL and MariaDB only)
([git_manager](../../../../features/6.0/extensions/git_manager.md)). Restore procedures:
[Restoring to a test server](../backups-and-recovery/restoring-to-a-test-server.md).

## What to test on staging

1. The change itself, by the people who asked for it.
2. `./console exp:velocity deploy --dry-run --allow-root-user` and the real deploy, with the time each step took.
3. **Setup > Upgrade check**: **Check file consistency** and **Check database consistency**
   ([Upgrade check before and after](upgrade-check-before-and-after.md)).
4. **Setup > System information**: health checks green; **Download report (.txt)** for the record.
5. **Setup > Log files**, **Error digest**: no new errors after the change.
6. A publish through the approval workflow, a search, a form submission.
7. Accessibility and layout of changed templates, in light and dark admin modes where the admin changed.

## The sign-off

Record who accepted the change on staging, on which commit, with which evidence, and attach it to the change record.
Production deploys the same commit in the approved window ([Maintenance windows](maintenance-windows.md)).

## Related pages
- [Environments: development, test and production](../hosting/environments-dev-test-prod.md)
- [Set up environment-specific settings](../../tasks/operations/set-up-environment-specific-settings.md)
- [Rollback planning](rollback-planning.md)
- [Deploying guide](../../../../guides/deploying.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
