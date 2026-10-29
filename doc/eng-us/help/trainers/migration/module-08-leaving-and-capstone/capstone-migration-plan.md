---
title: "Lesson 8.2: Capstone: a complete migration plan"
audience: trainer, admin, developer, project-manager
category: training
order: 82
summary: Teams write and present a complete migration plan for a given scenario - path, inventory, scope, mapping, tools, URLs, rehearsals, cut-over and rollback - and prove one part of it on their training installation; the trainer reviews it with a rubric. 90 minutes.
keywords: migrating to exponential, capstone, migration plan, team exercise, presentation, rubric, review, scenario, proof of concept, course wrap-up
type: help
---
# Lesson 8.2: Capstone: a complete migration plan

The capstone brings the whole course together. Each team receives a scenario and writes the migration plan a real
project would start with, proves one part of it on a training installation, and presents it. The trainer reviews it
with the rubric below. The rubric is the trainer's own course material, not an official certification.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 8: Leaving Exponential, and the capstone](README.md) |
| Duration | 90 minutes (10 presenting, 55 team work, 25 presentations and review) |
| Level | Advanced |
| Audience | Mixed teams: an administrator, a developer and, if possible, a project manager |
| Prerequisites | Modules 1 to 7, or at least modules 1 to 3 and the module of the team's scenario |

## Learning objectives

After this lesson, learners can:

- choose and justify a migration path for a realistic site;
- write a plan that covers every section of the common checklist;
- prove one risky part of the plan on a training installation;
- present the plan and answer questions on rollback and addresses.

## Trainer's notes

### How to present it

1. **Brief** (5 minutes). Hand out the scenarios (below), one per team. Each team writes its plan in a shared
   document using the headings in the exercise.
2. **Rules** (5 minutes). Every claim in the plan cites a page of the documentation. Where the documentation does not
   say something (a duration, a vendor detail), the plan says "to be measured" or "to be checked", never a guess. The
   proof runs only on the team's training installation.
3. **Team work** (55 minutes). Walk between the teams. Ask each team the two questions that matter most: "What do you
   do if the final import fails?" and "Where does `/old/address` lead on day one?"
4. **Presentations** (20 minutes). Five minutes per team; the other teams ask one question each.
5. **Review** (5 minutes). Score with the rubric; give each team one strength and one improvement.

### Scenarios

1. **The old intranet.** A 4.7 site of the legacy kernel on PHP 5 and MySQL with `latin1` tables, two siteaccesses,
   three own extensions, 9 000 objects in one language, the database cluster handler, 150 users (some with `mysql`
   hashes). Target: Exponential 6.
2. **The 5.x portal.** A community 2014.11 site with the dual-kernel stack: Twig front end, one REST bundle for an app,
   image variations in YAML, two siteaccesses matched by host. Target: Exponential 6.
3. **The Ibexa campaign site.** Ibexa OSS 4.6 with Netgen Layouts, RichText, 20 Page Builder landing pages and a team
   that knows Twig. Target: decide between Nexus and Exponential 6, then plan.
4. **The WordPress magazine.** 3 000 posts in two languages (a translation plugin), a page builder plugin on 40 pages,
   an SEO plugin, 12 000 attachments, `/%category%/%postname%/` permalinks, 30 editors. Target: Exponential 6.

### Common questions

**Can we pick a different scenario?** Yes, if it is a real kind of site and the team can cite the documentation for
it; for example the static HTML guide or another system guide of the migration hub.

**How detailed must the plan be?** Every heading filled; the depth goes into the riskiest part.

### Pitfalls

- **Plans without a rollback.** The rubric gives no points for a plan that cannot go back.
- **Proofs on a public demonstration installation or any shared site.** Proofs run only on the team's own training installation.
- **Invented numbers.** "The import takes three hours" without a rehearsal is a guess; write "to be measured in
  rehearsal 1".

## Hands-on exercise

In teams, with the migration hub and the chapter of your scenario open.

1. Write the heading **Path** and the guide the decision table of the
   [migration hub](../../../../install/migration/README.md#2-which-path-fits) gives, with the reason.
2. Write **Inventory**: the items of [checklist section 1](../../../../install/migration/checklist.md#1-inventory-know-what-you-have)
   and, for your scenario, the extra checks of its chapter (14.3, 15.4, 16.4 or the system guide).
3. Write **Scope and owners**: the table of checklist section 2 with a decision and an owner per row.
4. Write **Mapping**: a mapping table for the two most important content types, plus users and passwords.
5. Write **Tools**: the tool or script for each part (lesson 3.1's table).
6. Write **Addresses**: five rows of the redirect table, including the hardest one.
7. Write **Rehearsals and tests**: how many, what is measured, which tests from checklist section 7.
8. Write **Cut-over and rollback**: the runbook in ten lines, triggers, decider, decision window, dumps kept.
9. Choose the riskiest part of the plan and **prove it** on your training installation: for example a dry-run import
   with xrowextract, a redirecting alias or wildcard tested with `curl`, the preflight queries, or an INI translation
   checked with `exp:ini get`. Note the commands and the result.
10. Prepare a five-minute presentation: path, the riskiest part and its proof, rollback.

## Expected result

- A plan with all nine headings filled, every claim citing a documentation page, unknowns marked "to be measured" or
  "to be checked".
- A proof with commands and their output from the team's training installation.
- A presentation within five minutes.

The trainer scores each plan with this rubric (three levels per criterion, maximum 18 points):

| Criterion | 0: not yet | 1: partly | 2: fully |
|---|---|---|---|
| Path | none or unjustified | named, weak reason | named with the decision table and the generation or source |
| Inventory and scope | missing parts | most parts, some owners | every checklist item, every row with an owner |
| Mapping | no table | some fields | both types complete, users and passwords decided |
| Tools | none named | named without reason | each part with its tool or script and why |
| Addresses | none | some rows | five rows incl. query strings or patterns, test method |
| Rehearsal and tests | none | one run, few tests | two or more runs, counts, samples, redirects |
| Cut-over and rollback | missing | runbook or rollback | both, with triggers, decider and dumps |
| Proof | none | attempted | run, with commands and result |
| Sources | none | some | every claim cited; no invented figures |

## Quiz

1. What is the first thing a team does with its scenario?
2. Which three things must the rollback section contain?
3. Where may a team run its proof?
   A. On a public demonstration installation B. On any shared site C. Only on its own training installation D. On production
4. How does a plan treat a duration the documentation does not give?
5. True or false: a team that completes the capstone holds an official Exponential certification.

<details>
<summary>Answers</summary>

1. Find the path with the decision table of the
   [migration hub](../../../../install/migration/README.md#2-which-path-fits).
2. Triggers, who decides, and the decision window; plus the dumps kept
   ([checklist, section 9](../../../../install/migration/checklist.md#9-rollback)).
3. **C.**
4. As "to be measured" in a rehearsal ([chapter 14.15](../../../../install/14-migrating-from-4x.md#1415-how-long-it-takes)).
5. **False.** The rubric is the trainer's own course material; it is not an official certification; the partner program's certifications are separate ([Certifications](../../../partners/certifications.md)).

</details>

## Further reading

- [The common migration checklist](../../../../install/migration/checklist.md): the frame of every plan.
- [Migrating to and from Exponential](../../../../install/migration/README.md): the hub and every guide.
- [Quizzes and practical tasks](../../train-the-trainer/module-06-assessment-and-follow-up/quizzes-and-practical-tasks.md):
  how to build fair assessments and rubrics.

## Related pages

- [Module 8: Leaving Exponential, and the capstone](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
