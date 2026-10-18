---
title: Your first week as an advocate
audience: advocate
category: advocacy
order: 4
summary: A day-by-day plan for the first week of advocacy, with one small, useful task per day, the page that explains it and what you have at the end of the week.
keywords: first week, onboarding, advocate, plan, day by day, getting started, forum, Telegram, help page, demo, talking points
type: help
---
# Your first week as an advocate

This page is for people who just decided to become an Exponential advocate. It gives one task per day for seven
days, about 30 to 60 minutes each. At the end of the week you know the facts, the names and the channels, and you have
already helped at least two people.

## Contents

1. [Before you start](#before-you-start)
2. [Day 1: learn who and what](#day-1-learn-who-and-what)
3. [Day 2: try the product](#day-2-try-the-product)
4. [Day 3: learn the talking points](#day-3-learn-the-talking-points)
5. [Day 4: visit the channels](#day-4-visit-the-channels)
6. [Day 5: answer a question](#day-5-answer-a-question)
7. [Day 6: improve one page](#day-6-improve-one-page)
8. [Day 7: plan the next 90 days](#day-7-plan-the-next-90-days)
9. [Your week on one card](#your-week-on-one-card)

## Before you start

- A free GitHub account (for issues, pull requests and **Edit on GitHub**).
- An account on [Share](https://share.exponential.earth/) for the forums.
- Optional: Telegram, for the [community chat](https://t.me/exponentialcms).
- A notebook or a text file for your [contribution log](tracking-your-contributions.md).

## Day 1: learn who and what

Read, in this order:

1. [Who is the Exponential Foundation?, In short](../who-is-the-exponential-foundation.md#in-short)
2. [Who is 7x?, In short](../who-is-7x.md#in-short)
3. [Knowing the product family](knowing-the-product-family.md)
4. [The story of Exponential in two minutes](the-story-of-exponential-in-two-minutes.md)

**Done when** you can explain, without notes, what Exponential is, who 7x is and what the Foundation does.

## Day 2: try the product

Install it on your own machine. One command installs a complete site on SQLite, no database server needed
([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)):

```bash
./console exp:install --dry-run    # checks, installs nothing
./console exp:install              # installs the site on SQLite
```

Then follow [Getting started](../getting-started.md) and the [tour of the administration](../admin-tour.md). Open
the help with the "?" in the header.

> **Warning**
> The installer prints a generated administrator password and writes it to `var/log/initial-admin-password`.
> Change it after the first sign-in and delete that file
> ([The default administrator password](../default-administrator-password.md)). Never post it anywhere.

**Done when** you have edited and published one item and opened one layout in the layouts editor.

## Day 3: learn the talking points

1. Read [Talking points: the core set](talking-points-core-set.md) and say each point out loud once.
2. Read [Claims you must not make](claims-you-must-not-make.md).
3. Read the [Naming rules quick card](naming-rules-quick-card.md).
4. Pick the audience page closest to you: [editors](talking-points-for-editors.md),
   [developers](talking-points-for-developers.md), [administrators](talking-points-for-administrators.md) or
   [decision makers](talking-points-for-decision-makers.md).

**Done when** you can give the two-minute story and three talking points, each with its source.

## Day 4: visit the channels

| Channel | Do this |
|---|---|
| [Forums](https://share.exponential.earth/forums) | Read ten recent threads; note the questions you could answer |
| [Telegram](https://t.me/exponentialcms) | Join, read, say hello briefly |
| [Issues of Exponential](https://github.com/se7enxweb/exponential/issues) | Read five open issues; see how good reports look |
| [Community issues](https://github.com/se7enxweb/exponential-community/issues) | See what the project discusses beyond the code |
| [eZpedia](https://ezpedia.exponential.earth/) and [Projects](https://projects.exponential.earth/) | Browse; note one article or project to look at later |

Details: [The community channels](community-channels.md).

## Day 5: answer a question

Pick one open question from day 4 you can answer. Follow [Answering questions well](answering-questions-well.md):
restate the question, answer briefly, link the help page, say what version you checked it on.

**Done when** one person has an answer from you, with a source.

## Day 6: improve one page

Find one thing in the help that confused you this week: a typo, a missing step, an outdated sentence. Fix it with
**Edit on GitHub** and open a pull request ([Improving the help](improving-the-help.md)). Every pull request is merged
([How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows)).

## Day 7: plan the next 90 days

1. Decide which kinds of advocacy fit your time ([What an advocate is](what-an-advocate-is.md#kinds-of-advocacy)).
2. Copy the template of [A 90-day advocate plan](a-90-day-advocate-plan.md) and fill it in.
3. Start your [contribution log](tracking-your-contributions.md) with what you did this week.

## Your week on one card

```text
MY FIRST WEEK AS AN EXPONENTIAL ADVOCATE                    Start date: <date>

Day 1  Who and what          [ ] Foundation  [ ] 7x  [ ] product family  [ ] two-minute story
Day 2  Try it                [ ] installed   [ ] edited and published  [ ] opened a layout
Day 3  Talking points        [ ] core set    [ ] claims not to make     [ ] naming card
Day 4  Channels              [ ] forums  [ ] Telegram  [ ] issues  [ ] eZpedia  [ ] Projects
Day 5  One answer            Link: <forum or chat link>
Day 6  One page improved     Pull request: <link>
Day 7  90-day plan           [ ] written   [ ] contribution log started
```

## Related pages

- [Advocates: start here](advocates-start-here.md)
- [A 90-day advocate plan](a-90-day-advocate-plan.md)
- [Advocate checklists](advocate-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
