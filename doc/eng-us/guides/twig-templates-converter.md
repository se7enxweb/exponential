# Twig templates: the converter and porting a design

This guide is for site builders and template developers who convert `.tpl` templates (or `.stpl`) to `.twig`, or
`.twig` back to `.tpl`. It shows the commands, what the converter writes for each construct, how it protects files
changed by hand, and the notes it gives for what has no exact counterpart. All directions and the general command are
in [Converting templates between template engines](template-conversion.md); the outputs below were printed by the
commands on a development installation.

## The commands

| Command | Converts |
|---|---|
| `exp:template:convert:tpl-to-twig` | `.tpl` to `.twig` |
| `exp:template:convert:twig-to-tpl` | `.twig` (and `.html.twig`) to `.tpl` |
| `exp:template:convert:stpl-to-twig` | `.stpl` to `.twig` (through `.tpl`) |
| `exp:template:convert:twig-to-stpl` | `.twig` to `.stpl` (through `.tpl`) |
| `exp:template:convert --from-engine=<e> --to-engine=<e>` | any direction |
| `exp:template:convert:diff --file=<file> --to-engine=<e>` | prints one conversion, writes nothing |

Options of the direction commands: `--from=<design dir>` (every template below it), `--to=<design dir>` (default:
next to the sources), `--file=<one file>`, `--dry-run`, `--force`, `--override-ini=<file>` with `-s <siteaccess>`
(writes the override rules that make that siteaccess use the converted files), `--twig-suffix=.html.twig`.

## A design, converted

`templates/list.tpl` of a small design:

```
{def $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 3 ) )}
<ul>
{foreach $items as $child}
    <li>{$child.name|wash}</li>
{/foreach}
</ul>
{if $items|count|gt( 2 )}{'More'|i18n( 'design/standard/content' )}{/if}
{include uri='design:parts/footer.tpl'}
{undef $items}
```

```bash
./console exp:template:convert:tpl-to-twig --from=<design> --to=<design>_twig
```

```
WRITE <design>_twig/templates/list.twig
WRITE <design>_twig/templates/parts/footer.twig
PASS tpl->twig 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

`templates/list.twig`:

```twig
{% set items = fetch('content', 'list', {'parent_node_id': node.node_id, 'limit': 3}) %}
<ul>{% for child in items %}
    <li>{{ child.name|wash }}</li>{% endfor %}
</ul>{% if (items|count)|gt(2) %}
{{ 'More'|i18n('design/standard/content') }}{% endif %}
{% expinclude uri='design:parts/footer.tpl' %}
{% set items = null %}
{# exp:template:convert tpl->twig md5:543e672301da17d7166e2c1ac83c7696 from templates/list.tpl -- generated; a file changed by hand is not replaced without --force #}
```

The line breaks are placed so the `.twig` prints exactly what the `.tpl` prints: Twig drops the line break after a
tag, the template system does not. The include keeps the `.tpl` URI: it goes through the design resource and finds
the `.twig` (or the `.tpl`) as the override rules say.

## What the converter writes

| `.tpl` | `.twig` |
|---|---|
| `{$x}`, `{$x\|op( a )}` | `{{ x }}`, `{{ x\|op(a) }}` |
| `{op( a, b )}` | `{{ op(a, b) }}` |
| `{def $x = 1}`, `{set $x = 2}` | `{% set x = 1 %}`, `{% set x = 2 %}` |
| `{undef $x}` | `{% set x = null %}` |
| `{if}`, `{elseif}`, `{else}` | `{% if %}`, `{% elseif %}`, `{% else %}` |
| `{foreach $l as $k => $v}` | `{% for k, v in l %}` (with `offset=`, `max=`, `reverse` through `expslice()`) |
| `{delimiter}` | `{% if not loop.first %}` before the item |
| `{section loop=$l var=v}` | `{% for key, v in expsectionloop(l, 0, null, false, true) %}` |
| `{for 1 to 5 as $i}` | `{% for i in exprange(1, 5) %}` |
| `{while}`, `{do}` | `{% expwhile %}` |
| `{break}`, `{continue}`, `{skip}` | flags that end or skip the rest of the round |
| `{switch}`, `{case}` | an `{% if %}` chain |
| `{include uri=...}` | `{% expinclude uri=... %}` |
| `{cache-block}`, `{set-block}`, `{append-block}`, `{run-once}` | `{% expcacheblock %}`, `{% set %}...{% endset %}` / `{% expsetblock %}`, `{% expappendblock %}`, `{% exprunonce %}` |
| `{attribute_view_gui attribute=$a}` | `{{ attribute_view_gui({attribute: a}) }}` |
| a block function | `{% name ... %}...{% endname %}` |
| `hash( 'a', 1 )`, `array( 1, 2 )` | `{'a': 1}`, `[1, 2]` |
| an operator whose name Twig has itself (`merge`, `trim`, `round`, `and` ...) | `exp_<name>` |

A variable first set inside a loop is declared before the loop (`{% set x = x ?? null %}`) so it is still set after
the loop, as in a `.tpl`.

## Back to .tpl

`exp:template:convert:twig-to-tpl` reads the `.twig` as written (Twig's parser, none of the changes made for running
it) and writes the `.tpl`:

```
{def $items = fetch('content', 'list', hash('parent_node_id', $node.node_id, 'limit', 3))}<ul>{foreach $items as $child}    <li>{$child.name|wash}</li>{/foreach}</ul>{if $items|count|gt(2)}{'More'|i18n('design/standard/content')}{/if}{include uri='design:parts/footer.tpl'}{undef $items}
```

A converted template comes back as the same template (the tests convert every case of the template engine's own
tests there and back and compare the output). What a `.tpl` has no form for is kept as a comment and a NOTE:

```bash
./console exp:template:convert:diff --file=macro.twig --to-engine=tpl
```

```
{* TWIG (not converted: the Twig node MacroDeclarationNode has no template form) line 1 *}{$user_name|upcase}
NOTE macro.twig: {% macros %} have no template form
NOTE macro.twig: line 1: the Twig node MacroDeclarationNode has no template form
```

## Files changed by hand

Every written file ends with a marker comment that holds the md5 of the converted text. A second run writes only what
changed and leaves alone a target that has no marker or was changed after it was written:

```
SKIP templates/parts/footer.tpl: <design>_twig/templates/parts/footer.twig has no conversion marker (written by hand); --force replaces it
PASS tpl->twig 2 templates: 0 written, 1 unchanged, 1 skipped, 0 errors; 0 notes in 0 templates (a skipped file was changed or written by hand: --force replaces it)
```

With `--override-ini`, the override rule of a skipped template still points at it, so a hand-finished `.twig` stays in
use.

## Checking the result

Every `.twig` is compiled with Twig before it is written; a template that does not compile is an ERROR and is not
written. Check the whole converted design afterwards, with the strict level:

```bash
./console exp:template:check --design-dir=<design>_twig --level=strict
```

Then compare pages: render the same page from the `.tpl` siteaccess and the `.twig` siteaccess and compare the HTML
([Migrating a design](twig-templates-migrating.md)).

## Related

- [Converting templates between template engines](template-conversion.md)
- [Migrating a design](twig-templates-migrating.md)
- [Checking template syntax](template-syntax-check.md)
