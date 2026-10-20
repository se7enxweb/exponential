---
title: "Lesson 5.1: Which 5.x variant, and what carries over"
audience: trainer, admin, developer
category: training
order: 51
summary: Learners identify which of the three 5.x-era variants an old site runs (the dual-kernel stack, the 5.90 legacy-only line, or the legacy kernel run without Symfony), explain why the database carries over without conversion, and plan the migration with an inventory and a timeline. 45 minutes.
keywords: migrating to exponential, 5.x, dual kernel, ezpublish_legacy, legacy bridge, 5.90, community 2014.11, shared database, ezsqldiff, inventory, timeline, extraction
type: help
---
# Lesson 5.1: Which 5.x variant, and what carries over

Sites of the 5.x era ran two kernels side by side: a Symfony "new stack" and the legacy kernel, joined by a bridge.
Exponential continues the legacy kernel, so the move is an extraction rather than a rewrite. This lesson teaches
learners to recognise the variant, to see why the content needs no conversion, and to plan the work around the
Symfony code, which is what really takes time.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 5: From the 5.x legacy stack](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Module 4](../module-04-from-the-4x-line/README.md) (the database chain); shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- tell the three 5.x-era variants apart from the files in the old installation's root;
- explain why the new stack of 5.x had no tables of its own and what that means for the migration;
- name the field type that has no datatype in Exponential and needs a decision;
- write the inventory of chapter 15.4 and order the phases of its timeline.

## Trainer's notes

### How to present it

