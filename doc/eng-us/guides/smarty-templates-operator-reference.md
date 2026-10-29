# Smarty templates: operator reference

This page is for template developers who write `.stpl` templates and need the exact form of one operator. It lists
every template operator a standard installation registers, grouped by area, with its `.stpl` form, whether it needs
the prefix `exp_`, how it takes its input, and an example. The rules behind the columns are explained in
[Smarty templates: operators and fetch functions](smarty-templates-operators.md); read that first if the words
"input" and "param" below are new to you.

The list was taken from the code: the operator registrations of `lib/eztemplate/classes/eztemplateautoload.php`,
`kernel/common/eztemplateautoload.php`, `kernel/private/eztemplate/eztemplateautoload.php`,
`lib/ezpdf/classes/eztemplateautoload.php`, `[PHP] PHPOperatorList[]` of `settings/template.ini`, and the
`autoloads/eztemplateautoload.php` of the extensions shipped with the installation. The input mode of each operator
is what `expSmartyRenderer::operatorInputMode()` decides from the operator's template hints, and for the operators
without hints from `expSmartyRenderer::PARAMETER_OPERATORS` and `[SmartySettings] ParameterOperatorList[]` of
`smarty.ini`.

## How to read the tables

**Name in `.stpl`.** The name to write after the pipe or before the parentheses. `exp_<name>` where Smarty 5 has a
modifier of the same name (Smarty's own modifiers always win; `exp_` steps past them). An operator whose own name
starts with `exp_` (`exp_path`, `exp_environment`) is called by that name.

**Mode.**

- **input**: the operator works on its input. `$x|op:a:b` passes `$x` as the input and `a`, `b` as parameters;
  `op($x, a, b)` is the same call. Called without an input (a `.tpl` `op(a, b)` with no pipe), write
  `op(null, a, b)`. Every operator without template hints is of this kind, which includes nearly every operator of the
  extensions, except the kernel's operators that read their parameters only (below) and those an extension lists in
  `[SmartySettings] ParameterOperatorList[]` of `smarty.ini`.
- **param**: the operator reads its parameters only. The piped value becomes the first parameter, so `$a|op:b` and
  `op($a, b)` are the same call, in any order you like to write it. The function form of the `.tpl` is the `.stpl`
  form: `{ezhttp('SearchText', 'get')}`, `{if has_role('Editor')}`. For the kernel's operators without template hints
  that are of this kind (`ezhttp`, `has_role`, `topmenu` ...), a `null` in front, as in `ezhttp(null, 'SearchText',
  'get')`, the form converted templates had before, is left out, so both forms work.

**Example.** A `.stpl` line. Where the template engine's own tests (`eZTemplateOperatorMatrixTest`, checked against
`.stpl` by `tests/tests/lib/smarty/expSmartyOperatorParityTest.php`) have the case, the output is given after `→`.

Literals first, since several operators of the `.tpl` are literals in Smarty:

| `.tpl` | `.stpl` |
|---|---|
| `array(1, 2, 3)` | `[1, 2, 3]` |
| `hash('a', 1, 'b', 2)` | `['a' => 1, 'b' => 2]` |
| `true()`, `false()` | `true`, `false` |
| `null` (a bare word, which the `.tpl` reads as the text `'null'`) | `'null'`; for a real null write `null` |
| an empty parameter: `i18n('ctx',,hash(...))` | `expvoid()`: `i18n:'ctx':expvoid():[...]` |

