---
title: A deployment pipeline
audience: app-creator, developer, admin
category: app-creation
order: 540
summary: Move an application from a developer machine through test to production safely - environments with EXP_ENV, Git and Composer for code, packages and upgrade steps for data, the deploy command, maintenance mode, rollback and verification.
keywords: deployment pipeline, staging, production, environments, EXP_ENV, Git, Composer, release, upgrade step, maintenance mode, rollback, verification, app creator
type: help
---
# A deployment pipeline

This page is for application creators who set up how changes reach production. The goal: the same code and the same
steps on every environment, settings that differ per machine kept apart, and a way back when something goes wrong.

## Contents

1. [Environments](#environments)
2. [What moves how](#what-moves-how)
3. [A release, step by step](#a-release-step-by-step)
4. [Rollback](#rollback)
5. [Verification](#verification)

## Environments

| Environment | Purpose | Settings |
|---|---|---|
| Development | each developer's machine | `EXP_ENV=dev`: debug on, file mail transport |
| Test / staging | a copy of production for acceptance | `EXP_ENV=test` |
| Production | the live site | the base settings |

`EXP_ENV` names the environment, and every settings file can have a variant for it (`site.ini.test.append.php`)
([Environment settings and secrets](environment-settings-and-secrets.md), [Settings per environment](../../../features/6.0/environment-settings.md)).

## What moves how

| Thing | Moves by |
|---|---|
| Kernel and extensions | Git and Composer (`composer.lock` committed in the project) |
| Your extension | its Git tag, required by version |
| Settings | Git, without secrets; secrets set per machine |
| Content classes | packages or upgrade scripts, never by clicking twice ([Content packages](packages-and-ezpkg.md)) |
| Tables | your install and upgrade commands ([Database schema and .dba files](schema-and-dba-files.md)) |
| Content | stays in production; copy production to staging, never the other way |

## A release, step by step

1. Tests pass in CI ([Continuous integration for extensions](ci-for-extensions.md)).
2. Deploy to staging, run the upgrade steps, test.
3. Production: back up ([Backups and restores](../website-creators/backups-and-restores.md)).
4. Maintenance mode if the release changes data (`php bin/php/maintenance.php on --allow-admin`).
5. Update the code (Git pull or `composer update <package>` of the project), run the upgrade steps.
6. `./console exp:velocity deploy` (`--kernel` when kernel classes changed) ([Deploying with Velocity](deploying-with-velocity.md)).
7. Maintenance off; verify.

## Rollback

- Code: check out the previous tag and deploy again.
- Data: restore the backup taken before the release when upgrade steps changed data; that is why step 3 comes first.
- Write upgrade steps that only add (columns, classes) where possible, so the previous code still runs on the new data.

## Verification

- [ ] `exp:velocity status`: the server answers; `exp:cache status` ends in PASS.
- [ ] The front page, a page of each changed type, the administration.
- [ ] The error log is quiet.
- [ ] **Setup > Upgrade check** for file consistency ([Upgrade check](../../../guides/upgrade-check.md)).
- [ ] The about page shows the expected versions.

## Related pages

- [Deploying with Velocity](deploying-with-velocity.md)
- [Running a team on Git](../../leadership/running-a-team-on-git.md)
- [Compatibility, releases and deployment](../../../standards/architecture/compatibility-releases-deployment.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
