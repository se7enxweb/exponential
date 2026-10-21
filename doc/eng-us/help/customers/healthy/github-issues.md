---
title: GitHub issues
audience: customer, admin, partner
category: customer-success
order: 19
summary: How a site owner follows and files issues in Exponential's public GitHub trackers - which tracker is for what, how to search, what a good issue holds, what never to post, what to expect, and how a fix reaches your site.
keywords: GitHub, issues, issue tracker, bug report, feature request, se7enxweb, exponential, velocity, community, follow an issue
type: help
---
# GitHub issues

Exponential is developed in the open on GitHub. Bugs and requests are tracked in public issue trackers, which
anyone can read and anyone with a free GitHub account can write to. This page explains how your team uses them: to
check whether a problem is known, to follow a fix, and to report a new bug.

## The trackers

| Tracker | For |
|---|---|
| [Exponential (the software)](https://github.com/se7enxweb/exponential/issues) | Errors in the kernel, the administration, the bundled extensions and the documentation |
| [Exponential Velocity](https://github.com/se7enxweb/exponential-velocity/issues) | Errors of the Velocity application server: starting, workers, its response cache, HTTPS |
| [Exponential project and community](https://github.com/se7enxweb/exponential-community/issues) | The project, the website, the ecosystem, and everything that is not an error in software |
| An extension's own repository | Errors in an extension that has one; the help's report page lists them |

Source: `settings/exphelp.ini [IssueTrackers]` and [When something goes wrong](../../when-something-goes-wrong.md).

## Before filing: search

Search the tracker for words from the error message or the feature. "If the problem is already known, add what you
know to that issue instead of opening a new one" ([When something goes wrong](../../when-something-goes-wrong.md)).
Following an existing issue on GitHub lets you see when it is fixed.

## What a good issue holds

From [When something goes wrong](../../when-something-goes-wrong.md):

- **A short title** that names the problem.
- **What you did**, step by step, so someone else can make it happen again.
- **What you expected**, and **what happened instead**, with the exact message.
- **The environment**: Exponential version, PHP version, web server, database, administration design. The
  [help report page](the-help-report-page.md) and **Setup > System information** (**Copy report as text**) give
  a summary without passwords, keys and server paths.
- **Log lines** around the time, with anything private removed.

The help's report page drafts such an issue for you ([The help report page](the-help-report-page.md)).

Not every surprise is a bug: some behavior is deliberate or open to interpretation, and other sites depend on it. An
issue asking to change such a [negative feature](../../project/getting-support.md#negative-features) needs a clear
justification and may take several rounds of discussion; asking for an option rather than a new default helps.

## Never post

- Passwords, keys, session cookies, database addresses.
- Personal data of customers or users, including in screenshots and log lines.
- A security problem. Those go privately to security@se7enx.com ([Private security reports](private-security-reports.md)).

## What to expect

- **No promised answer time.** Free help "depends on the time people give, so nobody promises an answer time"
  ([When something goes wrong](../../when-something-goes-wrong.md)). If you cannot wait, paid support from 7x is
  the route the project's support page names ([Getting support](../../project/getting-support.md)).
- **A fix reaches you in a release.** Fixes are made on the maintained line and published with the next release; a
  published version is never changed (`SECURITY.md`). Your provider then applies that release
  ([How updates work](how-updates-work.md)).
- **You can help.** A precise reproduction, a test on the development line, or a fix sent as a pull request speeds
  things up; "Mention the issue in the pull request (for example `Fixes #123`) so both are linked"
  ([When something goes wrong](../../when-something-goes-wrong.md), [Develop with Git](../../develop-with-git.md)).

## Who files for your organization

Usually your administrator or partner files the issue, after they confirmed the problem is in the software and not
in your site's setup. Ask them to send you the link, and keep it in your notes for the problem. A partner of the
program can also use partner support tickets ([Filing bugs as a partner](../../partners/filing-bugs-as-a-partner.md)).

## Feature requests

A wish for a new feature is also an issue, in the software tracker or, for anything about the project, the community
tracker. Describe the need, not only the solution. If the feature matters to your business, sponsoring it is a
documented route ([Feature requests and sponsored features](../../partners/feature-requests-and-sponsored-features.md)).

## Related pages

- [Reporting problems](reporting-problems.md)
- [Working with 7x and the project](../partners-and-hosts/working-with-7x-and-the-project.md)
- [Contribute to the project](../../project/contribute-to-the-project.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