## Arrays and lists (eZTemplateArrayOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `array` | literal `[...]` | input | `{$list = [1, 2, 3]}` |
| `hash` | literal `[k => v]` | input | `{$h = ['x' => 'y']}{$h.x}` → `y` |
| `append` | `append` | input | `{[1]\|append:2:3\|exp_implode:','}` → `1,2,3` |
| `prepend` | `prepend` | input | `{[3]\|prepend:1:2\|exp_implode:','}` → `1,2,3` |
| `merge` | `merge` | input | `{(['a' => 1]\|merge:['a' => 2])\|expattr:'a'}` → `2` |
| `array_append`, `array_prepend`, `array_merge` | the same | input | deprecated names of `append`, `prepend`, `merge` |
| `contains` | `contains` | input | `{if [1, 2]\|contains:3}y{else}n{/if}` → `n`; works on strings too |
| `compare` | `compare` | input | `{if [1, 2]\|compare:[2, 1]}y{else}n{/if}` → `y` (order ignored) |
| `extract` | `extract` | input | `{'abcdef'\|extract:3}` → `def`; `extract:offset:length` |
| `extract_left` | `extract_left` | input | `{[1, 2, 3]\|extract_left:2\|exp_implode:','}` → `1,2` |
| `extract_right` | `extract_right` | input | `{[1, 2, 3]\|extract_right:2\|exp_implode:','}` → `2,3` |
| `begins_with` | `begins_with` | input | `{if [1, 2, 3]\|begins_with:1:2}y{/if}` → `y` |
| `ends_with` | `ends_with` | input | `{if [1, 2, 3]\|ends_with:2:3}y{/if}` → `y` |
| `implode` | `exp_implode` | input | `{$keywords\|exp_implode:', '}` |
| `explode` | `exp_explode` | input | `{'a,,b'\|exp_explode:','\|exp_count}` → `3` |
| `repeat` | `repeat` | input | `{[1, 2]\|repeat:2\|exp_implode:','}` → `1,2,1,2` |
| `reverse` | `reverse` | input | `{(['x', 'y']\|reverse)\|expattr:0}` → `y` |
| `insert` | `insert` | input | `{'ad'\|insert:1:'bc'}` → `abcd` |
| `remove` | `remove` | input | `{'abcd'\|remove:1:2}` → `ad` (offset, length) |
| `replace` | `exp_replace` | input | `{[1, 2, 3, 4]\|exp_replace:1:2:8:9\|exp_implode:','}` → `1,8,9,4` |
| `unique` | `unique` | input | `{['a', 'b', 'a']\|unique\|exp_implode:','}` → `a,b` |
| `array_sum` | `array_sum` | input | `{[0.5, 1.5]\|array_sum}` → `2` |

## Logic and comparison (eZTemplateLogicOperator, eZTemplateControlOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `eq` | `eq` | param | `{if eq(4, 4, 4)}y{/if}` → `y`; `{if $a\|eq:'x'}` |
| `ne` | `ne` | param | `{if $block.values.tag\|ne:''}` |
| `lt`, `gt`, `le`, `ge` | the same | param | `{if 2\|lt:3}y{/if}` → `y`; `{if $count\|gt:0}` |
| `and` | `and` | param | `{and(1, 'a', 'last')}` → `last` (the last value when all are true) |
| `or` | `or` | param | `{or(0, '', 'x', 'y')}` → `x` (the first true value) |
| `not` | `not` | param | `{if $x\|not}`, `{if not($list)}` |
| `true`, `false` | literals `true`, `false` | param | `{$flag = true}` |
| `null` | `null` (operator) | input | `{if 'x'\|null}y{else}n{/if}` → `n` |
| `choose` | `choose` | input | `{0\|choose:'a':'b'}` → `a`; `{2\|choose:'a':'b':'c'}` → `c` |
| `cond` | `cond` | param | `{cond($edit_language, 'yes', 'no')}`; write it as a function (see the note below) |
| `first_set` | `first_set` | param | `{first_set($redirect_uri, $redirect_url, '/')}`; `{first_set(0, 'b')}` → `0` |

`cond` and `first_set` read their parameters only. Piped, the piped value becomes the first parameter:
`$a|first_set:'d'` is "the first of `$a`, `'d'` that is set", and `$a|cond:'x':'y'` uses `$a` as the condition. A
`.tpl` `$a|first_set('d')` ignores `$a` and is always `'d'`; the converter writes it as `first_set('d')` to keep that.
Smarty's own `==`, `!=`, `&&`, `||`, `!` and `?:` work too, but `eq` compares as the `.tpl` does
(`{if eq(1, '1')}` is true; several parameters must all be equal).

## Types (eZTemplateTypeOperator)

All of them read their parameters: `is_set($x)` and `$x|is_set` are the same.

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `is_array` | `exp_is_array` | param | `{if exp_is_array($module_result.content_info)}` |
| `is_boolean`, `is_integer`, `is_float`, `is_numeric`, `is_string`, `is_object`, `is_null` | the same | param | `{if is_float(1.5)}y{/if}` → `y` |
| `is_class` | `is_class` | param | parameters as in the `.tpl` |
| `is_set`, `is_unset` | the same | param | `{if is_set($block.view_type)}`; Smarty's `isset()` also works |
| `get_type` | `get_type` | param | `{get_type(5)}` → `integer`; `{get_type(true)}` → `boolean[true]` |
| `get_class` | `get_class` | param | `{get_class($node)}` |

