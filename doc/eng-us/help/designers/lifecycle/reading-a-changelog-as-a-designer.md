---
title: "Reading a changelog as a designer"
audience: designer
category: theme-lifecycle
order: 50
summary: What to look for in an Exponential changelog when you maintain a theme - templates you override, design settings, operators, JavaScript libraries, security headers, PHP versions, accessibility - with real examples from the 6.0.15 changelog.
keywords: changelog, reading, impact, templates, operators, JavaScript, CSP, PHP version, accessibility, 6.0.15
type: help
---
# Reading a changelog as a designer

This page is for designers scanning a changelog. Most entries do not concern a theme; a few matter a lot.

## What to look for

| Look for | Why | Example |
|---|---|---|
| Templates you override | Your copy misses the fix or new markup | A fix in a standard template you copied into your design |
| Template operators and functions | New, changed or removed operators | An operator your templates call |
| Design settings (`design.ini`, `site.ini [DesignSettings]`) | Loading of stylesheets and scripts | `design.ini` marks `CSSFileList[]` deprecated in favor of `FrontendCSSFileList[]` |
| JavaScript libraries | Your scripts may rely on old APIs | jQuery 4 and the removal of YUI (1 and 2 October 2026) |
| Security headers and the Content Security Policy | Inline scripts may be reported or blocked | The administration's CSP, report-only by default |
| PHP versions | Your PHP code must run on them | "PHP 8.0 is the oldest supported version" (5 October 2026), then PHP 7.4 (29 October 2026), then PHP 7.4 (29 October 2026) |
| Accessibility changes | Expectations of the product rise | Contrast or focus changes in the shared components |

## Step by step

1. Open the changelog of the release ([Following Exponential releases](following-exponential-releases.md)).
2. Search it for the words in the first column, and for the names of templates you override:

   ```bash
   grep -n -i -E "template|design|jquery|yui|csp|content security|php 8" doc/changelogs/6.0/6.0.15.md | head -50
   ```

3. For each hit, decide: no impact, test needed, change needed.
4. Record the decisions in an issue; it becomes part of your release notes later.

## Related pages

- [Comparing your overrides with the standard templates](comparing-overrides-with-the-standard.md)
- [JavaScript library upgrades](javascript-library-upgrades.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
