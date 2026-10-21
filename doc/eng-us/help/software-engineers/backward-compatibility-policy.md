---
title: Backward compatibility policy
audience: software-engineer, developer
category: software-engineering
order: 640
summary: What counts as a breaking change in Exponential - settings, templates, PHP API, database, command line, behavior and files - the rules that come with one (upgrade notes, opt-in behavior, safe defaults, no renamed identifiers, kept entry points), how deprecation works, the known exceptions for security defaults, and how to evaluate the risk of an upgrade.
keywords: backward compatibility, BC, breaking change, deprecation, opt-in, safe default, upgrade notes, identifiers, entry points, security defaults, compatibility promise, software engineer
type: help
---
# Backward compatibility policy

This page is for engineers who change Exponential or an extension, and for those who plan upgrades and need to know
what may break. It summarizes [Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md#backward-compatibility-policy).

## Contents

1. [What counts as a break](#what-counts-as-a-break)
2. [The rules](#the-rules)
3. [Deprecation](#deprecation)
4. [Known exceptions](#known-exceptions)
5. [For the upgrading side](#for-the-upgrading-side)

## What counts as a break

A change breaks compatibility when an installation or extension that worked before needs an action to keep working, or
behaves differently without being asked to:

| Kind | Examples |
|---|---|
| Settings | a setting removed or renamed, its meaning or default changed, a section moved to another file |
| Templates | a template removed or moved, a variable no longer passed, an override `Source` path changed |
| PHP API | a public class, method or function removed or renamed, a signature or return type changed |
| Database | a table or column added, removed or changed (an upgrade needs SQL) |
| Command line | a command, option, output format or exit code changed |
| Behavior | a default changed (security headers, cookies, access checks), a permission now required |
| Files | a file that sites copy or include changed in form |

## The rules

- **Every break has upgrade notes** in `doc/eng-us/bc/<series>/`, opening with the In short table
  ([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).
- **New behavior is opt-in; bug fixes need not be.** A feature arrives switched off or with a default that keeps the
  old result.
- **New settings have a safe default** in the shipped file, a comment, a feature page section and a changelog line.
- **Identifiers are not renamed.** `eZ*` and `ezp*` classes, tables, settings and paths stay; the product rename changed
  only text ([The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)).
- **Moved code keeps its entry point.** When views, scripts and cronjob parts became classes in 6.0.15, every file
  kept its path as a one-line call.

## Deprecation

- A replaced API stays available and is marked `@deprecated` with its replacement; some write a strict-mode notice
  through `eZDebug::writeStrict()` when used.
- A renamed command keeps its old name as a deprecated alias that still works
  ([Platform package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)).
- A removal is announced in the changelog's Removed section and in an upgrade note.
- Deprecated APIs inherited from the old code base have no removal date; none is announced for the 6.0 line.

## Known exceptions

- **Security defaults changed without opt-in** in September and October 2026: security headers, `HttpOnly` and
  `SameSite` session cookies, a stricter upload check, new access requirements for some remote services. Security
  fixes are treated like bug fixes: on by default, documented with how to undo them deliberately.
- Accounts without an `ezuser_setting` row cannot sign in since August 2026, and override blocks follow `Priority`
  since July 2026 ([Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md)).

## For the upgrading side

- Read the checklist of every period you pass, in order.
- Search your extensions for overridden kernel classes and copied kernel templates: they do not get fixes and can break
  silently.
- `./console exp:checkclasses` reports classes PHP refuses to load; the upgrade check compares files and tables with the
  release.
- Test with your own content and your own extensions before the production upgrade ([Deploying a change](deploying-a-change.md)).

## Related pages

- [Upgrading](../../guides/upgrading.md)
- [The upgrade checklist for extensions](../extension-development/part-4-upgrading/the-upgrade-checklist.md)
- [Release and upgrade planning](../leadership/release-and-upgrade-planning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
