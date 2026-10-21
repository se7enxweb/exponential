---
title: Mentoring in the community
audience: community, advocate, developer, trainer
category: community
order: 43
summary: How experienced community members mentor newcomers through their first Exponential contributions - choosing a task, setting up, the first pull request, review rounds, and handing over - with a four-week plan and boundaries that keep it sustainable.
keywords: mentoring, mentor, mentee, first contribution, pull request, review, guidance, four-week plan, boundaries, community growth
type: help
---
# Mentoring in the community

The project grows when contributors bring in new contributors. Mentoring does not need a program: it is one
experienced member helping one newcomer through their first few contributions.

## Contents

1. [What a mentor does](#what-a-mentor-does)
2. [Choosing the first task](#choosing-the-first-task)
3. [A four-week plan](#a-four-week-plan)
4. [Boundaries](#boundaries)

## What a mentor does

- Helps choose a first task that can be finished.
- Answers "where is ..." questions that the documentation does not.
- Explains the review rounds before they happen, so they do not discourage
  ([Reviewing and being reviewed](../contributing/reviewing-and-being-reviewed.md)).
- Does not do the work for the mentee.

## Choosing the first task

| Good first tasks | Why |
|---|---|
| A help page fix | Small, visible, no code |
| A missing translation string | Small, teaches the workflow |
| A test for a fixed bug | Teaches the test suite |
| A bug the mentee found themselves | They understand it already |

Avoid large features, refactorings and anything that touches security as first tasks.

## A four-week plan

| Week | Mentee | Mentor |
|---|---|---|
| 1 | Sets up a test installation from a Git clone, runs `make hooks`, reads `CONTRIBUTING.md` | Answers setup questions; agrees the first task |
| 2 | Makes the change, commits with a prefixed message, updates the manifest | Reviews it privately before the pull request |
| 3 | Opens the pull request, answers review comments | Explains comments that are unclear; does not answer for the mentee |
| 4 | Squashes, rebases, sees it merged; chooses a second task alone | Suggests the next area; steps back |

The step-by-step guide for the first pull request is
[Mentoring a first contribution](../../advocates/mentoring-a-first-contribution.md).

## Boundaries

- Agree how and when you can be reached; you are a volunteer.
- Keep conversations about the work in public places where possible (the pull request, the forums), so others learn
  too.
- Follow the code of conduct; meeting one to one does not change it.
- If the mentee asks you to review a security finding, send them to the private channel (`SECURITY.md`).

## Related pages

- [Helping others in the community](README.md)
- [Mentoring a first contribution](../../advocates/mentoring-a-first-contribution.md)
- [The maintainer path](../../extension-development/part-5-extend-fork-contribute/the-maintainer-path.md)
- [From contributor to maintainer](../paths/from-contributor-to-maintainer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
