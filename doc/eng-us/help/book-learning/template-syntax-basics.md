---
title: "Template syntax basics: comments, delimiters, variables, flow"
audience: builder, designer, developer
category: books
order: 410
summary: The basics of the template language as the book teaches them (comments, braces in JavaScript, variables and their scope, section and if) with what to write today (foreach, def and set, no inline script).
keywords: template syntax, comment, ldelim, rdelim, literal, variable, def, set, let, namespace, section, foreach, if, switch, Learning book
type: help
---
# Template syntax basics: comments, delimiters, variables, flow

The template language is its own: tags in braces, variables with `$`, operators after a pipe. This page is the book's
introduction, made current. The language is described in full in [What the template language is](../template-language/what-the-template-language-is.md); look there for the details.

## Where the data in a template comes from

1. **Navigation** (menus, banners): fetched from the tree, not typed ([fetching content](fetching-content-in-templates.md)).
2. **Applets** (polls, news boxes, feeds): small templates called with parameters, usually a node.
3. **`$module_result.content`**: the main result, drawn by the content view.
4. **What the view fetches itself**: the object with its attributes, a rich structure to use where it fits.

## Comments

```
{* This is a comment. It is dropped when the template is compiled. *}
```

A comment can span lines and hold template code that should not run. Write them: describe what a template is for and the
variables it expects. HTML comments (`<!-- -->`) are sent to the browser and are also parsed by the template engine, so
they can clash with template syntax; prefer `{* *}`.

## Braces: `ldelim`, `rdelim`, `literal`

`{` and `}` start and end a template tag. To print one, use `{ldelim}` and `{rdelim}`. To pass a longer block that has braces
(a JavaScript or CSS block) through untouched, wrap it in `{literal}`:

```
{literal}
function f( p ) { return p; }
{/literal}
```

In Exponential, put script in a file and load it from the design's list of script files, not into the template: the
content security policy blocks inline script ([Content Security Policy for extension authors](../extension-development/part-3-extension-developer/content-security-policy-for-extensions.md)).

## Variables

A variable holds a value set by a function, an operator, a module, or by the template itself. A variable you create in a
template is local to that template file. Create one with `def` (declared for the rest of the file) or `let` (for a block),
change it with `set`:

```
{def $title='Hello'}
{set $title=concat( $title, ' world' )}
{$title|wash}
```

The older form `{let fruit=array(apple,orange)}...{/let}` still works. Variables from `{section}` and `{foreach}`
are in the loop's own namespace (`$:item`, a leading colon, in `{section}`; a plain `$item` in `{foreach}`).
Variables set in an included template do not come back to the including one unless you pass them as parameters.
See [Variables in depth](../template-language/variables-in-depth.md) and [Namespaces](../template-language/namespaces.md).

## Control flow

The book teaches `{section}` for loops, conditions and switches. It still works, and a lot of existing templates use it. For new
templates, write:

```
{foreach $children as $child}
    <li>{$child.name|wash}</li>
{/foreach}

{if $node.depth|gt( 2 )}...{else}...{/if}

{switch match=$node.class_identifier}
    {case match='article'}...{/case}
    {case}...{/case}
{/switch}
```

[Sections versus foreach](../template-language/sections-vs-foreach.md) and the
[Control structures reference](../template-language/control-structures-reference.md) give every parameter.

Next: [fetching content in templates](fetching-content-in-templates.md).

## Source and license

Adapted from chapter 3, "Displaying Content with eZ publish Templates", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; syntax, operators, settings and examples updated to Exponential 6 (foreach, current settings, no inline style or script).
