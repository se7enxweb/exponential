---
title: Converting templates between template engines
audience: developer, designer, app-creator
category: templates
order: 57
summary: The exp:template:convert command space converts templates between Exponential templates (.tpl), Smarty templates (.stpl) and Twig templates (.twig) in every direction - the six named commands, the general command, the diff, the marker that protects files changed by hand, NOTE lines, exit codes, override rules for a siteaccess, and a worked example through all three engines.
keywords: convert, converter, exp:template:convert, tpl-to-twig, twig-to-tpl, tpl-to-stpl, stpl-to-tpl, stpl-to-twig, twig-to-stpl, exp:template:convert:diff, exp:template:engines, marker, NOTE, override-ini, round trip
type: help
---
# Converting templates between template engines

Exponential renders three kinds of templates: Exponential templates (`.tpl`), Smarty templates (`.stpl`) and Twig
templates (`.twig`, `.html.twig`). One command space converts between them in every direction, a single template or
a whole design. This chapter is for developers and designers who move templates from one engine to another; the
output below was printed by the commands on a development installation.

## The engines

```bash
./console exp:template:engines
```

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
PASS 3 engines available
```

An engine is named `tpl` (also `exp`, `exponential`), `stpl` (also `smarty`) or `twig`.

## The commands

| Command | Converts |
|---|---|
| `exp:template:convert:tpl-to-stpl`, `exp:template:convert:stpl-to-tpl` | `.tpl` and `.stpl` |
| `exp:template:convert:tpl-to-twig`, `exp:template:convert:twig-to-tpl` | `.tpl` and `.twig` |
| `exp:template:convert:stpl-to-twig`, `exp:template:convert:twig-to-stpl` | `.stpl` and `.twig`, through `.tpl` in one run |
| `exp:template:convert --from-engine=<e> --to-engine=<e>` | any direction |
| `exp:template:convert:diff --file=<file> --to-engine=<e> [--against=<file>]` | prints one conversion, or its difference to a file; writes nothing |

The options of every direction:

| Option | Meaning |
|---|---|
| `--from=<dir>` | a design directory: every template of the source engine below it (`templates/` and `override/templates/`) |
| `--to=<dir>` | where the converted files go, at the same place below it (default: next to the sources) |
| `--file=<file>` | one template only |
| `--dry-run` | say what would be written, write nothing |
| `--force` | replace a target that was changed or written by hand |
| `-s <siteaccess> --override-ini=<file>` | write the override rules of that siteaccess for the converted files |
| `--twig-suffix=.html.twig` | the ending of written Twig templates (default `.twig`) |

Exit codes: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options; 3 the
conversion is not available (an engine's library is missing).

## Through all three engines

A `.tpl` converted to `.stpl`:

```bash
./console exp:template:convert:tpl-to-stpl --from=<design> --to=<design>_stpl
```

```
PASS tpl->stpl 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

```smarty
{$items = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 3])}
<ul>{foreach $items as $child}
    <li>{$child.name|wash}</li>{/foreach}
</ul>{if ($items|exp_count)|gt:2}
{'More'|i18n:'design/standard/content'}{/if}
```

and the `.stpl` converted on to `.twig` (through `.tpl`, in one run):

```bash
./console exp:template:convert:stpl-to-twig --from=<design>_stpl --to=<design>_twig
```

```
PASS stpl->twig 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

The `.twig` is the same text, line for line, as the one `tpl-to-twig` writes from the `.tpl`: every direction keeps
what the template prints, so the engines can be chained.

## Files changed by hand

Every written file ends with a marker comment naming the direction, the md5 of the converted text and the source:

```
{# exp:template:convert stpl->twig md5:543e672301da17d7166e2c1ac83c7696 from templates/list.stpl -- generated; a file changed by hand is not replaced without --force #}
```

A second run writes only what changed (`unchanged` otherwise) and leaves alone a target without a marker or changed
since it was written (`SKIP ... --force replaces it`). With `--override-ini`, the rule of a skipped template still
points at it.

## NOTE lines

A construct that has no exact counterpart in the target engine is written as close as it can be, and named in a
`NOTE` line on the error output; what cannot be written at all is kept as a comment:

```
{* TWIG (not converted: the Twig node MacroDeclarationNode has no template form) line 1 *}{$user_name|upcase}
NOTE macro.twig: {% macros %} have no template form
```

## Checking the result

Every converted template is checked before it is written (compiled by its engine). After a whole design, check it
with `./console exp:template:check --design-dir=<dir> --level=strict` and compare pages rendered from the old and the
new design.

## Where to go next

- [Moving from .tpl to .twig](moving-from-tpl-to-twig.md), [Moving from .tpl to .stpl](moving-from-tpl-to-stpl.md)
- [Converting templates between template engines](../../guides/template-conversion.md) (guide)
- [The three template engines compared](../../guides/template-engines.md)
