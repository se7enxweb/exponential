---
title: The template engine and the design chain
audience: software-engineer, developer, designer
category: software-engineering
order: 170
summary: How Exponential renders HTML - the eZTemplate engine that compiles .tpl files to PHP, operators and functions registered from settings, fetch functions as the only data access, the design chain and override rules that pick a template, cache blocks, escaping, and the review rules for templates.
keywords: template engine, eZTemplate, tpl, compiled templates, operators, template functions, fetch, design chain, override.ini, Priority, cache-block, wash, escaping, pagelayout, software engineer
type: help
---
# The template engine and the design chain

This page is for engineers who need to understand how a page is rendered, how a template is chosen, and what can go
wrong there. The template language is Exponential's own; it is not Twig or Smarty, although its brace syntax looks
similar. The authoritative rules are in [Kernel architecture](../../standards/architecture/kernel-architecture.md#the-template-engine-operators-and-functions).

## Contents

1. [The engine](#the-engine)
2. [Data access from templates](#data-access-from-templates)
3. [The design chain](#the-design-chain)
4. [Overrides](#overrides)
5. [Caching inside templates](#caching-inside-templates)
6. [Debugging rendering](#debugging-rendering)
7. [Review rules](#review-rules)

## The engine

`eZTemplate` (`lib/eztemplate/classes/eztemplate.php`) compiles `.tpl` files to PHP and runs the compiled files
(`site.ini [TemplateSettings] TemplateCompile=enabled` shipped). Operators (`|wash`, `ezurl`, `i18n`) and functions
(`{def}`, `{foreach}`, `{include}`, `{cache-block}`) are PHP classes registered in `eztemplateautoload.php` files as
entries of `$eZTemplateOperatorArray` and `$eZTemplateFunctionArray`. The engine loads them from
`[TemplateSettings] AutoloadPathList[]` and from the extensions listed in `ExtensionAutoloadPath[]`.

The language reference is in the help: [Template language](../template-language/README.md) and
[Template operators](../template-operators/README.md).

## Data access from templates

Templates do not query the database: the language has no SQL construct. Data comes from:

- **the view's variables**, set by the module view before rendering;
- **fetch functions**: `fetch( 'content', 'list', hash( ... ) )`, mapped in `kernel/<module>/function_definition.php`
  and executed by `eZFunctionHandler`. A fetch that returns content applies the user's permissions;
- **template operators**, which are PHP and can do anything, so they are reviewed like views.

## The design chain

A design is a directory of templates, stylesheets and images. Designs are stacked so a site contains only what differs
from the base: `site.ini [DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` and `StandardDesign`
(`standard`). Extensions add design directories through `design.ini [ExtensionSettings] DesignExtensions[]`.
`eZTemplateDesignResource` builds the chain: extension design directories first, then `design/<design>/`.

## Overrides

`override.ini` blocks choose another template for a view when conditions match: `Source`, `MatchFile`, `Subdir` and
`Match[...]` (for example the class identifier or the node). Since July 2026 overrides are ordered by `Priority`, lower
first ([Template override ordering](../../features/6.0/template-override-ordering.md)). The administration page
Design > Templates writes only `settings/siteaccess/<sa>/override.ini.append.php`.

A site or extension overrides a kernel template through `override.ini` or its own design directory; it must not edit
files in `design/standard/` or `design/admin*/`.

## Caching inside templates

| Cache | What | Cleared by |
|---|---|---|
| Compiled templates | the PHP compiled from each `.tpl` | `php bin/php/ezcache.php --clear-tag=template --allow-root-user` |
| Template override cache | the resolved override rules | `--clear-id=template-override`; a new template file is not found without it |
| Cache blocks | the output of `{cache-block}` sections (`[TemplateSettings] TemplateCache=enabled`) | expiry, keys, `--clear-id=template-block` |
| Content view cache | the whole rendered content view | content changes; `--clear-tag=content` |

See [Cache invalidation and clearing](cache-invalidation-and-clearing.md).

## Debugging rendering

- **Template path comments** wrap each rendered template in HTML comments naming its file
  ([Template path comments](../../features/6.0/template-path-comments.md)).
- The **debug bar** lists the templates used by a page ([The Exp Debug bar](../../features/6.0/exp-debug-bar.md)).
- `./console exp:eztemplatecheck` (`bin/php/eztemplatecheck.php`) checks templates for syntax errors.

## Review rules

- Every variable from content or input is printed with `|wash`, or through an operator that escapes.
- An operator that builds HTML in PHP escapes it there.
- No SQL in templates, and no raw database handle passed to one.
- In the administration designs: no `<style>` block or `style=""` attribute, and no inline script code; this is
  checked by tests in `tests/tests/design/` ([Styling the administration](../../bc/6.0/admin4-styling.md)).
- Scripts that must stay inline carry the Content Security Policy nonce: `<script nonce="{csp_nonce()}">`.

## Related pages

- [Templates and design](../../guides/templates-and-design.md)
- [View results and the pagelayout](../creators/app-creators/view-results-and-the-pagelayout.md)
- [The design chain](../extension-development/part-2-builder/the-design-chain.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
