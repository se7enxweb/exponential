---
title: Variables and def
audience: designer, developer
category: templates
order: 30
summary: Creating, changing and removing template variables with def, set and undef; scopes (current, root, global) for passing values up; default for optional parameters; and the naming habits that avoid clashes between included templates.
keywords: def, set, undef, let, default, template variables, scope, scope global, scope root, variable clash, is_set, first_set, variable naming
type: help
---
# Variables and def

This page is for designers who use variables in templates. It answers: how to create and change them, how long they
live, and how to avoid two templates using the same name for different things.

## Contents

1. [def, set, undef](#def-set-undef)
2. [Optional parameters: default and first_set](#optional-parameters-default-and-first_set)
3. [Scope](#scope)
4. [Naming](#naming)
5. [Older forms](#older-forms)

## def, set, undef

From the syntax comment of `lib/eztemplate/classes/eztemplatedeffunction.php`:

```tpl
{def $i=10 $j=20}
{def $s1='hello' $s2='world'}
{set $i=$i|inc}
{undef $i}
{undef $s1 $s2}
```

- `{def}` creates variables. Defining a variable that already exists assigns the new value and writes the warning "Variable ... is already defined." to the debug output.
- `{set}` changes an existing variable.
- `{undef}` removes variables; `{undef}` alone removes every variable this template defined.

## Optional parameters: default and first_set

A template that may be included with or without a parameter:

```tpl
{default image_class='medium'}
```

sets `$image_class` only if the caller did not pass it (the kernel's `ezimage.tpl` does this). In an expression,
`first_set( $a, $b, 'fallback' )` returns the first value that is set, and `is_set( $x )` / `is_unset( $x )` test it.

## Scope

A variable defined in a template is visible in templates it includes, not in its caller. To hand a value back up
(for example from a full view to the pagelayout), the kernel uses `scope`:

```tpl
{set-block scope=root variable=cache_ttl}400{/set-block}
```

writes `cache_ttl` into the root namespace, where the content view reads it. For page-level values, prefer the
[persistent variable](persistent-variable.md).

## Naming

Included templates share the namespace with their includer: a `{def $item ...}` in a part overwrites (with a warning) when the
including template already has `$item`. Habits that prevent it:

- prefix variables of a part with a short tag (`$tz_` in a teaser part, `pl_` as the starter pagelayout does);
- `{undef}` at the end of the part what it defined;
- pass values to parts as include parameters instead of relying on the includer's names.

## Older forms

`{let}` ... `{/let}` with `{set}` inside, and `{section}`-scoped variables, come from older templates; new templates use
`{def}`/`{undef}` ([Section (legacy)](section-legacy.md)).

## Related pages

- [TPL syntax basics](tpl-syntax-basics.md)
- [Include](include.md)
- [Set-block and append-block](set-block-and-append-block.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
