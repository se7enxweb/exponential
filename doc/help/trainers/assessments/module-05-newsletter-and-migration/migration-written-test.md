---
title: "Lesson 5.3: Migrating to Exponential, written test"
audience: trainer, admin, developer
category: training
order: 53
summary: A fourteen-question written test for the end of the Migrating to Exponential course, on choosing a path, migrating from older releases of the kernel and from the Symfony-based platforms, mapping a content model, redirects, rehearsal, cut-over, rollback and leaving Exponential, with an answer key that cites the migration chapters. 30 minutes for learners.
keywords: assessments and practical exercises, migration, written test, older releases, 4.x, 5.x legacy, eZ Platform, Ibexa, content model mapping, remote id, redirects, URL alias, URL wildcard, rehearsal, cut-over, rollback, leaving Exponential
type: help
---
# Lesson 5.3: Migrating to Exponential, written test

This test closes the migration course. It checks method, not memory of commands: choosing the right guide, keeping
the old site's addresses, making an import repeatable, rehearsing, and knowing when to roll back.

> **Note**
> This is the trainer's own assessment, not an official certification. The product's former name appears in the
> migration chapters as history; use it in questions only where a learner needs it to recognize their old system.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 5: Assessing the newsletter and migration courses](README.md) |
| Duration | 45 minutes (5 briefing, 30 test, 10 going through the answers) |
| Level | Advanced |
| Audience | Learners at the end of the Migrating to Exponential course |
| Prerequisites | The migration course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- choose the right migration guide for a source system;
- explain what stays and what changes from an older release of the kernel;
- map a source content model to classes, attributes and locations;
- plan redirects, rehearsals, cut-over and rollback;
- name the tools for exporting content when leaving.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 30 minutes.
2. Go through the answers with the [migration overview](../../../../install/migration/README.md) and the
   [checklist](../../../../install/migration/checklist.md) on the projector.

### Common questions

**Our course covered only WordPress.** Replace questions 3 to 6 with questions from the
[WordPress guide](../../../../install/migration/wordpress.md), with the same rule: each answer cites a section.

### Pitfalls

- **"Convert XmlText to RichText".** Learners who read the vendor's old guides expect it. Question 4 discusses it.

## The test

1. A site runs on an older release of this kernel (4.x). Which chapter is the guide?
2. Should the old installation be migrated in place? What is done instead?
3. Why do the database update files run in two phases (schema first, repair scripts second) with today's code?
4. Is rich text (`ezxmltext`) converted when moving from 4.x or 5.x legacy to Exponential 6?
5. A team on Ibexa wants the lowest risk for a live site. Which path does chapter 16 point to first?
6. Name one "rule of thumb that holds for every path" from the migration overview.
7. In a content model mapping, what does a WordPress post category usually become in Exponential?
8. Why does every imported object get a remote id that names its source?
9. Name the three ways to keep an old address working in Exponential.
10. Old addresses with query strings (`?p=123`) cannot be URL aliases. Where are they handled?
11. How often should the import be rehearsed before the cut-over, and on what?
12. What should be decided before the cut-over about rollback?
13. User passwords from another system: what is usually planned, and why?
14. A customer leaves Exponential. Which tool exports all content of a site, and what does it never export?

<details>
<summary>Answers</summary>

1. Chapter 14, [Migrating from 4.x](../../../../install/14-migrating-from-4x.md).
2. No. Build the new site next to the old one, rehearse on a copy of the production database, then repeat once
   against a fresh copy while the old site is in maintenance
   ([14.2](../../../../install/14-migrating-from-4x.md#142-plan-the-migration-on-a-staging-copy)).
3. The repair scripts run on the 6.0 kernel, whose persistent object definitions describe the current schema
   ([14.5.4](../../../../install/14-migrating-from-4x.md#1454-two-phases-schema-first-data-repair-second)).
4. No. `ezxmltext` stays `ezxmltext`; there is no XmlText-to-RichText conversion
   ([14.1](../../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not),
   [15.1](../../../../install/15-migrating-from-5x-legacy.md#151-what-changes-and-what-the-vendors-guide-says)).
5. Path A, to Exponential Platform Nexus of the same generation, a package swap; change the generation afterwards
   ([16.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path)).
6. One of: content first, presentation second; one change at a time; keep every old address working; rehearse
   ([Migration overview](../../../../install/migration/README.md#2-which-path-fits)).
7. A location in the content tree (or keywords or tags, by decision)
   ([Checklist](../../../../install/migration/checklist.md#3-map-the-content-model),
   [Content model mapping](../../../../install/migration/content-model-mapping.md#1-the-building-blocks-at-a-glance)).
8. So re-running the import updates instead of duplicating, and redirects can be generated from it
   ([Checklist](../../../../install/migration/checklist.md#3-map-the-content-model)).
9. The node's own URL alias, a custom URL alias that redirects, or a URL wildcard
   ([Checklist](../../../../install/migration/checklist.md#4-urls-and-redirects)).
10. In the web server or a small redirect module before Exponential, from the same redirect table
    ([Checklist](../../../../install/migration/checklist.md#4-urls-and-redirects)).
11. At least twice, on a copy of the target, timed
    ([Checklist](../../../../install/migration/checklist.md#6-build-the-import-and-rehearse-it)).
12. What triggers a rollback and who decides; keep the old site running read-only and a backup taken before the final
    import ([Checklist](../../../../install/migration/checklist.md#9-rollback)).
13. A reset by mail with a link, because source hash types are rarely readable by the target
    ([Checklist](../../../../install/migration/checklist.md#3-map-the-content-model)).
14. The xrowextract site archive (`./console ext:xrowextract:archive --set=everything ...`); password hashes are never
    exported ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md#1-what-to-export)).

</details>

## Scoring

- One point per question; question 9 needs all three ways for the point.
- Pass: 10 of 14 points or more, with questions 2 and 9 correct: migrating in place and losing addresses are the two
  most expensive mistakes.

## Further reading

- [The migration overview](../../../../install/migration/README.md) and the
  [checklist](../../../../install/migration/checklist.md)
- [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md)
- [Chapter 17: Migration reference](../../../../install/17-migration-reference.md)

## Related pages

- [Lesson 5.4: Migrating to Exponential, practical task](migration-practical-task.md)
- [Module 5: Assessing the newsletter and migration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
