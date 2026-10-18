---
title: Filing bugs well
audience: partner
category: partnership
order: 660
summary: How partners file bug issues that get fixed - which tracker, how to reproduce on a clean install, what to include, how to remove customer data and secrets, how to follow up, and when to send the fix yourself.
keywords: bug, bug report, issue, GitHub, reproduce, tracker, exponential-community, environment, follow up, fix
type: help
---
# Filing bugs well

This page is for anyone in a partner company who finds a bug. A bug report earns 50 points
(<https://exponential.earth/partner/requirements>), but a good one is worth far more: it is the first step to a fix
that protects all your customers.

## Which tracker

| Problem | Tracker | Source |
|---|---|---|
| A bug in the Exponential software | <https://github.com/se7enxweb/exponential/issues> | README, [When something goes wrong](../when-something-goes-wrong.md) |
| A bug in an extension | That extension's repository | its `ezinfo.php` / `extension.xml` |
| The project, its websites or the community | <https://github.com/se7enxweb/exponential-community/issues> | README |
| A security vulnerability | **No tracker**: e-mail security@se7enx.com | SECURITY.md |

## Before you file

- [ ] Reproduce it on a clean installation of a current release, without your customer's code if you can.
- [ ] Search open and closed issues.
- [ ] Note the exact versions: Exponential, PHP, database, engine, extensions.

## What to include

The help's guide has the full checklist: [When something goes wrong, "A good bug description"](../when-something-goes-wrong.md#a-good-bug-description).
In short: steps, expected result, actual result, versions, log lines.

```text
Title: <what breaks> when <doing what> (Exponential <version>)

Steps:
1. ...
Expected: ...
Actual: ...
Environment: Exponential <version>, PHP <x.y>, <database>, <engine>, extensions: <...>
Log (secrets and customer data removed):
<lines>
```

## Remove before posting

Customer names, e-mail addresses, private URLs, host names of databases, keys, passwords, session ids. Replace them
with placeholders ([Protecting customer data](protecting-customer-data.md)).

## Follow up

- Answer questions on the issue quickly; an unanswered question is the most common reason a bug stalls.
- Test the fix when it lands on `main` ([Develop with Git, "Use a fix before it is released"](../develop-with-git.md#use-a-fix-before-it-is-released)).
- Close the loop: say it is fixed for you.

## Send the fix

If you know the fix, send it: [When something goes wrong, "Even better: send the fix"](../when-something-goes-wrong.md#even-better-send-the-fix)
and [Contributing code back](contributing-code-back.md).

## Related pages

- [Opening a partner support ticket](opening-a-partner-support-ticket.md)
- [Testing releases before your customers do](testing-releases.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
