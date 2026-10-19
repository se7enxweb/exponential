---
title: While and do loops
audience: designer, developer
category: templates
order: 70
summary: The while and do loop functions of the template language, their syntax with sequence, delimiter, break, continue and skip, and why foreach is almost always the better choice.
keywords: while, do, do while, loop, condition loop, template loop, break, continue
type: help
---
# While and do loops

This page is for designers who meet `{while}` or `{do}` in a template. Both repeat while a condition holds. In design
templates they are rare: most loops go over a list, which `{foreach}` does better ([Foreach loops](foreach-loops.md)).

## Contents

1. [while](#while)
2. [do ... while](#do--while)
3. [When to use them](#when-to-use-them)

## while

From `lib/eztemplate/classes/eztemplatewhilefunction.php`:

```text
{while <condition> [sequence <array> as $seqVar]}
    [{delimiter}...{/delimiter}]
    [{break}] [{continue}] [{skip}]
{/while}
```

```tpl
{def $level = $node.depth}
{while gt( $level, 1 )}
    <span class="indent"></span>
    {set $level = $level|dec}
{/while}
{undef $level}
```

The condition is checked before each run; change a variable in the body or the loop never ends.

## do ... while

From `lib/eztemplate/classes/eztemplatedofunction.php`:

```text
{do}
    ...
{/do while <condition> [sequence <array> as $seqVar]}
```

The body runs once before the condition is checked.

## When to use them

Almost never in a design. Counting is clearer with `{for}` ([For loops](for-loops.md)), lists with `{foreach}`. A
runaway loop renders until PHP's time limit; the template engine's `template.ini [ControlSettings] MaxLevel` limits
nesting of templates, not loop runs.

## Related pages

- [Foreach loops](foreach-loops.md)
- [For loops](for-loops.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