Sources: [chapter 15.1](../../../../install/15-migrating-from-5x-legacy.md#151-what-changes-and-what-the-vendors-guide-says),
[15.2](../../../../install/15-migrating-from-5x-legacy.md#152-which-variant-do-you-run),
[15.3](../../../../install/15-migrating-from-5x-legacy.md#153-what-carries-over-the-shared-database) and
[15.4](../../../../install/15-migrating-from-5x-legacy.md#154-plan-and-timeline).

1. **The other direction** (4 minutes). The vendor documented this migration toward its Symfony-only product, which
   drops the legacy kernel. Exponential goes the other way: it keeps the legacy kernel and drops the Symfony side. So
   many of the vendor's steps do not apply or apply in reverse. Show three rows of the table in 15.1: XmlText is
   **not** converted to RichText, ezflow pages stay, legacy datatypes and templates keep working.
2. **Three variants** (5 minutes). Show the table of 15.2: `ezpublish/`, `ezpublish_legacy/`, `web/` and `src/` mean
   the dual-kernel stack; only the legacy tree with `VERSION_MAJOR = 5` and `VERSION_MINOR = 90` means the 5.90 line
   (which follows chapter 11); a dual-kernel tree whose document root points at `ezpublish_legacy/` is the legacy
   kernel run without Symfony.
3. **The shared database** (5 minutes). The new stack's persistence layer read and wrote the legacy schema directly;
   content created through the Public API is made of the same rows. Exponential's schema is that legacy schema plus
   ten tables, all created by the update steps. Tables Exponential does not know are ignored.
4. **Plan** (6 minutes). The duration depends on the amount of Symfony code, not on the amount of content. Walk
   through the inventory list and the seven phases of 15.4: inventory, rehearsal install, rewrite, dress rehearsal,
   freeze, cut-over, watch.

### Common questions

**Do we convert XmlText to RichText as the vendor's guide says?** No. XmlText (`ezxmltext`) is the kernel's rich text
datatype; nothing is converted (15.1).

**Someone already ran the eZ Platform update file `dbupdate-5.4.0-to-6.13.0.sql`. Is the database lost?** No. None
of its changes stop Exponential; set the version row as chapter 15.7 says, so that the update files apply (15.3).

**What decides how long it takes?** The amount of Symfony code: bundles, Twig templates, controllers, listeners (15.4).

### Pitfalls

- **Installing Exponential into the old project.** The old `composer.json` sets `symfony-app-dir`; make a new
  directory instead (lesson 5.2).
- **Ignoring `ezrichtext`.** Classes with it are rare on a 5.x site, but each needs a decision (lesson 5.3).
- **Forgetting what the Symfony side injected into legacy.** Settings that were overwritten at run time are often
  empty in the legacy INI files (lesson 5.2).

## Hands-on exercise

Part 1 is read-only on your training installation, which stands in for the extracted site; part 2 is a planning
exercise.

1. In the installation root, read the version of the code:

   ```bash
   grep -n "const VERSION_" lib/version.php
   ```

2. Count the tables of your training database:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table';"
   ```

3. List the tables that Exponential added to the legacy schema:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT name FROM sqlite_master WHERE type = 'table' AND (name LIKE 'exp%' OR name = 'ezrss_export_opml_item') ORDER BY name;"
   ```

   Compare the list with the ten tables named in chapter 15.3.
4. Planning: an old site's root contains `ezpublish/EzPublishKernel.php`, `ezpublish/config/ezpublish.yml`,
   `ezpublish_legacy/`, `web/` and `src/`, and its `composer.json` sets `"symfony-app-dir": "ezpublish"`. Which variant
   is it, and which chapter do you follow?
5. The same site has three Symfony bundles in `src/`: one renders the public pages in Twig, one adds a REST route for
   a mobile app, one listens to publishing events and sends a notification to a chat service. Write the inventory
   line of chapter 15.4 for each, with a decision: rewrite as an extension, drop, or keep on a bridge.
6. Write the seven phases of the timeline of chapter 15.4 with a "done when" line each, for this site.

## Expected result

- Step 1 shows the version constants of Exponential 6.0 (major 6, minor 0).
- Step 3 lists the `expaudit_*`, `expbookmark_folder`, `expmail_*` and `ezrss_export_opml_item` tables, matching
  chapter 15.3.
- Step 4: the dual-kernel stack (5.0 to 5.4, or community 2013.x and 2014.x); chapter 15.
- Step 5: each bundle has an owner and a decision; the Twig front end is marked as the largest item.
- The trainer checks the decisions of step 5 and asks one learner to explain why the database needs no conversion.

## Quiz

1. Which files in the old root tell you it is the dual-kernel stack?
2. A 5.x site's content was created through the Public API. Can Exponential read it?
   A. No, it must be converted B. Yes, it is the same rows of the legacy schema C. Only after a RichText conversion
   D. Only through REST
3. What decides the duration of a 5.x migration, according to chapter 15.4?
4. True or false: Exponential converts XmlText fields to RichText during this migration.
5. Which field type of the new stack has no datatype in Exponential?

<details>
<summary>Answers</summary>

1. `ezpublish/` (with `EzPublishKernel.php`, `config/ezpublish.yml`), `ezpublish_legacy/`, `web/`, `src/`
   ([chapter 15.2](../../../../install/15-migrating-from-5x-legacy.md#152-which-variant-do-you-run)).
2. **B** ([chapter 15.3](../../../../install/15-migrating-from-5x-legacy.md#153-what-carries-over-the-shared-database)).
3. The amount of Symfony code, not the amount of content
   ([chapter 15.4](../../../../install/15-migrating-from-5x-legacy.md#154-plan-and-timeline)).
4. **False.** XmlText is the kernel's rich text datatype; nothing is converted
   ([chapter 15.1](../../../../install/15-migrating-from-5x-legacy.md#151-what-changes-and-what-the-vendors-guide-says)).
5. `ezrichtext` (RichText)
   ([chapter 15.8](../../../../install/15-migrating-from-5x-legacy.md#158-field-types-against-datatypes)).

</details>

## Further reading

- [Chapter 15: Migrating from the 5.x legacy stack](../../../../install/15-migrating-from-5x-legacy.md): the full
  chapter.
- [Chapter 11.5: From 5.4 or 5.90 to 6.0.0](../../../../install/11-upgrading.md#115-from-54-or-590-to-600): the
  database path of the 5.90 line.
- [Older releases of this kernel](../../../../install/migration/older-releases.md): the table of starting points.

## Related pages

- [Module 5: From the 5.x legacy stack](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
