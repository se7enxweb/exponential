---
title: Quality gates and acceptance
audience: project-manager, developer
category: leadership
order: 100
summary: The checks Exponential already runs on every change (CI workflows, test suites, design and accessibility tests, CSP reports) and how to build your project's own quality gates and acceptance on top of them.
keywords: quality, gate, acceptance, CI, continuous integration, GitHub Actions, PHPUnit, PHPStan, code style, test suite, accessibility, a11y, WCAG, contrast, axe, CSP, Content Security Policy, definition of done, sign-off, UAT
type: help
---
# Quality gates and acceptance

This page is for project managers who must say "this is good enough to ship", and for the developers who give them
the evidence. It explains which checks the Exponential project itself runs on every change, which of them your own
project can reuse, and how to put them together into quality gates and an acceptance procedure with a clear sign-off.

A **quality gate** is a point in the project where work only moves on when named checks pass. **Acceptance** is the
last gate: the people who will use the site confirm that it does what was agreed.

## Contents

1. [What the project checks on every change](#what-the-project-checks-on-every-change)
2. [The test suites](#the-test-suites)
3. [Accessibility checks](#accessibility-checks)
4. [Security checks: the Content Security Policy and its reports](#security-checks-the-content-security-policy-and-its-reports)
5. [Checks on a running site](#checks-on-a-running-site)
6. [Designing your project's gates](#designing-your-projects-gates)
7. [Acceptance: procedure and checklist](#acceptance-procedure-and-checklist)
8. [Reading a red check](#reading-a-red-check)

## What the project checks on every change

The kernel repository `se7enxweb/exponential` runs four GitHub Actions workflows from `.github/workflows/`. The map
of them is [Continuous integration](../../bc/6.0/ci.md); the checks are described in
[Quality checks](../../features/6.0/quality-checks.md).

| Workflow | Runs on | What it checks | Blocks a change |
|---|---|---|---|
| **Quality** (`quality.yml`) | Every push to `main`, every pull request to it, and by hand | PHP syntax of changed files (PHP 8.3), JavaScript syntax of changed scripts (Node.js 22), the file manifest `share/filelist.md5`, commit messages, code style of changed files, PHPStan, the tests of the code style sniffs (PHP 8.4), and PHP 8.0 syntax and boot | Yes. Code style is a warning; the manifest is a warning on a pull request and blocking on `main` |
| **PHPUnit** (`phpunit.yml`) | Every push to `main` and every pull request, with no paths filter | The test suite on PHP 8.1 to 8.5 without a database, and database tests on MySQL 8.0 and PostgreSQL 15 | Yes |
| **Performance** (`performance.yml`) | A change to PHP, templates, `settings/`, the German translation or `tests/benchmark/` | A micro benchmark on PHP 8.5 against `tests/benchmark/baseline.json` | No, a warning only |
| **CI health** (`ci-health.yml`) | Mondays 06:17 UTC, and by hand | A report of pass rates, the steps and tests that fail most, and flaky runs | No, it only reports |

Points worth knowing as a manager:

- **A pull request is tested as merged into `main`.** A branch that was green can turn red when `main` moves
  ([CI](../../bc/6.0/ci.md)).
- **No PHPUnit paths filter.** The tests also read templates, stylesheets, scripts, translations, settings and
  documentation, so a change to any of them runs the suite ([Quality checks](../../features/6.0/quality-checks.md#pull-requests)).
- **Contributors are not blocked by commit message style.** A contributor's subject outside the `Added:` /
  `Updated:` / `Fixed:` / `Removed:` / `Renamed:` format is a warning; it is an error only for the project's own authors.
- **The same checks run locally.** `make hooks` installs Git hooks that check syntax, the manifest, code style and
  the commit message at `git commit`, and PHPStan at `git push`; `make check` runs lint, manifest, phpcs and phpstan
  as CI does ([Quality checks](../../features/6.0/quality-checks.md)).

### The CI health baseline

The project measured its own CI on 2026-10-17 for the period 2026-10-03 to 2026-10-17, before that week's repairs:
1,540 finished runs, 61.7 % passed ([CI, "The baseline"](../../bc/6.0/ci.md)). The page says to compare later reports
with these figures. The report itself is `python3 .github/scripts/ci_health_report.py --days 14`. Use the same
report on your own repositories if they run on GitHub Actions: it is one way to put a number on "how reliable is our
pipeline".

## The test suites

`phpunit.xml` groups the tests into suites. The names in the file are:

| Suite | Covers |
|---|---|
| `security` | security-related kernel tests |
| `kernel-classes`, `kernel-content`, `kernel-datatypes`, `kernel-search`, `kernel-private` | the kernel |
| `kernel-contentfinder`, `kernel-contentcheck`, `kernel-logviewer`, `kernel-requestlog` | the content finder, content checks, log viewer and request logs |
| `lib` | the libraries in `lib/` |
| `mongodb`, `mongodb-live` | the MongoDB layer; the live part needs a server |
| `cjw_newsletter`, `explayouts_admin`, `ezjscore`, `ezmultiupload`, `expservices`, `ezoe`, `maps`, `admin-extensions` | extensions |
| `quality` | the project's own code style sniffs |
| `design` | the administration designs: styling rules, accessibility, keyboard, CSP |

Facts from [Quality checks](../../features/6.0/quality-checks.md#run-only-the-tests-of-a-change):

- The whole run has "more than 10,000 tests" and takes a long time on a busy machine.
- For an everyday change it is enough to run the test files that cover the touched files, found through
  `tests/test-map.json`, the class names and the paths. The full run stays the check before a release.
- Tests that need a live database or web server read their address from environment variables and skip without
  them, so a plain `vendor/bin/phpunit` passes on a machine without those servers.

For your project: your own extensions can carry their own tests in the same style. Ask your developers which suite
covers which of your features, and put that list into the gate definition.

## Accessibility checks

The admin4 design follows WCAG rules that tests check ([Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility)).
These tests are in `tests/tests/design/` and run in the `design` suite on every change:

| Test | What it checks | Source |
|---|---|---|
| `AdminTokenContrastTest` | Contrast of the color tokens with the WCAG 2 formula, light and dark: text 4.5:1, focus ring and field edges 3:1 | the test file |
| `AdminLandmarksTest` | Skip link, landmarks, one main landmark | [admin4 design](../../features/6.0/admin4-design.md) |
| `AdminKeyboardTest` | The markup and hooks keyboard use relies on | [admin4 design](../../features/6.0/admin4-design.md) |
| `AdminFormLabelsTest`, `AdminFormErrorsTest` | Every form field has a name; errors are announced | [Styling the administration](../../bc/6.0/admin4-styling.md#code-review-checklist) |
| `StandardContentEditAccessibleNamesTest` | Every control of the public edit form has an accessible name | the test file |
| `AdminStylingRulesTest` | No inline CSS in admin templates; tokens with dark values | [Styling the administration](../../bc/6.0/admin4-styling.md) |

**axe.** The changelog records manual checks with the axe accessibility engine, for example "axe reports no serious
finding in the page frame on 10 classic pages at 1440 px and at 960 px with 200 % zoom, on both web servers"
([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)). axe is not one of the CI workflows. If your acceptance needs an
automated audit of your own pages, plan it as a separate task with its own tool and owner.

The [code review checklist](../../bc/6.0/admin4-styling.md#code-review-checklist) for administration templates asks
for a check "in light and dark mode at 1440 px and at 960 px with scale 2: no light card on the dark page, text
contrast at least 4.5:1, no horizontal overflow, no console errors". It is a good model for your own front-end gate.

Right-to-left languages are part of accessibility for many audiences: see [Right to left](../../features/6.0/right-to-left.md)
and [Accessibility, security and compliance checklists](accessibility-security-and-compliance-checklists.md).

## Security checks: the Content Security Policy and its reports

Exponential sends a Content Security Policy from `site.ini [ContentSecurityPolicy]`; by default it is report-only on
the administration and off on the sites ([Content Security Policy](../../features/6.0/content-security-policy.md)).
The kernel collects the browser's reports itself: the view `csp/report` writes one line per violation to
`var/log/csp.log`, which the log viewer lists as **Content Security Policy reports**
([Reports](../../features/6.0/content-security-policy.md#reports)).

This gives you a measurable gate before you switch a policy to `enforce`:

1. Run the policy in `report-only` while editors work normally for an agreed period.
2. Read the error digest of the `csp` log (**Setup > Log files > Error digest**), grouped by directive and blocked
   source ([Log viewer](../../features/6.0/log-viewer.md#error-digest)).
3. The gate passes when no unexplained violation remains. Then follow "Switching to enforce, step by step" on the
   [Content Security Policy](../../features/6.0/content-security-policy.md) page.

The test `AdminContentSecurityPolicyTest` in the `design` suite keeps the administration's templates compatible with
a strict policy.

## Checks on a running site

These checks need no developer and can be part of every gate from "staging is ready" on.

| Check | Where | Passes when | Source |
|---|---|---|---|
| Health checks | **Setup > System information** | No failure; warnings explained | [System information](../../guides/system-information.md#3-health-checks) |
| File consistency | **Setup > Upgrade check** | Only files changed on purpose are listed | [The upgrade check](../../guides/upgrade-check.md) |
| Database consistency | **Setup > Upgrade check** | No difference, or only engine notes | same |
| Broken relations, missing files | **Setup**, content checks | Nothing unexplained | [Content checks](../../features/6.0/content-checks.md) |
| Errors | **Setup > Log files > Error digest** | No new group you cannot explain | [Log viewer](../../features/6.0/log-viewer.md#error-digest) |
| Audit chain | **Audit** tab, or `./console exp:audit verify` | Every channel intact | [Audit trail](../../features/6.0/audit-trail.md) |
| Cronjobs | **Setup > Cronjobs**, Recent runs | Runs are OK; issues explained | [Cronjobs](../../guides/cronjobs.md#10-recent-runs-and-how-issues-are-counted) |
| Performance | `./console exp:benchmark --baseline=<file>` | Exit status 0, no regression | [Benchmarking](../../guides/benchmarking.md#9-comparing-runs-baselines-and-regressions) |
| Security hardening | The checklist of the help page | Every line ticked or consciously waived | [Security hardening](../security-hardening.md#checklist) |

## Designing your project's gates

A simple model with four gates. Adapt the names and checks; keep the rule that a gate has named checks and a named
owner who says "pass".

| Gate | When | Checks | Owner |
|---|---|---|---|
| G1: Change ready | Each pull request of your own code | Your CI green (or the project's checks run locally with `make check`); review done; tests of the change pass | Lead developer |
| G2: Staging ready | Before editors see the build | G1 for every change; site checks of the table above on staging; content model matches the agreed model | Lead developer and administrator |
| G3: Acceptance | Before go-live | Acceptance checklist below signed; accessibility and CSP gates passed; performance baseline saved | Project manager with the product owner |
| G4: Live | After go-live | Launch checklist done; first-week checks of [10.15](../../install/10-after-installing.md#1015-a-checklist-for-the-first-week) done | Administrator |

> **Tip**
> Write the definition of each gate into the project charter at the start ([Project templates](project-templates.md)).
> A gate that is only defined when the deadline is near is a negotiation, not a gate.

### Definition of done for a change

```text
Definition of done: <project name>
[ ] The change does what its ticket says; the ticket names how it was tested
[ ] php -l passes for every touched PHP file
[ ] Commit subject starts with Added: / Updated: / Fixed: / Removed: / Renamed:
[ ] Tests that cover the change pass; new behavior has a test, or the ticket says why not
[ ] Template changes: no inline CSS; checked in light and dark mode at 1440 px and 960 px at 200 %
[ ] Settings changes are in version control, not written on the server
[ ] Documentation updated where users or administrators will notice the change
[ ] Reviewed by a second person
```

## Acceptance: procedure and checklist

1. **Agree the scenarios early.** Each requirement of the charter gets at least one scenario: "As an editor I create
   an article in two languages and publish it".
2. **Prepare the acceptance site.** A copy with realistic content, the real roles and the real siteaccesses. Give
   each tester an account with the role they will have on the live site, never a shared administrator account.
3. **Run the scenarios.** Testers record pass or fail with the page address and, on an error page, the error
   reference shown at the bottom ([When something goes wrong](../when-something-goes-wrong.md)).
4. **Triage.** Each failure becomes "fix before launch", "fix after launch" or "not a defect". A failure in
   Exponential itself is reported upstream ([Working with the community](working-with-the-community.md)).
5. **Sign off.** The product owner signs the checklist; the project manager files it with the handover document.

```text
Acceptance checklist: <project name>, build <version or commit>, date <date>
Testers: <roles>          Site: <acceptance copy address>

Content and editing
[ ] Every content class of the model can be created, edited, previewed and published by its role
[ ] Versions: a previous version can be viewed and restored
[ ] Translations: every agreed language can be added and published
[ ] Media: images and files upload; alt text can be entered
[ ] Layouts (if used): a layout can be edited, previewed and published

Roles and access
[ ] Each role sees only what it should; a forbidden page shows the access message, not an error
[ ] No tester needed the Administrator role for their daily tasks

Site
[ ] Every siteaccess answers; language switch works; right-to-left languages read right to left
[ ] Search finds new content
[ ] Forms and mail reach their recipients

Quality
[ ] System information shows no failure
[ ] Error digest shows nothing unexplained after the test period
[ ] CSP reports reviewed
[ ] Accessibility checks of your project passed
[ ] Performance baseline saved

Sign-off: <product owner role>   Result: accepted / accepted with listed defects / rejected
```

## Reading a red check

When a workflow of the project is red, [CI](../../bc/6.0/ci.md) tells developers how to find the failed step
(`gh run view <id> --log-failed`) and run the same command on their machine. Points for a manager:

- **A red check on a pull request that conflicts with `main` shows no checks at all.** The fix is to merge `main`
  into the branch, not to re-run anything.
- **A cancelled run on a pull request is not a failure**: a newer push replaced it.
- **The first pull request of a new contributor waits for a maintainer's approval** before any workflow runs.
- **Performance is a warning.** A slower benchmark does not block a change; it asks for a look.

## Related pages

- [Continuous integration](../../bc/6.0/ci.md) and [Quality checks](../../features/6.0/quality-checks.md)
- [Styling the administration](../../bc/6.0/admin4-styling.md), [Content Security Policy](../../features/6.0/content-security-policy.md)
- [Benchmarking](../../guides/benchmarking.md), [System information](../../guides/system-information.md)
- [Release and upgrade planning](release-and-upgrade-planning.md), [Risk management](risk-management.md)
- [Accessibility, security and compliance checklists](accessibility-security-and-compliance-checklists.md)
- [Project templates](project-templates.md), [Running a team on Git](running-a-team-on-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
