---
title: Bugs you find while learning
audience: student
category: learning
order: 41
summary: How a learner tells a bug from a misunderstanding, chooses the right tracker (the software, Velocity, an extension, or the community issue tracker), writes an issue others can reproduce with the Report an error page, keeps security problems private, and follows the issue through.
keywords: bug, issue, issue tracker, community issue tracker, exponential-community, Report an error, reproduce, security, SECURITY.md, GitHub, learner
type: help
---
# Bugs you find while learning

While you learn, you will meet things that do not work. Some are your own mistakes, some are the software's. A clear
issue about a real bug helps every user after you, and it is a contribution you can show. This page tells you how.

## Contents

1. [Bug or misunderstanding?](#bug-or-misunderstanding)
2. [Choose the right tracker](#choose-the-right-tracker)
3. [Write an issue others can reproduce](#write-an-issue-others-can-reproduce)
4. [Security problems stay private](#security-problems-stay-private)
5. [After you file it](#after-you-file-it)

## Bug or misunderstanding?

Before you file an issue, rule out the usual causes on a study site:

| Check | Page |
|---|---|
| Is it a permission page, not an error? | [Reading logs and errors](reading-logs-and-errors.md#is-it-an-error-at-all) |
| Is it a cache? | [Understanding caches](understanding-caches.md#cache-problem-or-real-error) |
| Is the setting in the right file and siteaccess? | [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md#common-mistakes) |
| Does it happen on a fresh installation? | [Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md) |
| Is it already known? | Search the tracker first |

If it still happens on a fresh installation with the documented steps, it is probably a bug. If a help page tells
you something the software does not do, that is a bug of the documentation: file it too, or fix it
([Improving the help as a student](improving-the-help-as-a-student.md)).

## Choose the right tracker

From [When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential) and
[Which tracker for which report](../advocates/which-tracker-for-which-report.md#the-decision-table):

| It is about | Where |
|---|---|
| The kernel, the administration, a bundled extension or the documentation | [Exponential issues](https://github.com/se7enxweb/exponential/issues) |
| The Velocity server: starting, workers, its response cache, HTTPS | [Velocity issues](https://github.com/se7enxweb/exponential-velocity/issues) |
| An extension with its own repository | That repository's issues |
| The project, the website, the ecosystem, anything that is not a software error | The [community issue tracker](https://github.com/se7enxweb/exponential-community/issues) |
| A "how do I ...?" question | The forums or Telegram ([Asking for help and study groups](asking-for-help-and-study-groups.md)) |
| A security problem | **Privately**, see below |

You need a free GitHub account to open an issue.

## Write an issue others can reproduce

The **Report an error** page of the help (`exphelp/report` in the administration) chooses the tracker, fills in a
summary of the installation and opens a new issue with a prepared title and text
([When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential)). On the command line,
`php bin/php/console exp:info --plain --no-logo` gives a summary that never prints a password, key, token or database
address.

A good issue has ([A good bug description](../when-something-goes-wrong.md#a-good-bug-description)):

```text
Title:     <short, names the problem: "Upload page: ZIP file with a folder fails with temp_dir">

What I did:
  1. <step>
  2. <step>
What I expected:  <...>
What happened:    <..., with the exact message>
Environment:      <Exponential version, PHP version, web server, database, administration design>
Fresh install?    <yes / no>
```

Screenshots help if they show no passwords and no personal data. Say that you are a learner, so people know how much
detail to give in answers.

## Security problems stay private

If you find a way to see or change what you should not, it is a security problem. It never goes into a public issue,
a forum or a chat. Follow `SECURITY.md` in the root of the kernel ([Security policy](../../../../SECURITY.md)); the
advocates' decision table names the address security@se7enx.com and GitHub's private vulnerability reporting
([Which tracker for which report](../advocates/which-tracker-for-which-report.md#the-decision-table)).

## After you file it

- Watch the issue and answer questions quickly.
- If someone asks you to test a fix, test it on your study site and say what you saw.
- If you find the cause yourself, you may send the fix ([Your first contribution, step by step](your-first-contribution-step-by-step.md)).
- Note the issue in your [study journal](keeping-a-study-journal.md) and, once resolved, in your portfolio.

## Related pages

- [When something goes wrong](../when-something-goes-wrong.md)
- [Testing and reporting bugs](../supporters/testing-and-reporting-bugs.md)
- [Contributing as a student](contributing-as-a-student.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
