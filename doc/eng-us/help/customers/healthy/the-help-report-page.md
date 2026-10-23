---
title: The help report page
audience: customer, admin, editor
category: customer-success
order: 18
summary: The Report an error page of the help in the Exponential administration - who can open it, what it prepares (the right tracker, a drafted issue, safe diagnostics, the private path for security problems), and how a site owner's team uses it.
keywords: report an error, exphelp/report, help center, issue, diagnostics, tracker, GitHub, prefilled issue, security
type: help
---
# The help report page

Every Exponential administration has a help center, and in it a page called **Report an error** at
`/exphelp/report`. It guides a person through reporting a bug in the software: which tracker to use, what to write, a
prefilled issue and a summary of the installation to copy, and where to report a security issue privately
([The help system](../../../features/6.0/help-system.md)). This page explains what it is for, from the owner's side.

## Who can open it

The report page is part of the help, which needs the policy `exphelp/read`. The Administrator role has it, a new
installation gives it to the Editor role, and the help is never shown to visitors who are not signed in
([The help system](../../../features/6.0/help-system.md)). On an installation from before the help system, an
administrator grants it with one command described on the same page.

The help dashboard is at `/exphelp/dashboard`; the **Help** button in the header of the administration opens the
help page that belongs to the page you are on.

## What the page prepares

| Part | What it does |
|---|---|
| Before you report | Points to the help search, the logs and the error digest, and to trying once more: the answer may already be documented |
| Where to report | The trackers configured in `settings/exphelp.ini [IssueTrackers]`: Exponential (the software), Exponential Velocity (the application server), and the project and community; plus an extension's own repository where one exists |
| Write the report | Fields for a title, what happened, the steps to reproduce and what you expected |
| Diagnostics | A summary of the installation (versions, PHP, engine, design) to copy; "never passwords, keys, database hosts or personal data" |
| Security issues | The private path: e-mail to security@se7enx.com, or GitHub's private vulnerability reporting |

When the report is written, the page prepares a new issue in the chosen tracker with that text, ready to open. You
need a free GitHub account to submit it ([When something goes wrong](../../when-something-goes-wrong.md)). The
diagnostics and the issue text stay in English on purpose, "because the maintainers read them"
([The help system](../../../features/6.0/help-system.md)).

## Which tracker

From `settings/exphelp.ini`:

| Tracker | For |
|---|---|
| Exponential (the software) | "Errors in the kernel, the administration, the bundled extensions and the documentation" |
| Exponential Velocity | "Errors of the Velocity server itself: starting, workers, its response cache, HTTPS, the exp:velocity command" |
| Exponential project and community | "The project, the website, the ecosystem and anything that is not an error in a piece of software" |

Extensions shipped inside the kernel's repository (expimageeditor, expservices, ezformtoken, ezjscore, ezoe) go to the software
tracker; other active extensions from the project's GitHub organization are offered with a tracker of their own
(`[IssueTrackers] BundledExtensions[]` and `ExtensionOwners[]`).

## How your team uses it

- **Not for every problem.** Most problems are in the site's own setup, templates or hosting, and go to your
  administrator, partner or host ([Reporting problems](reporting-problems.md)). The report page is for a bug in the
  software itself.
- **Usually your administrator or partner files it**, after they confirmed it is not local. Editors can still use the
  page to see what information a good report needs.
- **Search first.** If the problem is already known, add what you know to that issue instead of opening a new one.
- **Ask whether it is deliberate.** Behavior that looks wrong to you may be a
  [negative feature](../../project/getting-support.md#negative-features) other sites depend on; changing it needs a
  clear justification and can take several rounds of review.
- **Check the text before submitting.** The diagnostics leave out secrets, but your own description might not: remove
  customer names, e-mail addresses and anything private.

> **Warning**
> A security problem never goes into a public issue. The page has its own section for it; see
> [Private security reports](private-security-reports.md).

## Related pages

- [GitHub issues](github-issues.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [System information](../../../guides/system-information.md): its **Copy report as text** gives a similar safe summary
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
