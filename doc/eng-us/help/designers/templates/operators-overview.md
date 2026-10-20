---
title: Operators overview
audience: designer, developer
category: templates
order: 170
summary: What template operators are, the two call styles, where operators come from (the template library, the kernel, PHP functions in template.ini, extensions), the families designers use most, and how to find out whether an operator exists on a siteaccess.
keywords: operators, template operators, pipe, operator families, string operators, array operators, url operators, i18n, fetch, extension operators, unknown operator, PHPOperatorList
type: help
---
# Operators overview

This page is for designers who want the big picture of template operators: the functions that compute values in
templates. The [Operator reference](operator-reference.md) lists every one.

## Contents

1. [Two call styles](#two-call-styles)
2. [Where operators come from](#where-operators-come-from)
3. [The families](#the-families)
4. [Does this operator exist here?](#does-this-operator-exist-here)

## Two call styles

```tpl
{$title|upcase}                 {* pipe: input on the left *}
{upcase( $title )}              {* call *}
{$text|shorten( 100, '...' )}   {* pipe with parameters *}
{$items|count|gt( 0 )}          {* chained *}
```

Some operators take no input and are only called: `fetch( ... )`, `array( ... )`, `hash( ... )`, `true()`,
`currentdate()`, `ezini( ... )`.

## Where operators come from

| Source | Registered in | Examples |
|---|---|---|
| The template library | `lib/eztemplate/classes/eztemplateautoload.php` | `array`, `hash`, `count`, `eq`, `cond`, `wash`, `shorten`, `l10n`, `fetch`, `sprintf` |
| The kernel | `kernel/common/eztemplateautoload.php` | `ezurl`, `ezroot`, `ezdesign`, `ezimage`, `ezini`, `ezhttp`, `i18n`, `csp_nonce`, `module_params`, `topmenu` |
| PHP functions by setting | `template.ini [PHP] PHPOperatorList[]` | `urlencode`, `urldecode`, `rawurlencode`, `rawurldecode` |
| Extensions | `extension/<ext>/autoloads/eztemplateautoload.php`, when the extension is active and listed in `site.ini [TemplateSettings] ExtensionAutoloadPath[]` | ezjscore's `ezcss_load`, `json_encode`; ezwebin's `ezpagedata`; layouts' `expl_*` |

## The families

| Family | Page |
|---|---|
| strings: `upcase`, `shorten`, `trim`, `wash`, `concat`, PHP string functions | [String operators](string-operators.md), [PHP string functions as operators](string-template-operators.md) |
| arrays and hashes: `array`, `hash`, `append`, `merge`, `contains`, `implode`, `explode`, `extract` | [Array operators](array-operators.md) |
| logic: `eq`, `ne`, `gt`, `and`, `or`, `not`, `is_set` | [Logic and comparison operators](logic-and-comparison-operators.md) |
| math: `sum`, `sub`, `mul`, `div`, `mod`, `round`, `count` | [Math operators](math-operators.md) |
| choice: `cond`, `first_set`, `choose` | [cond and first_set](cond-and-first-set.md) |
| escaping: `wash` | [Wash and escaping](wash-and-escaping.md) |
| URLs and design files: `ezurl`, `ezroot`, `ezdesign`, `ezimage` | [URL operators](url-operators.md) |
| translation and locale: `i18n`, `l10n`, `datetime`, `currentdate` | [The i18n operator](i18n-operator.md), [Dates, numbers and l10n](dates-numbers-and-l10n.md) |
| settings and request: `ezini`, `ezhttp`, `ezpreference` | [The ezini operator](ezini-operator.md), [The ezhttp operator](ezhttp-operator.md) |
| data: `fetch` | [Fetch basics](fetch-basics.md) |
| assets: `ezcss_load`, `ezscript_load`, `json_encode` | [ezjscore template operators](ezjscore-template-operators.md) |

## Does this operator exist here?

An operator from an extension exists only on siteaccesses where the extension is active. An unknown operator is
reported in the debug output as "Operator '<name>' is not registered", and the template prints nothing for it. The
generated example pages of the help list operators with examples where available. When you depend on an extension's
operator, say so in your theme's README.

## Related pages

- [Operator reference](operator-reference.md)
- [TPL syntax basics](tpl-syntax-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
