# The history of Exponential, December 2023 to today

This is the story of every change made to Exponential and to the repositories around it since the se7enxweb era began
in December 2023, written so that you can learn from it: what a change delivers, how to use it, and what to check
when you upgrade. It has two halves. The **chronicle** tells the story month by month. The **ledger** is the complete,
machine-made record of every commit. Between them nothing is missing, and [the coverage page](coverage.md) proves it.

## In short

- Read **one month page** to learn what became possible that month; each starts with a short summary and ends with where to go next.
- Every change carries its commit, for example `2ddbc4c624`. Open it with `git show 2ddbc4c624`.
- The **ledger** lists all changes of a repository with date, kind, size and release tag: [ledger](ledger/README.md).
- The **coverage** page shows, per repository and month, that every change is explained, listed as small, or marked as without user benefit: [coverage](coverage.md).

## How to read the chronicle

1. Pick a month from the table below. A month page opens with **In short**: three to five lines you can read in half a minute.
2. The body is grouped by day or by theme. Each paragraph names what changed, why it matters, and what you can do now that you could not do before. Commit hashes are in backticks.
3. **Smaller changes** lists the changes that do not need a story of their own, each with its commit.
4. **Where to go next** links the feature pages (how to use it), the specifications (every setting and class), the behaviour change notes (what to check when you upgrade) and the changelog of the release.
5. A month with two pages (`a` and `b`) was split by day because it was busy.

A change that is not in the chronicle is in the ledger. A change that has no user benefit, such as a funding file, is marked as such, with a reason, on the coverage page.

## How to read the ledger

Open [the ledger](ledger/README.md), choose a repository, then a month. Each line gives:

| Column | Meaning |
|---|---|
| Date and commit | When the change was made, and its hash (short form) |
| Kind | Added, Updated, Removed, Renamed, Fixed, merge or other, taken from the commit message |
| Change | The commit subject |
| Files and +/- | How many files, how many lines added and removed |
| Release | The release tag that first contained the change, when there is one |

To see which release contains a change in this installation, list the tags in version order:

```bash
git tag -l 'v6.0.*' --sort=version:refname | tail -3
```

Expected: the newest tags, ending with `v6.0.14`. The 6.0.15 line is in development and has no tag yet; its notes are in
the [6.0.15 changelog](../changelogs/6.0/6.0.15.md).

## The chronicle, month by month

