---
title: The maintainer path
audience: developer, app-creator
category: extension-development
order: 140
summary: What maintaining an extension means week to week - issues, pull requests, releases, PHP and kernel updates, security reports, hand-over - and a routine that keeps it to a few hours a month.
keywords: maintainer, maintenance, issues, pull requests, releases, security, routine, hand-over, responsibility, community
type: help
---
# The maintainer path

Part 5, chapter 14. Writing an extension takes days. Maintaining it takes years. A maintainer is the person users can
rely on to answer, fix and release. This chapter describes the job, so you can decide to take it on with open eyes,
and gives a routine that keeps it manageable.

## The steps of the path

Most maintainers did not plan to become one. The path usually looks like this:

| Step | You... |
|---|---|
| User | install extensions and report problems ([Writing good issues](writing-good-issues.md)) |
| Contributor | send fixes as pull requests ([Contributing back with pull requests](contributing-back-with-pull-requests.md)) |
| Reviewer | try and comment on others' pull requests ([Reviewing other people's work](reviewing-other-peoples-work.md)) |
| Co-maintainer | can merge, with the original maintainer still around |
| Maintainer | decide what goes in, and release |

Each step is useful on its own. Nobody has to go all the way.

## What the job contains

| Task | How often | Notes |
|---|---|---|
| Read and answer new issues | weekly | even "thanks, confirmed" helps the reporter |
| Review and merge pull requests | weekly | small ones quickly; large ones with a plan |
| Release | when there is something worth releasing | [Part 7: Releasing your extension](../part-7-releasing/README.md) |
| Follow PHP releases | yearly (PHP has one minor release a year) | add the new version to CI, fix its deprecations |
| Follow kernel releases | each release | read the behavior changes; test |
| Handle security reports | as they come, quickly and privately | see below |
| Keep the documentation current | with every change that affects users | |
| Hand over when you stop | once | see below |

## A routine for a few hours a month

- **One fixed slot a week**, say an hour on Monday, for issues and pull requests. Users learn when to expect answers.
- **Labels** on issues: `bug`, `feature`, `question`, `help wanted`, `good first issue`. The last two invite
  contributors.
- **CI on every push** ([Tests and CI for an upgraded extension](../part-4-upgrading/tests-and-ci-for-an-upgraded-extension.md)),
  so you review results, not run tests by hand.
- **Small releases.** Release when a fix is merged; do not collect for months.
- **Watch the kernel's repository** for releases (GitHub: **Watch > Custom > Releases**).

## Security reports

Someone may report a vulnerability in your extension. Then:

1. Thank them and confirm you received it, privately.
2. Do not discuss it in public issues until a fix is released.
3. Fix it, release, and say in the release notes that a security problem was fixed and what users must do.
4. Credit the reporter if they agree.

Put a short `SECURITY.md` in your repository that says how to report privately. GitHub can also enable private
vulnerability reporting for a repository in its settings.

## Saying no

A maintainer says no more often than yes: to features that do not fit, to changes that would break users, to code
nobody can maintain. Say it kindly and with the reason. A clear no is better than a pull request left open for a year.

## Handing over

When you no longer have time, say so in the README ("looking for a maintainer") and in a pinned issue. When someone
offers, add them as a co-maintainer, let them release once with you, then hand over. Transfer the repository and the
Packagist package if you both agree. That is the friendly end of the path; the alternative is an abandoned extension
someone else has to adopt ([Adopting an abandoned extension](adopting-an-abandoned-extension.md)).

## Related pages

- Previous: [Adopting an abandoned extension](adopting-an-abandoned-extension.md)
- Next: [Growing the community through code](growing-the-community-through-code.md)
- [The maintenance life cycle](../part-7-releasing/the-maintenance-life-cycle.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
