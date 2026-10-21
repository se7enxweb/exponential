---
title: Templates and overrides for CMS developers
audience: cms-developer
category: cms-development
order: 23
summary: For CMS developers, how a page is drawn. It covers the pagelayout and the module result, the design chain, override rules that pick a template per class, node, section or view mode, the template language in brief, where your templates live, and how to find the template behind any part of a page.
keywords: templates, TPL, template language, override, override.ini, Match, class_identifier, pagelayout, module_result, design chain, SiteDesign, AdditionalSiteDesignList, design extension, ShowUsedTemplates, template debugging, node/view/full.tpl
type: help
---
# Templates and overrides for CMS developers

This page is for developers who build the presentation of a site. It gives the whole mechanism in one place and
sends you to the designers' and the book's pages for the details. If you only remember one thing: you never edit a
shipped template; you add a template of your own and a rule that says when to use it.

## Contents

1. [How a page is drawn](#how-a-page-is-drawn)
2. [The design chain](#the-design-chain)
3. [Override rules](#override-rules)
4. [The template language in ten lines](#the-template-language-in-ten-lines)
5. [Where your templates live](#where-your-templates-live)
6. [Finding the template behind a page](#finding-the-template-behind-a-page)
7. [Rules for maintainable templates](#rules-for-maintainable-templates)
8. [Where to go deep](#where-to-go-deep)

## How a page is drawn

```text
Request -> siteaccess -> module/view (content/view/full/61)
   view template: node/view/full.tpl  -> override rule -> full/exp_event.tpl  -> $module_result.content
   pagelayout.tpl wraps it: <html>, header, menus, footer, {$module_result.content}
```

Every page is the result of a view, drawn by a view template, placed inside the **pagelayout** (`pagelayout.tpl`),
the outer template of the design. The pagelayout receives the view's result as `$module_result` with the content, the
path and information about the node ([The pagelayout](../designers/theme-design/the-pagelayout.md),
[Module result and content info](../designers/templates/module-result-and-content-info.md)). Pages built with
Exponential Layouts add zones and blocks to this picture ([Layouts for CMS developers](layouts-for-cms-developers.md)).

## The design chain

A template path such as `node/view/line.tpl` is searched in a chain of designs: the siteaccess's `SiteDesign`, then
`AdditionalSiteDesignList[]`, then the standard design, each also inside every active extension that has a `design/`
folder for it. The first match wins. That is how your design extension's templates win over the shipped ones without
touching them ([The design chain](../extension-development/part-2-builder/the-design-chain.md),
[The design cascade](../designers/theme-design/the-design-cascade.md)).

## Override rules

For content, the chain alone is not enough: an article and an event both use `node/view/full.tpl`. Override rules in
`override.ini` choose a different file by condition:

```ini
[exp_mysite_full_event]
Source=node/view/full.tpl
MatchFile=full/exp_event.tpl
Subdir=templates
Match[class_identifier]=exp_event
Priority=10
```

The match keys are `class_identifier`, `class`, `node`, `parent_node`, `parent_class_identifier`, `object`,
`remote_id`, `section`, `section_identifier`, `depth`, `url_alias`, `viewmode` and `view_offset`; all conditions of a
rule must match, and the first matching rule in order wins
([Override basics](../designers/templates/override-ini-basics.md), [Override match keys](../designers/templates/override-match-keys.md),
[Override ordering](../designers/templates/override-ordering.md)). Match on identifiers, not on numeric IDs, when a rule
must work on every installation; `remote_id` is the stable way to target one object.

## The template language in ten lines

```smarty
{* a comment *}
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )}
{if $children|count()}
<ul>
    {foreach $children as $child}
        <li>{node_view_gui content_node=$child view='line'}</li>
    {/foreach}
</ul>
{/if}
{undef $children}
<p>{$node.data_map.intro.content|wash()}</p>
```

Functions use braces (`{if}`, `{foreach}`, `{def}`, `{include}`), operators are piped (`|wash()`, `|ezurl`,
`|count()`), and templates have no access to PHP: anything they need comes from fetch functions, operators and
variables. `wash` escapes output; use it on everything you print that is not already HTML from a datatype
([What the template language is](../template-language/what-the-template-language-is.md),
[Template operators](../template-operators/README.md)). If you know Smarty, read
[Smarty side by side](../template-language/smarty-side-by-side.md).

## Where your templates live

In your design extension, never in `design/standard` or another shipped design:

```text
extension/exp_mysite/
  design/exp_mysite/templates/pagelayout.tpl              your pagelayout
  design/exp_mysite/templates/node/view/line.tpl          a default for a view mode
  design/exp_mysite/override/templates/full/exp_event.tpl a template chosen by a rule
  design/exp_mysite/stylesheets/ ... javascript/ ... images/
  settings/design.ini.append.php                          registers the design in the extension
  settings/siteaccess/<public siteaccess>/override.ini.append.php   the rules
```

Templates hold no `<style>` block and no `style=""` attribute; CSS goes into stylesheets loaded through
`[StylesheetSettings]` ([No inline CSS](../designers/theme-design/no-inline-css.md)). Building the extension step by
step: [Your own design extension](../build-two-ways/your-own-design-extension.md).

## Finding the template behind a page

On a development installation:

```bash
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

The debug output then lists every template a page used and the override rule that chose it, and the page source
names the template of each part in HTML comments
([Finding the template behind a page](../trainers/developers/module-03-designs-overrides-and-debugging/finding-the-template-behind-a-page.md),
[Template path comments](../designers/templates/template-path-comments.md)). Check a template's syntax from the shell
with `./console exp:eztemplatecheck --help`.

## Rules for maintainable templates

- One template per class and view mode; shared parts in `{include}` files.
- Fetch in the view template, not in the pagelayout, unless the data is the same on every page (menus), and then in
  a cache block ([Caching for CMS developers](caching-for-cms-developers.md)).
- Print only washed values or datatype output; never build HTML from user input by concatenation.
- Use `i18n` for fixed strings ([Translations and languages](translations-and-languages.md#template-strings)).
- Keep business logic in operators or fetch functions of your extension, not in long template conditions
  ([Template style guide](../designers/templates/template-style-guide.md)).

## Where to go deep

| Topic | Page |
|---|---|
| The language, completely | [The template language](../template-language/README.md) |
| Every operator | [Template operators](../template-operators/README.md) |
| Designers' template reference | [Templates overview](../designers/templates/templates-overview.md) |
| The builder's chapter | [Templates for builders](../extension-development/part-2-builder/templates-for-builders.md) |
| The course | [Module 2: The template language](../trainers/developers/module-02-the-template-language/README.md) |

## Related pages

- [View modes](view-modes.md)
- [Fetching content in templates](fetching-content-in-templates.md)
- [Templates and design](../../guides/templates-and-design.md): the administrator's guide
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
