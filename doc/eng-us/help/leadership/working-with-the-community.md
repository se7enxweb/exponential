---
title: Working with the community and contributing back
audience: project-manager, developer
category: leadership
order: 120
summary: Where the Exponential community meets, which tracker takes which report, how a fix or a page of documentation goes back upstream, and how a project team can plan and budget for contributing.
keywords: community, contribute, contribution, pull request, issue, tracker, bug report, security, disclosure, forum, upstream, fork, GPL, GFDL, translation, documentation, sponsor, Foundation, open source policy
type: help
---
# Working with the community and contributing back

This page is for project managers and developers whose project uses Exponential and who want the project to work
**with** the community rather than next to it. It answers: where do people meet, where does a bug report go, how
does a fix reach the next release, what may your team contribute, and how do you plan that work into a project.

Exponential is an open source project. It is operated by the Exponential Foundation, a nonprofit, and 7x is its main
contributor ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)). Exponential continues
the code base of eZ Publish, a product its creator discontinued after almost 18 years; the history is on
[Who is 7x?](../who-is-7x.md).

## Contents

1. [Why contributing back pays off for a project](#why-contributing-back-pays-off-for-a-project)
2. [Where the community meets](#where-the-community-meets)
3. [Which tracker takes which report](#which-tracker-takes-which-report)
4. [Security problems: private, never public](#security-problems-private-never-public)
5. [Contributing a fix: the pull request path](#contributing-a-fix-the-pull-request-path)
6. [How the project treats pull requests](#how-the-project-treats-pull-requests)
7. [Contributing documentation and translations](#contributing-documentation-and-translations)
8. [Licensing of what you contribute](#licensing-of-what-you-contribute)
9. [Planning contribution into a project](#planning-contribution-into-a-project)
10. [A team contribution policy to adapt](#a-team-contribution-policy-to-adapt)
11. [Supporting the project financially](#supporting-the-project-financially)

## Why contributing back pays off for a project

The help page [Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request) puts it plainly:
send the fix back "so that it is in the next release and you do not have to carry it yourself". For a project
manager the reasons translate into cost and risk:

| Without contributing | With contributing |
|---|---|
| Every local patch must be re-applied and re-tested at every upgrade | The fix ships in the next release; the upgrade carries it |
| Knowledge of the fix stays in one team | The fix is reviewed and documented in the public history |
| A bug found by your editors may hit you again elsewhere | Every site, including your next project, gets the fix |
| Your team works around the product | Your team helps shape it |

## Where the community meets

From [Community places](../who-is-the-exponential-foundation.md#community-places) and
[Who is 7x?, "The community"](../who-is-7x.md#the-community):

| Place | Use it for |
|---|---|
| [share.exponential.earth](https://share.exponential.earth/) | The community portal: forums, blogs, tutorials and projects (extensions) |
| [The forums](https://share.exponential.earth/forums) | Questions and discussions ("how do I ...?") |
| Telegram | The community support chat; the address is on [Community places](../who-is-the-exponential-foundation.md#community-places) |
| [eZpedia](https://ezpedia.exponential.earth/) | The free encyclopedia of the earlier years, since 2006, under the GNU FDL |
| [Documentation](https://doc.exponential.earth/) | "The Official Documentation For Exponential 6 CMS" |
| [Training books mirror](https://github.com/se7enxweb/mirror.exponential.se7enx.com) | Three original developer training books as PDFs |
| [Community issue tracker](https://github.com/se7enxweb/exponential-community/issues) | The project, community, ecosystem and websites |
| Discord and Slack | Named among the support channels of the project's website |

> **Note**
> The sources used for this help document no events (conferences, meetups). If your organization wants to host
> one, ask on the forums or the community tracker.

Practical advice for a project team:

- **Search first.** The forums, the trackers and this help may already have the answer.
- **Ask in public.** An answer in the forums helps the next person; a private message helps one.
- **Share what you learned.** A tutorial on the portal or a page of this help (see below) returns the time others saved you.

## Which tracker takes which report

From [When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential):

| Tracker | For |
|---|---|
| [Exponential (the software)](https://github.com/se7enxweb/exponential/issues) | Errors in the kernel, the administration, the bundled extensions and the documentation |
| [Exponential Velocity](https://github.com/se7enxweb/exponential-velocity/issues) | The Velocity application server: starting, workers, its response cache, HTTPS |
| The extension's own repository | Errors in one extension that has its own repository |
| [Exponential project and community](https://github.com/se7enxweb/exponential-community/issues) | The project, the website, the ecosystem, and everything that is not an error in software |

The **Report an error** page of the help (`/exphelp/report` in the administration) guides the reporter: it says
which tracker to use, fills in a summary of the installation and opens a new issue with a prepared title and text
([Help system](../../features/6.0/help-system.md)). The page lists the extensions' own trackers too.

### What makes a report useful

From [A good bug description](../when-something-goes-wrong.md#a-good-bug-description):

- a short title that names the problem;
- what you did, step by step;
- what you expected and what happened instead, with the exact message;
- the environment: Exponential version, PHP version, web server, database, administration design
  (**Setup > System information > Copy report as text** gives this summary without passwords, keys or server paths);
- log lines around the time, with anything private removed.

Never put passwords, keys, session cookies, database addresses or the personal data of customers or users into a
public issue.

### A triage routine for your team

1. An editor meets an error and passes the **error reference** (`ERR-...`), the time, the address and the steps to
   the administrator.
2. The administrator finds the request in the logs
   ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)) and decides: our site's
   setup, our own code, or Exponential itself.
3. Our setup or code: fix it in the project. Exponential itself: search the tracker, then report it with the
   **Report an error** page.
4. The project manager links the public issue in the project's own ticket, so that the upgrade that contains the
   fix can close it.

## Security problems: private, never public

A security problem is **never** filed in a public tracker. Report it privately, as
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) says: to the security address named
there, or with GitHub's private vulnerability reporting of the repository
([Security hardening, "Reporting a vulnerability"](../security-hardening.md#reporting-a-vulnerability)).

What SECURITY.md commits to and asks for:

- "You can expect a first answer within one business day."
- You are told whether the report is accepted or declined, and kept informed while a fix is prepared.
- An accepted issue is fixed on `main` and released with the next 6.0.x version.
- Include the version, the PHP version, the engine that serves the site and the database, what an attacker can do,
  and the steps to reproduce. Leave out passwords, secrets and personal data.

Put this into your team's incident procedure, and make sure everyone knows that "found a hole" means "private
report", not "issue on GitHub" or "post in the chat".

## Contributing a fix: the pull request path

The procedure, step by step, is in [Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)
and [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md). In short:

1. **Fork** the repository on GitHub (the kernel or the extension).
2. **Branch** in your clone: `git switch -c fix-<what>`.
3. **Change and test** on your site.
4. **Commit** with a subject that starts with what the change does: `Added:`, `Updated:`, `Fixed:`, `Removed:` or
   `Renamed:`.
5. **Push** to your fork and **open a pull request**; describe what you fixed and how you tested it. Mention the
   issue (`Fixes #123`) so both are linked.

For the kernel, CONTRIBUTING.md adds:

- `make hooks` once in your clone: the commit hook checks syntax, the file manifest and the code style.
- Every commit that adds, changes or removes a file updates its line in `share/filelist.md5` (`make manifest-fix`).
- Administration templates and stylesheets follow [Styling the administration](../../bc/6.0/admin4-styling.md):
  no inline CSS, shared components and tokens.
- Keep a pull request focused; several contributions are several pull requests.

> **Tip**
> Ask your developers to install the site as Git clones from the start ([Develop with Git](../develop-with-git.md)).
> A fix is then "a branch in your fork and a pull request away", and the cost of contributing drops to minutes.

## How the project treats pull requests

From [Contributors and pull requests](../who-is-the-exponential-foundation.md#contributors-and-pull-requests) and
[Continuous integration](../../bc/6.0/ci.md):

- **Every pull request is merged.** While it is open, its author squashes it and rebases it onto `main` as the
  reviews ask; once it is merged, `main`'s history is never rewritten, and later fixes come as new pull requests.
- **A contributor's commit message is never a blocker.** A subject outside the format is a warning, not a failure.
- **The checks run on the merge with `main`.** A branch that was green can turn red when `main` moves; merge `main`
  into the branch and push again.
- **A first-time contributor's first pull request waits for a maintainer's approval** before any workflow runs.
- **Work of others is credited.** The project rebuilt the LoveStack improvements of Mugo Web "with full credit to
  Mugo Web", and Exponential Layouts ports the Netgen Layouts concept (README, quoted on the Foundation page).

For a project plan this means: a contribution does not depend on winning an argument. Plan the time to write and
test it well; the merge itself is not the uncertain part.

## Contributing documentation and translations

**Documentation.** Every help page has an *Edit on GitHub* link to its source file in `doc/`. Correct it and open a
pull request the same way as for code. The pages are free documentation under the GNU Free Documentation License 1.3
or any later version, and contributions are accepted under the same license
([Free documentation](../free-documentation.md)).

Good first documentation contributions for a project team:

- the page you wished had existed when your project started;
- a correction where a page did not match what you saw;
- a worked example from your project, with the customer's details removed.

**Translations.** The administration has many translations (the current totals are at the top of the languages page), and 268 locale files exist
([Languages](../../languages.md)). How to add or complete an administration translation is in
[section 8 of Languages](../../languages.md#8-adding-or-completing-an-administration-translation). A team that
builds a site in a language whose administration translation is partial can complete it for everyone.

## Licensing of what you contribute

- **Code** is GPL v2 or later. "By contributing, you agree that your contributions will be licensed under the GNU
  General Public License, version 2.0 or later" (CONTRIBUTING.md).
- **Documentation** is GNU FDL 1.3 or later ([Free documentation](../free-documentation.md)).

Check before your team contributes:

- Does your employment or customer contract allow staff to publish code they wrote on the project? Ask your legal
  contact; this page is not legal advice.
- Is the code free of customer-specific secrets, names and data?
- Is the code yours to give? Code copied from elsewhere keeps its own license.

More on licenses for buyers: [Licensing explained for buyers](../business/licensing-for-buyers.md).

## Planning contribution into a project

| Phase | Contribution task | Owner |
|---|---|---|
| Planning | Decide the contribution policy (below); put "upstream fixes" into the estimate | Project manager |
| Build | Report bugs found in Exponential; send fixes as pull requests rather than local patches | Developers |
| Content migration | Report gaps in the documentation you hit | Whoever hit them |
| Acceptance | Link every upstream issue to your own defect list | Project manager |
| Launch | Write down which of your fixes are upstream and which are still local | Lead developer |
| Operate | Re-check the local patches at each upgrade; drop those that are now released | Developer |

A simple measure for the handover document: **number of local patches still carried**. Lower is better; every patch
that went upstream is one less to re-apply at the next upgrade.

## A team contribution policy to adapt

```text
Contribution policy: <organization name>, version <date>

1. Scope. Staff may contribute fixes, documentation and translations to Exponential and its extensions
   when the work was done on <projects / time allowed>.
2. Approval. <role> approves the first contribution of each person; after that, no approval is needed for
   fixes and documentation. New features need <role>'s approval.
3. Content. Never contribute customer names, data, secrets, host names or credentials. Remove them from
   code, logs and screenshots.
4. Licenses. Code goes out under GPL v2 or later, documentation under GNU FDL 1.3 or later, as the project
   requires. Only contribute work the organization owns.
5. Security. Security problems are reported privately as SECURITY.md describes, never in public.
6. Accounts. Contributions are made from personal GitHub accounts linked to <organization> / from the
   organization account <name>.
7. Time. <n> hours per sprint / per month are reserved for upstream work.
8. Record. Every upstream issue and pull request is linked from the internal ticket that caused it.
```

## Supporting the project financially

The Foundation and 7x each have a sponsor page, and the Foundation also takes subscriptions and one-time donations
through its website. The addresses are on [How to support or join](../who-is-the-exponential-foundation.md#how-to-support-or-join).
The Foundation states that donations are tax deductible in the United States; check with your own tax adviser.

## Related pages

- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) and [Who is 7x?](../who-is-7x.md)
- [When something goes wrong](../when-something-goes-wrong.md), [Develop with Git](../develop-with-git.md)
- [Running a team on Git](running-a-team-on-git.md), [Becoming a leader in the Exponential community](becoming-a-community-leader.md)
- [Growing the community](../business/growing-the-community.md), [The community and ecosystem as a selling point](../business/community-and-ecosystem.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
