---
title: Testing and reporting bugs
audience: supporter
category: supporting
order: 60
summary: How any user supports Exponential by testing and filing good bug issues - which tracker to use, what a good bug description contains, testing development versions, security problems that must be reported privately, and following up.
keywords: testing, bug, bug report, issue, GitHub, tracker, reproduce, development version, main branch, security, follow up
type: help
---
# Testing and reporting bugs

This page is for every user of Exponential. You do not need to write code to make the software better: a precise
bug description is often half of the fix.

## Which tracker

| Problem | Where |
|---|---|
| A bug in the Exponential software | <https://github.com/se7enxweb/exponential/issues> |
| A bug in an extension | That extension's repository on GitHub |
| The project, its websites or the community | <https://github.com/se7enxweb/exponential-community/issues> |
| A security vulnerability | **Privately** to security@se7enx.com; never a public issue (SECURITY.md) |

## A good bug description

The help's own guide explains it fully: [When something goes wrong](../when-something-goes-wrong.md#a-good-bug-description).
The essentials:

- What you did, step by step.
- What you expected, and what happened instead.
- The versions: Exponential, PHP, database, engine, extensions. One command collects them all:
  `./console exp:info --json --no-logo` (or `--plain` for a text block). Paste its output as it is: it never
  contains a password, key, token, database user or full database address
  ([The system card](../../features/6.0/system-card.md)).
- Error messages and log lines, with passwords, keys and personal data removed.
- Whether it happens on a clean installation.

## Testing before a release

Development happens on the `main` branch, and fixes are "published with the next 6.0.x release" (SECURITY.md).
Testing `main` on a test installation finds problems before they reach everyone:

1. Install from Git ([Develop with Git](../develop-with-git.md)).
2. Update to the latest `main`.
3. Do what you normally do: edit, publish, upload, search, switch languages.
4. Report anything that broke, with the commit you tested.

Never test development versions on a production site.

## Follow up

- Answer questions on your issue; unanswered questions are the most common reason a bug stalls.
- Test the fix when it lands and say whether it works.
- If you can fix it yourself: [When something goes wrong, "Even better: send the fix"](../when-something-goes-wrong.md#even-better-send-the-fix).

## Related pages

- [When something goes wrong](../when-something-goes-wrong.md)
- [Contributing code](contributing-code.md)
- [Helping others in the community](helping-in-the-community.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