## Arithmetic (eZTemplateArithmeticOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `sum`, `sub`, `mul`, `div`, `mod` | the same | param | `{1\|sum:2:3}` → `6`; `{div(100, 5, 2)}` → `10`; `{sub(mul(3, 4), div(10, 5))}` → `10` |
| `inc`, `dec` | the same | param | `{inc(9)}` → `10` |
| `max`, `min` | the same | param | `{max(1.5, 2.5, 2)}` → `2.5` |
| `abs`, `ceil`, `floor`, `int`, `float` | the same | param | `{-2.5\|abs}` → `2.5`; `{floor(-1.5)}` → `-2` |
| `round` | `exp_round` | param | `{2.6\|exp_round}` → `3` |
| `roman` | `roman` | param | `{4\|roman}` → `IV` |
| `rand` | `rand` | param | `{rand(1, 6)}` |
| `count` | `exp_count` | input | `{$list\|exp_count}`; `{exp_count(null, 'abc')}` → `3` (a string counts its characters) |

Smarty's `+`, `-`, `*`, `/` and `%` are fine for numbers too.

## Strings (eZTemplateStringOperator, eZTemplateTextOperator, eZTemplateNl2BrOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `wash` | `wash` | input | `{$node.name\|wash}`; `wash:'email'`, `wash:'javascript'`, `wash:'pdf'`; see [Security and escaping](smarty-templates-security.md) |
| `upcase`, `downcase` | the same | input | `{'äöü'\|upcase}` → `ÄÖÜ` |
| `upfirst`, `upword` | the same | input | `{$title\|upfirst}` |
| `count_words` | `exp_count_words` | input | `{''\|exp_count_words}` → `0` |
| `count_chars` | `count_chars` | input | `{'äöü'\|count_chars}` → `3` |
| `trim` | `trim` | input | `{'xxaxx'\|trim:'x'}` → `a` |
| `simplify` | `simplify` | input | `{'a--b---c'\|simplify:'-'}` → `a-b-c` |
| `shorten` | `shorten` | input | `{'hello world'\|shorten:7:'..'}` → `hello..`; `shorten:7:'...':'middle'` |
| `pad` | `pad` | input | `{'ab'\|pad:4}` → `ab  ` |
| `wrap` | `wrap` | input | `{$text\|wrap:72}` |
| `break` | `break` | input | `{$text\|break}` (line breaks as `<br />`) |
| `chr`, `ord` | the same | input | `{[97]\|chr}` → `a`; `{'AB'\|ord\|exp_implode:','}` → `65,66` |
| `concat` | `concat` | param | `{concat(1, 2, 3)}` → `123`; `{'a'\|concat:'b'}` → `ab` |
| `indent` | `exp_indent` | input | `{'x'\|exp_indent:3:'custom':'-'}` → `---x` |
| `nl2br` | `exp_nl2br` | input | `{$text\|wash\|exp_nl2br}` |

`indent` and `nl2br` hint that their input is a parameter, but read it as their input when they run; the bridge
lists them in `expSmartyRenderer::INPUT_AT_RUN_TIME`.

## PHP string functions (eZTemplateStringsOperator)

Sixty-one PHP string functions are operators ([PHP string functions as template operators](../features/6.0/string-template-operators.md)).
All are **input** operators: the piped value is the PHP function's subject.

| Name in `.stpl` | Operators |
|---|---|
| with `exp_` | `exp_strip_tags`, `exp_strlen`, `exp_substr`, `exp_number_format` |
| as they are | `addslashes`, `addcslashes`, `bin2hex`, `hex2bin`, `chunk_split`, `convert_uudecode`, `convert_uuencode`, `hebrev`, `html_entity_decode`, `htmlentities`, `htmlspecialchars_decode`, `lcfirst`, `levenshtein`, `ltrim`, `rtrim`, `metaphone`, `quoted_printable_decode`, `quoted_printable_encode`, `quotemeta`, `similar_text`, `soundex`, `sprintf`, `vsprintf`, `str_contains`, `str_ends_with`, `str_starts_with`, `str_getcsv`, `str_shuffle`, `str_split`, `str_word_count`, `strcasecmp`, `strcmp`, `strcoll`, `strcspn`, `stripcslashes`, `stripslashes`, `stripos`, `stristr`, `strnatcasecmp`, `strnatcmp`, `strncasecmp`, `strncmp`, `strpbrk`, `strpos`, `strrchr`, `strripos`, `strrpos`, `strspn`, `strstr`, `strtok`, `strtr`, `substr_compare`, `substr_count`, `substr_replace`, `ristring`, `rstring` |