| Month | What happened first | Also |
|---|---|---|
| [December 2023](2023/2023-12.md) | The first outside change of the fork was merged on 11 December 2023 (upstream pull request #40). | [platform](ecosystem/months/2023-12.md) [extensions](extensions/months/2023-12.md) |
| [January 2024, first half](2024/2024-01a.md) | 6.0.0 became stable on 1 January 2024. | [platform](ecosystem/months/2024-01.md) [extensions](extensions/months/2024-01.md) |
| [January 2024, second half](2024/2024-01b.md) | 6.0.1 was cut on 29 January 2024. | [platform](ecosystem/months/2024-01.md) [extensions](extensions/months/2024-01.md) |
| [February 2024](2024/2024-02.md) | 6.0.2 was released on 29 February 2024. | [platform](ecosystem/months/2024-02.md) [extensions](extensions/months/2024-02.md) |
| [March 2024](2024/2024-03.md) | The root of the content tree can be viewed. | [platform](ecosystem/months/2024-03.md) [extensions](extensions/months/2024-03.md) |
| [April 2024](2024/2024-04.md) | 6.0.3 was released on 2 April 2024. | [platform](ecosystem/months/2024-04.md) [extensions](extensions/months/2024-04.md) |
| [May 2024](2024/2024-05.md) | No change to the main installation in May 2024; the work happened in the platform repositories. | [platform](ecosystem/months/2024-05.md) |
| [June 2024](2024/2024-06.md) | A quiet month: two edits to `composer.json` kept the distribution package list current. | [platform](ecosystem/months/2024-06.md) |
| [July 2024](2024/2024-07.md) | No change to the main installation in July 2024; the site bundle and the admin interface moved forward upstream. | [platform](ecosystem/months/2024-07.md) [extensions](extensions/months/2024-07.md) |
| [August 2024](2024/2024-08.md) | August 2024 is the month of admin3, a complete responsive administration design (22 August). | [platform](ecosystem/months/2024-08.md) [extensions](extensions/months/2024-08.md) |
| [September 2024](2024/2024-09.md) | 6.0.4 was released on 5 September 2024. | [platform](ecosystem/months/2024-09.md) [extensions](extensions/months/2024-09.md) |
| [October 2024](2024/2024-10.md) | 6.0.5 was released on 1 October 2024. | [platform](ecosystem/months/2024-10.md) [extensions](extensions/months/2024-10.md) |
| [November 2024](2024/2024-11.md) | A second pass over admin3 after testing on real devices. | [platform](ecosystem/months/2024-11.md) [extensions](extensions/months/2024-11.md) |
| [December 2024](2024/2024-12.md) | 6.0.6 was released on 17 December 2024. | [platform](ecosystem/months/2024-12.md) |
| [January 2025](2025/2025-01.md) | 6.0.7 was in development. | [platform](ecosystem/months/2025-01.md) [extensions](extensions/months/2025-01.md) |
| [February 2025](2025/2025-02.md) | 6.0.7 was released on 1 February 2025. | [platform](ecosystem/months/2025-02.md) |
| [March 2025](2025/2025-03.md) | No change to the main installation in March 2025. | [platform](ecosystem/months/2025-03.md) |
| [April 2025](2025/2025-04.md) | PHP 8.4 support begins and PHP 7 support ends. | [platform](ecosystem/months/2025-04.md) |
| [May 2025](2025/2025-05.md) | 6.0.8 was published; development of 6.0.9 began. | [platform](ecosystem/months/2025-05.md) |
| [June 2025](2025/2025-06.md) | 6.0.9 was released on 10 June 2025. | [platform](ecosystem/months/2025-06.md) |
| [July 2025](2025/2025-07.md) | Licensing and credit were made explicit with `COPYRIGHT.md` and `LICENSE.md` (GNU GPL v2 or later). | [platform](ecosystem/months/2025-07.md) |
| [August 2025](2025/2025-08.md) | The product was renamed to Exponential on 12 August 2025. | [platform](ecosystem/months/2025-08.md) [extensions](extensions/months/2025-08.md) |
| [September 2025](2025/2025-09.md) | PHP 8.1 and 8.2 clean-up and fixes for notification mail and the view count cronjob. | [platform](ecosystem/months/2025-09.md) [extensions](extensions/months/2025-09.md) |
| [October 2025](2025/2025-10.md) | No change to the main installation in October 2025. | [platform](ecosystem/months/2025-10.md) |
| [November 2025](2025/2025-11.md) | No change to the main installation in November 2025. | [platform](ecosystem/months/2025-11.md) |
| [December 2025](2025/2025-12.md) | PHP 8.5 is supported: two classes got the nullable types it requires and Zeta Components were raised. | [platform](ecosystem/months/2025-12.md) [extensions](extensions/months/2025-12.md) |
| [January 2026](2026/2026-01.md) | 6.0.12 was in development. | [platform](ecosystem/months/2026-01.md) |
| [February 2026](2026/2026-02.md) | 6.0.12 was released on 10 February 2026. | [platform](ecosystem/months/2026-02.md) |
| [March 2026](2026/2026-03.md) | The test tool-chain moved to PHPUnit 13. | [platform](ecosystem/months/2026-03.md) [extensions](extensions/months/2026-03.md) |
| [April 2026](2026/2026-04.md) | 6.0.13 was released on 18 April 2026. | [platform](ecosystem/months/2026-04.md) [extensions](extensions/months/2026-04.md) |
| [May 2026](2026/2026-05.md) | A quiet month: one PHP type fix for the content diff engine and a regenerated release file list. | [platform](ecosystem/months/2026-05.md) |
| [June 2026, first half](2026/2026-06a.md) | MongoDB 8 arrived as a database on 1 June 2026. | [platform](ecosystem/months/2026-06.md) [extensions](extensions/months/2026-06.md) |
| [June 2026, second half](2026/2026-06b.md) | 6.0.15 was in development. | [platform](ecosystem/months/2026-06.md) [extensions](extensions/months/2026-06.md) |
| [July 2026](2026/2026-07.md) | The command line grew up: `ezpm` joined the console, a one command installer and the Kickstarter arrived, and a password reset tool. | [platform](ecosystem/months/2026-07.md) [extensions](extensions/months/2026-07.md) [Velocity](velocity/2026-07.md) |
| [August 2026](2026/2026-08.md) | Template debugging tells the truth: path comments name the template that really ran, and the extension list sorts and downloads. | [extensions](extensions/months/2026-08.md) [Velocity](velocity/2026-08.md) |
| [September 2026, first half](2026/2026-09a.md) | Long lists in the administration interface are paged, sortable and sized by settings, so installations with thousands of roles or locations draw... | [platform](ecosystem/months/2026-09.md) [extensions](extensions/months/2026-09.md) [Velocity](velocity/2026-09a.md) |
| [September 2026, second half](2026/2026-09b.md) | Velocity, a persistent-worker web server for Exponential, arrived on 22 September and became the recommended way to be fast within two days. | [platform](ecosystem/months/2026-09.md) [extensions](extensions/months/2026-09.md) [Velocity](velocity/2026-09a.md) |
| [October 2026](2026/2026-10.md) | 354 changes in two days (1 and 2 October), no release tag; they belong to the 6.0.15 line. | [extensions](extensions/months/2026-10.md) [Velocity](velocity/2026-10.md) |

## The other chronicles

| Part | Covers | Start here |
|---|---|---|
| Platform ecosystem | The Symfony based repositories around Exponential: admin interface, layouts, starters, bridges, forks | [ecosystem overview](ecosystem.md) |
| Extensions | The legacy extensions, themes and packages, one chronicle per repository | [extensions](extensions/README.md) |
| Velocity | The web server engine, from the first commit in July 2026 | [Velocity chronicle](velocity/README.md), [installers and packages](velocity/installers-and-packages.md) |
| Release notes | One page per release and per extension | [6.0.15](../changelogs/6.0/6.0.15.md), [extensions](../changelogs/extensions/README.md) |

## Use the history to learn

- **You want to use a feature:** follow the link from its month to the feature page, or start from the [guides](../guides/README.md).
- **You are upgrading:** read the [6.0.15 changelog](../changelogs/6.0/6.0.15.md), then the behaviour change notes it links, newest last.
- **You meet a word you do not know:** [glossary](../glossary.md).
- **You hunt for one commit:** search the ledger of the repository, or run `git log --oneline -S"text"` in the installation.

## Where to go next

- [Guides: the learning path](../guides/README.md)
- [Glossary](../glossary.md)
- [Coverage: every change accounted for](coverage.md)
- [Ledger of all repositories](ledger/README.md)
