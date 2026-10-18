---
title: Reporting bugs well
audience: advocate
category: advocacy
order: 102
summary: How advocates write bug reports that get fixed, and teach others to - search first, one problem per issue, steps to reproduce, expected and actual result, environment from the Report an error page, safe logs - with a template and a review checklist.
keywords: bug report, issue, reproduce, steps, expected, actual, environment, Report an error, System information, logs, template, GitHub issues
type: help
---
# Reporting bugs well

This page is for advocates who report bugs and who help others report them. A good report is the fastest way to get a
problem fixed for everyone. The project's own guidance is in
[When something goes wrong, A good bug description](../when-something-goes-wrong.md#a-good-bug-description); this
page turns it into a routine.

## Contents

1. [Before you file](#before-you-file)
2. [The parts of a good report](#the-parts-of-a-good-report)
3. [The template](#the-template)
4. [Getting the environment safely](#getting-the-environment-safely)
5. [Reviewing someone else's report](#reviewing-someone-elses-report)
6. [After filing](#after-filing)

## Before you file

1. Is it a bug, or a question? Questions go to the forums ([Which tracker for which report](which-tracker-for-which-report.md)).
2. Is it a security problem? Then private ([Security reports stay private](security-reports-stay-private.md)).
3. Search the tracker; if it exists, add what you know there.
4. Can you reproduce it on a fresh installation? If not, it may be the site's own setup.

## The parts of a good report

| Part | Example |
|---|---|
| Short title naming the problem | *Upload page: ZIP file with a folder fails with "temp_dir"* |
| Steps to reproduce | 1. Open the media library. 2. Upload a ZIP containing a folder. 3. ... |
| Expected result | The files are imported |
| Actual result, exact message | The exact error text |
| Environment | Exponential version, PHP, engine, database, design |
| Log lines | Around the time, with private parts removed |

The title example comes from [When something goes wrong](../when-something-goes-wrong.md#a-good-bug-description).

## The template

```text
Title: <Where>: <what goes wrong>

Steps to reproduce
1. ...
2. ...

Expected
...

Actual
... (exact message, screenshot with invented data)

Environment
Exponential <version or commit>, PHP <version>, <Velocity | FrankenPHP | Apache/nginx + PHP-FPM>,
<database>, <admin4 | classic | AdminUI>

Logs (private data removed)
...

Notes
Reproduced on a fresh installation: <yes/no>. Related: <links>.
```

## Getting the environment safely

- The **Report an error** page of the help (`exphelp/report`) fills in a summary of the installation.
- **Setup > System information**, **Copy report as text**, gives the same summary.
- Both leave out passwords, keys and server paths ([When something goes wrong](../when-something-goes-wrong.md#a-good-bug-description)).
- Remove IP addresses, e-mail addresses and names from logs before posting.

## Reviewing someone else's report

Help a reporter improve their issue with a friendly comment:

```text
[ ] Can I follow the steps? If not, ask for the missing one
[ ] Is the exact message there?
[ ] Is the environment there?
[ ] Is there anything private in it? (then ask them to edit it out at once)
[ ] Can I reproduce it? (then say so, with my environment)
```

See [Reproducing and confirming issues](reproducing-and-confirming-issues.md).

## After filing

- Answer questions on the issue promptly.
- If you find the cause, consider sending the fix ([Even better: send the fix](../when-something-goes-wrong.md#even-better-send-the-fix)).
- When it is fixed, thank the people involved.

## Related pages

- [Which tracker for which report](which-tracker-for-which-report.md)
- [Reproducing and confirming issues](reproducing-and-confirming-issues.md)
- [GitHub for advocates](github-for-advocates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
