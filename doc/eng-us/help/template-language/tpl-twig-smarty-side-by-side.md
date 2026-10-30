---
title: Templates side by side - Exponential templates, Twig and Smarty
audience: developer, designer, app-creator
category: templates
order: 35
summary: A cheat sheet that puts the same template in the three engines Exponential renders - Exponential templates (.tpl), Twig (.twig) and Smarty (.stpl) - for output, attributes, variables, loops, conditions, cache blocks, fetch, translations, URLs, includes, sections, sequences and escaping, every row converted by the kernel's converters and rendered with identical output, plus how every operator and template function is written in Twig.
keywords: Twig, Smarty, tpl, stpl, twig, cheat sheet, Rosetta stone, side by side, comparison, syntax, operators, filters, functions, exp_ prefix, fetch, i18n, ezurl, include, expinclude, cache-block, expcacheblock, section, sequence, wash
type: help
---
# Templates side by side: Exponential templates, Twig and Smarty

Exponential renders three template languages in the same template system: its own (`.tpl`), Twig 3 (`.twig`) and
Smarty 5 (`.stpl`). This page is a cheat sheet for developers who know one of them and read or write another: the same
task in all three, next to each other.

Every example was written as a `.tpl`, converted to `.stpl` and `.twig` by the kernel's own converters
(`exp:template:convert`), and all three were rendered through the template system on a development installation with
the same variables (`list` = `['a', 'b', 'c']`, `user_name` = `'Ada'`, `price` = `12.5`, `html` = `'<b>bold</b>'`,
`items` = three hashes with `name` and `price`, `node` = the article "Why Track Your Workouts?"). In every row that
shows an output, the three outputs were identical; rows without one show forms that need more of a page around them
(a child node, a layout block) and were not rendered for this page. The Twig and Smarty forms shown are the converters' output, tidied where the converter
writes line breaks for safety; a hand-written template may use the shorter native forms named in the notes.

## Contents

