---
title: Variables in depth
audience: developer, designer, app-creator
category: development
order: 80
summary: The exact rules of def, undef, set, let and default from eZTemplateDefFunction and eZTemplateSetFunction - the warnings for defining twice, setting an unknown variable and undefining one that def did not make, undef without arguments, the lifetime of a variable (the file, the let block), and set with scope=relative, root or global across an include.
keywords: variables, def, undef, set, let, default, scope, relative, root, global, lifetime, warnings, local variables, include
type: help
---
# Variables in depth

[Variables and def](../designers/templates/variables-and-def.md) shows how to use variables. This page gives the exact
rules, read from `lib/eztemplate/classes/eztemplatedeffunction.php` and `lib/eztemplate/classes/eztemplatesetfunction.php`,
and shows each case on alpha, including the warnings the engine writes.

## Contents

1. [The five functions](#the-five-functions)
2. [def, set, undef](#def-set-undef)
3. [def on a variable that exists](#def-on-a-variable-that-exists)
4. [set on a variable that does not exist](#set-on-a-variable-that-does-not-exist)
5. [undef with and without names](#undef-with-and-without-names)
6. [let and default: variables for a block](#let-and-default-variables-for-a-block)
7. [Lifetime: a variable belongs to its file](#lifetime-a-variable-belongs-to-its-file)
8. [set with a scope](#set-with-a-scope)
9. [Related pages](#related-pages)

## The five functions

| Function | Class | Creates | Changes | Notes |
|---|---|---|---|---|
| `def` | `eZTemplateDefFunction` | yes | only with a warning | variable lives until the end of the file or `undef` |
| `undef` | `eZTemplateDefFunction` | | removes | only variables made with `def`; no names: all of them |
| `set` | `eZTemplateSetFunction` | no, warns | yes | takes `scope` and `name` |
| `let` | `eZTemplateSetFunction` | yes, for its block | | removes what it made at `{/let}` |
| `default` | `eZTemplateSetFunction` | only if missing, for its block | | keeps an existing value |

The dollar sign in front of a name is optional for all of them (`{def x=1}` works); the parser skips it.

## def, set, undef

Several variables can be handled in one tag:

```tpl
{def $count = 1 $label = 'items'}
[{$count} {$label}]
{set $count = $count|inc $label = 'things'}
[{$count} {$label}]
{undef $label}
[{if is_set( $label )}label still set{else}label removed{/if}] [{$count}]
```

Output on alpha:

```text
[1 items][2 things][label removed] [2]
```

## def on a variable that exists

`process()` checks `hasVariable()` first. If the variable exists, it writes the warning "Variable '...' is already
defined." and assigns the new value anyway:

```tpl
{def $color = 'red'}
{def $color = 'blue'}
[{$color}]
```

Output on alpha:

```text
[blue]
```

Logged by the template engine:

```text
WARNING def: Variable 'color' is already defined.
```

So a repeated `{def}` is not fatal, but the warning means the template has two definitions where it wanted one, or a
variable leaks in from an including template.

## set on a variable that does not exist

`set` changes existing variables only. For an unknown name it writes "Variable '...' doesn't exist, cannot set" and does
nothing:

```tpl
{set $size = 'large'}
[{if is_set( $size )}size exists{else}size was not created{/if}]
```

Output on alpha:

```text
[size was not created]
```

Logged by the template engine:

```text
WARNING set: Variable 'size' doesn't exist, cannot set
```

## undef with and without names

`{undef $a $b}` removes the named variables, but only those made with `{def}` in the current file
(`hasLocalVariable()`); for any other name it warns "Variable '...' is not defined with {def}." and leaves it. Since the
3.8 fix recorded in the changelog, `undef` cannot remove variables it did not create. Without names, `{undef}` removes
every variable the current file defined with `def` (`unsetLocalVariables()`):

```tpl
{def $a = 1 $b = 2 $c = 3}
{undef}
[{if is_set( $a )}a{/if}{if is_set( $b )}b{/if}{if is_set( $c )}c{/if}] all removed
```

Output on alpha:

```text
[] all removed
```

```tpl
{undef $never_defined}
done
```

Output on alpha:

```text
done
```

Logged by the template engine:

```text
WARNING undef: Variable 'never_defined' is not defined with {def}.
```

## let and default: variables for a block

`let` and `default` are block functions (`hasChildren()` returns true for them). They define their variables, process
the children, then remove only what they defined (`cleanupVariables()`). `default` defines a variable only when it is
missing, which made it the classic way to give an included template optional parameters; `first_set` does the same in
an expression.

```tpl
{let greeting='hi'}inside let: [{$greeting}]{/let}
after let: [{if is_set( $greeting )}still set{else}gone{/if}]
{def $size = 'given'}
{default size='fallback' color='fallback'}[{$size}] [{$color}]{/default}
```

Output on alpha:

```text
inside let: [hi]after let: [gone][given] [fallback]
```

## Lifetime: a variable belongs to its file

Each template file opens its own list of local variables (`createLocalVariablesList()`), and when the file ends,
the variables it made with `def` are removed. A variable defined inside an included template is therefore gone after
the `{include}`:

```tpl
{def $inner = 'made in the part'}
part: [{$inner}]
```

```tpl
{include uri='file:var/tmp/examples/tl-vars/parts/maker.tpl'}
after: [{if is_set( $inner )}inner visible{else}inner gone after the include{/if}]
```

Output on alpha:

```text
part: [made in the part]
after: [inner gone after the include]
```

Parameters passed to `{include}` are set before the file runs and removed (or restored to their old value) after it,
by `eZTemplateIncludeFunction::process()`.

## set with a scope

`set` (and `let` and `default`) accept `scope=relative`, `scope=root` or `scope=global`, and `name=` to pick a
namespace. Written without a dollar sign, `scope` and `name` are not variables but these two options (the parser renames
them to `-scope` and `-name`).

| Scope | Namespace used | Typical use |
|---|---|---|
| `relative` (default) | the current namespace | ordinary assignment |
| `root` | the root namespace of the current template file | change a value of the file from inside a `section` or `let` |
| `global` | the empty namespace, the top of the page | hand a value back from a template included with `name=` |

The included file below runs in its own namespace (`name='part'`), so it does not see `$total` of the caller, but
`set scope=global` changes the caller's variable:

```tpl
{def $inner = 'made in the part'}
part sees: [{if is_set( $total )}{$total}{else}no $total{/if}]
{set scope=global total=10}
```

```tpl
{def $total = 1}
{include uri='file:var/tmp/examples/tl-vars/parts/counter.tpl' name='part'}
after include: [{$total}] [{if is_set( $inner )}inner visible{else}inner not visible{/if}]
```

Output on alpha:

```text
part sees: [no $total]after include: [10] [inner not visible]
```

Use this sparingly: a template that changes its caller's variables is hard to follow. To pass values to the pagelayout
there is the persistent variable ([The persistent variable](../designers/templates/persistent-variable.md)).

## Related pages

- Previous: [Values and literals](values-and-literals.md)
- Next: [Namespaces](namespaces.md)
- [Variables and def](../designers/templates/variables-and-def.md)
- [cond and first_set](../designers/templates/cond-and-first-set.md)
- [def](../../examples/template-functions/def.md), [set](../../examples/template-functions/set.md), [undef](../../examples/template-functions/undef.md), [let](../../examples/template-functions/let.md), [default](../../examples/template-functions/default.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
