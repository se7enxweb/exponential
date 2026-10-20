---
title: History of the template language
audience: developer, designer, app-creator
category: development
order: 20
summary: How the template language grew, told only from what the repository records - the git history from 2002, the changelogs from 2.9, the compiler of 2003, the def/if/foreach syntax of 2005 (version 3.6) that replaced let and section, and what still remains of the older style.
keywords: history, template language, eZ TPL, ezpl, section, let, foreach, def, compiler, process cache, 3.6, improved template syntax, deprecated
type: help
---
# History of the template language

The template language is older than most of the code around it. This page tells its story from what the repository
itself records: the git history, the changelogs in `doc/changelogs/`, the feature notes in `doc/en/us/features/` and comments
in the source. Nothing here comes from memory or from outside sources.

## Contents

1. [Where it came from](#where-it-came-from)
2. [The record in the repository](#the-record-in-the-repository)
3. [From process cache to compiler (2003)](#from-process-cache-to-compiler-2003)
4. [The improved syntax (2005, version 3.6)](#the-improved-syntax-2005-version-36)
5. [What the older style looks like today](#what-the-older-style-looks-like-today)
6. [Exponential carries it on](#exponential-carries-it-on)
7. [Related pages](#related-pages)

## Where it came from

Exponential continues the code base that was published for many years as eZ Publish. That product was discontinued by
its creator after almost 18 years; the copyright notice still names both owners of the code: "Copyright (C) 1998 - 2026
7x & Exponential Foundation" and "Copyright (C) eZ Systems AS", the line at the top of
`lib/eztemplate/classes/eztemplate.php`. The template library has been part of the code base for its whole recorded
history, and 7x has carried it forward since taking over the project in 2023 ([Who is 7x?](../who-is-7x.md)).

## The record in the repository

| Source | What it covers | Where |
|---|---|---|
| git history | from the first commit, 2 September 2002 ("Moved all files in root to trunk") | `git log -- lib/eztemplate` |
| Changelogs | from version 2.9 to 5.2, then 6.0.x | [doc/changelogs](../../../../changelogs/README.md) |
| Feature notes | from version 3.5 | `doc/en/us/features/<version>/` |
| Source comments | operators kept for compatibility | `lib/eztemplate/classes/eztemplateautoload.php` |

## From process cache to compiler (2003)

The template compiler started under another name. The commit of 14 May 2003 (`88c1f9460c`) "Renamed the process cache
system to template compiling which is a better name", and the 3.1 changelog notes "Finished the template compiling
system, it will now generate PHP template files that can be properly executed". Two days later (`4fe8ef4f1c`) node
transformation support was added for `let`, `default` and `section`, the hook that still lets a function write its own
PHP code (`templateNodeTransformation()`).

The 3.4 changelog then reports "Implemented template compilation for almost all template operators and functions",
added parameters to `eztc.php` (the command that compiles templates ahead of time) and the `subtree_expiry` option of
`{cache-block}`. The settings that control all of this are still in `settings/site.ini` [TemplateSettings]:
`TemplateCompile`, `TemplateOptimization`, `TemplateCache`, `DevelopmentMode`.

## The improved syntax (2005, version 3.6)

The biggest change to the language itself came with commit `7460c738b2` of 18 March 2005: "Initial implementation of
the new template functions: {def}, {undef}, {if}, {elseif}, {else}, {while}, {do}, {for}, {foreach}, {break},
{continue}, {skip}". Its design note is still in the repository,
[doc/en/us/features/3.6/improved_template_syntax.txt](../../features/3.6/improved_template_syntax.txt). Its stated incentive:
"To make template syntax clearer and more similar to syntax of widespread procedural programming languages."

What it changed:

- **The dollar sign everywhere**, also in assignments: `{set $i=10}`. "Old-style assignments without "$" shall work."
- **def and undef** instead of `{let}...{/let}` blocks, because a block "makes harder to read tpl file and move code
  within it". A variable made with `{def}` lives until the end of its file.
- **if, while, do, for and foreach** as "much simplier to understand and use by developers than the allmighty
  {section} function".
- **break, continue, skip and delimiter** for every loop.

The note also records what `foreach` deliberately does not do: it does not loop over the characters of a string or a
number range (use `for`), and it has no proxy iterator (`$item.index`); `section` keeps those. The 3.6 changelog carries
the first bug reports against "the new template syntax" (#6629) and "the new template loop" (the `{delimiter}` fix),
and the 3.8 changelog the fix that `{undef}` "was able to destroy template variables created with not {def}".

## What the older style looks like today

Both styles still run side by side. The same list, written the old way and the new way, rendered on alpha:

```tpl
{* the older style: let ... /let and section *}
{let items=array( 'red', 'green', 'blue' )}
old: {section name=Colors loop=$items}{$Colors:item}{delimiter}, {/delimiter}{/section}
{/let}
{* the style introduced with def and foreach *}
{def $items = array( 'red', 'green', 'blue' )}
new: {foreach $items as $item}{$item}{delimiter}, {/delimiter}{/foreach}
{undef $items}
```

Output on alpha:

```text
old: red, green, bluenew: red, green, blue
```

Other traces of the history in today's code:

- `lib/eztemplate/classes/eztemplateautoload.php` still registers `array_prepend`, `array_append` and `array_merge`,
  marked `// DEPRECATED/OBSOLETE`, next to their replacements `prepend`, `append` and `merge`.
- `let` and `default` are still registered (`eZTemplateSetFunction`), as is `section` with its `section-else`,
  `section-exclude` and `section-include` children ([Sections versus foreach](sections-vs-foreach.md)).
- The parser still accepts `set`, `let` and `default` parameters without a dollar sign, and treats `name` and `scope`
  written without one as the namespace and scope of the function (`parseSetFunction()` in
  `lib/eztemplate/classes/eztemplatemultipassparser.php`).

## Exponential carries it on

Under 7x the language is maintained as part of the kernel and extended where it helps current sites, always keeping the
templates of existing designs working. Examples of what the 6.0 line added in the template engine and around it: the
`ShowTemplatePathComments` setting that wraps each rendered template in HTML comments naming its file
(`eZTemplate::showTemplatePathComments()`), a warning written once per operator and parameter for a missing default value
(`eZTemplate`), and the `csp_nonce` and `exp_environment` operators of the kernel. The changelog of each release lists
the changes: [doc/changelogs](../../../../changelogs/README.md).

## Related pages

- Previous: [What the template language is](what-the-template-language-is.md)
- Next: [Smarty and the template language side by side](smarty-side-by-side.md)
- [Sections versus foreach](sections-vs-foreach.md)
- [Section (legacy)](../designers/templates/section-legacy.md) for converting old templates
- [Who is 7x?](../who-is-7x.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
