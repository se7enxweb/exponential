---
title: "Smarty and the template language: the differences in depth"
audience: developer, designer, app-creator
category: development
order: 40
summary: The differences that trip up Smarty users, each shown on alpha - no infix arithmetic or comparison, no PHP in templates, no automatic escaping, unknown operators that pass their input through, strict def and set, braces in CSS and JavaScript, operators as registered PHP classes, fetch and objects, overrides instead of inheritance, namespaces and whitespace.
keywords: Smarty, differences, infix, operators, php tag, escaping, wash, def, set, warnings, braces, css, literal, override, inheritance, namespaces, whitespace
type: help
---
# Smarty and the template language: the differences in depth

[The side-by-side table](smarty-side-by-side.md) shows where the two languages match. This page takes the places where
they do not, explains each from the code of the template engine and shows what happens on alpha when a Smarty habit
meets the template language.

## Contents

1. [No infix arithmetic](#no-infix-arithmetic)
2. [No infix comparison](#no-infix-comparison)
3. [Operators instead of symbols](#operators-instead-of-symbols)
4. [No automatic escaping](#no-automatic-escaping)
5. [Unknown operators pass their input through](#unknown-operators-pass-their-input-through)
6. [def and set are strict](#def-and-set-are-strict)
7. [Braces in CSS and JavaScript](#braces-in-css-and-javascript)
8. [No PHP in templates](#no-php-in-templates)
9. [Operators and functions are registered PHP classes](#operators-and-functions-are-registered-php-classes)
10. [Data comes from fetch, and values are objects](#data-comes-from-fetch-and-values-are-objects)
11. [Overrides instead of inheritance](#overrides-instead-of-inheritance)
12. [Namespaces](#namespaces)
13. [Whitespace around tags](#whitespace-around-tags)
14. [Related pages](#related-pages)

## No infix arithmetic

The expression parser (`parseVariableTag()` in `lib/eztemplate/classes/eztemplateelementparser.php`) knows values,
variables, attribute lookups (`.` and `[ ]`), operators (`name(` or `|name`) and nothing else. A `+` is not part of the
language. It reads the number `1`, finds characters it cannot use, logs a parser error and prints what it had:

```tpl
[{1+2}]
```

Output on alpha:

```text
[1]
```

Logged by the template engine:

```text
ERROR : parser error @ tl-diff/01-no-infix-arithmetic.tpl:1[2]
Extra characters found in expression, they will be ignored.
+2' (1)
```

## No infix comparison

The same goes for `>`, `<`, `==`, `&&` and `||`. In an `{if}` the condition parser stops after `$a`, the rest is
reported as invalid, and the condition that is actually tested is only `$a`. With `$a = 0` that is false, so the
template prints "not greater", but only by accident: with `$a = 1` the same tag would print "greater" although 1 is
not greater than 1.

```tpl
{def $a = 0}
[{if $a > 1}greater{else}not greater{/if}]
```

Output on alpha:

```text
[not greater]
```

Logged by the template engine:

```text
ERROR : parser error @ tl-diff/02-no-infix-comparison.tpl:2[7]
Invalid parameter characters in function 'if': '> 1'
```

## Operators instead of symbols

Every calculation and comparison is an operator, either called or piped: `sum`, `sub`, `mul`, `div`, `mod`, `inc`,
`dec`; `eq`, `ne`, `gt`, `ge`, `lt`, `le`; `and`, `or`, `not`. They nest freely:

```tpl
{def $a = 0}
[{sum( 1, 2 )}] [{1|sum( 2 )}] [{mul( 3, 4 )}] [{10|sub( 4 )}]
[{if $a|gt( 1 )}greater{else}not greater{/if}]
[{if and( $a|ge( 0 ), $a|lt( 10 ), not( $a|eq( 5 ) ) )}in range{/if}]
```

Output on alpha:

```text
[3] [3] [12] [6]
[not greater]
[in range]
```

## No automatic escaping

Output is never escaped by the engine. What a variable holds is written as it is, so text from content, URLs or
request parameters must be passed through `wash` (or another escaping operator) where it is printed:

```tpl
{def $text = '<script>alert(1)</script> & "quotes"'}
raw:    {$text}
washed: {$text|wash}
```

Output on alpha:

```text
raw:    <script>alert(1)</script> & "quotes"
washed: &lt;script&gt;alert(1)&lt;/script&gt; &amp; &quot;quotes&quot;
```

Attributes of content objects that are already HTML, such as the output of a rich text field, are printed through their
view templates (`attribute_view_gui`) and must not be washed a second time. See
[Wash and escaping](../designers/templates/wash-and-escaping.md).

## Unknown operators pass their input through

Smarty fails to compile a template that uses an unknown modifier. The template language logs a warning
("Operator '...' is not registered") and leaves the value as it was. A misspelled operator therefore does not stop the
page; it silently does nothing, so read the debug output:

```tpl
[{'unchanged'|nosuchoperator}]
```

Output on alpha:

```text
[unchanged]
```

Logged by the template engine:

```text
WARNING : Operator 'nosuchoperator' is not registered
```

## def and set are strict

`{def}` creates a variable and warns if it exists already (it then assigns the value anyway); `{set}` changes a variable
and refuses to create one. Both write a warning, not an error, so the page renders on:

```tpl
{def $color = 'red'}
{def $color = 'blue'}
{set $size = 'large'}
[{$color}]
```

Output on alpha:

```text
[blue]
```

Logged by the template engine:

```text
WARNING def: Variable 'color' is already defined.
WARNING set: Variable 'size' doesn't exist, cannot set
```

The rules come from `eZTemplateDefFunction::process()` and `eZTemplateSetFunction::process()`; see
[Variables in depth](variables-in-depth.md).

## Braces in CSS and JavaScript

Any `{` starts a tag. A CSS rule or a JavaScript object in a template is parsed as a tag named after whatever follows
the brace, which is an error, and everything after it is lost. Here even the word "after" disappears:

```tpl
before
.box { color: red; }
after
```

Output on alpha:

```text
before
.box
```

Logged by the template engine:

```text
ERROR : parser error @ tl-diff/07-braces-in-text.tpl:2[11]
Invalid parameter characters in function '': ': red; '
WARNING : Function "" is not registered
```

The fixes are the same as in Smarty: `{literal}...{/literal}` around the block, or `{ldelim}` and `{rdelim}` for single
braces. In Exponential's admin designs scripts and styles do not belong in templates at all; they live in files loaded
through the design's script and style lists.

```tpl
before
{literal}.box { color: red; }{/literal}
.box {ldelim} color: red; {rdelim}
after
```

Output on alpha:

```text
before
.box { color: red; }
.box { color: red; }after
```

## No PHP in templates

There is no `{php}` tag. The parser treats it as a function name, the engine has no such function, and the block is
dropped with a warning:

```tpl
[{php}echo 1;{/php}]
```

Output on alpha:

```text
[]
```

Logged by the template engine:

```text
WARNING : Function "php" is not registered
```

Logic that needs PHP goes into an operator, a function or a fetch function of an extension.

## Operators and functions are registered PHP classes

A Smarty plugin is a PHP function found by its file name. A template operator is a method of a class that an
`eztemplateautoload.php` file lists (`$eZTemplateOperatorArray`); the engine reads those lists from the paths in
`site.ini` [TemplateSettings] `AutoloadPathList` and from every extension named in `ExtensionAutoloadPath`
(`eZTemplate::factory()` and `eZTemplate::autoload()`). The class is loaded the first time one of its operators is used.
Template functions are registered the same way through `$eZTemplateFunctionArray`. The two are compared in
[Functions versus operators](functions-vs-operators.md).

## Data comes from fetch, and values are objects

A Smarty template usually receives all its data from the PHP that renders it. A template of Exponential often asks for
data itself: `fetch( 'content', 'list', hash( ... ) )` runs a fetch function of the `content` module, with the
permissions of the current user. The values are PHP objects (nodes, content objects, attributes) read through
`attribute()`: `$node.data_map.title.content`. See [Attribute access](attribute-access.md) and
[Fetch basics](../designers/templates/fetch-basics.md).

## Overrides instead of inheritance

Smarty templates extend each other with `{extends}` and `{block}`. In Exponential the pagelayout wraps every page and
prints the result of the module view (`$module_result.content`), and the override system (`override.ini`) chooses
which template renders a view by class, section, node, parent, view mode and more. Parts of a page are shared with
`include` and the `*_view_gui` functions. See [Override basics](../designers/templates/override-ini-basics.md).

## Namespaces

Variables live in namespaces. An `include` without a `name` runs in the namespace of the caller and sees all its
variables; with `name='box'` it runs in its own namespace and sees only the parameters it was given. `section` and
`let` with a `name` create namespaces too. See [Namespaces](namespaces.md).

## Whitespace around tags

The parser removes one newline directly before and one directly after every function tag (`{if}`, `{/foreach}`,
`{def ...}`), but not around output tags such as `{$x}`. Templates can therefore put each function on its own line
without adding blank lines to the HTML. See [Lexical structure](lexical-structure.md#whitespace-around-tags).

## Related pages

- Previous: [Smarty and the template language side by side](smarty-side-by-side.md)
- Next: [Moving a template from Smarty](moving-from-smarty.md)
- [Common template mistakes](../designers/templates/common-template-mistakes.md)
- [Logic and comparison operators](../designers/templates/logic-and-comparison-operators.md)
- [Literal and delimiters](../designers/templates/literal-and-delimiters.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
