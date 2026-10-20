---
title: GitHub for advocates
audience: advocate
category: advocacy
order: 73
summary: How non-developer and developer advocates use GitHub for Exponential - following releases, reading and commenting on issues, reacting instead of +1, helping triage, documentation pull requests, reviewing, and staying safe with secrets and personal data.
keywords: GitHub, se7enxweb, issues, pull requests, releases, watch, notifications, comments, reactions, triage, review, fork, Edit on GitHub, account
type: help
---
# GitHub for advocates

This page is for advocates who are new to GitHub, and for developers who want to use it as an advocate, not only as
a coder. Exponential lives on GitHub under the account [se7enxweb](https://github.com/se7enxweb): the kernel, the
extensions, Velocity and the community tracker. You need a free GitHub account to open issues and pull requests
([When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential)).

## Contents

1. [The repositories you will use](#the-repositories-you-will-use)
2. [Follow what happens](#follow-what-happens)
3. [Issues: reading, commenting, reacting](#issues-reading-commenting-reacting)
4. [Pull requests](#pull-requests)
5. [Helping without code](#helping-without-code)
6. [Safety on GitHub](#safety-on-github)

## The repositories you will use

| Repository | Use |
|---|---|
| [se7enxweb/exponential](https://github.com/se7enxweb/exponential) | The kernel, the administration, the bundled extensions and the documentation (this help is in `doc/`) |
| [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity) | The Velocity server |
| [se7enxweb/exponential-community](https://github.com/se7enxweb/exponential-community) | The project, website, ecosystem, ideas |
| Each extension's own repository | Its code and issues; the help's **Report an error** page lists them |

## Follow what happens

- **Watch > Custom > Releases** on a repository to be notified of new releases only.
- The [releases page](https://github.com/se7enxweb/exponential/releases) lists published versions.
- The commit list of `main` shows daily work; the [history](../../history/README.md) explains it month by month.
- Do not turn on notifications for everything unless you want hundreds of mails.

## Issues: reading, commenting, reacting

| Do | Don't |
|---|---|
| Search before opening a new issue | Open a duplicate |
| Add **new information** to an existing issue: your version, a reproduction | Comment "+1" or "same here" (use a reaction instead) |
| Use a thumbs-up reaction to show the issue affects you | Ask "any news?" repeatedly |
| Be specific and kind | Demand a fix or a date |
| Link related issues and forum threads | Paste logs with personal data or secrets |

How to write a good issue: [Reporting bugs well](reporting-bugs-well.md). Which repository:
[Which tracker for which report](which-tracker-for-which-report.md).

## Pull requests

- **Documentation**: **Edit on GitHub** on any help page opens the file; GitHub forks the repository for you
  ([Improving the help](improving-the-help.md)).
- **Code**: fork, branch, commit, pull request ([Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)).
- **The CLA**: a code contribution to the kernel (`se7enxweb/exponential`), in any form, is merged only once its
  author's signed Contributor Licensing Agreement has been received
  ([Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla)). Your own extensions are
  not affected.
- **Commit messages** start with `Added:`, `Updated:`, `Fixed:`, `Removed:` or `Renamed:`
  ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
- **Every pull request is merged**, and its commits are kept; what needs fixing is fixed in commits on top
  ([How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows)).

## Helping without code

| Help | How |
|---|---|
| Confirm a bug | Reproduce it on your installation and comment with your environment ([Reproducing and confirming issues](reproducing-and-confirming-issues.md)) |
| Improve an issue | Add steps, a smaller example, the exact message |
| Test a pull request | Try the change on a test installation and report what you saw |
| Review documentation | Read a docs pull request for clarity and names |
| Translate | Pull requests to the translation files ([Helping with translations](helping-with-translations.md)) |
| Welcome a first-time contributor | A friendly comment on their first pull request |

## Safety on GitHub

- Everything on a public repository is public forever, including edited-away text in history.
- Never post passwords, keys, cookies, database addresses or personal data in issues, comments or code.
- Security problems never go into a public issue: use the private path of `SECURITY.md`
  ([Security reports stay private](security-reports-stay-private.md)).
- Turn on two-factor authentication for your GitHub account.

## Related pages

- [Which tracker for which report](which-tracker-for-which-report.md)
- [Mentoring a first contribution](mentoring-a-first-contribution.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
