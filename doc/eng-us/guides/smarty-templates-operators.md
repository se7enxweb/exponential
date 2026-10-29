# Smarty templates: operators and fetch functions

This page is for template developers who write `.stpl` templates. Every Exponential template operator can be used in
a `.stpl`, as a Smarty modifier and as a function, and every module fetch function through `fetch()`. This page
explains the two forms, how an operator gets the value it works on, when an operator needs the prefix `exp_`, and how
to write the common operators, with each `.tpl` form next to its `.stpl` form. The examples are taken from the media
design and its Smarty port (`extension/sevenx_themes_media/design/media/` and `.../design/smarty_media/`).

Start with [Smarty templates: writing and running .stpl templates](smarty-templates.md) if you have not written a
`.stpl` yet.

## Two forms: modifier and function

An operator is called as a **modifier**, with the value it works on before the pipe and its parameters after colons:

```smarty
{$node.name|wash}
{'Skip to main content'|i18n:'design/media/pagelayout'}
{$block.values.tag|ne:''}
```

or as a **function**, with its parameters in parentheses:

```smarty
{concat('h', $block.values.level)}
{ezini('SiteSettings', 'SiteName')}
{fetch('content', 'node', ['node_id' => 2])}
```

The two forms are the same call: Smarty compiles `op(a, b, c)` as `a|op:b:c`. The first argument of a function is
what a modifier gets before the pipe.

Two `.tpl` habits do not carry over:

- Modifier parameters follow colons. `$x|i18n('context')` is a Smarty syntax error; write `$x|i18n:'context'`.
- A parameter that is itself an expression with a pipe or an operator goes in parentheses:
  `$attributes|merge:(['class' => $class])`, `'Play video'|i18n:'design/media/video':'':(['%title' => $ytp_title])`.

Modifiers chain from left to right, and a whole expression can be piped when it is in parentheses:

```smarty
{(concat('/content/view/full/', $node.object.owner.id))|ezurl}
{('Read more about %name'|i18n:'design/media/content':'':(['%name' => $node.name]))|wash}
```

## How an operator gets its input

In a `.tpl`, an operator has an input (the value before the pipe) and parameters. Most operators work on their input:
`wash` washes it, `i18n` translates it. Others ignore the input and work on their parameters: `concat` joins them,
`eq` compares them, `ezini` reads the setting they name. The bridge (`expSmartyRenderer::operatorInputMode()`) reads
each operator's template hints and calls it the way the compiled template system does:

| The operator | In a `.stpl` | Examples |
|---|---|---|
| reads its input | the value before the pipe, or the first function argument, is the input; the rest are parameters | `i18n`, `ezurl`, `ezroot`, `ezimage`, `ezdesign`, `wash`, `contains`, `implode`, `explode`, `append`, `merge`, `count`, `l10n`, `upcase` |
| reads its parameters only (hint `input-as-parameter` true, or no input at all, or a control operator) | the value before the pipe becomes the first parameter | `eq`, `ne`, `lt`, `gt`, `le`, `ge`, `and`, `or`, `not`, `concat`, `sum`, `sub`, `mul`, `div`, `ezini`, `ezini_hasvariable`, `is_set` and the other type checks, `fetch`, `cond`, `first_set` |

For a template author this means: **`a|op:b` and `op(a, b)` always do the same**, and for operators of the second
kind it does not matter which value comes first in which form. `$a|eq:2` and `eq($a, 2)` compare `$a` with 2;
`'a'|concat:'b'` and `concat('a', 'b')` are both `ab`; `1|sum:2` and `sum(1, 2)` are both 3.

Operators with the hint `input-as-parameter` set to `always` (`contains`, `implode`, `append`, `merge`, `i18n`,
`ezurl` and others) are of the first kind: the hint describes only how the template compiler inlines them, and at run
time they read their input.

Where it does matter:

- **An input-reading operator called without an input.** A `.tpl` can write `count($list)` or `merge($a, $b)` without
  a pipe; the template system then passes no input. The exact `.stpl` form passes `null` as the input:
  `exp_count(null, $list)`, `merge(null, $a, $b)`. This is what the converter writes. For most such operators passing
  the value as the input gives the same result (`$list|exp_count`).
- **`cond` and `first_set` with a pipe.** In a `.tpl`, `$a|first_set($b)` ignores `$a` and is `$b` when `$b` is
  set; `$a|cond(...)` ignores `$a` too. In a `.stpl` the piped value becomes the first parameter, so
  `$a|first_set:$b` is "the first of `$a`, `$b` that is set" and `$a|cond:x:y` uses `$a` as the condition. Write them
  as functions: `first_set($a, 'default')`, `cond($test, 'yes', 'no')`. The converter keeps the `.tpl` behavior by
  leaving the input out (see [what to check after converting](smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting)).

## The exp_ prefix

Smarty's own modifiers come first. Where Smarty has a modifier of the same name as an Exponential operator, the
operator is called with the prefix `exp_`; `exp_<name>` always calls the Exponential operator `<name>`:

