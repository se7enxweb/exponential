---
title: Learning the template language
audience: student
category: learning
order: 22
summary: A study plan for Exponential's template language (eZ TPL) - what it is, a first template to type, the five constructs to learn first, a reading order through the template language and designer pages, practice drills on your study site, and the mistakes learners make most.
keywords: template language, eZ TPL, tpl, templates, def, foreach, if, wash, fetch, attribute_view_gui, Smarty, study plan, exercises, learner
type: help
---
# Learning the template language

Exponential renders every page, mail and PDF with its own template language, known as eZ TPL: brace tags like
Smarty's, with operators and functions for content objects, a fetch API to read data, an override system to choose
templates, and a compiler that turns templates into PHP ([The template language](../template-language/README.md)).
Designers and developers both need it. This page gives you a study plan.

## Contents

1. [The five constructs to learn first](#the-five-constructs-to-learn-first)
2. [Your first template](#your-first-template)
3. [A reading order](#a-reading-order)
4. [Drills](#drills)
5. [Mistakes learners make](#mistakes-learners-make)
6. [If you know Smarty](#if-you-know-smarty)

## The five constructs to learn first

| Construct | Example | Meaning |
|---|---|---|
| Output with escaping | `{$node.name\|wash}` | Print a value; `wash` escapes HTML. Use it on anything a user typed |
| Variables | `{def $items = ...}`, `{set $items = ...}`, `{undef $items}` | Declare once, change later, free |
| Data | `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )` | Read content; `hash` builds named values |
| Loops and conditions | `{foreach $items as $child}...{/foreach}`, `{if $node.depth\|gt( 2 )}...{/if}` | Operators are written as pipes: `gt`, `lt`, `eq`, `and`, `or`, `not` |
| Showing content | `{attribute_view_gui attribute=$node.data_map.title}`, `{node_view_gui content_node=$child view='line'}` | Let each attribute and node use its own template |

Source: [Templates and design, part 6](../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes).
Comments are written `{* ... *}`. Links use `ezurl`, written without quotes in an attribute:
`<a href={$child.url_alias|ezurl}>`.

## Your first template

The templates guide gives a small template to try in an override (it lists the children of the current item's
parent, ten at most):

```text
<h1>{$node.name|wash}</h1>
{def $siblings = fetch( 'content', 'list', hash( 'parent_node_id', $node.parent_node_id, 'limit', 10 ) )}
<ul>
{foreach $siblings as $sibling}
    <li><a href={$sibling.url_alias|ezurl}>{$sibling.name|wash}</a></li>
{/foreach}
</ul>
{undef $siblings}
```

Expected: the heading followed by a list of links. Put it in the override of
[project 3](project-03-a-template-override.md), then explain each line in your
[study journal](keeping-a-study-journal.md).

## A reading order

Start task-oriented, then go deep:

1. [Templates overview](../designers/templates/templates-overview.md) and
   [TPL syntax basics](../designers/templates/tpl-syntax-basics.md), the gentle introduction.
2. [Templates and design](../../guides/templates-and-design.md), parts 1 to 6: how a template is found, overrides,
   syntax checks.
3. [What the template language is](../template-language/what-the-template-language-is.md).
4. [Values and literals](../template-language/values-and-literals.md) and
   [Variables in depth](../template-language/variables-in-depth.md).
5. [Control structures reference](../template-language/control-structures-reference.md).
6. [The object model in templates](../template-language/object-model.md) and
   [Data map and content](../template-language/data-map-and-content.md).
7. [Errors and debugging](../template-language/errors-and-debugging.md).
8. [Template resources and overrides](../template-language/resources-and-overrides.md) and
   [Template caches](../template-language/template-caches.md).
9. The operator reference as you need it: [Template operators](../template-operators/README.md).

The trainers' [Developers](../trainers/developers/README.md) course has a whole module on the language,
[module 2](../trainers/developers/module-02-the-template-language/README.md), with exercises and quizzes.

## Drills

Short exercises, each on your study site, in an override you can remove afterwards:

1. Print the name and the node number of the current item.
2. List the first five children of the item with links. Then show "No items" when there are none.
3. Show the title attribute with `attribute_view_gui`, then print `$node.data_map.title.content` and compare.
4. Use `{delimiter}` to put commas between names in a loop.
5. Put a deliberate error in a template (an unknown operator), check it with
   `php bin/php/eztemplatecheck.php`, then read what the engine logs
   ([Errors and debugging](../template-language/errors-and-debugging.md)).

## Mistakes learners make

| Mistake | Result | Fix |
|---|---|---|
| Forgetting `wash` | Text with `<` or quotes can break the page or open a security hole | `\|wash` on every value from content or visitors |
| `{def}` the same variable twice | A warning | `{def}` once, `{set}` afterwards |
| Editing `design/standard/templates/` | Lost at the next upgrade | An override in your own design |
| Expecting arithmetic like `$a + 1` | Does not work | Operators such as `sum`; see [the differences from Smarty](../template-language/smarty-differences-in-depth.md) |
| A new template path without clearing caches | The old template is used | Clear the override and template caches once |

## If you know Smarty

Read [Smarty and the template language side by side](../template-language/smarty-side-by-side.md) and
[the differences in depth](../template-language/smarty-differences-in-depth.md): no infix arithmetic or comparison,
no PHP in templates, no automatic escaping, and overrides instead of inheritance.

## Related pages

- [Project 3: a template override](project-03-a-template-override.md)
- [Learning path: designer](learning-path-designer.md)
- [Learning path: developer](learning-path-developer.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
