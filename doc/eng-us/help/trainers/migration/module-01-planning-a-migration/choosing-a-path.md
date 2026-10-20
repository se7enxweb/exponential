---
title: "Lesson 1.1: The Exponential family and choosing a path"
audience: trainer, admin, developer, project-manager
category: training
order: 11
summary: Learners name the products of the Exponential family, tell the legacy kernel from the Symfony-based platforms, and use the decision table of the migration hub to pick the right guide for a given site. 45 minutes.
keywords: migrating to exponential, migration path, exponential family, platform legacy, nexus, dxp, exponential basic, decision table, migration hub
type: help
---
# Lesson 1.1: The Exponential family and choosing a path

Every migration starts with two questions: where does the site come from, and where should it go? This lesson gives
learners the map of the Exponential family and the decision table of the migration hub, so that the rest of the
course always starts from the right guide.

> **Note**
> Product versions move on. The migration hub names the date on which its product table was read. Before the class,
> open it and check that the versions you will show are still the ones it lists.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 1: Planning a migration](README.md) |
| Duration | 45 minutes (25 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Administrators, developers and project managers who plan a migration |
| Prerequisites | None in this course; a working knowledge of what a CMS is |

## Learning objectives

After this lesson, learners can:

- name the products of the Exponential family and say which of them contain the Exponential 6 legacy kernel;
- explain the difference between the legacy kernel (INI, TPL) and the Symfony-based platforms (YAML, Twig);
- use the decision table of the migration hub to find the guide for a described site;
- apply the rule "one change at a time" to a migration plan.

## Trainer's notes

### How to present it

Source: [Migrating to and from Exponential](../../../../install/migration/README.md), sections 1 and 2.

1. **The family** (8 minutes). Show the product table of the hub (section 1.1). Do not read every row. Point out the
   three groups: the Exponential 6 kernel itself (and its starter), the Symfony-only platforms (Exponential Platform,
   DXP), and the hybrids and distributions that carry both (Platform Legacy, Nexus on its older lines). Exponential
   Basic is a different code base (the continuation of the classic 2.x CMS), and Exponential Velocity is an
   application server, not a content product.
2. **What each has** (5 minutes). Show the table in section 1.3: content model, templates, page building, REST,
   database engines. The message: the legacy kernel and the Symfony platforms share the content model (classes or
   content types, objects, locations) but differ in templates, configuration and custom code.
3. **History, in one sentence** (2 minutes). Exponential 6 is the continuation of the eZ Publish legacy kernel, a
   product its creator discontinued after almost 18 years. The old name appears in the migration chapters only to
   identify the releases learners migrate from.
4. **The decision table** (6 minutes). Walk through section 2 of the hub: answer the questions in order, the first
   "yes" gives the guide.
5. **The rules of thumb** (4 minutes). Content first, presentation second; one change at a time; keep every old
   address working; rehearse.

### Common questions

**Is Exponential Platform the same as Exponential?** No. "Exponential Platform" without a suffix is the Symfony-only
distribution; "Exponential 6" is the legacy kernel this help belongs to. The hub has a table "The names you may have
heard and what they mean" for exactly this confusion.

**Which product should a new project choose?** That is not a migration question. Point to the hub's decision table
for migrations, and to the introduction of the installation book for new sites.

**Can we change the product, the PHP version and the database engine in one go?** The hub advises against it: move
to the closest target first, let it settle, then change the next thing.

**Is "Nexus 5" a version number?** No. The numbers 2.5, 4.x or 5.x after Nexus are platform generations inside
Nexus, not Nexus versions. The hub explains the release lines in section 1.2.

### Pitfalls

- **Teaching from memory.** Version numbers and release lines change. Always show the hub, never a slide with
  numbers you typed months ago.
- **Getting lost in the product table.** Learners need the three groups, not every package name. Keep the details
  for module 6.
- **Promising a tool that does not exist.** If a learner asks for a one-click converter from another system, the
  honest answer is in the hub: every guide names the real tools and where a script of your own is needed.

## Hands-on exercise

This is a paper exercise with the migration hub open in a browser or in the help center of the training
installation. Work in pairs.

1. Open [Migrating to and from Exponential](../../../../install/migration/README.md).
2. Read section 2, **Which path fits?**
3. For each site below, write down the guide the decision table gives, and the first question that said "yes":
   - a site on a 4.6 release of the old legacy kernel, on PHP 5;
   - a WordPress blog with 800 posts;
   - an Ibexa 4.6 site with custom Symfony bundles, whose team wants to keep its Twig templates;
   - a hand-made HTML site of 40 pages;
   - an Exponential 6 site whose owner wants the Symfony stack but must keep the legacy admin for a year.
4. For the Ibexa 4.6 site, open [chapter 16.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path)
   and write down which Nexus line has the same generation.
5. Pick one of the five sites and write a three-step plan that follows "one change at a time".
6. Compare your answers with another pair.

## Expected result

- Each pair has a guide per site: chapter 14 (or "Older releases of this kernel"), the WordPress guide, chapter 16
  then the family guide of the target, the static HTML guide, and the Platform Legacy guide.
- For the Ibexa 4.6 site the pair named the Nexus `1.2.0.x` line, the 4.6 generation.
- The three-step plan changes one thing per step (for example: same generation first, then the PHP version, then the
  design).
- The trainer checks by asking two pairs to read their answers aloud and comparing them with the hub.

## Quiz

1. Which of these products is a different code base and data model from Exponential 6?
   A. Exponential Platform Legacy B. Exponential Basic C. Exponential Starter D. CJW Exponential Platform Nexus
2. What do the legacy kernel and the Symfony-based platforms share?
3. True or false: Exponential Velocity is a content product you can migrate a site to.
4. The decision table: what do you do with the questions, and when do you stop?
5. Name two of the four rules of thumb that hold for every migration path.

<details>
<summary>Answers</summary>

1. **B.** Exponential Basic continues the classic 2.x CMS with its own modules
   ([migration hub, section 1.1](../../../../install/migration/README.md#11-the-products)).
2. The content model: classes or content types, objects, locations, in the same tables
   ([migration hub, section 1.3](../../../../install/migration/README.md#13-what-each-product-has-in-the-terms-of-a-migration)).
3. **False.** It is the PHP application server recommended for Exponential 6, not a content product (hub, section 1.1).
4. Answer them in order; the first "yes" gives the guide
   ([migration hub, section 2](../../../../install/migration/README.md#2-which-path-fits)).
5. Any two of: content first, presentation second; one change at a time; keep every old address working; rehearse
   (hub, section 2).

</details>

## Further reading

- [Migrating to and from Exponential](../../../../install/migration/README.md): the hub with the product table and
  the decision guide.
- [Chapter 16.1: The two destinations](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations):
  the release lines of Nexus and Platform Legacy in detail.
- [Older releases of this kernel](../../../../install/migration/older-releases.md): which chapter fits which old
  release.

## Related pages

- [Module 1: Planning a migration](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
