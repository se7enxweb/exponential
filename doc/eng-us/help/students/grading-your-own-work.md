---
title: Grading your own work
audience: student
category: learning
order: 24
summary: How learners, study groups and teachers grade the study projects - the four common criteria scored 0 to 3, project-specific checks, grade bands, self and peer review, feedback that helps, and an honest statement of what a grade from these projects means and does not mean.
keywords: grading, rubric, self assessment, peer review, score, feedback, criteria, study projects, marks, grade bands, honest, learner
type: help
---
# Grading your own work

Each study project ends with a rubric. This page explains how to use it, alone, with a fellow learner or as a
teacher, so that a grade says something real about the work.

## Contents

1. [The four criteria](#the-four-criteria)
2. [Project checks](#project-checks)
3. [From scores to a grade](#from-scores-to-a-grade)
4. [Self review](#self-review)
5. [Peer review](#peer-review)
6. [Feedback that helps](#feedback-that-helps)
7. [What a grade means](#what-a-grade-means)

## The four criteria

Score each criterion from 0 to 3.

| Score | It works | It is built the documented way | It is safe | It is explained |
|---|---|---|---|---|
| **0** | Does not run, or not handed in | Core files edited, settings in the wrong place | Secrets in files, or unescaped user input | No report |
| **1** | Partly works; the main goal is not met | Works, but against the documented way in places | One safety gap | A report that a reader cannot follow |
| **2** | Meets the goal | Follows the documented way, with small lapses | No gap found | A reader can follow and repeat it |
| **3** | Meets the goal and handles edge cases | Follows the documented way throughout, and cites the pages | Safe, and the report says what was checked | Clear, short, with sources and the release |

"The documented way" is what the help pages say, for example: overrides instead of edited files in
`design/standard/` ([Templates and design](../../guides/templates-and-design.md#1-how-a-template-is-found)),
`.ini.append.php` files instead of edits to `settings/*.ini`
([How INI settings work](../extension-development/part-2-builder/how-ini-settings-work.md)), and `wash` on output
([Templates and design](../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)).

## Project checks

Every project page adds a short list of yes or no checks, for example "the class has an object name pattern" or "the
API key is not in the repository". Each check you can answer with yes, with evidence (a screenshot, a command and its
output), counts. Each project says how many checks it has.

## From scores to a grade

| Total of the four criteria (0 to 12) and checks | Grade band |
|---|---|
| 11 to 12, and every check met | Excellent |
| 8 to 10, and at least three quarters of the checks met | Good |
| 6 to 7, and at least half of the checks met | Pass |
| Below 6, or a 0 in "It is safe" | Not yet: fix and hand in again |

A 0 in "It is safe" always means "not yet", whatever the other scores. A site that leaks a password does not pass.

A teacher may use other bands; these are suggestions for self study.

## Self review

1. Wait a day after you finish.
2. Repeat the project from your own report, on a fresh study site
   ([Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md)). Where you get stuck, your
   report is not clear enough.
3. Score each criterion and each check, with a sentence of evidence.
4. Fix the lowest score first, then score again.

## Peer review

Swap projects with another learner:

1. Each reads the other's report and repeats the work on their own study site.
2. Each scores the four criteria with evidence and lists the checks met.
3. Each writes one thing that works well and one thing to improve.
4. Meet for 20 minutes and agree on the scores. Where you cannot agree, ask a third learner.

Never ask a reviewer to sign in to your study site with your password; hand over the files and the report instead.

## Feedback that helps

| Instead of | Write |
|---|---|
| "Good job" | "The override rule matches only the one article, as the goal asked." |
| "The template is wrong" | "Line 4 prints `$node.name` without `wash`." |
| "Bad report" | "Step 3 does not say which siteaccess; I used `site` and saw no change." |

The trainers' lesson [Giving feedback on results](../trainers/assessments/module-01-designing-assessments/giving-feedback-on-results.md)
goes further, and [Building scoring rubrics](../trainers/assessments/module-01-designing-assessments/building-scoring-rubrics.md)
explains how rubrics like these are designed.

## What a grade means

A grade from these projects says that you, a fellow learner or your teacher checked your work against a published
rubric on a named release. It is evidence for your portfolio ([Building a portfolio](building-a-portfolio.md)).

It is **not** an official certification. The Exponential Partner Program names certifications for partner teams,
but their syllabus, exams and examiners are not published, and they are not issued for these projects
([Certifications and certificates](certifications-and-certificates.md)). Do not describe a project grade as
"Exponential certified" or as issued or endorsed by 7x or the Exponential Foundation.

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Certifications and certificates](certifications-and-certificates.md)
- [Student checklists](student-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
