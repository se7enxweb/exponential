---
title: "Lesson 6.2: Path A: to Exponential Platform Nexus"
audience: trainer, developer
category: training
order: 62
summary: Developers plan a same-generation move from eZ Platform or Ibexa to Exponential Platform Nexus - bring the source to the last release of its line, swap the packages, rename console commands, rebuild landing pages with Exponential Layouts, and verify. 45 minutes.
keywords: migrating to exponential, path A, Exponential Platform Nexus, package swap, composer, se7enxweb forks, replace, console commands, exponential:reindex, Page Builder, landing pages, version traps, verify
type: help
---
# Lesson 6.2: Path A: to Exponential Platform Nexus

Within one generation, moving to Nexus is mostly a Composer change. This lesson teaches the order chapter 16.5 gives:
the source at the last release of its line, the package swap, the console command names, page building, and the
checks afterwards. It is a planning lesson; nobody installs Nexus in class.

> **Note**
> Never run `exponential:install` against a production database: it creates a new repository (chapter 16.5.1).

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 6: From the Symfony-based platforms](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Symfony developers |
| Prerequisites | [Lesson 6.1](two-destinations-and-choosing.md); Composer basics |

## Learning objectives

After this lesson, learners can:

- pick the Nexus line for a source generation and say what to carry into the new project;
- list the upstream update steps that bring a source to the last release of its line;
- write the package swap in `composer.json` for one generation;
- find and rewrite console command names in cron entries and deployment scripts;
- plan landing pages of the Page Builder as a content task with Exponential Layouts.

## Trainer's notes

### How to present it

Source: [chapter 16.5](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#165-path-a-to-exponential-platform-nexus).

1. **Pick the generation** (4 minutes). Show the table of 16.5.1. A Nexus line brings a demo site; for an existing
   site, create the project from the line (or the matching skeleton) and carry your own `src/`, `templates/`,
   `translations/`, `assets/`, configuration and storage into it.
2. **Last release first** (4 minutes). The forks track the last upstream release of each line. Follow the vendor's
   update steps on the source; never pass `--force` to `mysql` or `psql`.
3. **The swap** (4 minutes). Show the before and after `composer.json` of 16.5.3: `ibexa/oss` becomes
   `se7enxweb/oss`. Version traps from 16.1: five-part tags are not on Packagist, and a Nexus install without a
   version can pick an upstream tag.
4. **Command names** (4 minutes). Since 2026-04-07 the forks use `exponential:` names; on Platform v5 the old
   `ibexa:` names of renamed commands do not exist. Rewrite cron entries and deployment scripts before the cut-over.
5. **Page building** (4 minutes). The commercial Page Builder has no counterpart in Nexus; no tool converts landing
   pages into layouts. Plan it as a content task (16.5.9).

### Common questions

**Does our custom code need rewriting?** Code written for the same generation runs unchanged: the namespaces stay
upstream (16.1). What changes are command names, branding, license wording and the database installers.

**How do we check the result?** The commands of 16.8: `php bin/console list exponential`, `debug:router`,
`composer why ibexa/core`, and status codes of the front page and the admin; then content counts before and after.

**Can we go back?** Within one generation, restore `composer.json` and `composer.lock`, install, clear caches (16.9).

### Pitfalls

- **A bare `composer create-project` of Nexus.** Always name a version.
- **Upstream and fork side by side.** The checklist of 16.11 says: forks installed, no upstream duplicates.
- **Cron still calling old command names on v5.** The renamed commands are not registered there; jobs fail silently
  unless you check.

## Hands-on exercise

A planning exercise in pairs, with chapter 16.5 open. The scenario: an Ibexa OSS 4.6 project with Netgen Layouts,
two custom bundles, cron entries in `/etc/cron.d/site` and a deployment script `deploy/release.sh`, and 35 landing
pages built with the Page Builder.

1. From [16.5.1](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1651-pick-the-generation), write the
   target Nexus line and the alternatives of the same generation.
2. From [16.5.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1652-bring-the-source-to-the-last-release-of-its-line),
   write what must happen on the source first.
3. Write the `require` section of the new `composer.json`, starting from the "after" example of
   [16.5.3](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1653-swap-the-packages).
4. Write the `grep` command of [16.5.4](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1654-console-command-names)
   that finds old command names in the cron directory and the deployment script, and note which line of the table
   applies to Nexus `1.2.0.x`.
5. Write a three-step plan for the 35 landing pages from
   [16.5.9](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1659-page-building-page-builder-against-exponential-layouts),
   including the SQL that counts them.
6. Write the verification commands of [16.8](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#168-verify-the-migration)
   you will run after the swap, and the three counting queries.

## Expected result

- Step 1: Nexus `1.2.0.x`; alternatives `se7enxweb/exponential-platform-v4x-dxp-skeleton` and Platform Legacy
  `v4.6.23.x`.
- Step 2: the source to 4.6 LTS and its latest patch, with the vendor's steps and without `--force`.
- Step 3: `se7enxweb/oss` `~4.6.0` replaces `ibexa/oss`, with the PHP constraint of the Nexus line.
- Step 4: the `grep -rnE` line for `ibexa|ezplatform|ezpublish` commands; for Nexus `1.2.0.x` the primary names stay
  `ibexa:*`, because its lock file installs upstream `ibexa/core`.
- Step 5: list with `SELECT COUNT(*) ... WHERE data_type_string = 'ezlandingpage'`, rebuild each as a layout with a
  rule, then remove or ignore the field.
- The trainer checks steps 3 and 5.

## Quiz

1. What do you carry from the old project into a new project created from a Nexus line?
2. Why must a Nexus install always name a version?
   A. Composer is slow without one B. The repository keeps upstream tags, so it can install an upstream release
   C. Packagist requires it D. Nexus has only one release
3. Which option must never be passed to `mysql` or `psql` when running update scripts?
4. On Platform v5, does `ibexa:reindex` still exist?
5. Which tool converts Page Builder landing pages into layouts of Exponential Layouts?

<details>
<summary>Answers</summary>

1. Your own `src/`, `templates/`, `translations/`, `assets/`, configuration and storage, pointed at your database
   ([chapter 16.5.1](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1651-pick-the-generation)).
2. **B** ([chapter 16.1](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations)).
3. `--force`: a failing statement must stop the run
   ([chapter 16.5.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1652-bring-the-source-to-the-last-release-of-its-line)).
4. No. On v5 no aliases are registered; the command is `exponential:reindex`
   ([chapter 16.5.4](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1654-console-command-names)).
5. None; plan it as a content task
   ([chapter 16.5.9](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1659-page-building-page-builder-against-exponential-layouts)).

</details>

## Further reading

- [Chapter 16.5: Path A](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#165-path-a-to-exponential-platform-nexus):
  every step with the generation's details.
- [Exponential Platform Nexus](../../../../install/migration/exponential-platform-nexus.md): the family guide.
- [Package forks and command renames](../../../../bc/6.0/platform-package-forks-and-command-renames.md): what changed
  in the forks.

## Related pages

- [Module 6: From the Symfony-based platforms](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
