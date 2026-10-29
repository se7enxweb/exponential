---
title: "Lesson 4.1: Deploying a change"
audience: trainer, hosting, admin
category: training
order: 41
summary: Learners ship a PHP or INI change with exp:velocity deploy, read the dry run step by step, explain why the order of the steps matters, and handle template-only changes without a restart. 45 minutes.
keywords: hosting and operations, deploy, exp:velocity deploy, dry run, autoloads, INI cache, PHP-FPM reload, PhpFpmReload, WarmUpURL, template change, exp:cache clear, response cache
type: help
---
# Lesson 4.1: Deploying a change

Under persistent servers, a changed PHP class or INI file does not take effect by itself, and caches cleared at the
wrong moment fill again with pages from the old code. The result looks exactly like "the change did not work". This
lesson teaches the one command that does every step in the right order, and the smaller routine for templates.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 4: Deploying and cron](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators, hosting support staff and developers who deploy |
| Prerequisites | [Module 2](../module-02-velocity/README.md); a training installation served by Velocity |

## Learning objectives

After this lesson, learners can:

- run `exp:velocity deploy --dry-run` and explain each step it prints;
- explain why caches holding rendered output are cleared only after PHP-FPM and Velocity run the new code;
- choose the right options: `--kernel`, `--no-velocity`, `--no-fpm`, `--fpm-reload`, `--packer`;
- deploy a template or stylesheet change without a restart;
- set warm-up addresses so the first visitor does not rebuild the caches.

## Trainer's notes

### How to present it

Source: [Deploying changes](../../../hosting/operations/deploying-changes.md),
[Deploying Exponential, section 7](../../../../guides/deploying.md#7-ship-a-php-change-with-one-command) and
[Chapter 8, section 8.3.14](../../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy).

1. **The problem** (3 minutes). Workers keep classes, INI values and compiled templates in memory. A PHP-FPM pool
   with OPcache may keep old code until it revalidates or reloads.
2. **The steps** (6 minutes). Show the steps table of *Deploying changes*: extension autoloads, kernel autoloads
   (only with `--kernel`), INI caches, template caches, engine archive, PHP-FPM, restart Velocity, content, HTTP cache
   and template-block caches, the response cache, warm-up requests. "The order is the point."
3. **Options** (4 minutes). `--no-velocity` for a site served by Apache or nginx alone, `--no-fpm` for Velocity alone,
   `--fpm-reload` after a `php.ini` or pool change, `--packer` when an `ezjscServer_*` function changed. With
   `PhpFpmReload=auto`, the deploy waits for OPcache revalidation instead of reloading a shared master.
4. **Templates** (3 minutes). Template or stylesheet edits need no restart: clear the template caches, then the
   response cache.
5. **Reading a run** (4 minutes). Each step prints `PASS`, `FAIL` or `SKIP` with its time; the first failure stops
   the run with exit status 1, and the steps not run are listed as `NOT RUN`.

### Common questions

**Can I just clear all caches?** Clearing every cache and forgetting the servers leaves the old classes in memory in
FPM and Velocity. Use the deploy.

**Why did the reload step say SKIP?** It is skipped with a note when no pool was found, when the deploy is not run as
root, or when there is no `systemctl`. On a Velocity-only site set `[DeploySettings] PhpFpmService=disabled`.

**Is a deploy safe during visiting hours?** A Velocity restart drops nothing that `graceful` would keep; a PHP-FPM
reload makes every site of that master wait for the longest running request. Schedule `--fpm-reload` as server
maintenance.

**What about several web servers?** *Deploying changes* has a section on large changes and several nodes; the deploy
runs on each node.

### Pitfalls

- **Running the deploy as root** on a site whose files belong to another user. The caches it writes may then belong
  to root. Run as the site's user where possible.
- **Skipping the dry run** in class. Always start with `--dry-run`; it is the teaching tool.
- **Expecting a template change to need a restart.** It does not, and restarting during class wastes time.

## Hands-on exercise

Use your own training installation, served by Velocity, and a terminal in the installation root.

1. Preview a deploy:

   ```bash
   php bin/php/console exp:velocity deploy --dry-run
   ```

2. Count the steps and find the one that clears Velocity's response cache. Note its number.
3. Read the PHP-FPM step and note whether it would reload, wait or skip, and why.
4. Make a harmless INI change for your site, for example a new site name:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteSettings/SiteName
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Training site (deploy test)" global
   ```

5. Reload the front page. Note whether the new name already shows.
6. Run the deploy for real: `php bin/php/console exp:velocity deploy`.
7. Read the output: every step `PASS` or `SKIP`, the time for each.
8. Reload the front page and check the site name.
9. Set the old name back with `exp:ini set` and the value from step 4, then run the deploy again.
10. Clear only what a template change needs:

    ```bash
    php bin/php/console exp:cache clear --id=template
    php bin/php/console exp:velocity cache clear --apply
    ```

## Expected result

- Step 1 lists every step marked `DRY` and changes nothing.
- Step 5: the old name may still show (workers and caches hold the old value).
- Step 6 ends with every step `PASS` or `SKIP`; step 8 shows the new name.
- After step 9 the original name is back.
- The trainer checks by asking for the number of the response-cache step and the reason the PHP-FPM step gave.

![A maintenance window, planned before a deploy](../../../images/trainers/ho-maint.webp)

## Quiz

1. Why are the content view cache and the response cache cleared near the end of a deploy?
2. Which option do you add when a kernel class was added or renamed?
   A. `--packer` B. `--kernel` C. `--rebuild-phar` D. `--no-autoload`
3. A site is served by Apache with PHP-FPM only. Which option skips the Velocity step?
4. What does a deploy do when a step fails?
5. What is enough after a template-only change?

<details>
<summary>Answers</summary>

1. Cleared earlier, every page requested in between is rendered by the old code and cached again
   ([Deploying changes](../../../hosting/operations/deploying-changes.md#the-steps-and-their-order)).
2. **B.** `--kernel` also regenerates the kernel autoloads.
3. `--no-velocity`.
4. It stops with exit status 1; the steps not run are listed as `NOT RUN`.
5. Clearing the template caches and then Velocity's response cache; no restart.

</details>

## Further reading

- [Deploying changes](../../../hosting/operations/deploying-changes.md): the host's view, settings per environment.
- [Deploying Exponential](../../../../guides/deploying.md): the walkthrough from a fresh install to HTTPS.
- [Managed by deployment](../../../../features/6.0/managed-by-deployment.md): configuration kept by the deployment.
- [Exp INI command](../../../../features/6.0/exp-ini-command.md): reading and writing settings on the command line.

## Related pages

- [Module 4: Deploying and cron](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
