---
title: Upgrades without breaking integrations
audience: integrator, developer, admin
category: integration
order: 400
summary: Keeping integrations working through Exponential, extension and PHP upgrades - what tends to change under an integration, reading the behavior change notes, pinning versions with Composer, a rehearsal on a copy, regression tests from the acceptance cases, and the order of an upgrade with integrations paused.
keywords: upgrade, update, behavior changes, bc notes, composer constraint, tilde, version pin, deprecation, ezprestapiprovider, PHP upgrade, regression test, rehearsal, pause cronjobs, maintenance window, upgrade check
type: help
---
# Upgrades without breaking integrations

This page is for integrators and operators who must upgrade an installation that other systems depend on. Exponential
is maintained actively; each release brings fixes, security hardening and new features, and a few of them change
behavior an integration relies on. Upgrades are safe when you know where to look and rehearse once. The upgrade itself
is described in [Upgrading](../../guides/upgrading.md).

## Contents

1. [What tends to change under an integration](#what-tends-to-change-under-an-integration)
2. [Read before you upgrade](#read-before-you-upgrade)
3. [Versions and Composer](#versions-and-composer)
4. [The upgrade, with integrations](#the-upgrade-with-integrations)
5. [Writing integrations that age well](#writing-integrations-that-age-well)

## What tends to change under an integration

| Area | Example from the 6.0 line | Effect on an integration |
|---|---|---|
| Input validation | Datatypes refuse values they used to store (`ezprice` refuses a non-numeric price, `ezurl` refuses unsafe schemes, `ezboolean` maps text to 0 or 1) | An import that sent sloppy values now fails for those records; good, but plan for it |
| Permissions | The REST content controller checks content policies on every route since 6.0.15 | A key or token whose user lacks a policy gets `403` where it used to succeed |
| Extensions | `ezprestapiprovider` is deprecated; `ezprestapi` serves v1 and v2 | Overrides naming the old provider class work through a shim; move to `ezp7xRestApiProvider` |
| Operations and triggers | Operations beyond `AvailableOperationList[]`'s shipped list may change without compatibility rules | Workflow events on extra operations need a test after each upgrade |
| Runtime | Velocity workers, PHP 8.x deprecations | Statics and dynamic properties in old code; restart after class changes |
| Schema | New tables and columns (for example `expapikey`) | Run the database update of your engine before using the feature |

## Read before you upgrade

- The **behavior change** notes in `doc/eng-us/bc/6.0/` (for example [Behavior changes 2026-10](../../bc/6.0/behaviour-changes-2026-10.md)
  and [Behavior changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)): each says what changed,
  who is affected, how to check and how to fix.
- The release notes of every extension your integration uses (`doc/changelogs/extensions/`).
- The upgrade guide's checklist for each period you pass ([Upgrading, part C](../../guides/upgrading.md#part-c-from-any-60x-to-today)).

Search them for the names your integration uses: the classes it calls, the settings it sets, the routes it calls.

## Versions and Composer

- The root `composer.json` uses tilde constraints (`~1.2.6`): the last position floats, so `composer update` brings
  fixes of the same line but no new minor line.
- Your integration extension declares what it needs in its own `composer.json` (`"se7enxweb/ezprestapi": "~1.2.6"`), so
  an installation cannot update into a line you have not tested
  ([composer.json for extensions](../extension-development/part-3-extension-developer/composer-json-for-extensions.md)).
- Record the versions in the handover documents; `composer show` and **Setup > Extensions** list them.

## The upgrade, with integrations

1. **Rehearse on a copy** of production with the same integration versions; run the acceptance cases and the import
   dry runs ([Testing integrations](testing-integrations.md)).
2. **Tell the owners of the other systems** the window; calls during it may fail.
3. **Pause** the integration's cronjob parts at Setup > Cronjobs (and the queue workers), so no import runs half on the
   old and half on the new code ([Cronjobs for integrations](cronjobs-for-integrations.md#pausing-during-maintenance)).
4. Optionally switch maintenance mode on (`php bin/php/maintenance.php on --message="..." --until=1h`); API clients get
   `503` with `Retry-After` and should back off.
5. **Upgrade**: code, database update, autoloads, caches, `php bin/php/console exp:velocity deploy`.
6. **Check**: the acceptance cases, one dry run per import, one call per API client, `exp:audit sinks list`.
7. **Resume** the cronjob parts and watch the first runs.
8. Write down what changed for the integration in its changelog.

## Writing integrations that age well

- Use the supported APIs (`eZContentFunctions`, operations, `expservices`, the REST routes) rather than tables and
  internal methods.
- Keep the code in one extension with tests, not in scattered scripts.
- Guard optional kernel classes with `class_exists()` (the audit API documents this) so a missing piece degrades
  instead of failing.
- Prefer settings to code for anything a site may need to change.
- Follow the project: contribute fixes you need upstream, so the next release carries them
  ([Contributing integrations back](contributing-integrations-back.md)).

## Related pages

- [Upgrading](../../guides/upgrading.md)
- [Part 4 of the extension book: upgrading](../extension-development/part-4-upgrading/README.md)
- [Keeping customer sites current](../partners/keeping-customers-current.md)
- [Release and upgrade planning](../leadership/release-and-upgrade-planning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
