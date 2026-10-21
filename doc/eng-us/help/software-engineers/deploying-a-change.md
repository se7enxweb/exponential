---
title: Deploying a change
audience: software-engineer, admin, developer
category: software-engineering
order: 410
summary: How a PHP, settings, template or stylesheet change reaches every runtime that serves an Exponential site - the steps of exp:velocity deploy and why their order matters, PHP-FPM reloads and OPcache, Velocity restarts, cache clears last, warm-up, upgrades with database updates, and the rules for code that ships with a deploy.
keywords: deploy, deployment, exp:velocity deploy, autoload, OPcache, PHP-FPM reload, Velocity restart, cache clear order, warm-up, upgrade, database update, class_exists, software engineer
type: help
---
# Deploying a change

This page is for engineers who ship changes to a running site. Exponential keeps code in more than one runtime
(PHP-FPM with OPcache, Velocity's persistent workers) and rendered output in several caches; a deploy that does the
right steps in the wrong order looks as if it had not worked. The standard is
[Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md#deployment-standard);
the command is documented in [Deploying a PHP change](../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

## Contents

1. [One command](#one-command)
2. [The steps and their order](#the-steps-and-their-order)
3. [Which change needs what](#which-change-needs-what)
4. [Upgrading an installation](#upgrading-an-installation)
5. [Rules for code that ships](#rules-for-code-that-ships)
6. [After the deploy](#after-the-deploy)

## One command

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user  # what it would do, and nothing else
php bin/php/console exp:velocity deploy --allow-root-user            # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
```

It prints PASS, FAIL or SKIP with the time of each step, stops at the first failure and exits with status 1, listing
the steps not run. `--packer` also clears the packed scripts and styles.

## The steps and their order

| # | Step | Why here |
|---|---|---|
| 1 | Extension class maps (`ezpgenerateautoloads.php -e`) | new classes must be findable |
| 2 | Kernel class maps, only with `--kernel` | the same for the kernel |
| 3 | INI caches (`--clear-tag=ini`) | settings are read again |
| 4 | Template, template-override, translation and design_base caches | new templates and design directories are found |
| 5 | The engine archive, when Velocity runs from one | rebuilt only when `kernel/`, `lib/` or `autoload/` changed, every file parsed first |
| 6 | PHP-FPM: wait for OPcache to revalidate, or reload it | the new code runs under FPM |
| 7 | Restart Velocity, when it is running | workers inherit the classes of the warm-up |
| 8 | Content, HTTP cache and template-block caches | rendered output is cleared only now |
| 9 | Velocity's response cache | the same, in the server |
| 10 | Warm-up URLs (`[DeploySettings] WarmUpURL[]`) | the first visitor does not rebuild every cache |

**The order is the point.** Cleared before step 6 and 7, every page requested in between is rendered by the old code
and cached again. A file that does not parse fails step 5, before PHP-FPM or Velocity is touched.

**PHP-FPM is reloaded only when it has to be** (`[DeploySettings] PhpFpmReload=auto`): a reload makes every pool of
that PHP version wait for its longest running request. When OPcache revalidates files, the deploy waits
`revalidate_freq` plus one second instead. On a server with several PHP versions, name the service that really serves
the site in `[DeploySettings] PhpFpmService`.

## Which change needs what

| Change | Needs |
|---|---|
| A PHP class, operator or INI file | the whole deploy |
| A new or renamed kernel class | the deploy with `--kernel` |
| An existing template | the template caches and Velocity's response cache; no restart |
| A stylesheet or script of a design | the deploy with `--packer`, or the packer and template-block caches |
| Content | nothing; the kernel invalidates |
| A newly activated extension | the deploy: its classes, settings and design directories (design_base) |

## Upgrading an installation

1. Back up files and database.
2. Put the new code in place, regenerate the class maps, clear all caches, reload PHP-FPM, restart Velocity.
3. Apply the database update once, whole, in order (`update/database/<driver>/6.0/`), then the scripts the guide names.
4. Read the upgrade notes of every period passed in `doc/eng-us/bc/6.0/` and the changelogs.
5. Check extensions: `./console exp:checkclasses` reports classes PHP refuses to load.
6. Run the upgrade check: Setup > System upgrade, or `php bin/php/checkmanifest.php --all --extensions`.

Guides: [Upgrading](../../guides/upgrading.md), [Update with Git](../update-with-git.md).

## Rules for code that ships

- A new class referenced from a shipped INI file should be guarded with `class_exists()` in the kernel: a Velocity
  worker started before the deploy does not know it.
- Templates that use a new class or operator must not reach the servers before the class does; deploy the kernel side
  first.
- A deployment must reach every PHP runtime that serves the site; clearing caches alone leaves PHP-FPM and Velocity
  on the old classes.
- With several web nodes, deploy each node; `exp:velocity deploy` covers one machine.
- In production, consider `ManagedByDeployment=enabled`, so that the administration cannot write settings or
  templates that the next deploy would drop ([Configuration managed by the deployment](../../features/6.0/managed-by-deployment.md)).

## After the deploy

- Request the front page and the administration login; check a page whose content you know.
- Watch `error.log` in the log viewer for a few minutes.
- After a Velocity restart, check that a new parent process runs and the port answers
  ([Velocity persistent workers](velocity-persistent-workers.md)).

## Related pages

- [Deploying](../../guides/deploying.md)
- [Deployment pipeline](../creators/app-creators/deployment-pipeline.md)
- [Deploying changes, for hosts](../hosting/operations/deploying-changes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