```smarty
{$text|exp_substr:0:5}
{if $text|str_contains:'Expo'}...{/if}
{'%s has %d items'|sprintf:$name:$count}
{1234567.891|exp_number_format:2:'.':','}
{$node.data_map.success_text.data_text|exp_strip_tags}
{(first_set($form.parent.parent.vars, $form.parent.vars))|expattr:'name'|ristring:'_':'-'}
```

Smarty's own `substr`, `strlen`, `strip_tags` and `number_format` are PHP's functions with the same order of
parameters, so the unprefixed names usually print the same; the prefixed names are what the converter writes and
what is guaranteed to behave as in the `.tpl`.

## Digests and units (eZTemplateDigestOperator, eZTemplateUnitOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `md5`, `crc32`, `rot13` | the same | input | `{$module_result.content\|md5}`; `{'a1z'\|rot13}` → `n1m` |
| `si` | `si` | input | `{1048576\|si:'byte'}` → `1.00 MB` |

## Dates and locale (eZTemplateLocaleOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `l10n` | `l10n` | input | `{$node.object.published\|l10n:'shortdate'}`; types `time`, `shorttime`, `date`, `shortdate`, `datetime`, `shortdatetime`, `currency`, `clean_currency`, `number` |
| `locale` | `locale` | input | `{(((ezini('RegionalSettings', 'Locale'))\|locale)\|expattr:'http_locale_code')\|wash}` |
| `datetime` | `datetime` | input | `{$time\|datetime:'custom':'%d.%m.%Y'}` |
| `currentdate` | `currentdate` | param | `{currentdate()}` |
| `maketime`, `makedate` | the same | input | without an input: `maketime(null, hour, minute, second, month, day, year)`, `makedate(null, month, day, year)` |
| `gettime` | `gettime` | input | `{($time\|gettime)\|expattr:'year'}` |

## Fetching and attributes (eZTemplateExecuteOperator, eZTemplateAttributeOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `fetch` | `fetch` | param | `{$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10])}` |
| `fetch_alias` | `fetch_alias` | param | `{$children = fetch_alias('children', ['parent_node_id' => $node.node_id])}` |
| `attribute` | `attribute` | input | `{$node\|attribute:'show'}`: a table of the object's attributes, for debugging |
| `dump` | `dump` | input | `{$node\|dump}` |

