---
title: "Case study: a pull request to an upstream author"
audience: developer, app-creator
category: extension-development
order: 170
summary: A real pull request to another organization's project, read section by section: what was broken, the one-line fix, the kept security intent, tests that fail without it, and what was not checked.
keywords: case study, pull request, upstream, request-shield, description, tests, Referrer-Policy, Origin, review, contribution
type: help
---
# Case study: a pull request to an upstream author

Part 5, chapter 17, the last of this part. Earlier chapters described what a good pull request contains. This one
reads a real one: pull request 4 to `cjw-network/request-shield` on GitHub, opened on 8 October 2026 and merged the
same day. request-shield is a project of another organization (MIT-licensed), which Exponential sites can use; the
pull request came from this project, as an outside contributor would send it. You can read it in full on GitHub.

## The title

```text
Fixed: The dashboard login works in a browser (Referrer-Policy same-origin instead of no-referrer)
```

The symptom a user sees first, the fix in brackets. A maintainer who reads only titles knows what this is.

## "What was broken"

The description opens with the bug, explained from the cause to the symptom:

- the dashboard's sign-in form accepts a POST only when its `Origin` host is the request's host ("That check is
  right.");
- every dashboard page is sent with `Referrer-Policy: no-referrer`, and with that policy Chromium and Firefox send
  `Origin: null` with the page's own form POST ("checked in both with Playwright");
- so every sign-in from a real browser was refused with 403, "whatever the token".

Then a sentence many descriptions leave out: why the existing tests did not catch it. They posted either without an
`Origin` (as curl does) or with a hand-written correct one, "No browser sends that combination". That tells the
maintainer the tests were not wrong, only incomplete.

## "The fix"

One line in one file: `same-origin` instead of `no-referrer`. The description adds what did **not** change: "The
`Origin` check stays as strict as it was: `Origin: null` is still refused", and why accepting it would be wrong ("a
form from a sandboxed frame or any other opaque origin").

A contributor touching security code must show that the protection is intact. Saying it explicitly saves the
maintainer from having to work it out.

## "Why same-origin keeps the privacy intent"

The original author had chosen `no-referrer` for a reason: dashboard addresses must not leak to other sites through
links. The pull request explains that `same-origin` serves the same purpose: other sites still get no `Referer`;
only the dashboard's own requests carry one.

This is respect in technical form: the contributor understood why the original was written that way and kept its
goal, instead of just making the symptom go away.

## "Tests"

The new checks were added to the two existing sign-in tests (one unit test of the gate, one end-to-end test with the
built-in server), and the description shows them failing without the fix and passing with it:

```text
without the fix:  ... FAIL - 4 passed, 2 failed, 0 skipped (PHP 8.5.11)
with the fix:     PASS - 6 passed, 0 failed, 0 skipped (PHP 8.5.11)
```

## The honest part

The last section is the one that builds trust:

- the whole suite was run on `main` and on the branch, with the same result on both, in two configurations;
- one test fails on both, and the description explains why (the machine has a PHP extension loaded that the test
  expects to be missing) and that it is unrelated;
- one run against a built file stops before it starts, on `main` too, "so it is not caused by this change";
- static analysis "was not run locally: the development tools are not installed on the test machine. The change is
  a string literal in an existing array plus test code."

Nothing is hidden, and nothing is claimed that was not done.

## The files

Four files changed: the one-line fix, the test, a line in `CHANGELOG.md` under "Unreleased / Fixed", and the feature
documentation page that names the header. A design proposal that still names the old header was "left as written, as
a record of the design": the contributor updated what describes the present and left the history alone.

## What to copy

| Habit | In this pull request |
|---|---|
| Cause before symptom | "What was broken", step by step |
| Why tests missed it | one paragraph |
| Smallest fix | one line |
| Protection kept, and said so | `Origin: null` still refused |
| Original intent respected | the privacy reason for the old header, kept |
| Tests that fail without the fix | shown with their output |
| Same results as main, in a table | two configurations |
| What was not checked, and why | static analysis, a pre-existing failure |
| Changelog and docs updated in the same pull request | `CHANGELOG.md` and the feature page |

## Related pages

- Previous: [Case study: porting a package from another project with credit](case-study-ported-from-another-project.md)
- Next: [Part 6: Theme developers](../part-6-theme-developers/README.md)
- [Contributing back with pull requests](contributing-back-with-pull-requests.md)
- [Pull request etiquette](pull-request-etiquette.md)
- The pull request itself: `github.com/cjw-network/request-shield/pull/4`
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
