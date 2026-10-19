---
title: Template style guide
audience: designer, developer
category: templates
order: 340
summary: House style for Exponential templates: a header comment, prefixed variables and undef, wash at output, i18n contexts, fetch once with limits, small parts, no inline CSS or JavaScript, semantic and accessible markup, and HTML5 without XHTML slashes.
keywords: style guide, template conventions, coding standards templates, naming, indentation, header comment, prefixes, undef, wash, i18n context, html5, best practices
type: help
---
# Template style guide

This page is for designers and developers who want templates that others can read and maintain. It collects the habits
the shipped designs follow.

## Contents

1. [Files](#files)
2. [Variables](#variables)
3. [Output](#output)
4. [Data](#data)
5. [Markup](#markup)
6. [Formatting](#formatting)

## Files

- One purpose per template; split long templates into parts under a folder of your own (`pagelayout/`, `parts/`).
- A header comment: what the template draws, which parameters it takes, any non-obvious decision
  ([Comments](comments.md)).
- Paths and names in lowercase with `_` or `-`; class identifiers in file names (`full/ng_article.tpl`).

## Variables

- `{def}` near the top, `{undef}` at the end of parts.
- Prefix the variables of parts (`$tz_`, `$pl_`) so they never clash with the includer's.
- Optional parameters with `{default ...}` and documented in the header comment.

## Output

- `|wash` every value from content, settings or the request, at output time ([Wash and escaping](wash-and-escaping.md)).
- Every interface text through `i18n` with a context `design/<theme>/<area>` ([The i18n operator](i18n-operator.md)).
- URLs only through `ezurl`, `ezroot`, `ezimage`, `ezdesign` ([URL operators](url-operators.md)).

## Data

- Fetch once, with a `limit`, outside loops ([Fetch performance](fetch-performance.md)).
- Test optional fields with `has_content` before drawing them.
- Prefer `content_info` values in the pagelayout over fetching the node again.
- Cache-blocks with explicit keys around expensive parts ([Cache-block](cache-block.md)).

## Markup

- HTML5: no XHTML slashes (`<br>`, not `<br />`), no `type` on `<script>` and `<style>` links (the ezwebin 6.0.3 release
  removed them from 57 files).
- Semantic elements (`<article>`, `<nav>`, `<main>`, `<time>`), one `h1`, headings in order.
- No `<style>`, no `style=""`, no inline scripts or `on*=` handlers ([CSP-safe design](../theme-design/csp-safe-design.md)).
- Classes describe the component, not the look (`.teaser-title`, not `.big-red`).

## Formatting

- Indent nested tags by four spaces, as the shipped templates do.
- Keep template tags and HTML on separate lines when they nest deeply; whitespace in templates becomes whitespace in
  the output, which is harmless in HTML.
- Long operator calls broken over lines with aligned parameters (the media and starter designs do this for
  `ezcss_load` lists).

## Related pages

- [Common template mistakes](common-template-mistakes.md)
- [Checklist: code review for templates](../theme-design/checklist-code-review-for-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
