---
title: Updates and upgrade paths for bundled installations
audience: bundler, hosting, admin
category: bundling
order: 420
summary: How bundled installations get new Exponential releases - the two halves of every update (code and database), the update routes (new image, package manager, Git and Composer, the administration's Git Manager and ezupdate), who does what, how to announce updates, and security releases.
keywords: update, upgrade, upgrade path, release, security fix, dbupdate, update/database, Git, Composer, Git Manager, ezupdate, image, container, package manager, maintenance mode, bundle
type: help
---
# Updates and upgrade paths for bundled installations

This page is for bundlers who must keep the installations made from their bundle current. Decide the update path
**before** the first release: recipients will ask how to update on the first day a fix is published.

## Contents

1. [Two halves of every update](#two-halves-of-every-update)
2. [Update routes by kind of bundle](#update-routes-by-kind-of-bundle)
3. [The routine on an installation](#the-routine-on-an-installation)
4. [Who does what](#who-does-what)
5. [Security releases](#security-releases)
6. [Announcing updates](#announcing-updates)

## Two halves of every update

1. **The code**: the kernel and the extensions, from a new tag and a new lock file.
2. **The database**: the release's update file and scripts. For the 6.0 line the file is
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, for MySQL, PostgreSQL and SQLite; "Apply it **once,
   whole, in order**" ([11.6 Step 2](../../install/11-upgrading.md#step-2-apply-the-database-update)). Releases can
   add scripts too, such as `php update/common/scripts/6.0/createaudittables.php` for the audit index.

"Moving the code is only half of an upgrade: the database update files of chapter 11 are the other half"
([3.2 A](../../install/03-getting-the-code.md#a-git-clone-and-composer-install-recommended)). Then read "the
checklist of each period you pass" ([11.6](../../install/11-upgrading.md#116-from-any-60x-to-today)).

## Update routes by kind of bundle

| Bundle | Code route | Database route |
|---|---|---|
| Container image | a new image; the volumes keep `var/` and settings | a one-off job with the new image, before the new containers take traffic |
| Server image, appliance | your update channel, or Git and Composer on the box | the same channel, as a separate, logged step |
| Panel installer | the panel's update button, or the customer's Git and Composer | the customer or the panel, with the instructions linked |
| Distribution package | the package manager | the administrator, following the notice your package prints ([Linux distribution packaging](linux-distribution-packaging.md)) |
| Starter kit project | Git and Composer in the project ([Update with Git](../update-with-git.md)) | the project team |

## The routine on an installation

[Update with Git](../update-with-git.md) gives seven steps for the command line and the administration:

| Step | What |
|---|---|
| 1 | Back up, start the maintenance window (`php bin/php/maintenance.php on`) |
| 2 | See what is waiting |
| 3 | Update the kernel with Git |
| 4 | Update the extensions with Composer |
| 5 | Check files and database, apply the database update |
| 6 | Clear the caches, reload the code (`exp:velocity deploy` on a Velocity site) |
| 7 | Check the site, leave the window (`php bin/php/maintenance.php off`) |

In the administration, **Git Manager** pulls a branch and **Setup > Updates and packages** (ezupdate) runs Composer;
"a release tag, a merge conflict and the Velocity restart still need the command line". ezupdate's updating is "Off
until `[UpdateSettings] AllowUpdate` is switched on" ([Automated updates with ezupdate](../hosting/operations/automated-updates-with-ezupdate.md)).

For live sites follow "only finished, tested versions": "the newest **release tag**"
([Choose how closely you follow](../update-with-git.md#choose-how-closely-you-follow)).

## Who does what

Write it into your terms and your getting-started text:

```text
We deliver:        new bundle releases within <time> of an Exponential release
We test:           <databases, PHP versions, upgrade from the last <n> bundle versions>
The recipient does: backups before updating; applying the update; their own extensions
Database updates:  <who runs them, how>
End of updates:    <date or policy per bundle version>
```

## Security releases

- "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x
  release. A published tag is never changed" (`SECURITY.md`).
- Watch the releases on <https://github.com/se7enxweb/exponential/releases> and the project's channels; partners
  receive information through the partner program ([Keeping customer sites current](../partners/keeping-customers-current.md)).
- Release a bundle update promptly, and say in its notes which security release it carries.

## Announcing updates

- A changelog per bundle release, naming the Exponential version inside.
- The database steps, explicitly, even when there are none.
- A note when a previous bundle release was wrong, instead of changing it ([Versions, tags and pinning](versions-tags-and-pinning.md#a-published-release-never-changes)).

## Related pages

- [Chapter 11: Upgrading](../../install/11-upgrading.md)
- [Upgrading customer sites](../hosting/operations/upgrading-customer-sites.md)
- [Testing a bundle](testing-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
