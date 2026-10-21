---
title: "Learning path: administrator"
audience: student
category: learning
order: 6
summary: A step-by-step learning path for future site administrators, built from existing help pages and guides - installing, siteaccesses and settings, users and roles, sections, languages, caches, cronjobs, logs, security, backups and updates - with a practice task and a check for every step.
keywords: learning path, administrator, site administration, install, roles, policies, sections, languages, caches, cronjobs, logs, security, upgrade, track
type: help
---
# Learning path: administrator

This path is for learners who will run an Exponential site: install it, decide who may do what, keep it fast and
safe, and update it. Finish the [editor path](learning-path-editor.md) first. You need a terminal on the machine of
your study installation.

Every command below is run from the root of the installation. The console is `php bin/php/console`; many
installations add a shortcut so that `./console` works too, and many help pages write it that way. When you work as
`root`, add `--allow-root-user` where a script asks for it ([Installing Exponential 6.0](../../INSTALL.md)).

## Contents

1. [The steps](#the-steps)
2. [Going further](#going-further)
3. [Where the path leads next](#where-the-path-leads-next)

## The steps

### Step 1: Install from the command line (2 hours)

- Read: [Getting started: install Exponential](../../guides/getting-started.md),
  [Installing Exponential 6.0](../../INSTALL.md) sections 1 to 4.
- Practice: install a study site with `exp:install` and serve it with the `php` engine
  ([Installing a study site in one command](installing-a-study-site-in-one-command.md)).
- Check: what are the three siteaccesses every installation has? (`site`, `admin` and `editor`.)

### Step 2: Settings and siteaccesses (2 hours)

- Read: [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md),
  [How INI settings work](../extension-development/part-2-builder/how-ini-settings-work.md),
  [exp:ini](../../features/6.0/exp-ini-command.md).
- Practice: run `php bin/php/console exp:ini where site/DesignSettings/SiteDesign site` and explain each line of the output.
- Check: why should you never edit `settings/site.ini` itself?

### Step 3: Users, groups, roles and policies (3 hours)

- Read: [Roles, policies and unactivated users](../../guides/roles-and-policies.md),
  [How do I give a group edit rights on one section?](../manual/how-do-i-give-a-group-edit-rights-on-one-section.md).
- Practice: [project 9](project-09-roles-for-a-restricted-editor.md).
- Check: what does a role do before it is assigned? (Nothing.)

### Step 4: Sections and object states (1 hour)

- Read: [Sections](../../guides/sections.md), [Object states](../../guides/object-states.md).
- Practice: create a section and assign a subtree of your study site to it.

### Step 5: Languages (2 hours)

- Read: [Content languages](../../guides/content-languages.md) sections 1 to 5.
- Practice: [project 5](project-05-a-multilingual-site.md).
- Check: what does adding a language not do? (It translates nothing and changes no siteaccess.)

### Step 6: Caches (1 hour)

- Read: [Understanding caches](understanding-caches.md), [Which cache do I clear?](../manual/which-cache-do-i-clear.md),
  [Caches](../../guides/caches.md).
- Practice: publish an item, find it in a list, then clear only the content cache with `--clear-tag=content`.

### Step 7: Cronjobs and workflows (1 hour)

- Read: [Cronjobs](../../guides/cronjobs.md), [Workflows](../../guides/workflows.md).
- Practice: list the cronjob parts with `php bin/php/console list cron`.

### Step 8: Logs, errors and the audit trail (1 hour)

- Read: [Reading logs and errors](reading-logs-and-errors.md), [How do I read the log files?](../manual/how-do-i-read-the-log-files.md),
  [Security and audit](../../guides/security-and-audit.md).
- Practice: open `var/log/error.log` after a page with a deliberate template error, then fix it.

### Step 9: Security (2 hours)

- Read: [Security hardening](../security-hardening.md), [Two-factor login](../../guides/two-factor-login.md),
  [Change the admin password](../change-the-admin-password.md).
- Practice: turn on two-step sign-in for a test account
  ([How do I turn on two-step sign-in?](../manual/how-do-i-turn-on-two-step-sign-in.md)).

### Step 10: Packages, extensions and updates (2 hours)

- Read: [Site packages for learners](site-packages-for-learners.md), [Extensions](../../guides/extensions.md)
  sections 1 to 5, [Update with Git](../update-with-git.md), [Upgrading](../../guides/upgrading.md).
- Practice: open **Setup > Extensions** and find three active extensions; read what each one does.
- Check: in an update, which comes first, Git or Composer? (Git first, then Composer, as
  [Develop with Git](../develop-with-git.md#keep-up-to-date) says.)

## Going further

| Topic | Pages |
|---|---|
| Databases other than SQLite | [Database support](../database-support.md), [Databases](../../install/09-databases.md) |
| Serving a real site | [Serving the site](../../install/08-serving-the-site.md), [Deploying](../../guides/deploying.md) |
| Search | [project 6](project-06-search-that-finds-the-right-things.md), the trainers' [Search](../trainers/search/README.md) course |
| Several sites in one installation | [Site packages: start here](../site-packages/README.md), [Multisite and site packages](../trainers/multisite-and-packages/README.md) |
| Hosting work | [Hosting companies: start here](../hosting/hosting-companies-start-here.md) |
| Operating day to day | [Operating a site](../../guides/operating-a-site.md) |

## Where the path leads next

- Study projects: [1](project-01-a-content-class-of-your-own.md), [2](project-02-a-small-site-structure.md),
  [5](project-05-a-multilingual-site.md), [6](project-06-search-that-finds-the-right-things.md),
  [9](project-09-roles-for-a-restricted-editor.md), [capstone](project-10-capstone-a-complete-small-site.md).
- With a trainer: [Site administration](../trainers/site-administrators/README.md), then
  [Hosting and operations](../trainers/hosting-operations/README.md).
- Next path: [developer](learning-path-developer.md).

## Related pages

- [Choosing a learning path](choosing-a-learning-path.md)
- [Understanding caches](understanding-caches.md)
- [Student checklists](student-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
