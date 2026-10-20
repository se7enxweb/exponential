---
title: Template overrides for site builders
audience: website-creator, developer
category: website-creation
order: 120
summary: Give one class, one section, one node or one view mode its own template with an override rule - by click path in the template editor or as files in your design extension - and the match keys you can use.
keywords: override, override.ini, template override, Match, class_identifier, section, node, viewmode, MatchFile, Source, template editor, full view, line view, website creator
type: help
---
# Template overrides for site builders

This page is for website creators who want a template for a particular kind of page: articles look one way, products
another, the front page a third. An **override** is a rule in `override.ini` that says "for pages matching these
conditions, use this file instead of the standard template". The step-by-step tutorial is
[4. Make a change with an override](../../../guides/templates-and-design.md#4-make-a-change-with-an-override); this page
is the site builder's reference.

## Contents

1. [The parts of a rule](#the-parts-of-a-rule)
2. [Match keys](#match-keys)
3. [A full and a line template per class](#a-full-and-a-line-template-per-class)
4. [Click path or files](#click-path-or-files)
5. [Order: which rule wins](#order-which-rule-wins)
6. [Checklist](#checklist)

## The parts of a rule

```ini
# settings/siteaccess/<site>/override.ini.append.php
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

| Key | Meaning |
|---|---|
| `[full_article]` | the block name; any unique name |
| `Source` | the template being replaced |
| `MatchFile` | your file, relative to `design/<design>/override/templates/` |
| `Subdir` | always `templates` |
| `Match[...]` | the conditions; all must hold |
| `Priority` | optional; rules with a priority come first, the lowest number first |

## Match keys

For node views, the kernel offers these keys (`kernel/classes/eznodeviewfunctions.php`):

| Key | Example | Use it for |
|---|---|---|
| `class_identifier` | `article` | a template per class (the most common) |
| `class` | `2` | the same by class ID |
| `node` | `2` | one location (the front page) |
| `object` | `1234` | one object, at all its locations |
| `parent_node` | `58` | everything directly in one folder |
| `section` / `section_identifier` | `members` | everything in a section |
| `state` / `state_identifier` | an object state | content in a workflow stage |
| `depth` | `2` | top-level pages |
| `url_alias` | `About-us` | one address |
| `remote_id`, `node_remote_id` | | content created by imports, stable across installations |
| `viewmode` | `line` | a view mode, for templates not keyed by `Source` |
| `class_group` | | all classes of a class group |
| `navigation_part_identifier` | | admin navigation parts |

Prefer `class_identifier`, `section_identifier`, `state_identifier` and `node_remote_id` over IDs: identifiers mean the
same on every installation, IDs do not.

## A full and a line template per class

Most sites need two templates per class: **full** (the page of the item) and **line** (the item in a list).

```ini
[full_event]
Source=node/view/full.tpl
MatchFile=full/event.tpl
Subdir=templates
Match[class_identifier]=event

[line_event]
Source=node/view/line.tpl
MatchFile=line/event.tpl
Subdir=templates
Match[class_identifier]=event
```

`design/<design>/override/templates/full/event.tpl`:

```text
<article class="event">
  <h1>{$node.name|wash}</h1>
  {attribute_view_gui attribute=$node.data_map.start_date}
  {attribute_view_gui attribute=$node.data_map.description}
</article>
```

`design/<design>/override/templates/line/event.tpl`:

```text
<li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
```

A folder's full template shows its children with `{node_view_gui content_node=$child view='line'}`, so the line
override is picked up wherever events are listed.

## Click path or files

| | Template editor (click path) | Files in your design extension |
|---|---|---|
| Where | **Design > Template Editor** | `override/templates/` and `settings/siteaccess/<site>/override.ini.append.php` |
| Writes | the rule in the siteaccess's `override.ini.append.php` and the template file | what you write |
| Good for | trying, one-off changes | anything you keep: it is in Git and deploys with the extension |
| Order | drag and drop | the order of the blocks, or `Priority` |

The template editor's features (default copy, match by one object, remove that cleans up) are described in
[Template editor](../../../features/6.0/template-editor-overrides.md).

## Order: which rule wins

When several rules match, the first wins. Rules with a `Priority` come before rules without one; among those, the
lowest number first. Rules in extensions are ranked by extension order. Put specific rules (one node) before general
ones (one class). [Template editor: reorder](../../../features/6.0/template-editor-overrides.md#reorder-by-drag-and-drop-september-2026),
[Extension loading order](../../../features/6.0/extension-loading-order.md).

## Checklist

- [ ] The template passes `php bin/php/eztemplatecheck.php -s<site> <file>`.
- [ ] After a new rule or file: `--clear-id=template-override`, `--clear-tag=template` and `--clear-tag=content`.
- [ ] Every user-typed value is printed with `|wash`.
- [ ] No inline `<style>` or `<script>` blocks: stylesheets and scripts go through the design's lists
      ([Stylesheets and scripts for your site](stylesheets-and-scripts.md)).
- [ ] With template path comments on, the page source shows your file for the page.

## Related pages

- [Designs, siteaccesses and the design fallback chain](designs-and-the-fallback-chain.md)
- [The pagelayout: your site's frame](the-pagelayout.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Extension module override](../../../features/6.0/extension-module-override.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
