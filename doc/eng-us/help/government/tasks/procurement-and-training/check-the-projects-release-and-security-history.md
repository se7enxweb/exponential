---
title: Check the project's release and security history
audience: government, project-manager, admin
category: public-sector
order: 100
summary: Step by step, check for yourself how often Exponential is released, how security issues are handled and how changes are documented - from the tags, the changelogs, SECURITY.md and the hardening records in the repository - instead of relying on a vendor's description.
keywords: release history, security history, tags, changelog, SECURITY.md, hardening, project health, due diligence, evaluation, procurement task
type: help
---
# Check the project's release and security history

**Who:** an IT specialist or security officer on the evaluation team. **When:** before the tender or during evaluation.
The method is in [Evaluating open source project health](../../procurement/evaluation/evaluating-open-source-project-health.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What to record](#what-to-record)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

You need a clone of the repository or a browser on GitHub ([3. Getting the code](../../../../install/03-getting-the-code.md)).

## Steps

1. **List the releases**, sorted by version, not by name (by name, `v6.0.10` sorts before `v6.0.9`):

   ```bash
   git fetch --tags
   git tag -l 'v6.*' --sort=version:refname | tail -10
   ```

   The standards page records tags `v6.0.0` to `v6.0.14` and GitHub releases from `v6.0.10`
   ([Source control and releases](../../../../standards/source-control-and-releases.md#the-kernels-versions)).
2. **Note the dates** of recent releases; the roadmap page records `v6.0.11` to `v6.0.14` between December 2025 and
   June 2026 ([The roadmap](../../../project/roadmap.md#what-has-already-been-delivered)).
3. **Read the changelog** of the current line and skim what changed ([6.0.15 changelog](../../../../../changelogs/6.0/6.0.15.md);
   [Reading a changelog](../../../press/reading-a-changelog.md)).
4. **Read `SECURITY.md`**: supported versions, private reporting, first answer within one business day, fixes as the next
   release.
5. **Read the hardening records**: the security standards page links three documented hardening passes
   ([Security standards](../../../../standards/security.md#status)).
6. **Check the policy gaps** the project states itself: no signed commits or tags, no branch protection, no dependency
   scan in CI ([Source control and releases](../../../../standards/source-control-and-releases.md#known-gaps),
   [Security standards](../../../../standards/security.md#known-gaps)).
7. **Check the extensions you will use** the same way: their tags and `doc/changelogs/extensions/<name>.md`.

## What to record

```text
Project history check - <date>, by <name>
Latest release: <tag>, <date>       Releases in the last 12 months: <n>
Changelog current: yes/no           Supported line per SECURITY.md: <...>
Security reporting channel: <...>   Documented hardening passes: <list>
Known gaps stated by the project: <list>
Extensions checked: <name: latest tag, date>
Conclusion: <...>
```

## Result

A dated record, made by your own staff from primary sources, for the evaluation file.

## Pitfalls

- Sorting tags by name and picking the wrong "latest".
- Taking the roadmap as delivered work: the changelog is the record ([Reading the roadmap as a public body](../../procurement/lifecycle/reading-the-roadmap.md)).

## Related pages

- [Procurement and training tasks](README.md)
- [Release policy and version numbers](../../procurement/lifecycle/release-policy-and-version-numbers.md)
- [Evaluating security evidence](../../procurement/evaluation/evaluating-security-evidence.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
