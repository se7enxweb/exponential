---
title: Reproducing and confirming issues
audience: advocate
category: advocacy
order: 103
summary: How advocates help maintainers by reproducing reported bugs on a fresh installation, confirming or narrowing them down, and commenting with useful, safe information - a step-by-step routine with comment templates.
keywords: reproduce, confirm, triage, issues, bug, fresh installation, narrowing down, comment, environment, GitHub, maintainers, help without code
type: help
---
# Reproducing and confirming issues

This page is for advocates who want to help fix bugs without writing code. Reproducing a reported bug on another
installation tells maintainers it is real and where it lives. It is one of the most useful contributions there is.

## Contents

1. [Pick an issue](#pick-an-issue)
2. [Reproduce it](#reproduce-it)
3. [Narrow it down](#narrow-it-down)
4. [Comment](#comment)

## Pick an issue

Open the [Exponential issues](https://github.com/se7enxweb/exponential/issues) or the
[Velocity issues](https://github.com/se7enxweb/exponential-velocity/issues) and choose one with steps you can follow on
your own installation. Skip anything that looks like a security problem; that belongs in private channels.

## Reproduce it

1. Make a fresh installation with invented data ([Install in one command](../../features/6.0/install-in-one-command.md)),
   or use your test installation. Never a production or someone else's site.
2. Use the reported version if possible; then the current one.
3. Follow the steps exactly.
4. Write down what you see, with the exact message.

## Narrow it down

| Try | Tells maintainers |
|---|---|
| Another database (SQLite vs. a server) | Whether it is database-specific |
| Another engine (Velocity vs. PHP-FPM) | Whether it is engine-specific ([Which tracker](which-tracker-for-which-report.md)) |
| Another design (admin4 vs. classic) | Whether it is a template problem |
| The latest `main` | Whether it is already fixed ([Testing the development line](testing-the-development-line.md)) |
| Fewer steps | The smallest reproduction |

## Comment

```text
REPRODUCED
I can reproduce this on Exponential <version/commit>, PHP <version>, <engine>, <database>, <design>,
fresh installation. Steps as described; result: <exact message>.
Not reproducible with <other database/engine>, so it seems specific to <...>.

NOT REPRODUCED
I could not reproduce this on <environment>, following the steps exactly. Could you share <missing detail>?

ALREADY FIXED
On the current main (<commit>) this no longer happens; on <older version> it does.
```

Leave out passwords, keys, server paths and personal data
([When something goes wrong](../when-something-goes-wrong.md#a-good-bug-description)). Use a reaction instead of
"+1" comments ([GitHub for advocates](github-for-advocates.md)).

## Related pages

- [Reporting bugs well](reporting-bugs-well.md)
- [Testing the development line](testing-the-development-line.md)
- [Proposing a contribution sprint](proposing-a-contribution-sprint.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
