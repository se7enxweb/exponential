---
title: Changelogs and upgrade notes
audience: software-engineer, developer
category: software-engineering
order: 620
summary: Where Exponential records what changed and what an installation has to do about it - the changelog pages per version and per extension, the Added, Updated, Removed and Renamed sections, the upgrade notes in doc/eng-us/bc/ with their In short table, the period checklists, and the history ledger - and how to write an entry for your own change.
keywords: changelog, release notes, upgrade notes, backward compatibility, bc, doc/changelogs, Added, Updated, Removed, Renamed, In short table, behavior changes, history ledger, software engineer
type: help
---
# Changelogs and upgrade notes

This page is for engineers who write the record of a change, and for those who read it before an upgrade. Exponential
keeps two records with different jobs: the changelog says **what** changed, the upgrade notes say **what to do** about
it.

## Contents

1. [Where the records are](#where-the-records-are)
2. [The changelog](#the-changelog)
3. [Upgrade notes](#upgrade-notes)
4. [Writing the record of your change](#writing-the-record-of-your-change)
5. [Reading the record before an upgrade](#reading-the-record-before-an-upgrade)

## Where the records are

| Record | Location | Example |
|---|---|---|
| Kernel changelog per version | `doc/changelogs/<series>/<version>.md` | [6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Extension changelogs | `doc/changelogs/extensions/<name>.md` | [Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md) |
| Upgrade notes per change | `doc/eng-us/bc/<series>/` | [Trusted proxies](../../bc/6.0/trusted-proxies.md) |
| Checklists per period | `doc/eng-us/bc/6.0/behaviour-changes-*.md` | [Behaviour changes of 1 and 2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md) |
| Feature pages | `doc/eng-us/features/6.0/` | [Quality checks](../../features/6.0/quality-checks.md) |

Older series back to 2.9 are kept in `doc/changelogs/` for history.

## The changelog

- One page per release; entries grouped as **Added**, **Updated**, **Removed** and **Renamed** (the 6.0.15 page also
  by period, because the line is long).
- Each page starts with **Upgrading**, which links the upgrade notes in the order to read them.
- Entries are assembled from commit subjects, which use the same prefixes (`Added:`, `Updated:`, `Fixed:`, `Removed:`,
  `Renamed:`); fixes appear under Updated in the Velocity changelog.
- A page for a version in development says so on its first line ("Not released yet").

## Upgrade notes

Every change that needs action from an installation, or behaves differently without being asked to, gets a page in
`doc/eng-us/bc/<series>/`. A page opens with an **In short** table:

| Row | Answers |
|---|---|
| What changed | the behavior before and after |
| Who is affected | which installations, extensions or users |
| How to check | a command or a page that shows whether you are affected |
| How to fix | what to do, or how to undo it deliberately |

Changes that span many areas are collected per period with a deploy order and a checklist table. See
[Backward compatibility policy](backward-compatibility-policy.md) for what counts as such a change.

## Writing the record of your change

1. Write the commit subject with the right prefix; the body says why and what ([Git workflow](git-workflow.md)).
2. Add the entry to the changelog page of the version in development, under the matching section.
3. If an installation must act, write the upgrade note with the four rows, and link it from the changelog's
   Upgrading section.
4. A new setting gets its feature page section and a line in the changelog.
5. Name the product Exponential, and keep identifiers such as `eZINI` as they are.
6. Check the pages with `exp:help check` ([Documentation as code](documentation-as-code.md)).

## Reading the record before an upgrade

Read the Upgrading section of every version you pass, then the checklist of every period in the order it gives, then
the changelogs of the extensions you use. The upgrade guide walks through it ([Upgrading](../../guides/upgrading.md)).

## Related pages

- [Changelogs, for extension maintainers](../extension-development/part-7-releasing/changelogs.md)
- [Commit messages](../extension-development/part-5-extend-fork-contribute/commit-messages.md)
- [Release engineering](release-engineering.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
