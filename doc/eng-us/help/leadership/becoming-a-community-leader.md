---
title: Becoming a leader in the Exponential community
audience: project-manager, sales-marketing, editor, admin, developer
category: leadership
order: 210
summary: A 30/60/90-day plan that uses only this help and the project's documented channels, a contribution ladder from user to reviewer, and what each step looks like for project managers, sales, editors, administrators and developers.
keywords: leadership, community, contribute, contributor, maintainer, reviewer, 30 60 90, plan, ladder, forums, Telegram, issue tracker, pull request, translation, documentation, sponsor, Exponential Foundation, 7x
type: help
---
# Becoming a leader in the Exponential community

This page is for anyone who uses Exponential and wants to do more than use it: to help other users, improve the
product and its documentation, and be someone others in the community turn to. It does not matter whether you are a
project manager, in sales or marketing, an editor, an administrator or a developer. Every role has a way up.

It answers three questions: what leadership in this community looks like, which documented places you use to take
part, and what to do in your first 30, 60 and 90 days, with nothing but this help as your guide.

## Contents

1. [What leadership means here](#what-leadership-means-here)
2. [The community places](#the-community-places)
3. [The contribution ladder](#the-contribution-ladder)
4. [The ladder for each role](#the-ladder-for-each-role)
5. [The 30/60/90-day plan](#the-306090-day-plan)
6. [Habits of a good community leader](#habits-of-a-good-community-leader)
7. [What the sources do not say](#what-the-sources-do-not-say)
8. [A personal tracking sheet](#a-personal-tracking-sheet)

## What leadership means here

Exponential is open source software with a long history. Its code base goes back to 1999 ("Copyright (C) 1999 - 2014
eZ Systems AS" in `COPYRIGHT.md`), and Exponential continues the code base of eZ Publish, a product its creator
discontinued after almost 18 years ([Who is 7x?](../who-is-7x.md#the-ez-publish-years-1999-to-2021)). Since December
2023 the company **7x** leads the project, and since March 2026 the nonprofit **Exponential Foundation** operates it
([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

A project like this lives from people who:

- **answer questions** so others do not stay stuck;
- **report problems well**, so they can be fixed for everyone;
- **write things down**, because the documentation is free and anyone may improve it
  ([Free documentation](../free-documentation.md));
- **send changes back** instead of carrying private fixes;
- **bring others in**: new users, new contributors, new sponsors.

The project states its own attitude: "The Exponential Project and the Exponential Foundation support the open source
community directly, as part of our daily routine and work ethic, and put the support needs of community users first"
(`README.md`, "An open community"). A community leader acts in the same spirit.

## The community places

Use only the places the project documents. They are listed on
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places) and
[Who is 7x?](../who-is-7x.md#the-community).

| Place | Use it for | Source |
|---|---|---|
| [share.exponential.earth](https://share.exponential.earth/) and its [forums](https://share.exponential.earth/forums) | Questions, answers, blogs, tutorials, projects (extensions) | README, "Where to get more help" |
| [Telegram community chat](https://t.me/exponentialcms) | Quick questions and conversation | README |
| [Exponential issue tracker](https://github.com/se7enxweb/exponential/issues) | Bugs and improvements of the software | [When something goes wrong](../when-something-goes-wrong.md) |
| [Exponential Velocity issue tracker](https://github.com/se7enxweb/exponential-velocity/issues) | Bugs of the Velocity server | same |
| [Community issue tracker](https://github.com/se7enxweb/exponential-community/issues) | "almost anything Exponential Project / Community / Ecosystem / Website Software related" | README |
| [eZpedia](https://ezpedia.exponential.earth/) | "The free eZ Publish encyclopedia that anyone can edit", under the GNU FDL | [Who is 7x?](../who-is-7x.md#the-community) |
| [Documentation site](https://doc.exponential.earth/) | "The Official Documentation For Exponential 6 CMS" | same |
| The repositories under [github.com/se7enxweb](https://github.com/se7enxweb) | Code, pull requests, extension issues | [Develop with Git](../develop-with-git.md) |
| [Sponsor the Foundation](https://github.com/sponsors/exponentialcms), [sponsor 7x](https://github.com/sponsors/se7enxweb) | Funding the work | [How to support or join](../who-is-the-exponential-foundation.md#how-to-support-or-join) |

Discord and Slack are named among the support channels on the project website; the sources used here give no
address for them, so this page does not link them.

> **Warning**
> Security problems never go into a public place. Report them privately as `SECURITY.md` describes (an e-mail
> address of the project, or GitHub's private vulnerability reporting). "You can expect a first answer within one
> business day" (`SECURITY.md`).

## The contribution ladder

Each step builds on the one before. You can stay on any step as long as you like; every step is valuable.

| Step | What you do | What you need | Where it shows | Help page |
|---|---|---|---|---|
| **1. User** | Use Exponential, read the help, find your way | An account on an installation | Your own work | [Getting started](../getting-started.md) |
| **2. Reporter** | Report errors well; ask and answer questions | A free GitHub account for issues; an account on the forums | Issues and forum threads with your name | [When something goes wrong](../when-something-goes-wrong.md) |
| **3. Documenter** | Correct, extend and translate the help; write tutorials | GitHub, or the "Edit on GitHub" link of each help page | Merged documentation changes | [Free documentation](../free-documentation.md) |
| **4. Contributor** | Send fixes and features as pull requests to the kernel or an extension | Git clones, a fork, `CONTRIBUTING.md` | Merged pull requests | [Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request) |
| **5. Reviewer, maintainer** | Review others' pull requests, look after an extension, guide newcomers | Experience on steps 2 to 4 | Comments on pull requests, an extension you care for | [Working with the community](working-with-the-community.md) |

### Step 2 in detail: a reporter others thank

- Search the tracker first; add to an existing issue instead of opening a duplicate
  ([When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential)).
- Use the help's **Report an error** page (`/exphelp/report`): it chooses the tracker, prepares the issue and fills
  in a summary of the installation without passwords, keys or server paths.
- Write a short title, the steps, what you expected and what happened, and the environment
  ([A good bug description](../when-something-goes-wrong.md#a-good-bug-description)).
- On the forums, answer with a link to the help page that answers the question. That teaches people where the
  answers live.

### Step 3 in detail: a documenter

- Every help page has an **Edit on GitHub** link to its source file under `doc/`
  ([The help system](../../features/6.0/help-system.md)).
- A documentation change is accepted under the GNU Free Documentation License 1.3 or any later version, so it stays
  free for everyone ([Free documentation](../free-documentation.md#help-others)).
- Translations count: the administration has many translations (the current totals are at the top of the languages page), and content can be written in
  368 locales ([Languages in Exponential](../../languages.md)). Completing a translation is a contribution any
  bilingual editor can make.
- Good first documentation tasks: a page that confused you, a missing example, a screenshot-free step-by-step for a
  task you just did.

### Step 4 in detail: a contributor

- Install the kernel and extensions as Git clones: "The recommended way is to get the kernel and every se7enxweb
  extension as Git clones with their full history" (`README.md`, "Installation"; [Develop with Git](../develop-with-git.md)).
- Fork, branch, commit, push, open a pull request ([Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)).
- Follow `CONTRIBUTING.md`: commit subjects start with `Added:`, `Updated:`, `Fixed:`, `Removed:` or `Renamed:`;
  one focused change per pull request; update the documentation; keep the file manifest current (`make manifest-fix`;
  `make hooks` installs the checks).
- Know the license: "any modifications made to the project are also released under the same GPLv2 or later
  license" (`CONTRIBUTING.md`).
- The project's practice is generous to contributors: every pull request is merged, once its author has squashed it
  and rebased it onto `main` as the reviews ask; after the merge, `main` is never rewritten
  ([Continuous integration](../../bc/6.0/ci.md)).

### Step 5 in detail: reviewer and maintainer

- "All contributions will be reviewed before merging" (`CONTRIBUTING.md`). Anyone can review in public: read a pull
  request, try it on your installation, and comment with what you found.
- Learn the checks a pull request must pass: the **Quality** and **PHPUnit** workflows block, **Performance** warns,
  **CI health** reports ([Continuous integration](../../bc/6.0/ci.md)).
- Learn the release rules before you look after an extension: published versions never change, the last position
  of a version counts on (`0.1.9` is followed by `0.1.10`), and an extension states its version in `ezinfo.php` and
  `extension.xml` ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).

## The ladder for each role

| Role | Step 2 (reporter) | Step 3 (documenter) | Step 4 (contributor) | Step 5 (leader) |
|---|---|---|---|---|
| **Project manager** | Collect the team's bugs and file them well | Write up a project method that worked, as a tutorial or help page | Sponsor a fix: let your developers send it back instead of keeping it | Run a monthly "community hour" for your team; mentor other project managers on the forums |
| **Sales and marketing** | Report where the public story and the product disagree | Improve the business pages of this help | Write case studies with customers' permission ([Writing case studies](../business/case-studies-and-content-marketing.md)) | Grow the community ([Growing the community](../business/growing-the-community.md)) |
| **Editor** | Report confusing screens with steps | Fix the editor help pages; complete a translation | Test pull requests that touch editing and say what you found | Answer editors' questions on the forums |
| **Administrator** | Report with the error reference and the system information report | Write down an operating procedure that worked | Send settings and script fixes | Review security and operations changes; share hardening experience ([Security hardening](../security-hardening.md)) |
| **Developer** | Report with a reproduction and log lines | Document the extension points you used | Pull requests to the kernel and extensions | Review pull requests; look after an extension |

## The 30/60/90-day plan

The plan uses only this help and the documented places. Times are what a person with a regular job can spare: a few
hours a week.

### Days 1 to 30: learn and listen

| Week | Do | Read | Done when |
|---|---|---|---|
| 1 | Learn who makes Exponential and what the product family is | [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md), [What Exponential is](what-exponential-is.md) | You can explain 7x, the Foundation and the product family in three sentences |
| 2 | Walk the administration on an installation you control | [Getting started](../getting-started.md), [A tour of the administration](../admin-tour.md), [The layouts editor](../layouts-editor.md) | You have edited, previewed and published one item and one layout |
| 3 | Join the places: forums, Telegram; watch the issue trackers | [The community places](#the-community-places) | You have read a week of questions without answering yet |
| 4 | Read the rules of contribution and the licenses | `CONTRIBUTING.md`, [Free documentation](../free-documentation.md), [Develop with Git](../develop-with-git.md) | You know what GPL v2 or later and GFDL 1.3 mean for your contribution |

### Days 31 to 60: contribute

| Week | Do | Done when |
|---|---|---|
| 5 | Answer three forum questions, each with a link to a help page | Three answers |
| 6 | File one good bug report, or improve an existing issue with a reproduction | The issue has all the fields of [A good bug description](../when-something-goes-wrong.md#a-good-bug-description) |
| 7 | Make one documentation change through "Edit on GitHub" | A pull request is open |
| 8 | Developers: one code fix as a pull request; others: a translation or a tutorial | A second contribution is open or merged |

### Days 61 to 90: lead

| Week | Do | Done when |
|---|---|---|
| 9 | Pick one area you care for: an extension, a help section, a language, a forum category | You have said so publicly, in the community issue tracker or the forums |
| 10 | Review or test two pull requests of others in your area | Two review comments |
| 11 | Bring in one new person: a colleague, a customer's editor, a translator | They have made their first contribution (step 2 or 3) |
| 12 | Write a short report of your 90 days for your team and the community | Posted on the forums or as a blog on share.exponential.earth |
| 13 | Plan your next quarter with the tracking sheet below | The sheet is filled in |

> **Tip**
> If you lead a team, run this plan for the whole team at once. The [leadership reading path](leadership-reading-path.md)
> gives each role its reading order week by week.

## Habits of a good community leader

- **Point to the source.** Answer with a link to the help, the README or a changelog. If no source says it, say so,
  as the help pages themselves do ([What the sources do not say](../who-is-the-exponential-foundation.md#what-the-sources-do-not-say)).
- **Name things right.** The product is **Exponential**, the company **7x**, the nonprofit the
  **Exponential Foundation**. "eZ Publish" is the product's past name, used for history only
  ([Brand and naming rules](../business/brand-and-naming-rules.md)).
- **Protect people's data.** No passwords, keys, server addresses or personal data of customers or users in a
  public issue or post ([When something goes wrong](../when-something-goes-wrong.md)).
- **Give credit.** The project credits the work it builds on, such as Netgen and Mugo Web
  ([Who is 7x?](../who-is-7x.md#contributors-and-friends-of-the-project)). Do the same for the people who help you.
- **Send it back.** A fix you keep is a fix you maintain alone. A fix you send back "reaches every site in the next
  release" ([When something goes wrong](../when-something-goes-wrong.md#even-better-send-the-fix)).
- **Be patient with newcomers.** You were one on day 1.

## What the sources do not say

To stay honest, this page does not invent rules the project has not written down:

- The sources do not describe a formal process to become a maintainer, a committer with write access, or a member
  of the Foundation's board. Ask in the [community issue tracker](https://github.com/se7enxweb/exponential-community/issues)
  if you want to take on such a role.
- The sources name no conferences or meetups ("No events (conferences, meetups) are documented", [Who is the
  Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places)).
- The project's code of conduct is `CODE_OF_CONDUCT.md`
  ([The Exponential code of conduct](../project/code-of-conduct.md)); leaders apply it in the spaces they run, and
  reports go to <community@se7enx.com>, where the Exponential Foundation decides.

## A personal tracking sheet

Copy it into your own notes and fill it in.

```text
Name or handle:       <your public handle>
Role on my team:      <project manager | sales and marketing | editor | administrator | developer>
Start date:           <yyyy-mm-dd>

Ladder step now:      <1 user | 2 reporter | 3 documenter | 4 contributor | 5 reviewer/maintainer>
Area I care for:      <extension, help section, language, forum category>

Days 1-30
  [ ] Read Who is 7x? and Who is the Exponential Foundation?
  [ ] Toured the administration; published one item and one layout
  [ ] Joined the forums and the Telegram chat
  [ ] Read CONTRIBUTING.md and Free documentation

Days 31-60
  [ ] 3 forum answers with help links:   <links>
  [ ] 1 bug report or improved issue:    <link>
  [ ] 1 documentation pull request:      <link>
  [ ] 1 more contribution:               <link>

Days 61-90
  [ ] Area announced:                    <link>
  [ ] 2 reviews or tests of pull requests: <links>
  [ ] 1 newcomer brought in:             <their first contribution>
  [ ] 90-day report posted:              <link>

Next quarter goals:
  1. <goal>
  2. <goal>
  3. <goal>
```

## Related pages

- [Working with the community and contributing back](working-with-the-community.md)
- [Growing the community](../business/growing-the-community.md)
- [Working together](working-together.md)
- [The leadership reading path](leadership-reading-path.md)
- [Develop with Git](../develop-with-git.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
