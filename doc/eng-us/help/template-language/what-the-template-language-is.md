---
title: What the template language is
audience: developer, designer, app-creator
category: development
order: 10
summary: The Exponential template language (eZ TPL, ezpl) in one page - a Smarty-like language for .tpl files, where it is used (designs, module views, mail, PDF), the parts of the engine in lib/eztemplate, and what it does beyond Smarty out of the box.
keywords: template language, eZ TPL, ezpl, tpl, Smarty, eZTemplate, lib/eztemplate, parser, compiler, design resource, overview
type: help
---
# What the template language is

Every page Exponential renders, every mail it sends from a template and every PDF export is written in its own
template language. In the code it is the `eZTemplate` library in `lib/eztemplate/`, the files end in `.tpl`, and people
call it **eZ TPL** or **ezpl**. In these pages it is "the template language".

It looks like Smarty: tags in curly braces, variables with `$`, values piped through `|`. Underneath it was built for a
content management system, so it has a large set of registered operators and functions, it reads PHP objects through an
attribute protocol, it fetches content itself with `fetch()`, and it chooses which template to use through the
override system instead of template inheritance.

## Contents

1. [A first look](#a-first-look)
2. [Where templates are used](#where-templates-are-used)
3. [The parts of the engine](#the-parts-of-the-engine)
4. [Beyond Smarty, out of the box](#beyond-smarty-out-of-the-box)
5. [How the examples in this section were made](#how-the-examples-in-this-section-were-made)
6. [Related pages](#related-pages)

## A first look

A template mixes text with tags. This one fetches the "Fit & Healthy" front page of alpha (node 89), prints its name
escaped for HTML and lists its first four children:

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
<h2>{$site.name|wash}</h2>
<p>{$site.children_count} pages below, class {$site.class_identifier}</p>
<ul>
{foreach $site.children as $child max 4}
  <li>{$child.name|wash} ({$child.class_identifier})</li>
{/foreach}
</ul>
```

Output on alpha:

```text
<h2>Fit &amp; Healthy</h2>
<p>16 pages below, class ng_frontpage</p>
<ul>  <li>Fitness (ng_landing_page)</li>  <li>Healthy eating (ng_landing_page)</li>  <li>Recipes (ng_category)</li>  <li>Running (ng_topic)</li></ul>
```

Four kinds of things appear in it:

- **Functions** are tags with a name: `{def ...}`, `{foreach ...}{/foreach}`. They control the template: they create
  variables, loop, branch, include other templates.
- **Variables** start with `$`: `$site`, `$child`.
- **Attributes** follow a dot: `$site.name`, `$child.class_identifier`. The value can be an array or a PHP object.
- **Operators** compute a value: `fetch( ... )`, `hash( ... )`, `|wash`. They are written either as a call or after a pipe.

## Where templates are used

| Where | How a template is reached | Example in the code |
|---|---|---|
| Designs (the site and the admin) | `design:` URIs resolved through the design bases and the override system | `kernel/common/eztemplatedesignresource.php` |
| Module views | a view's PHP sets variables and fetches a template, the result becomes `$module_result.content` in the pagelayout | `kernel/content/view.php` |
| Mail | the same engine renders the mail text | `kernel/user/ezuseroperationcollection.php` fetches `design:user/registrationinfo.tpl` |
| Notifications | handlers render their digest and subtree mails | `kernel/classes/notification/handler/ezsubtree/ezsubtreehandler.php` |
| PDF export | the `pdf` operator and the `content_pdf_gui` and `attribute_pdf_gui` functions | `lib/ezpdf/classes/ezpdf.php`, `kernel/content/pdf.php` |

The same engine object serves all of them: `eZTemplate::factory()` (in `lib/eztemplate/classes/eztemplate.php`) creates it
once per request, loads the operator and function lists, and registers the design resource.

## The parts of the engine

| Part | File | What it does |
|---|---|---|
| The engine | `lib/eztemplate/classes/eztemplate.php` | variables and namespaces, resources, registered operators and functions, processing |
| Parser | `lib/eztemplate/classes/eztemplatemultipassparser.php`, `eztemplateelementparser.php` | splits the text into text, tags and comments, removes whitespace around tags, builds the node tree |
| Node tree | `eztemplateroot.php`, `eztemplatetextelement.php`, `eztemplatevariableelement.php`, `eztemplatefunctionelement.php`, `eztemplateoperatorelement.php` | the parsed template in memory |
| Compiler | `lib/eztemplate/classes/eztemplatecompiler.php`, `eztemplateoptimizer.php`, `eztemplatenodetool.php` | turns the tree into PHP code once, so later requests only include a PHP file |
| Resources | `eztemplatefileresource.php`; `kernel/common/eztemplatedesignresource.php` | `file:` reads a path; `design:` searches the design bases and applies overrides |
| Tree cache | `lib/eztemplate/classes/eztemplatetreecache.php` | keeps parsed trees ([TemplateSettings] NodeTreeCaching) |
| Operators | `lib/eztemplate/classes/eztemplate*operator.php`, `kernel/common/`, extensions | one PHP class per family, listed in an `eztemplateautoload.php` |
| Functions | `lib/eztemplate/classes/eztemplate*function.php`, `kernel/common/ezobjectforwarder.php` | `if`, `foreach`, `def`, `include`, `cache-block`, the `*_view_gui` functions |

The library has no dependency on the content model: it only knows text, values and objects that answer `attributes()`,
`hasAttribute()` and `attribute()`. The kernel adds the content-aware parts: the design resource, the `fetch` functions
of each module, and the view functions such as `node_view_gui`.

## Beyond Smarty, out of the box

What the language offers without any extension installed, with the page that covers it:

- **Several hundred registered operators.** On alpha's `site` siteaccess the engine registers 395 operators and 48
  functions (counted by loading every entry of the operator and function lists). The template library alone brings
  arithmetic, arrays, strings, the PHP string functions, logic, types, locale, digests and roles.
- **Data access in the template.** `fetch( 'content', 'list', hash( ... ) )` reads content with filters, sorting and
  permissions, and every module can offer its own fetch functions.
- **Objects with attributes.** A node is a PHP object, yet `$node.parent.name` or `$node.data_map.title.content` reads
  like an array. See [Attribute access](attribute-access.md).
- **The override system** picks a template by class, section, node, view mode and more, instead of templates extending
  each other ([Override basics](../designers/templates/override-ini-basics.md)).
- **Namespaces** keep the variables of included templates apart ([Namespaces](namespaces.md)).
- **A compiler** that writes PHP for each template and **cache blocks** that store finished HTML
  ([Template compilation and cache](../designers/templates/template-compilation-and-cache.md)).
- **Translation and localization** built in: `i18n`, `l10n`, `datetime`.

A second example uses several of these on one article (node 90, "Privacy Policy"):

```tpl
{def $article = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
Fields: {$article.data_map|count}
Title field: {$article.data_map.title.content|wash}
Parent: {$article.parent.name|wash}
Path: {foreach $article.path as $step}{$step.name|wash} / {/foreach}{$article.name|wash}
URL: {$article.url_alias|ezurl( 'no', 'relative' )}
Can the visitor read it: {if $article.can_read}yes{else}no{/if}
```

Output on alpha:

```text
Fields: 18
Title field: Privacy Policy
Parent: Fit &amp; Healthy
Path: Websites / Fit &amp; Healthy / Privacy Policy
URL: /privacy-policy
Can the visitor read it: yes
```

## How the examples in this section were made

Every example in the template language pages was rendered on alpha, the reference installation, by the real engine
against real content, and the output under it is what the engine printed. The examples are kept outside the design tree
and read with the `file:` resource; compiling and template caching were switched off for those runs, and each example
was rendered a second time through the compiler to check that both give the same output. Where an example shows an
error on purpose, the page also shows what the engine logged.

## Related pages

- Next: [History of the template language](history.md)
- [Templates overview](../designers/templates/templates-overview.md) for designers
- [TPL syntax basics](../designers/templates/tpl-syntax-basics.md)
- [Operator reference](../designers/templates/operator-reference.md) and [Function reference](../designers/templates/function-reference.md)
- [Every template operator](../../examples/template-operators/README.md) and [every template function](../../examples/template-functions/README.md), generated from the code
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
