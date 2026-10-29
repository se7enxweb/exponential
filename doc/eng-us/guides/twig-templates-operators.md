# Twig templates: operators and fetch functions

This guide is for template developers who call Exponential template operators and fetch functions from `.twig`. It
explains the two forms an operator has in Twig, how an operator gets its input, when a name needs the prefix `exp_`,
`fetch()`, and the helper functions the converter writes. Every operator with its forms is listed in the
[Operator reference](twig-templates-operator-reference.md). The examples were rendered by the template system on a
development installation, with `list` = `['a', 'b', 'c']`, `user_name` = `'Ada'` and `price` = `12.5`.

## Filter or function

Every operator of the template system (the kernel's and every active extension's) is both a Twig filter and a Twig
function. Write it the way the `.tpl` does:

- **with an input** (`.tpl` `{$x|op( a, b )}`): a filter, `{{ x|op(a, b) }}`;
- **without an input** (`.tpl` `{op( a, b )}`): a function, `{{ op(a, b) }}`.

```twig
{{ list|implode(', ') }}
{{ concat('a', 'b', user_name) }}
{{ cond(price|gt(10), 'expensive', 'cheap') }}
{{ first_set(missing, 'a default') }}
```

```
a, b, c
abAda
expensive
a default
```

The operator receives exactly what the template system would hand it in a compiled `.tpl`, so the two forms behave as
they do there. The operators that take the input as their first parameter (`sum`, `concat`, `eq`, `gt` and the other
logic and arithmetic operators, whose template hints say `input-as-parameter`) give the same result either way:
`{{ price|mul(2) }}` and `{{ mul(price, 2) }}` both print `25`.

## The prefix exp_

Twig has filters and functions of its own (`length`, `merge`, `round`, `date`, `replace`, `trim`, `json_encode` ...)
and words its parser reserves (`and`, `or`, `not`, `true`, `false`, `null`). An operator with such a name is called
`exp_<name>`; the plain name keeps Twig's meaning:

| `.tpl` | `.twig` |
|---|---|
| `{and( $a, $b )}` | `{{ exp_and(a, b) }}` |
| `{$a\|merge( $b )}` | `{{ a\|exp_merge(b) }}` |
| `{$text\|trim}` | `{{ text\|exp_trim }}` |
| `{true()}` | `{{ exp_true() }}` (or Twig's `true`) |

`exp_<name>` also works for every other operator (`{{ list|count }}`, `{{ list|exp_count }}` and `{{ exp_count(list) }}` all
print `3`),
which is useful when an extension may add a Twig filter of the same name later. An operator whose own name starts
with `exp_` (`exp_path`, `exp_environment`) is written by its name. An `exp_` name that is no operator at all passes
its input through and writes an error to the debug output, as an unknown operator of a `.tpl` does;
`exp:template:check --level=strict` reports it (`TWIG-STRICT-UNKNOWN-OPERATOR`).

## Operators that run directly in PHP

The logic, arithmetic and control operators (`eq`, `ne`, `lt`, `gt`, `le`, `ge`, `and`, `or`, `not`, `count`, `sum`,
`sub`, `mul`, `div`, `mod`, `inc`, `dec`, `cond`, `first_set`), `wash` (for xhtml), `implode`, `contains` and
`concat` are in every condition and loop, so they run directly in PHP (`expTwigNativeOperators`), with exactly the
results of the template system: the engine parity tests hold them to it. A compiled template calls them as static
methods, without a lookup. An extension that registers its own operator of one of these names keeps its own: the
direct way is only taken while the kernel's class holds the name. Every other operator runs through the template
system's own code (`eZTemplate::processOperator()`).

## fetch()

`fetch( module, function, parameters )` is the template system's fetch, with the parameters as a Twig hash:

```twig
{% set children = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
{{ children|length }} children
```

```
3 children
```

`fetch('content', 'node', {node_id: 2})`, `fetch('user', 'current_user')`, `fetch('content', 'list_count', {...})`:
every fetch function of every module, with the same parameters as in a `.tpl` ([fetch functions](../examples/fetch-functions/README.md)).
`hash( 'a', 1 )` of a `.tpl` is `{a: 1}`; `array( 1, 2 )` is `[1, 2]`.

## Settings, INI values and translations

```twig
{{ ezini('SiteSettings', 'SiteName') }}
{{ 'Close'|i18n('design/standard/content') }}
{{ node.object.published|l10n('shortdate') }}
{{ price|l10n('currency') }}
```

```
Fit & Healthy
Close
07/23/2018
$12.50
```

A constant `i18n` text is translated when the template is compiled (the compiled template is kept per siteaccess and
locale), so it costs nothing per request.

## The helpers

The converter writes a few functions of its own where Twig has no construct for a `.tpl` one. They work in
hand-written templates too:

| Helper | For |
|---|---|
| `expvoid()` | an empty parameter: `.tpl` `i18n( 'ctx',, hash(...) )` is `'text'\|i18n('ctx', expvoid(), {...})` |
| `expvar(name, namespace)` | a variable of another namespace (`$#name`, `$:name` of a `.tpl`) |
| `expattr(value, key)` | an attribute with a computed name (`$a[$key]` where `$key` is not a literal) |
| `expsectionloop(loop, offset, max, reverse)` | the items of a `{section loop=...}`; a number n loops 1..n |
| `expslice(list, offset, max, reverse)` | a `{foreach}` with `offset=`, `max=`, `reverse` |
| `expsequence(list, index)` | the `sequence=` value of a loop round |
| `exprange(from, to)` | a `{for 1 to 5 as $i}` |
| `expcolumn(list, key)` | the values of one attribute of every item |
| `expi18nargs(text, arguments)` | the arguments of an `i18n` whose text was translated when the template was compiled |

## Related

- [Operator reference](twig-templates-operator-reference.md)
- [Template functions and tags](twig-templates-functions.md)
- [Writing operators and functions](twig-templates-plugins.md)