More on `fetch()`: [Fetch functions](smarty-templates-operators.md#fetch-functions).

## Images (eZTemplateImageOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `texttoimage` | `texttoimage` | input | parameters as in the `.tpl` |
| `image`, `imagefile` | the same | param | parameters as in the `.tpl` |

An extension that registers an operator of the same name replaces the kernel's: in a siteaccess with
`sevenx_themes_media`, `image` is the theme's operator (an **input** operator, written `image(null, ...)`).

## Roles and policies (eZTemplateRoleOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `has_role`, `has_role_id` | the same | param | `{if has_role('Editor')}`; `{if has_role_id(5, $user_id)}` |
| `has_any_role` | `has_any_role` | param | `{if has_any_role(['Administrator', 'Publisher'])}` |
| `has_role_by_user`, `has_role_by_user_id` | the same | param | `{if has_role_by_user($user, 5)}`, parameters as in the `.tpl` |
| `has_policy`, `has_policy_by_user` | the same | param | `{if has_policy('content', 'edit')}` |

They read their parameters only (the current user is the default): write them as in the `.tpl`. A value piped into
them becomes the first parameter, so `$current_user|has_role:'Editor'` asks for a role named after the user; write
`has_role('Editor')`. Details:
[Role and policy checks in templates](../features/6.0/role-and-policy-template-operators.md).

## Extensions, installation and environment (lib and kernel)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `expinfo` | `expinfo` | param | `{if expinfo('explayouts_ui')}...{/if}` |
| `installation_name`, `is_production_system` | the same | param | `{installation_name()}`; `{installation_name(true)}` (empty on a production system) |
| `exp_environment`, `exp_environment_badge` | the same (own `exp_` names) | param | `{exp_environment()}` |
| `exp_demo_mode` | `exp_demo_mode` | param | `{if exp_demo_mode()}...{/if}` |
| `audit_label` | `audit_label` | input | parameters as in the `.tpl` |
| `csp_nonce` | `csp_nonce` | param | `<script nonce="{csp_nonce()}">...</script>`; never inside `{expcacheblock}` ([Security and escaping](smarty-templates-security.md)) |
| `view_parameter` | `view_parameter` | input | `{$value\|view_parameter}`: a value encoded for `/(name)/<value>` |
| `transfer_tools`, `transfer_actions`, `transfer_related`, `transfer_tool` | the same | param | parameters as in the `.tpl` |
| `ezpackage` | `ezpackage` | input | parameters as in the `.tpl` |
| `ezmodule` | `ezmodule` | param | `{if ezmodule('content/edit')}` |
| `module_params` | `module_params` | param | `{module_params()}` |
| `ezpreference` | `ezpreference` | param | `{ezpreference('admin_left_size')}` |

## URLs, settings and the request (eZURLOperator, eZi18nOperator)

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `ezurl` | `ezurl` | input | `<a href={$node.url_alias\|ezurl}>`; `{'/content/view'\|ezurl:'no'}`; `{'/content/view'\|ezurl:'no':'full'}` |
| `ezroot` | `ezroot` | input | `<img src={$image.url\|ezroot} />` |
| `ezdesign` | `ezdesign` | input | `<link rel="stylesheet" href={'stylesheets/site.css'\|ezdesign} />` |
| `ezimage` | `ezimage` | input | `<img src={'logo.png'\|ezimage} />` |
| `exturl` | `exturl` | input | parameters as in the `.tpl` |
| `ezsys` | `ezsys` | input | `{'indexfile'\|ezsys}` |
| `ezini` | `ezini` | param | `{ezini('SiteSettings', 'SiteName')}`; `{ezini('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `ezini_hasvariable` | `ezini_hasvariable` | param | `{if ezini_hasvariable('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `ezhttp` | `ezhttp` | param | `{ezhttp('SearchText', 'get')}` (see the note) |
| `ezhttp_hasvariable` | `ezhttp_hasvariable` | param | `{if ezhttp_hasvariable('SearchText', 'get')}` |
| `i18n` | `i18n` | input | `{'Read more'\|i18n:'design/standard/node'}`; `{'Order %id'\|i18n:'design/shop':expvoid():(['%id' => $order.order_nr])}` |
| `d18n` | `d18n` | input | as `i18n`, for a dynamic text |
| `x18n` | `x18n` | input | `{'Text'\|x18n:'myext':'design/myext'}` (extension, context, comment, arguments) |

`ezhttp` and `ezhttp_hasvariable` take the name and the type (`post`, `get`, `session`, `cookie`) as their
**parameters**. They have no template hints; the bridge knows them as operators that read their parameters only
(`expSmartyRenderer::PARAMETER_OPERATORS`), so the function form of the `.tpl` works: `{ezhttp('SearchText', 'get')}`.
`'SearchText'|ezhttp:'get'` is the same call, and `ezhttp(null, 'SearchText', 'get')`, which converted templates
wrote before, still works. Wash what it returns: `{ezhttp('SearchText', 'get')|wash}`.

`ezurl`, `ezroot`, `ezdesign` and `ezimage` print the address in double quotes by default (`href={...|ezurl}`);
`:'no'` leaves the quotes out, `:'single'` uses single quotes. With `ezurlmultisite` active, its own
operators take over these ten names with the same forms.

## Content, menus and text (kernel/common)

None of these has template hints. The **input** ones work on the value piped into them; as a function without an
input, write `null` first, with the parameters of the `.tpl`. The **param** ones read their parameters only
(`expSmartyRenderer::PARAMETER_OPERATORS`): write them as in the `.tpl`.

| Operator | Name in `.stpl` | Mode | Example |
|---|---|---|---|
| `autolink` | `autolink` | input | `{$text\|wash\|autolink}` |
| `simpletags` | `simpletags` | input | `{$text\|simpletags}` |
| `wordtoimage` | `wordtoimage` | input | `{$text\|wordtoimage}` |
| `mimetype_icon`, `class_icon`, `classgroup_icon`, `action_icon`, `icon`, `flag_icon` | the same | input | `{$node.class_identifier\|class_icon:'small'}`; `{$language.locale\|flag_icon}` |
| `icon_info` | `icon_info` | param | `{icon_info('mimetype')}` |
| `alphabet` | `alphabet` | param | `{alphabet()}` |
| `month_overview` | `month_overview` | input | `{$events\|month_overview:'published':$date}` |
| `treemenu` | `treemenu` | param | `treemenu($module_result.path, $node_id, ['folder'], 0, 5)`, parameters as in the `.tpl` |
| `content_structure_tree` | `content_structure_tree` | param | `content_structure_tree(2, ['folder'], 2)`, parameters as in the `.tpl` |
| `topmenu` | `topmenu` | param | `{topmenu('content')}` |
| `eztoc` | `eztoc` | param | `{eztoc($node.data_map.body)}` |
| `siteaccess_url` | `siteaccess_url` | param | parameters as in the `.tpl` |
| `siteaccess_menu`, `siteaccess_menu_key` | the same | param | parameters as in the `.tpl` |
| `language_switcher` | `language_switcher` | param | `{language_switcher($uri)}` |
| `pdf` (lib/ezpdf) | `pdf` | param | `{pdf('new_line')}`, parameters as in the `.tpl` |

`siteaccess_url`, `siteaccess_menu` and `siteaccess_menu_key` say they take no input (`'input' => false`), so they are
**param** operators: `siteaccess_url(a, b)` and `a|siteaccess_url:b` are the same.

## PHP functions of template.ini

`[PHP] PHPOperatorList[]` of `settings/template.ini` makes PHP functions operators: `urlencode`, `urldecode`,
`rawurlencode` and `rawurldecode` in a standard installation. They are **param** operators with one parameter:

```smarty
<a href="/search?q={$text|rawurlencode}">...</a>
```

## Operators of the shipped extensions

Operators registered by an extension's `autoloads/eztemplateautoload.php` exist only in a siteaccess where the
extension is active. Of the extensions shipped with the installation, only `ezurlmultisite` gives its operators
template hints (the same as the kernel's URL operators); **every other extension operator is an input operator**:
`$x|op:a` passes `$x` as its input, and a `.tpl` call without a pipe is written `op(null, ...)` in a `.stpl`. An
extension makes an operator without hints that reads its parameters only a **param** operator with
`[SmartySettings] ParameterOperatorList[]` in its `smarty.ini.append.php` (see
[Writing operators and functions](smarty-templates-plugins.md#operators-without-hints-that-read-their-parameters)).

| Extension | Operators |
|---|---|
| `ezjscore` | `ezscript`, `ezscript_require`, `ezscript_load`, `ezscriptfiles`, `ezcss`, `ezcss_require`, `ezcss_load`, `ezcssfiles`, `json_encode` (write `exp_json_encode`: Smarty has `json_encode`), `xml_encode`, `node_encode`, `has_access_to_limitation` |
| `eztags` | `eztags_parent_string`, `latest_tags`, `user_limitations`, `tag_icon`, `eztagscloud` |
| `explayouts` | `expl_parse`, `expl_strip`, `expl_has`, `expl_first`, `explayouts_safe_url`, `explayouts_safe_embed_url`, `explayouts_safe_color`, `explayouts_safe_tag`, `explayouts_safe_template_name`, `explayouts_safe_css_class` |
| `explayouts_ui` | `explayouts_ui_has_template` |
| `ezoe` | `ezoe_ini_section` |
| `ezfind` | `solr_escape`, `solr_quotes_escape` |
| `ezwebin`, `ezdemo` | `ezkeywordlist`, `ezarchive`, `eztagcloud`, `ezpagedata`, `ezpagedata_set`, `ezpagedata_append` |
| `ezurlmultisite` | the ten URL operators above, and `ezurl_path`, `ezurl_siteaccess`, `ezurl_roots` |
| `ezstarrating` | `fetch_starrating_data`, `fetch_starrating_stats`, `fetch_by_starrating` |
| `xrowmetadata` | `metadata`, `xrowmetadata_license`, `xrowmetadata_license_enabled`, `xrowmetadata_license_choices`, `xrowmetadata_license_info`, `xrowmetadata_license_inherited`, `xrowmetadata_license_class_settings` |
| `expai` | `expai_money`, `expai_tokens`, `expai_number`, `expai_json`, `expai_retention`, `expai_help_url` |
| `expui` | `exp_config` (own `exp_` name) |
| `cjw_newsletter` | `cjw_newsletter_preg_replace`, `cjw_newsletter_str_replace`, `cjw_newsletter_variable`, `cjwnl_condition_open`, `cjwnl_condition_else`, `cjwnl_condition_close`, `cjwnl_plaintext`, `cjwnl_richtext`, `cjwnl_interests_block`, `cjwnl_abs_url`, `cjwnl_text_underline`, `cjwnl_text_wrap`, `cjwnl_rendering_data` |
| `ezflow` | `unserialize`, `json`, `feedreader`, `red5list`, `pagelink` |
| `ezwt` | `ezcreateclasslistgroups` |
| `enhancedezbinaryfile` | `parsexml` |
| `bcwebsitestatistics` | `bc_ga_urchin`, `bc_ga_urchinOrder`, `bc_ga_xmlAttributeValue`, `bc_ga_jsEscapedString`, `bc_ga_formatNumericDecimal`, `bc_ga_script_url` |
| `str_replace` | `ezstr_replace` |
| `owsimpleoperator` | `example_sum` |
| `sevenx_themes_media` | `absolute_url`, `app`, `asset`, `component_content`, `content_link`, `content_tags`, `controller`, `embed_image`, `enhanced_link`, `exp_can_login_to_siteaccess`, `exp_path`, `exp_url`, `explayouts_render_result`, `explayouts_render_zone`, `exponential`, `fieldRelation`, `fieldRelations`, `fieldValue`, `filterChildren`, `filterFieldRelationLocations`, `filterFieldRelations`, `firstNonEmptyField`, `getParameter`, `get_netgen_open_graph`, `hasField`, `hasParameter`, `haveToPaginate`, `image`, `image_link`, `intro`, `item_content_link`, `item_image_link`, `item_params`, `item_view_template`, `json_ld`, `layout_title`, `ng_image_alias`, `ng_query`, `ng_render_field`, `ng_view_content`, `ngsite`, `ngsite_group_fields`, `ngsite_language_name`, `ngsite_topic_path`, `pagerfanta`, `parameter`, `parent`, `path`, `player`, `player_slide`, `poster`, `poster_slide`, `recipe_schema`, `redirect_to_site_root`, `render`, `render_esi`, `saveXML`, `site_url`, `siteaccess_href`, `tag_url`, `title`, `tpl_block_template`, `trans`, `video_thumbnail`, `youtube_id`, and the deprecated aliases `ibexa`, `ibexa_path`, `ibexa_url`, `nglayouts_render_result`, `nglayouts_render_zone` |

Real lines from the media port, all input operators called without an input:

```smarty
<h3 class="title"><a href="{exp_path(null, $location)}">{ng_render_field(null, $content.fields.title)}</a></h3>
<article {item_params(null, $toolbar_macros, $content, $location)} class="view-type view-type-{$view_type}">
{$__loop1 = fieldRelations(null, $content, 'authors')}
```

An operator name that starts with `exp_` and is registered (`exp_path`, `exp_url`, `exp_config`,
`exp_can_login_to_siteaccess`, `exp_environment` ...) is that operator. Only a name that is not registered has its
prefix removed: `exp_count` is `count`, `exp_implode` is `implode`. So `exp_path` can never stand for an operator
named `path`; write `path` for that one.

## Bridge helpers (not operators)

`expattr`, `expvar`, `expvoid`, `expslice`, `expsectionloop`, `exprange`, `expsequence`, `expcolumn` are modifiers of
the bridge itself; see [The helpers of the bridge](smarty-templates-operators.md#the-helpers-of-the-bridge).

## Finding the operators of your installation

The tables cover what ships with the installation. Your own extensions add operators the same way: look for
`operator_names` in `extension/*/autoloads/eztemplateautoload.php` of the extensions active in the siteaccess. To find
out the mode of an operator, look for `operatorTemplateHints()` in its class: none, or `'input-as-parameter' =>
'always'`, means **input**; `'input-as-parameter' => true` or `'input' => false` means **param**. Writing an
operator that works in both engines: [Writing operators and functions for .stpl](smarty-templates-plugins.md).

## Related pages

- [Smarty templates: operators and fetch functions](smarty-templates-operators.md): the rules behind this page
- [Template functions and blocks](smarty-templates-functions.md)
- [Troubleshooting and FAQ](smarty-templates-troubleshooting.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
