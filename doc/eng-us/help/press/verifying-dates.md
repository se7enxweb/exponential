---
title: Verifying dates
audience: press
category: press-media
order: 133
summary: How to check dates about Exponential - release dates from Git tags, history dates from the commit log and the history pages, the dates of the organizations from their own pages - plus the date traps (founding versus copyright years, idea versus launch, tag versus announcement, time zones) and how to write "today".
keywords: dates, verify dates, release date, tag date, commit date, history, founding date, timeline, time zone, as of, today
type: help
---
# Verifying dates

This page is for reporters and fact-checkers. Dates around Exponential are well recorded, but several of them are
easy to confuse. This page shows where each kind of date lives and how to check it.

## Contents

1. [Where each date lives](#where-each-date-lives)
2. [Checking a release date](#checking-a-release-date)
3. [Date traps](#date-traps)
4. [Writing "today"](#writing-today)

## Where each date lives

| Date | Primary source |
|---|---|
| A release | Its Git tag; [release overview](../../release-overview) |
| A change | The commit; the month page in [the history](../../history/README.md) |
| A feature in 6.0.15 | The dated sections of the [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |
| The rename | <https://exponential.earth/about/project> (11 August 2025); [August 2025](../../history/2025/2025-08.md) (12 August 2025) |
| The Foundation | <https://foundation.exponential.earth/> (19 March 2026) |
| 7x's stages | [7x in three stages: 2014, 2022, 2023](7x-in-three-stages.md) |
| Repositories created | The GitHub API, quoted on [Who is 7x?](../who-is-7x.md#nearly-thirty-years-the-timeline) |

## Checking a release date

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git log -1 --format='%ad' --date=short v6.0.14     # the date of the tagged commit
git for-each-ref --format='%(refname:short) %(creatordate:short)' 'refs/tags/v6.0.*' --sort=version:refname
```

The tag date and a later announcement may differ by days. The release date is the tag's; the announcement has its
own date.

## Date traps

| Trap | Right reading |
|---|---|
| 1998 in the copyright notice | The first year of a copyright notice, not a founding date |
| 1999 | The first copyright year of eZ Systems AS in the code |
| 2001, 2014, 2022, 2023 for 7x | Founding as Brookins Consulting, the idea, the project start, the launch |
| 11 or 12 August 2025 for the rename | Website first, repository the next day; "August 2025" covers both |
| A date in the 6.0.15 changelog | When a feature arrived in development, not a release date |
| Dates in the press kit | The kit is dated June 2026; it predates newer releases ([The press kit](the-press-kit.md)) |
| Time zones | Commits carry their author's offset; for a day near midnight, give the month or check the offset |

## Writing "today"

"Currently", "the latest" and "today" age fast. Write the date you checked:

```text
"As of 18 October 2026, the newest release is 6.0.14."
"6.0.15 was in development when this article was published."
```

## Related pages

- [Timeline for reporters](timeline-for-reporters.md)
- [Verifying numbers](verifying-numbers.md)
- [Fact-checking guide](fact-checking-guide.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
