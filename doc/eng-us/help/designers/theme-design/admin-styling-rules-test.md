---
title: The admin styling rules test
audience: designer, developer
category: theme-design
order: 570
summary: What tests/tests/design/AdminStylingRulesTest.php checks in every administration design (no inline CSS, tokens only, dark values, focus outlines, stacking layers, identical component copies), how to run it, and how to fix what it reports.
keywords: AdminStylingRulesTest, styling test, design test, phpunit design suite, inline css test, token test, dark value test, admin css rules, CI design check
type: help
---
# The admin styling rules test

This page is for designers and developers who change an administration template or stylesheet. The rules of the admin
designs are not only written down; a test enforces them in continuous integration. The page answers: what it checks,
how to run it, and what to do when it fails.

## Contents

1. [What it checks](#what-it-checks)
2. [Running it](#running-it)
3. [Fixing a failure](#fixing-a-failure)
4. [Related tests](#related-tests)

## What it checks

`tests/tests/design/AdminStylingRulesTest.php` (in the PHPUnit testsuite `design`, no database needed) scans the
templates and stylesheets of the designs `admin4`, `admin4l`, `admin3` and `admin`. Among its checks:

| Test | Fails when |
|---|---|
| `testAdminTemplatesCarryNoInlineCss` | a template has a `<style>` block or a `style=""` attribute (outside the documented exceptions) |
| `testAllowlistOnlyShrinks` | a file on the migration allowlist no longer needs to be there (the list is empty today) |
| `testModuleStylesheetsUseOnlyTokens` | a file in `stylesheets/modules/` has a hard-coded color or radius |
| `testEveryColourTokenHasADarkValue` | a color token has no value under `html[data-a4-theme="dark"]` |
| `testBaseStylesheetsUseOnlyColourTokens`, `testPageStylesheetsUseOnlyColourTokens` | a base or page stylesheet uses a color literal |
| `testNoFocusRuleRemovesTheOutline` | a rule removes the focus outline without a replacement |
| `testStackingComesFromTheLayerTokens` | a `z-index` is a number instead of a `--a4-z-*` token, or the layer order changed |
| `testDataTablesShareOneZebra`, `testEmptyListsUseTheEmptyState` | tables or empty lists drift from the shared look |
| `testComponentsAreTheSameInAdminAndAdmin4` | the two copies of `components.css` differ |
| `testEveryListedStylesheetExists` | a stylesheet named in the settings is missing |

The one exception today is `design/admin/templates/pagelayout.tpl` (the editor's column widths before the page is
drawn), listed with its reason in the test.

## Running it

```bash
php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
# the whole design suite
php vendor/bin/phpunit --testsuite design
```

## Fixing a failure

Fix the template or stylesheet, never the test:

- **Inline CSS**: move the rule into a stylesheet ([No inline CSS](no-inline-css.md)); a per-element value becomes a
  class (for percentages `.a4-pct-0` to `.a4-pct-100`) or a `data-*` attribute.
- **A color literal**: use the token with that role ([admin4 tokens](admin4-tokens.md)).
- **No dark value**: add it to the dark block in both copies of `components.css`.
- **A z-index number**: use the layer token for that purpose.

Weakening the test or adding an allowlist entry to make a change pass is not accepted (project rule).

## Related tests

The design suite has more, for example `AdminContentSecurityPolicyTest` (inline scripts and handlers; see
[CSP-safe design](csp-safe-design.md)), `AdminTokenContrastTest` (text contrast on rows), `AdminResponsiveTest` and
`AdminCardsTest`.

## Related pages

- [Admin designs overview](admin-designs-overview.md)
- [admin4 module stylesheets](admin4-module-stylesheets.md)
- [Checklist: code review for templates](checklist-code-review-for-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