1. [Output and attributes](#output-and-attributes)
2. [Variables](#variables)
3. [Conditions](#conditions)
4. [Loops](#loops)
5. [Operators](#operators)
6. [Data, translations and URLs](#data-translations-and-urls)
7. [Includes, functions and blocks](#includes-functions-and-blocks)
8. [Literal text and comments](#literal-text-and-comments)
9. [Every operator and function in Twig](#every-operator-and-function-in-twig)
10. [Related pages](#related-pages)

## Output and attributes

| Task | `.tpl` | `.twig` | `.stpl` | Output |
|---|---|---|---|---|
| print a variable | `{$user_name}` | `{{ user_name }}` | `{$user_name}` | `Ada` |
| an attribute, escaped | `{$node.name\|wash}` | `{{ node.name\|wash }}` | `{$node.name\|wash}` | `Why Track Your Workouts?` |
| a field's content | `{$node.data_map.title.content\|wash}` | `{{ node.data_map.title.content\|wash }}` | `{$node.data_map.title.content\|wash}` | `Why Track Your Workouts?` |
| by index | `{$list[1]}` | `{{ list[1] }}` | `{$list.1}` | `b` |
| index and key | `{$list.0} {$items[1]['name']}` | `{{ list[0] }} {{ items[1].name }}` | `{$list.0} {$items.1.name}` | `a Kale` |
| escape for HTML and JavaScript | `{$html\|wash} {$html\|wash('javascript')}` | `{{ html\|wash }} {{ html\|wash('javascript') }}` | `{$html\|wash} {$html\|wash:'javascript'}` | `&lt;b&gt;bold&lt;/b&gt; \x3Cb\x3Ebold\x3C/b\x3E` |

None of the three escapes on its own by default: `wash` in all of them. Twig can escape everything automatically
(`[TwigSettings] Autoescape=enabled`); then `|raw` prints a value as it is and `wash` would escape twice
([Writing Twig-native templates](../../guides/twig-templates-native.md#escaping-wash-autoescape-and-raw)).

## Variables

| Task | `.tpl` | `.twig` | `.stpl` |
|---|---|---|---|
| define, change, remove | `{def $total=0}` `{set $total=...}` `{undef $total}` | `{% set total = 0 %}` `{% set total = ... %}` `{% set total = null %}` | `{$total = 0}` `{$total = ...}` `{$total = null}` |
| capture output | `{set-block variable=greeting}Hello {$user_name}{/set-block}` | `{% set greeting %}Hello {{ user_name }}{% endset %}` | `{capture assign=greeting}Hello {$user_name}{/capture}` |
| a variable of another scope | `{set-block scope=global variable=x}...{/set-block}` | `{% expsetblock variable='x' scope='global' %}...{% endexpsetblock %}` | `{expsetblock variable='x' scope='global'}...{/expsetblock}` |
| a default | `{first_set($missing, 'fallback')}` | `{{ first_set(missing, 'fallback') }}` or `{{ missing ?? 'fallback' }}` | `{first_set($missing, 'fallback')}` |

Summing a price with a loop, the whole template in each engine (output `9.75` in each):

```
.tpl   {def $total=0}{foreach $items as $item}{set $total=$total|sum($item.price)}{/foreach}{$total}{undef $total}
.twig  {% set total = 0 %}{% for item in items %}{% set total = total|sum(item.price) %}{% endfor %}{{ total }}{% set total = null %}
.stpl  {$total = 0}{foreach $items as $item}{$total = $total|sum:$item.price}{/foreach}{$total}{$total = null}
```

In a hand-written `.twig`, `total + item.price` is the same.

## Conditions

| Task | `.tpl` | `.twig` | `.stpl` | Output |
|---|---|---|---|---|
| if, elseif, else | `{if $price\|gt(10)}expensive{elseif $price\|gt(5)}fair{else}cheap{/if}` | `{% if price\|gt(10) %}expensive{% elseif price\|gt(5) %}fair{% else %}cheap{% endif %}` | `{if $price\|gt:10}expensive{elseif $price\|gt:5}fair{else}cheap{/if}` | `expensive` |
| and, or, not | `{if and($price\|gt(10), $user_name\|eq('Ada'))}both{/if}` | `{% if exp_and(price\|gt(10), user_name\|eq('Ada')) %}both{% endif %}` | `{if and($price\|gt:10, $user_name\|eq:'Ada')}both{/if}` | `both` |
| inline choice | `{cond($price\|gt(10), 'expensive', 'cheap')}` | `{{ cond(price\|gt(10), 'expensive', 'cheap') }}` | `{cond($price\|gt:10, 'expensive', 'cheap')}` | `expensive` |
| switch | `{switch match=$user_name}{case match='Ada'}hello Ada{/case}{case}someone{/case}{/switch}` | `{% set __switch1 = user_name %}{% if __switch1 == 'Ada' %}hello Ada{% else %}someone{% endif %}` | `{$__switch1 = $user_name}{if $__switch1 == 'Ada'}hello Ada{else}someone{/if}` | `hello Ada` |

`and`, `or` and `not` are words of Twig's own syntax, so the operators are `exp_and`, `exp_or`, `exp_not`; a
hand-written template writes `price > 10 and user_name == 'Ada'` and `price > 10 ? 'expensive' : 'cheap'`.

## Loops

| Task | `.tpl` | `.twig` | `.stpl` | Output |
|---|---|---|---|---|
| key and value, with a separator | `{foreach $list as $i => $v}{$i}:{$v}{delimiter}, {/delimiter}{/foreach}` | `{% for i, v in list %}{% if not loop.first %}, {% endif %}{{ i }}:{{ v }}{% endfor %}` | `{foreach $list as $i => $v}{if !$v@first}, {/if}{$i}:{$v}{/foreach}` | `0:a, 1:b, 2:c` |
| offset and max | `{foreach $items as $item offset 1 max 2}{$item.name} {/foreach}` | `{% for item in expslice(items, 1, 2, false) %}{{ item.name }} {% endfor %}` | `{$__loop1 = $items\|expslice:1:2:false}{foreach $__loop1 as $item}{$item.name} {/foreach}` | `Kale Tea` |
| a number range | `{for 1 to 3 as $i}{$i}{/for}` | `{% for i in exprange(1, 3) %}{{ i }}{% endfor %}` | `{foreach exprange(1, 3) as $i}{$i}{/foreach}` | `123` |
| a sequence of values | `{foreach $list as $v sequence array('odd','even') as $class}{$class} {/foreach}` | `{% for v in list %}{{ expsequence(['odd', 'even'], loop.index0) }} {% endfor %}` | `{$__s = ['odd','even']}{foreach $list as $v}{$__s\|expsequence:$v@index} {/foreach}` | `odd even odd` |
| the old section | `{section loop=$list}{$:item}{delimiter}-{/delimiter}{/section}` | `{% for key, item in expsectionloop(list, 0, null, false) %}{% if not loop.first %}-{% endif %}{{ item }}{% endfor %}` | `{$__loop1 = $list\|expsectionloop:0:null:false}{foreach $__loop1 as $key => $item}{if !$item@first}-{/if}{$item}{/foreach}` | `a-b-c` |

Natively in Twig: `{% for i in 1..3 %}`, `{% for item in items|slice(1, 2) %}`, `{{ cycle(['odd', 'even'], loop.index0) }}`,
`loop.index`, `loop.first`, `loop.last`. Twig has no `break` and `continue`: the converter writes flags; a hand-written
template filters with `{% if %}` inside the loop.

## Operators

| Task | `.tpl` | `.twig` | `.stpl` | Output |
|---|---|---|---|---|
| concatenate | `{concat('a', $user_name, 'b')}` | `{{ concat('a', user_name, 'b') }}` | `{concat('a', $user_name, 'b')}` | `aAdab` |
| join a list | `{$list\|implode(', ')}` | `{{ list\|implode(', ') }}` | `{$list\|exp_implode:', '}` | `a, b, c` |
| contains | `{$list\|contains('b')\|choose('no', 'yes')}` | `{{ (list\|contains('b'))\|choose('no', 'yes') }}` | `{($list\|contains:'b')\|choose:'no':'yes'}` | `yes` |
| count, merge, trim, round | `{$list\|count} {$list\|merge(array('d'))\|implode('')} {'  x  '\|trim} {$price\|round}` | `{{ list\|count }} {{ (list\|exp_merge(['d']))\|implode('') }} {{ '  x  '\|exp_trim }} {{ price\|exp_round }}` | `{$list\|exp_count} {($list\|merge:(['d']))\|exp_implode:''} {'  x  '\|trim} {$price\|exp_round}` | `3 abcd x 13` |
| reverse, slice, arithmetic, case | `{$list\|reverse\|implode(',')} {$list\|extract_left(2)\|implode(',')} {$price\|mul(2)} {$price\|sub(2.5)} {$user_name\|upcase}` | `{{ (list\|exp_reverse)\|implode(',') }} {{ (list\|extract_left(2))\|implode(',') }} {{ price\|mul(2) }} {{ price\|sub(2.5) }} {{ user_name\|upcase }}` | `{($list\|reverse)\|exp_implode:','} {($list\|extract_left:2)\|exp_implode:','} {$price\|mul:2} {$price\|sub:2.5} {$user_name\|upcase}` | `c,b,a a,b 25 10 ADA` |

The pattern: a `.tpl` operator with an input is a Twig filter (`x|op(a)`) and a Smarty modifier (`$x|op:a`); without
an input it is a Twig function (`op(a, b)`) and a Smarty function call. Where the other engine has a name of its own
(Twig: `merge`, `reverse`, `round`, `trim`, `join` ...; Smarty: `count`, `implode` ...), the operator gets the prefix
`exp_` in that engine.

## Data, translations and URLs

| Task | `.tpl` | `.twig` | `.stpl` | Output |
|---|---|---|---|---|
| a fetch | `{fetch('content', 'list_count', hash('parent_node_id', 2))}` | `{{ fetch('content', 'list_count', {'parent_node_id': 2}) }}` | `{fetch('content', 'list_count', ['parent_node_id' => 2])}` | `3` |
| fetch and loop | `{def $children=fetch('content', 'list', hash('parent_node_id', $node.parent_node_id, 'limit', 2))}{foreach $children as $child}{$child.name\|wash};{/foreach}` | `{% set children = fetch('content', 'list', {'parent_node_id': node.parent_node_id, 'limit': 2}) %}{% for child in children %}{{ child.name\|wash }};{% endfor %}` | `{$children = fetch('content', 'list', ['parent_node_id' => $node.parent_node_id, 'limit' => 2])}{foreach $children as $child}{$child.name\|wash};{/foreach}` | `7 Keystone Habits to Maximize Your Mornings;The BiPolar Learning Curve;` |
| translate | `{'Close'\|i18n('design/standard/content')}` | `{{ 'Close'\|i18n('design/standard/content') }}` | `{'Close'\|i18n:'design/standard/content'}` | `Close` |
| translate with an argument | `{'%count items'\|i18n('design/standard/shop',, hash('%count', 3))}` | `{{ '%count items'\|i18n('design/standard/shop', expvoid(), {'%count': 3}) }}` | `{'%count items'\|i18n:'design/standard/shop':expvoid():(['%count' => 3])}` | `3 items` |
| a link, with and without quotes | `{'content/view/full/2'\|ezurl} {'content/view/full/2'\|ezurl('no')}` | `{{ 'content/view/full/2'\|ezurl }} {{ 'content/view/full/2'\|ezurl('no') }}` | `{'content/view/full/2'\|ezurl} {'content/view/full/2'\|ezurl:'no'}` | `"/content/view/full/2" /content/view/full/2` |
| a file of the installation | `{'favicon.ico'\|ezroot}` | `{{ 'favicon.ico'\|ezroot }}` | `{'favicon.ico'\|ezroot}` | `"/favicon.ico"` |
| a file of the design | `{'stylesheets/core.css'\|ezdesign('no')}` | `{{ 'stylesheets/core.css'\|ezdesign('no') }}` | `{'stylesheets/core.css'\|ezdesign:'no'}` | `/extension/ezwebin/design/ezwebin/stylesheets/core.css` |
| an image of the design | `{'images/logo.png'\|ezimage}` | `{{ 'images/logo.png'\|ezimage }}` | `{'images/logo.png'\|ezimage}` | the address of the first design that has it |
| a date | `{$node.object.published\|l10n('shortdate')}` | `{{ node.object.published\|l10n('shortdate') }}` | `{$node.object.published\|l10n:'shortdate'}` | `07/23/2018` |
| a setting | `{ezini('SiteSettings', 'SiteName')}` | `{{ ezini('SiteSettings', 'SiteName') }}` | `{ezini('SiteSettings', 'SiteName')}` | `Fit & Healthy` |

`hash( 'a', 1 )` is `{a: 1}` in Twig and `['a' => 1]` in Smarty; `array( 1, 2 )` is `[1, 2]` in both. An empty
parameter of a `.tpl` (`i18n( 'ctx',, ... )`) is `expvoid()`; a hand-written `.twig` may write `''` where the operator
treats an empty text like a missing parameter, as `i18n` does.

## Includes, functions and blocks

| Task | `.tpl` | `.twig` | `.stpl` |
|---|---|---|---|
| include through the design (override rules apply) | `{include uri='design:parts/cookie_settings_link.tpl'}` | `{% expinclude uri='design:parts/cookie_settings_link.tpl' %}` | `{expinclude uri='design:parts/cookie_settings_link.tpl'}` |
| a template function | `{attribute_view_gui attribute=$node.data_map.title}` | `{{ attribute_view_gui({'attribute': node.data_map.title}) }}` | `{attribute_view_gui attribute=$node.data_map.title}` |
| a node in a view mode | `{node_view_gui content_node=$child view='line'}` | `{{ node_view_gui({content_node: child, view: 'line'}) }}` | `{node_view_gui content_node=$child view='line'}` |
| a cache block | `{cache-block keys=array($node.node_id)}...{/cache-block}` | `{% expcacheblock id=1 keys=[node.node_id] %}...{% endexpcacheblock %}` | `{expcacheblock id=1 keys=[$node.node_id]}...{/expcacheblock}` |
| once per page | `{run-once}...{/run-once}` | `{% exprunonce id=1 %}...{% endexprunonce %}` | `{exprunonce id=1}...{/exprunonce}` |
| a block function of an extension | `{explblock name='x'}...{/explblock}` | `{% explblock name='x' %}...{% endexplblock %}` | `{explblock name='x'}...{/explblock}` |
| template inheritance | (none: override rules) | `{% extends 'layout/base.twig' %}{% block body %}...{% endblock %}` | `{extends file='layout/base.stpl'}{block name='body'}...{/block}` |
| reusable snippets | (an include with parameters) | `{% macro badge(text) %}...{% endmacro %}` | `{function name='badge'}...{/function}{call name='badge' text='New'}` |

The include through the design printed `<a href="/cookie-policy#cookie-settings" class="js-open-ng-cc d-block my-2">Cookie settings</a>`
in all three. A cache block's `id` numbers the blocks of one template; the converter adds it, a hand-written template
should give each block its own. The last two rows have no `.tpl` form and are not converted. They were written by
hand: the Twig forms and the Smarty `{function}` with `{call}` were rendered (the Smarty one printed
`<span class="badge">New</span>`), the Smarty `{extends}` row was not run here. See
[Writing Twig-native templates](../../guides/twig-templates-native.md).

## Literal text and comments

| Task | `.tpl` | `.twig` | `.stpl` | Output |
|---|---|---|---|---|
| a comment | `{* a comment *}` | `{# a comment #}` | `{* a comment *}` | nothing |
| a brace | `{ldelim}literal{rdelim}` | `{{ '{' }}literal}` | `{ldelim}literal{rdelim}` | `{literal}` |
| a literal block | `{literal}{not parsed}{/literal}` | `{not parsed}` (Twig does not read single braces) or `{% verbatim %}...{% endverbatim %}` | `{ldelim}not parsed}` | `{not parsed}` |

CSS and JavaScript with braces need nothing in Twig: only `{{`, `{%` and `{#` start a tag.

## Every operator and function in Twig

Every template operator of the installation and of its active extensions is both a Twig filter and a Twig function;
every template function is a Twig function (without a body) or a tag (with a body). The rules:

| `.tpl` | `.twig` | When |
|---|---|---|
| `{$x\|op( a )}` | `{{ x\|op(a) }}` | always: the operator gets `x` as its input |
| `{op( a, b )}` | `{{ op(a, b) }}` | always: the operator has no input |
| `{$x\|op}` where Twig has a filter `op` | `{{ x\|exp_op }}` | Twig's filters and functions keep their names: `merge`, `reverse`, `round`, `trim`, `join`, `date`, `max` ... |
| `{and( ... )}`, `{or( ... )}`, `{not( ... )}`, `{true()}`, `{false()}` | `{{ exp_and(...) }}` ... or Twig's `and`, `or`, `not`, `true`, `false` | words of Twig's syntax |
| `{some_function name=value}` | `{{ some_function({name: value}) }}` | a template function without a body |
| `{some_block name=value}...{/some_block}` | `{% some_block name=value %}...{% endsome_block %}` | a template function with a body |

**The fast path.** These operators run directly in PHP, compiled into the template as static calls, with exactly the
results of the template system (the engine parity tests hold them to it): `eq`, `ne`, `lt`, `gt`, `le`, `ge`, `and`,
`or`, `not`, `count`, `sum`, `sub`, `mul`, `div`, `mod`, `inc`, `dec`, `cond`, `first_set`, `wash` (for xhtml),
`implode`, `contains` and `concat`. An extension that registers an operator of one of these names keeps its own: the
direct way is only taken while the kernel's class holds the name.

**Every other operator is bridged.** The compiled template calls the template system's own operator code
(`eZTemplate::processOperator()` through a static bridge call), so it behaves as in a `.tpl`, including an
extension's operators and their permissions. An operator registered later by an extension needs no Twig code of its
own ([Writing operators and functions](../../guides/twig-templates-plugins.md)).

**An unknown name.** A name that is neither an operator nor one of Twig's is a compile error
(`Unknown "wahs" filter.`). `exp_<name>` of a name that is no operator compiles, passes its input through and writes
`Unknown operator <name>` to the debug output, as an unknown operator of a `.tpl` does;
`exp:template:check --level=strict` reports it (`TWIG-STRICT-UNKNOWN-OPERATOR`).

The full list, every operator of the kernel, the template library and the standard extensions with its filter and
function forms, is the [Twig operator reference](../../guides/twig-templates-operator-reference.md); the Smarty forms
are in the [Smarty operator reference](../../guides/smarty-templates-operator-reference.md).

## Related pages

- [Smarty and the template language side by side](smarty-side-by-side.md)
- [Moving from Exponential templates (.tpl) to Twig templates (.twig)](moving-from-tpl-to-twig.md)
- [Converting templates between template engines](converting-between-template-engines.md)
- [The three template engines compared](../../guides/template-engines.md)
- [Speed of the three template engines](../../guides/template-engines-speed.md)
