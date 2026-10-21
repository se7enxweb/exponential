---
title: How to study with the help
audience: student
category: learning
order: 2
summary: Study habits that work with the Exponential help - how the help is organized, the read, do, check loop, a study installation of your own, notes and a study journal, reviewing with the trainers' quizzes, and what to do when a page and the software disagree.
keywords: study, study habits, learning, help center, reading, practice, notes, review, quiz, spaced review, how to learn, self study
type: help
---
# How to study with the help

This page is for anyone who learns Exponential on their own or alongside a course: students, apprentices, career
changers and new team members. It explains how the help is organized and gives you a way of studying that turns
reading into skill.

## Contents

1. [How the help is organized](#how-the-help-is-organized)
2. [The read, do, check loop](#the-read-do-check-loop)
3. [Study on an installation of your own](#study-on-an-installation-of-your-own)
4. [Take notes you will use again](#take-notes-you-will-use-again)
5. [Review with quizzes](#review-with-quizzes)
6. [When a page and the software disagree](#when-a-page-and-the-software-disagree)
7. [A weekly rhythm](#a-weekly-rhythm)

## How the help is organized

The help is a set of Markdown pages in the `doc/` folder of the kernel, shown in the administration under **Help**.
Its dashboard lists every page by audience and topic, and its search finds any page
([Getting started](../getting-started.md#getting-help)).

| Kind of page | What it gives you | Example |
|---|---|---|
| Start pages | A map of one audience's pages | [Trainers: start here](../trainers/trainers-start-here.md) |
| Task pages | Steps for one job, with what you should see | [The layouts editor](../layouts-editor.md) |
| "How do I" pages | One short answer per question | [Manual](../manual/README.md) |
| Guides | Longer step-by-step guides for administrators and developers | [Guides](../../guides/README.md) |
| Courses | Lessons with objectives, exercises and quizzes, written for trainers | [Course catalogue](../trainers/course-catalogue.md) |
| Reference | Settings, template language, glossary | [Template language](../template-language/README.md), [Glossary](../../glossary.md) |

The pages of this folder point you to the right ones in the right order. You do not need to read the help from
start to end.

## The read, do, check loop

Most task pages and guides show a command or a click path and then say what you should see ("Expected: ..."). Use
that as your study loop:

1. **Read** one section, not a whole page.
2. **Do** it on your study installation, typing the commands yourself rather than pasting them.
3. **Check** the result against the page. If it matches, write one line in your journal. If it does not, find out
   why before you go on: that is where you learn most.

Example from [the installation guide](../../guides/getting-started.md#2-get-the-files): after
`php bin/php/console --version` the guide expects a first line reading `console (Exponential) 6.0.15stable` or the version
you installed. If you see something else, you already have a question worth asking.

## Study on an installation of your own

Reading about a content management system without one is like reading about swimming. Install a site that is only
yours, where you can break things and start again:

- [Your first site: three ways](your-first-site-three-ways.md) compares the public demo, the one-command install and
  the setup wizard.
- [Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md) shows how to start over.

Never practice on a live site, and never on a site that holds other people's data.

## Take notes you will use again

- Keep a **study journal** ([Keeping a study journal](keeping-a-study-journal.md)): the date, what you did, what you
  expected, what happened, and the page you used.
- Write the **commands that worked** in a personal cheat sheet, with one line saying what each does.
- Collect **words** you did not know and check them in the [Glossary for learners](glossary-for-learners.md).
- Note the **release** you study on (`php bin/php/console --version`). Pages describe a release; your notes should too.

Never write passwords, keys or tokens into notes you share or commit.

## Review with quizzes

The trainers' lessons end with a quiz and its answers ([Trainers: start here](../trainers/trainers-start-here.md)).
You may use them on your own:

1. Do the lesson's hands-on exercise on your study installation.
2. A day later, answer the quiz without looking.
3. Check the answers, which cite the help pages, and reread what you missed.
4. A week later, answer it again.

Short, spaced reviews keep more than one long session. The [Course catalogue](../trainers/course-catalogue.md)
shows which course covers which subject.

## When a page and the software disagree

Pages are written against a release and checked on real installations, but software changes. When a step does not
work:

1. Check your release against the one the page names.
2. Check that you are on the right siteaccess and signed in with the right role; menus show only what your role
   allows ([Getting started](../../guides/getting-started.md#5-first-login)).
3. Read `var/log/error.log` ([Reading logs and errors](reading-logs-and-errors.md)).
4. If the page is wrong, tell the project: every page has **Edit on GitHub**
   ([Improving the help as a student](improving-the-help-as-a-student.md)).

## A weekly rhythm

| Day | Activity | Time |
|---|---|---|
| 1 | Read and do one new section of your learning path | 60 minutes |
| 2 | Repeat it from your notes, without the page | 30 minutes |
| 3 | Work on your current study project | 60 to 90 minutes |
| 4 | Quiz review of last week's lesson | 15 minutes |
| 5 | Read one page outside your path (a guide, a reference page) | 30 minutes |
| Weekend | Optional: answer a question in the community or fix a typo in the help | as you like |

Adjust the times; keep the order. Doing comes before reading more.

## Related pages

- [Choosing a learning path](choosing-a-learning-path.md)
- [Keeping a study journal](keeping-a-study-journal.md)
- [Study projects: overview](study-projects-overview.md)
- [Students and learners: start here](students-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
