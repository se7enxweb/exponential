---
title: Filing a bug as a community member
audience: community, user, admin, editor, developer
category: community
order: 23
summary: How to file a bug in Exponential so it gets fixed - choosing the right tracker, searching first, the parts of a good bug description, a safe summary of the installation, and what happens after you file it.
keywords: bug, issue, tracker, GitHub issues, Report an error, exphelp/report, system information, exp:info, reproduce, Velocity issues, community issues
type: help
---
# Filing a bug as a community member

A good bug description is one of the most valuable contributions there is: it turns one person's problem into a fix
for everyone. This page is the community-member view; the full instructions are in
[When something goes wrong](../../when-something-goes-wrong.md).

## Contents

1. [Is it a bug?](#is-it-a-bug)
2. [The right tracker](#the-right-tracker)
3. [The parts of a good bug description](#the-parts-of-a-good-bug-description)
4. [What never goes into an issue](#what-never-goes-into-an-issue)
5. [After you file it](#after-you-file-it)

## Is it a bug?

| It is | Then |
|---|---|
| The software does something wrong, and you can make it happen again | A bug: file it |
| You do not know how to do something | A question: ask in the forums ([Asking a good question](../joining/asking-a-good-question.md)) |
| Someone could see or change what they should not | A security problem: privately to security@se7enx.com (`SECURITY.md`) |
| An idea for the project, the website or the community | The community issue tracker |
| It works as designed, but not the way you want | Maybe a **negative feature**: a deliberate behavior, not a bug. Read [Negative features](../../project/getting-support.md#negative-features) before you file it |

> **Warning:** some behavior that looks wrong is on purpose, or open to interpretation. Changing such a negative feature can
> affect every site that depends on it, so an issue or pull request about it needs a clear justification and may go
> through several rounds of review before it is accepted. See [Negative features](../../project/getting-support.md#negative-features).

## The right tracker

| Tracker | For |
|---|---|
| [se7enxweb/exponential](https://github.com/se7enxweb/exponential/issues) | The kernel, the administration, the bundled extensions, the documentation |
| [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity/issues) | The Velocity application server |
| The extension's own repository | An extension with its own repository; the **Report an error** page lists them |
| [se7enxweb/exponential-community](https://github.com/se7enxweb/exponential-community/issues) | The project, website, ecosystem, anything that is not a software bug |

Source: [When something goes wrong](../../when-something-goes-wrong.md). More detail on choosing:
[Which tracker for which report](../../advocates/which-tracker-for-which-report.md).

## The parts of a good bug description

- **A title that names the problem**, not "It does not work".
- **The steps**, numbered, from a known starting point.
- **Expected and actual result**, with the exact message.
- **The environment**: version, PHP version, engine (Velocity, Apache or nginx with PHP-FPM, FrankenPHP), database,
  design. **Setup** > **System information** > **Copy report as text** or `./console exp:info --plain --no-logo`
  give a summary that leaves out passwords, keys and server paths.
- **Log lines** around the time, cleaned of anything private.
- **A screenshot** if it helps, with no private data visible.

## What never goes into an issue

Passwords, keys, session cookies, database addresses, or the personal data of customers or users
([When something goes wrong](../../when-something-goes-wrong.md)).

## After you file it

- Answer questions from maintainers; an issue that cannot be reproduced is often closed.
- If you find a workaround, add it to the issue.
- If you can, send the fix: "With the code installed as Git clones, a fix is a branch in your fork and a pull request
  away" ([When something goes wrong](../../when-something-goes-wrong.md)). Mention the issue (`Fixes #123`) in the
  pull request.
- When a release fixes it, confirm in the issue.

## Related pages

- [Contributing as a community member](README.md)
- [Reporting bugs well](../../advocates/reporting-bugs-well.md)
- [Testing and reporting bugs](../../supporters/testing-and-reporting-bugs.md)
- [Reporting problems](../../customers/healthy/reporting-problems.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
