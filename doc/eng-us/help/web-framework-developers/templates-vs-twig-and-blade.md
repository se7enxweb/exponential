---
title: "The view layer: the template language beside Twig and Blade"
audience: web-framework-developer, developer, designer
category: web-frameworks
order: 100
summary: The Exponential template language for developers who know Twig, Blade, Jinja or ERB - syntax side by side, operators instead of infix symbols, no automatic escaping, fetch in the template, the pagelayout instead of extends, includes and view functions, compilation and caches - with the pitfalls and the pages that go deeper.
keywords: template language, tpl, Twig, Blade, Jinja, ERB, Smarty, syntax, wash, escaping, operators, def, foreach, include, pagelayout, layout, extends, compile, cache-block, web framework developer
type: help
---
# The view layer: the template language beside Twig and Blade

This page is for developers who write Twig, Blade, Jinja or ERB templates. Exponential renders HTML with its own
template language: `.tpl` files that `eZTemplate` (`lib/eztemplate/classes/eztemplate.php`) compiles to PHP. It looks
like Smarty at first sight; [Moving a template from Smarty](../template-language/moving-from-smarty.md) and
[What the template language is](../template-language/what-the-template-language-is.md) cover it in depth. Here are
the differences that matter most when you come from another framework.

## Contents

1. [Syntax side by side](#syntax-side-by-side)
2. [Five differences that bite](#five-differences-that-bite)
3. [Layouts](#layouts)
4. [Includes, partials and components](#includes-partials-and-components)
5. [Extending the language](#extending-the-language)
6. [Compilation and caches](#compilation-and-caches)
7. [Can I use Twig instead?](#can-i-use-twig-instead)

## Syntax side by side

| Task | Twig | Blade | Exponential template language |
|---|---|---|---|
| Print, escaped | `{{ title }}` | `{{ $title }}` | `{$title\|wash}` |
| Print raw | `{{ html\|raw }}` | `{!! $html !!}` | `{$html}` |
| Variable | `{% set n = 3 %}` | `@php($n = 3)` | `{def $n = 3}` (new), `{set $n = 4}` (change) |
| Condition | `{% if a > 1 %}` | `@if($a > 1)` | `{if $a\|gt( 1 )}` ... `{/if}` |
| Loop | `{% for p in pages %}` | `@foreach($pages as $p)` | `{foreach $pages as $p}` ... `{/foreach}` |
| Attribute | `page.parent.name` | `$page->parent->name` | `$page.parent.name` |
| Filter or helper | `name\|upper` | `strtoupper($name)` | `$name\|upcase` |
| Arithmetic | `a + b` | `$a + $b` | `sum( $a, $b )` or `$a\|sum( $b )` |
| Translation | `'Save'\|trans` | `__('Save')` | `'Save'\|i18n( 'extension/expdemo' )` |
| URL | `path( 'item', {id: 42} )` | `route( 'item', 42 )` | `'expdemo/item/42'\|ezurl` |
| Comment | `{# ... #}` | `{{-- ... --}}` | `{* ... *}` |

## Five differences that bite

From [Smarty and the template language: the differences in depth](../template-language/smarty-differences-in-depth.md):

1. **No automatic escaping.** Output is never escaped by the engine; write `|wash` on everything that comes from
   content, URLs or request parameters. Rendered rich text is printed through its view template
   (`attribute_view_gui`) and must not be washed again.
2. **No infix arithmetic or comparison.** `{if $a > 1}` does not do what it says; use `gt`, `eq`, `and`, `sum` and the
   other operators.
3. **Unknown operators pass their input through** with a logged error instead of failing the page, so a typo can
   print raw data. Watch the debug output while developing.
4. **`def` and `set` are strict**: `def` creates, `set` changes; misuse writes a warning.
5. **No PHP in templates.** Logic goes into operators, fetch functions and the view.

## Layouts

There is no `{% extends %}` or `@extends`. The siteaccess's **pagelayout** (`pagelayout.tpl`) is the base layout of
every page: the view renders its own template, and the pagelayout prints it with `{$module_result.content}`
([Responses](responses-redirects-and-errors.md#the-pagelayout)). Where Twig would override a block of a parent
template, Exponential replaces a whole template by an **override** chosen by conditions such as the content class,
the node or the view mode ([Template resolution](template-resolution-designs-and-overrides.md)).

## Includes, partials and components

| Need | Use |
|---|---|
| A partial | `{include uri='design:parts/menu.tpl' title=$node.name}`; extra parameters become variables in the included template |
| Render a content item in a view mode (a "component" for content) | `{node_view_gui content_node=$child view='line'}` picks `node/view/line.tpl` and its overrides |
| Render one field | `{attribute_view_gui attribute=$node.data_map.title}` uses the datatype's view template |
| Keep variables apart | namespaces, set by the view functions ([Namespaces](../template-language/namespaces.md)) |

## Extending the language

| Twig or Blade | Exponential |
|---|---|
| Twig filter or function, Blade directive | a template operator: a PHP class registered in `extension/<ext>/autoloads/eztemplateautoload.php` ([Template operators](../creators/app-creators/template-operators.md)) |
| Twig tag | a template function ([Custom template functions](../template-language/custom-template-functions.md)) |
| View composer or a Twig global from a service | a fetch function (`fetch( 'expdemo', 'stats', hash() )`) ([Getting data into templates](fetch-functions-and-template-data.md)) |

## Compilation and caches

Templates are compiled to PHP files under the cache directory and rebuilt when the source changes
([Template compilation](../template-language/template-compiler.md)). Content views are additionally kept in the view
cache, and `{cache-block}` stores finished HTML fragments ([Template caches](../template-language/template-caches.md),
[Caching](caching-layers.md)). When a template "does not change", the cause is almost always one of these caches.

## Can I use Twig instead?

Not in the legacy kernel alone. Twig is available when the kernel runs inside the Symfony platform through the legacy
bridge: the public site can render with Twig while editors keep the legacy administration, and the `ngsymfonytools`
extension lets a `.tpl` template include a Twig template or render a Symfony controller
([Symfony legacy bridge](symfony-and-the-legacy-bridge.md), [Platform site bundles](../../features/6.0/platform-site-bundles.md)).

## Related pages

- [The template language: index](../template-language/README.md)
- [Smarty and the template language side by side](../template-language/smarty-side-by-side.md)
- [Wash and escaping](../designers/templates/wash-and-escaping.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