```smarty
{if $sections|exp_count}
<meta name="keywords" content="{($meta.keywords|exp_implode:',')|wash}" />
{if and(is_set(expvar('extra_data', 'Header')), exp_is_array(expvar('extra_data', 'Header')))}
{$node.data_map.success_text.data_text|exp_strip_tags}
```

The names that need the prefix are the modifiers Smarty 5 has (the converter's list, `expSmartyConverter::SMARTY_MODIFIERS`):
`capitalize`, `cat`, `count`, `count_characters`, `count_paragraphs`, `count_sentences`, `count_words`,
`date_format`, `debug_print_var`, `default`, `empty`, `escape`, `explode`, `from_charset`, `implode`, `in_array`,
`indent`, `is_array`, `isset`, `join`, `json_encode`, `lower`, `mb_wordwrap`, `nl2br`, `noprint`, `number_format`,
`raw`, `regex_replace`, `replace`, `round`, `spacify`, `split`, `str_repeat`, `string_format`, `strip`,
`strip_tags`, `strlen`, `substr`, `to_charset`, `truncate`, `unescape`, `upper`, `wordwrap`; and the helper names of
the bridge itself: `expattr`, `expvoid`, `expvar`, `expsequence`, `expslice`, `exprange`, `expsectionloop` (plus `expcolumn`).

Without the prefix you get Smarty's modifier, which can differ:

| Unprefixed (Smarty) | Prefixed (Exponential) |
|---|---|
| `$text\|explode:','` takes the separator as its input: wrong way round for a `.tpl` habit, and deprecated | `$text\|exp_explode:','` splits `$text` at commas |
| `$list\|implode:','` works but is deprecated in Smarty 5 | `$list\|exp_implode:','` |
| `'abc'\|count` is 1 (anything that is not an array or countable counts 1) | `'abc'\|exp_count` is 3 (the length of a string, as in `.tpl`) |
| `is_array($x)` is PHP's | `exp_is_array($x)` is the operator |

For an operator that Smarty does not have, both work: `$x|wash` and `$x|exp_wash` are the same.

## Common operators, .tpl next to .stpl

Every operator of the installation, grouped by area, with its mode: [Operator reference](smarty-templates-operator-reference.md).

| `.tpl` | `.stpl` |
|---|---|
| `{'Read more'\|i18n('design/standard/node')}` | `{'Read more'\|i18n:'design/standard/node'}` |
| `{'Order %order_id'\|i18n('design/ezwebin/shop/orderview',,hash('%order_id', $order.order_nr))}` | `{'Order %order_id'\|i18n:'design/ezwebin/shop/orderview':expvoid():(['%order_id' => $order.order_nr])}` |
| `<a href={$node.url_alias\|ezurl}>` | `<a href={$node.url_alias\|ezurl}>` |
| `{'/content/view'\|ezurl('no')}` | `{'/content/view'\|ezurl:'no'}` |
| `{'/content/view'\|ezurl('no', 'full')}` | `{'/content/view'\|ezurl:'no':'full'}` |
| `<img src={$image.url\|ezroot} />` | `<img src={$image.url\|ezroot} />` |
| `{'logo.png'\|ezimage}` | `{'logo.png'\|ezimage}` |
| `{$node.name\|wash}`, `{$email\|wash('email')}` | `{$node.name\|wash}`, `{$email\|wash:'email'}` |
| `{if $methods\|contains('code')}` | `{if $methods\|contains:'code'}` |
| `{$keywords\|implode(', ')}` | `{$keywords\|exp_implode:', '}` |
| `{def $parts = $text\|explode(',')}` | `{$parts = $text\|exp_explode:','}` |
| `{set $items = $items\|append('large')}` | `{$items = $items\|append:'large'}` |
| `{set $attributes = $attributes\|merge(hash('class', $class))}` | `{$attributes = $attributes\|merge:(['class' => $class])}` |
| `{concat('h', $block.values.level)}` | `{concat('h', $block.values.level)}` |
| `{if $block.values.tag\|ne('')}` | `{if $block.values.tag\|ne:''}` |
| `{if eq($a, 2)}`, `{if $a\|eq(2)}` | `{if eq($a, 2)}`, `{if $a\|eq:2}`, or Smarty's `{if $a == 2}` |
| `{if and($img_w\|gt(0), $img_h\|gt(0))}` | `{if and($img_w\|gt:0, $img_h\|gt:0)}` |
| `{if or($a, $b)}`, `{if $x\|not}` | `{if or($a, $b)}`, `{if $x\|not}` |
| `{cond(is_set($redirect_uri), $redirect_uri, '/user/edit')\|ezurl}` | `{(cond(is_set($redirect_uri), $redirect_uri, '/user/edit'))\|ezurl}` |
| `{first_set($redirect_uri, $redirect_url, '/')}` | `{first_set($redirect_uri, $redirect_url, '/')}` |
| `{ezini('SiteSettings', 'SiteName')}` | `{ezini('SiteSettings', 'SiteName')}` |
| `{ezini('SiteInfo', 'CookiePolicyID', 'menu.ini')}` | `{ezini('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `{if ezini_hasvariable('SiteInfo', 'CookiePolicyID', 'menu.ini')}` | `{if ezini_hasvariable('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `{set $total = sum($total, $item.item_count)}` | `{$total = sum($total, $item.item_count)}` |
| `{if $list\|count}`, `{count($list)}` | `{if $list\|exp_count}`, `{exp_count(null, $list)}` |
| `{if is_set($block.view_type)}` | `{if is_set($block.view_type)}`, or Smarty's `isset($block.view_type)` |
| `{$node.object.published\|l10n('shortdate')}` | `{$node.object.published\|l10n:'shortdate'}` |
| `hash('node_id', 2, 'limit', 5)` | `['node_id' => 2, 'limit' => 5]` |
| `array('a', 'b')` | `['a', 'b']` |
| `true()`, `false()` | `true`, `false` |

The PHP string functions that are operators (see [PHP string functions as template operators](../features/6.0/string-template-operators.md))
work the same way, with `exp_` where Smarty has the name: `{$text|exp_substr:0:5}`, `{$text|str_contains:'Expo'}`.

## Empty parameters: expvoid()

A `.tpl` can leave a parameter empty so that the operator uses its default, as in
`i18n('context',,hash(...))`. Smarty has no empty parameter; write `expvoid()` in its place:

```smarty
{'My pending items [%pending_count]'|i18n:'design/ezwebin/content/pendinglist':expvoid():(['%pending_count' => $list_count])}
```

`expvoid()` is passed to the operator as an empty parameter, so the default applies. Printed on its own it is an empty
string.

## Fetch functions

`fetch()` calls any module fetch function, with its parameters as a Smarty array literal (the `.tpl` `hash(...)`):

```smarty
{$node = fetch('content', 'node', ['node_id' => $module_result.content_info.node_id])}
{$cat_children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'sort_by' => [['published', false]], 'limit' => $cat_limit, 'offset' => $cat_offset])}
{$cat_count = fetch('content', 'list_count', ['parent_node_id' => $node.node_id])}
{$activate_user = fetch('content', 'object', ['object_id' => $user.contentobject_id])}
{$el_layout = fetch('explayouts', 'resolve_layout_for_node', ['node_id' => $node.node_id])}
{$el_layout = fetch('explayouts', 'resolve_layout', [])}
<li>Exponential <strong>{(fetch('setup', 'version'))|wash}</strong></li>
```

- A fetch without parameters takes `[]` (the `.tpl` `hash()`).
- Nested arrays are nested literals: the `.tpl` `sort_by, array('priority', true())` is `'sort_by' => ['priority', true]`,
  and `array(array('published', false()))` is `[['published', false]]`.
- The fetch functions and their permission checks are the same as from a `.tpl`; a fetch from an extension works
  when that extension is active in the siteaccess.
- To read an attribute of a fetch result without a variable, use `expattr`: `{(fetch('shop', 'basket'))|expattr:'items'}`.
  Assigning the result to a variable first is easier to read.
- To loop over a fetch, assign it first (`{foreach}` iterates a variable, not an expression).
- `{expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10}` is the tag form:
  every parameter except `assign`, `module` and `function` becomes a fetch parameter.

## The helpers of the bridge

These modifiers are not Exponential operators; the bridge adds them for what Smarty cannot express. They are what the
converter writes, and can be used by hand:

| Helper | What it does | Example |
|---|---|---|
| `expattr` | an attribute or array key of any value | `(fetch('shop', 'basket'))\|expattr:'items'` |
| `expvar` | a variable of a global namespace, the `.tpl` `$#name` and `$#Namespace:name` | `expvar('extra_data', 'Header')` |
| `expvoid` | an empty operator parameter | `i18n:'context':expvoid():[...]` |
| `expslice` | an array sliced by offset and max, reversed or not, keys kept | `$list\|expslice:0:3:false` |
| `expsectionloop` | the items of a `{section loop=...}`: an array, or a number n for 1 to n | `($path)\|expsectionloop:0:null:false` |
| `exprange` | the integers from one number to another, counting up or down | `exprange(1, 5)` |
| `expsequence` | the item of a sequence at a position, cycling | `$__sequence1\|expsequence:$item@index` |
| `expcolumn` | one attribute of every item of an array | `$items\|expcolumn:'id'` |

## Related pages

- New to `.stpl`: [a first .stpl, step by step](smarty-templates-tutorial.md)
- Reference: [every operator](smarty-templates-operator-reference.md), [template functions and blocks](smarty-templates-functions.md), [troubleshooting and FAQ](smarty-templates-troubleshooting.md)
- Topics: [caching and performance](smarty-templates-caching.md), [performance (Speed)](smarty-templates-performance.md), [migrating a design](smarty-templates-migrating.md), [writing operators and functions](smarty-templates-plugins.md), [security and escaping](smarty-templates-security.md)
- [Smarty templates: writing and running .stpl templates](smarty-templates.md)
- [Smarty templates: the converter and porting a design](smarty-templates-converter.md)
- [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md), the feature page

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
