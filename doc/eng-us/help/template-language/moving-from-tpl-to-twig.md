---
title: Moving from Exponential templates (.tpl) to Twig templates (.twig)
audience: developer, designer, app-creator
category: templates
order: 56
summary: Exponential renders Twig 3 templates (.twig, .html.twig) itself, next to .tpl and .stpl - when to choose .twig, what changes and what stays (operators as filters and functions, fetch, template functions, includes, caching, the sandbox, escaping), a side-by-side table, the converter in both directions with real output, and checking the result.
keywords: Twig, twig, html.twig, tpl, convert, converter, exp:template:convert:tpl-to-twig, exp:template:convert:twig-to-tpl, port, migrate, expinclude, expcacheblock, exp_ prefix, sandbox, wash
type: help
---
# Moving from Exponential templates (.tpl) to Twig templates (.twig)

A design can hold Twig 3 templates (`.twig`, and `.html.twig` for files shared with Symfony-style projects) next to
its `.tpl` and `.stpl` templates, and the kernel renders them with every operator, fetch function, template function,
override rule and cache of the template system. This chapter is for developers and designers who have `.tpl`
templates and consider writing or converting them to `.twig`, or who have `.twig` and need `.tpl` again. It explains
when Twig is the better choice, what changes and what stays, and the commands that convert in either direction.
Examples were rendered and commands run on a development installation; the output is shown as it was printed.

## When to choose .twig, and when to stay with .tpl

Choose `.twig` when the people who write the templates know Twig (Symfony, Drupal, Craft), when templates are shared
with such a project, or when you want Twig's editor support, template inheritance and macros. Stay with `.tpl` for a
design that works and a team that knows it: every shipped design, every example and every extension's templates are
`.tpl`. Speed does not decide it ([The three template engines compared](../../guides/template-engines.md)).

## What stays the same

- **Finding templates**: the design resource, override rules (`MatchFile=full/article.twig`), the design fallback.
- **Variables**: `node`, `view_parameters`, `module_result` ... with the same attributes (`node.data_map.title.content`).
- **Every operator and fetch function**, with the template system's own code and permissions.
- **Every template function**: `attribute_view_gui`, `node_view_gui` and the others.
- **Caches**: view cache, cache blocks, the HTTP and response caches; the debug bar.
- **Escaping**: nothing is escaped unless the template says so; `wash` escapes.

## What changes

| `.tpl` | `.twig` |
|---|---|
| `{$node.name\|wash}` | `{{ node.name\|wash }}` |
| `{def $x = 1}` / `{set $x = 2}` / `{undef $x}` | `{% set x = 1 %}` / `{% set x = 2 %}` / `{% set x = null %}` |
| `{if $a}...{elseif $b}...{else}...{/if}` | `{% if a %}...{% elseif b %}...{% else %}...{% endif %}` |
| `{foreach $list as $item}...{/foreach}` | `{% for item in list %}...{% endfor %}` |
| `{delimiter}, {/delimiter}` | `{% if not loop.first %}, {% endif %}` |
| `{$list\|count}` | `{{ list\|count }}` (or Twig's `{{ list\|length }}`) |
| `{and( $a, $b )}` | `{{ exp_and(a, b) }}` (or Twig's `{{ a and b }}`) |
| `hash( 'a', 1 )`, `array( 1, 2 )` | `{a: 1}`, `[1, 2]` |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2 ) )` | `fetch('content', 'list', {parent_node_id: 2})` |
| `{attribute_view_gui attribute=$a}` | `{{ attribute_view_gui({attribute: a}) }}` |
| `{include uri='design:parts/x.tpl' a=$b}` | `{% expinclude uri='design:parts/x.tpl' a=b %}` |
| `{cache-block keys=$node.node_id}...{/cache-block}` | `{% expcacheblock keys=[node.node_id] %}...{% endexpcacheblock %}` |
| `{* comment *}` | `{# comment #}` |

Three things to know:

- **Operators are filters and functions.** `x|op(a)` is `{$x|op( a )}`, `op(a)` is `{op( a )}`. Where Twig has a
  filter or function of the same name (`merge`, `round`, `trim`, `replace` ...) or the name is one of Twig's words
  (`and`, `or`, `not`, `true`, `false`, `null`), the operator is `exp_<name>`.
- **The sandbox is on.** Twig's own tags, filters and functions work as `twig.ini` lists them; method calls on objects
  are refused. Read values through attributes, as in a `.tpl`.
- **No `{break}` and `{continue}`** in Twig: the converter writes them as flags; by hand, filter the loop or use
  `{% if %}`.

A small template, side by side, both printing `1:a 2:b 3:c ` for `list` = `['a', 'b', 'c']`:

```
{foreach $list as $i => $item}{$i|inc}:{$item} {/foreach}
```

```twig
{% for item in list %}{{ loop.index }}:{{ item }} {% endfor %}
```

## Converting

```bash
./console exp:template:convert:tpl-to-twig --from=<design> --to=<design>_twig
./console exp:template:convert:twig-to-tpl --from=<design>_twig --to=<design>_tpl
./console exp:template:convert:diff --file=<design>/templates/list.tpl --to-engine=twig
```

For the template

```
{def $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 3 ) )}
<ul>
{foreach $items as $child}
    <li>{$child.name|wash}</li>
{/foreach}
</ul>
{if $items|count|gt( 2 )}{'More'|i18n( 'design/standard/content' )}{/if}
```

the converter writes

```twig
{% set items = fetch('content', 'list', {'parent_node_id': node.node_id, 'limit': 3}) %}
<ul>{% for child in items %}
    <li>{{ child.name|wash }}</li>{% endfor %}
</ul>{% if (items|count)|gt(2) %}
{{ 'More'|i18n('design/standard/content') }}{% endif %}
```

and prints `WRITE` for each file and `PASS tpl->twig 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes
in 0 templates`. Every `.twig` is compiled before it is written, every file ends with a marker comment, and a file
changed by hand is not replaced without `--force`. `--override-ini=<file>` with `-s <siteaccess>` writes the override
rules that make a siteaccess use the converted files. The details, every construct and the notes are in
[The converter and porting a design](../../guides/twig-templates-converter.md).

## Checking

```bash
./console exp:template:check --design-dir=<design>_twig --level=strict
```

compiles every `.twig`, and at the strict level reports unknown operators, includes that do not resolve and what the
sandbox would refuse ([Checking template syntax](../../guides/template-syntax-check.md)).

## Where to go next

- [Twig templates](../../features/6.0/twig-templates.md) and the Twig guides
- [Converting between template engines](converting-between-template-engines.md)
- [Moving from .tpl to .stpl](moving-from-tpl-to-stpl.md)
