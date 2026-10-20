---
title: "Lesson 6.1: From eZ Platform or Ibexa: two destinations and how to choose"
audience: trainer, admin, developer
category: training
order: 61
summary: Learners identify the generation of an eZ Platform or Ibexa source, explain the two destinations (path A to Exponential Platform Nexus and the Platform distributions, path B down to the Exponential 6 legacy kernel or Platform Legacy), and choose a path with the decision table of chapter 16.2. 45 minutes.
keywords: migrating to exponential, eZ Platform, Ibexa, Symfony, generation, path A, path B, Exponential Platform Nexus, Exponential Platform Legacy, DXP, decision table, same generation first, inventory
type: help
---
# Lesson 6.1: From eZ Platform or Ibexa: two destinations and how to choose

Sites on eZ Platform or Ibexa run on the Symfony-based platforms that the vendor built after the legacy kernel. They
have two destinations in the Exponential family: the Exponential Platform distributions of the same generation
(path A), or the Exponential 6 legacy kernel, alone or as Platform Legacy (path B). This lesson teaches how to tell
the generation of the source and how to choose.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 6: From the Symfony-based platforms](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Developers, administrators and technical project managers |
| Prerequisites | [Lesson 1.1](../module-01-planning-a-migration/choosing-a-path.md); a general idea of Symfony |

## Learning objectives

After this lesson, learners can:

- name the five source generations of chapter 16.1 and how to recognise each (kernel package, configuration key,
  namespace);
- explain the three times the database schema changed along that line;
- describe path A and path B and what each keeps and loses;
- choose a path for a described site with the table of chapter 16.2 and the rule "same generation first".

## Trainer's notes

### How to present it

Sources: [chapter 16.1](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations),
[16.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path) and
[16.4](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#164-before-you-start-inventory-freeze-and-backups).

1. **The sources** (5 minutes). Show the table "What you migrate from": eZ Platform 1.x and 2.x (`ezpublish:` key,
   `eZ\Publish\API` namespace), 3.0 to 3.3 (`ezplatform:` key), Ibexa 4.x and 5.0 (`ibexa:` key, `Ibexa\Contracts`
   namespace). The schema changed three times: 3.0 dropped about 80 legacy tables, 4.0 added `ibexa_*` tables, 5.0
   renamed every core table to `ibexa_*`.
2. **Path A** (5 minutes). Nexus and the Platform distributions, one line per generation. Three facts: the forks
   replace the upstream package names, the PHP namespaces stay upstream, Nexus is built on the open source edition
   (no Page Builder, Form Builder or commercial workflow).
3. **Path B** (4 minutes). Exponential 6, the legacy kernel, or Platform Legacy, the hybrid that runs both kernels on
   one database. Moving "down" means moving to a database the 6.0 kernel can read.
4. **Choosing** (6 minutes). Walk through the table of 16.2 row by row. The rules of thumb: same generation first;
   path B is a rewrite of the presentation and custom code, not of the content; do not mix the paths in one
   cut-over.

### Common questions

**Which path has the lowest risk for a live site?** Path A within the same generation: a package swap (16.2).

**We use the commercial Page Builder. What happens?** There is no Page Builder in Nexus; landing pages are rebuilt
with Netgen Layouts on path A, or with Exponential Layouts on path B (16.2, 16.5.9).

**Do we have to know Composer version traps?** Yes, for path A: always name a version for Nexus, because the
repository keeps upstream tags (16.1). Lesson 6.2 covers it.

### Pitfalls

- **Jumping generations and products at once.** Move to the matching Exponential Platform line first, let it settle,
  then decide the next step.
- **Choosing path B because the team dislikes Symfony.** Path B is the highest effort: presentation and custom code
  are rewritten. Make the decision with the table, not with taste.
- **Version numbers from memory.** Read the tables of chapter 16.1 on the day.

## Hands-on exercise

A decision exercise in pairs, with chapter 16 open.

1. Open [chapter 16.1](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations).
2. For each source below, write its generation, its kernel package and its main configuration key:
   - a project whose `composer.json` requires `ezsystems/ezpublish-kernel` 7.5;
   - a project whose configuration starts with `ibexa:` and whose kernel is `ibexa/core` 4.6;
   - a project with `ezsystems/ezplatform-kernel` 1.x.
3. For each, find the matching Nexus line and the matching Platform Legacy line in the tables of 16.1.
4. Open [chapter 16.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path). For an
   Ibexa 4.6 site with eight custom bundles, RichText content, a team that knows Twig and editors used to the Symfony
   admin, write path A or path B per row of the table and a conclusion.
5. Do the same for a 2.5 site with two small bundles, no RichText, a team that knows INI and TPL, and a requirement
   to run on PHP 8.5.
6. Write the inventory queries of [chapter 16.4](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#164-before-you-start-inventory-freeze-and-backups)
   you would run first on each source, and why.

## Expected result

- Step 2: eZ Platform 2.5 (`ezpublish:`); Ibexa 4.6 (`ibexa:`); eZ Platform 3.x or Ibexa 3.3 (`ezplatform:`).
- Step 3: Nexus `1.0.0.x` or `v2.5.0.x` with Platform Legacy `v2.5.0.x`; Nexus `1.2.0.x` with Platform Legacy
  `v4.6.23.x`; Nexus `1.1.0.x` with Platform Legacy `v3.3.44.x`.
- Step 4: path A (Nexus `1.2.0.x`, same generation) for the 4.6 site.
- Step 5: path B is a reasonable choice for the 2.5 site; the pair names the work it means (templates and bundles
  rewritten).
- Step 6: release rows of `ezsite_data`, field types in use, sort fields 6 and 7, hash types, languages, sizes.
- The trainer checks steps 3 and 4 with two pairs.

## Quiz

1. Which generation renamed every core table to `ibexa_*`?
   A. eZ Platform 2.5 B. eZ Platform 3.0 C. Ibexa 4.0 D. Ibexa 5.0
2. What does "the forks replace the upstream names" mean for third-party packages?
3. What is path B a rewrite of, and what not?
4. Name the rule of thumb for an Ibexa 4.6 site that wants to reach Nexus 5 eventually.
5. True or false: Nexus includes the commercial Page Builder.

<details>
<summary>Answers</summary>

1. **D** ([chapter 16.1](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations)).
2. Each fork declares the upstream package under `replace`, so packages that require `ibexa/*` or `ezsystems/*` names
   keep resolving (chapter 16.1).
3. Of the presentation and the custom code, not of the content; the content tables are shared
   ([chapter 16.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path)).
4. Same generation first: Nexus `1.2.0.x` (or the 4.6 skeleton), then change the generation afterwards (chapter 16.2).
5. **False.** Nexus builds pages with Netgen Layouts; the Page Builder has no counterpart (chapter 16.1, 16.5.9).

</details>

## Further reading

- [Chapter 16: Migrating from eZ Platform and Ibexa](../../../../install/16-migrating-from-ez-platform-and-ibexa.md):
  both paths in full.
- [Exponential Platform Nexus](../../../../install/migration/exponential-platform-nexus.md) and
  [Exponential Platform Legacy](../../../../install/migration/exponential-platform-legacy.md): the family guides.
- [Platform package map](../../../../specifications/6.0/platform-package-map.md): the complete package list.

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
