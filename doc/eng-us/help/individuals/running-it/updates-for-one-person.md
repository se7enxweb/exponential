---
title: Updates for one person
audience: individual, customer, website-creator
category: personal-sites
order: 1642
summary: How one person keeps an Exponential site current - what an update covers, where releases are announced, security fixes first, the update steps with a backup and maintenance mode, the update tools in the administration (upgrade check, ezupdate, git_manager), PHP versions, and what to do if an update goes wrong.
keywords: updates, upgrades, security fixes, releases, composer, git, upgrade check, ezupdate, git_manager, maintenance mode, backup, PHP version, rollback, individual
type: help
---
# Updates for one person

This page is for individuals who run their own site and must keep it updated. Updates close security holes and fix
defects; a site that is never updated becomes a risk to you and to your visitors
([How updates work](../../customers/healthy/how-updates-work.md)).

## Contents

1. [What an update covers](#what-an-update-covers)
2. [Where to hear about releases](#where-to-hear-about-releases)
3. [Security fixes first](#security-fixes-first)
4. [The update, step by step](#the-update-step-by-step)
5. [Tools in the administration](#tools-in-the-administration)
6. [PHP and the operating system](#php-and-the-operating-system)
7. [If something goes wrong](#if-something-goes-wrong)

## What an update covers

| Part | Updated by |
|---|---|
| Exponential itself and its extensions | Composer or Git, run by you ([Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md)) |
| Your own design and settings | Not touched by updates, if kept in your own extension and `settings/override` |
| PHP | Your host, or you on your own server |
| The operating system | Your host, or you on your own server |

Published versions never change; a fix always arrives as the next version
([The support commitment](../../who-is-the-exponential-foundation.md#the-support-commitment)).

## Where to hear about releases

- The project's GitHub releases and the changelogs ([Release line and support](../../customers/healthy/release-line-and-support.md)).
- **Setup > Upgrade check** in the administration compares your files and database with the release; it only reads
  ([The upgrade check](../../../guides/upgrade-check.md)).
- Watching the repository on GitHub sends you a mail per release.

## Security fixes first

A release that fixes a security problem is applied within days. Feature releases can wait for a quiet weekend. If you
cannot apply a security fix soon, consider maintenance mode or switching off the affected feature until you can
([Security basics](../../customers/healthy/security-basics.md)).

## The update, step by step

1. **Read** the release notes: anything to do before or after?
2. **Back up** database and files ([Backups for one person](backups-for-one-person.md)).
3. **Maintenance mode on** ([Maintenance mode](../../creators/website-creators/maintenance-mode.md)).
4. **Update** the code with Composer or Git ([Update with Git](../../update-with-git.md),
   [Upgrading](../../../guides/upgrading.md)).
5. **Clear caches** and, if the notes say so, run the update scripts.
6. **Check**: front page, a deep page, sign in, edit and publish a test, a form.
7. **Maintenance mode off.** Keep the pre-update backup until the site has run normally for a while.

Under Velocity, follow its deploy step so the running workers load the new code
([Velocity is part of Exponential 6.0.15](../../velocity-is-part-of-exponential-6-0-15.md)).

![The upgrade check in the administration](../../images/update-upgrade-check.webp)

## Tools in the administration

For people without a shell, or who prefer the browser:

| Tool | What it does | Page |
|---|---|---|
| Upgrade check | Compares files and database with the release; changes nothing | [The upgrade check](../../../guides/upgrade-check.md) |
| `ezupdate` | Updates from the administration | [Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md) |
| `git_manager` | Git dashboard (pull, switch branches) and the backup manager | [git_manager](../../../features/6.0/extensions/git_manager.md) |

## PHP and the operating system

- Exponential 6.0.15 runs on PHP 8.0 and later (PHP 7.4 usable without support); 8.5 is the newest tested
  ([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)).
- When your host announces the end of a PHP version, test your site on the newer one first (a copy, or the host's
  per-site PHP switch) ([PHP versions and extensions](../../hosting/basics/php-versions-and-extensions.md)).
- On your own server, apply operating system security updates as they come.

## If something goes wrong

1. Stay calm; the site is in maintenance mode and nothing is lost.
2. Note the error and its reference ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).
3. Clear the caches once more.
4. If it still fails, return to the previous version: restore the code and the pre-update backup.
5. Ask for help with the error message ([When something goes wrong](../../when-something-goes-wrong.md),
   [When to hire help](when-to-hire-help.md)).

## Related pages

- [Running it alone](README.md)
- [Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md)
- [Upgrade day checklist](../../customers/healthy/upgrade-day-checklist.md)
- [Monthly upkeep checklist](../checklists/monthly-upkeep-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
