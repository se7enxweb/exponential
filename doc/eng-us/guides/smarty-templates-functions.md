# Smarty templates: template functions and blocks

This page is for template developers who need the tags of a `.stpl`: the Exponential template functions bridged as
Smarty tags (`{attribute_view_gui}`, `{node_view_gui}` ...), the functions with a body bridged as Smarty blocks
(`{explblock}`), the tags and blocks of the bridge itself (`{expinclude}`, `{expset}`, `{expundefall}`, `{expfetch}`,
`{expsetblock}`, `{expappendblock}`, `{expcacheblock}`, `{exprunonce}`), and Smarty's own tags. For each: its
parameters, which namespace it runs in, what it hands back, and an example. The code is in `lib/smarty/classes/`:
`expSmartyExtension` decides what a tag name stands for, the other classes are the tags.

## How a tag name is resolved

When Smarty compiles a tag it does not know itself, it asks `expSmartyExtension`, which answers, in this order:

1. **Smarty's own tags** always come first: `{if}`, `{foreach}`, `{for}`, `{while}`, `{section}`, `{include}`,
   `{capture}`, `{assign}`, `{function}`, `{call}`, `{block}`, `{extends}`, `{literal}`, `{ldelim}`, `{strip}`,
   `{nocache}`, `{config_load}`, `{debug}`, `{eval}`, `{setfilter}`, and the functions `{counter}`, `{cycle}`,
   `{fetch}`, `{html_checkboxes}`, `{html_image}`, `{html_options}`, `{html_radios}`, `{html_select_date}`,
   `{html_select_time}`, `{html_table}`, `{mailto}`, `{math}`, `{textformat}`.
2. **The bridge's own tags**: `{expinclude}`, `{expset}`, `{expundefall}`, `{expfetch}` and the blocks
   `{expsetblock}`, `{expappendblock}`, `{expcacheblock}`, `{exprunonce}`.
3. **The functions in `[SmartySettings] FunctionList[]`** of `settings/smarty.ini`, always as tags without a body.
4. **Any other template function** the template system has registered in the siteaccess, unless it is template
   structure: as a tag when it has no body, as a block (`{name}...{/name}`) when it has one (its class says
   `hasChildren()`).

Never bridged, because they are the structure of a `.tpl` and have Smarty equivalents: `if`, `elseif`, `else`,
`foreach`, `for`, `while`, `do`, `def`, `undef`, `set`, `let`, `default`, `section`, `section-else`,
`section-exclude`, `section-include`, `delimiter`, `switch`, `case`, `set-block`, `append-block`, `cache-block`,
`run-once`, `include`, `literal`, `ldelim`, `rdelim`, `break`, `continue`, `skip`
(`expSmartyExtension::NOT_BRIDGED`). A function whose name has a hyphen (`debug-log`, `debug-timing-point`,
`debug-accumulator`, `debug-trace`) cannot be a Smarty tag name.

The answer for each name is remembered for the life of the PHP process. A function that becomes available later (an
extension activated) is seen after PHP-FPM is reloaded or Velocity restarted.

## Template functions as tags

```smarty
{attribute_view_gui attribute=$node.data_map.body}
{node_view_gui view='line' content_node=$child}
{content_view_gui view='embed' content_object=$object}
{attribute_view_gui attribute=$node.data_map.body assign=body_html}
```

- **Parameters** are named, as in the `.tpl`, with Smarty values: `view='line'`, `content_node=$child`,
  `image_class=(concat('i', $size))`. A parameter whose value is an expression with a pipe or an operator goes in
  parentheses. Parameter names must be letters, digits and underscores starting with a letter, or the call prints
  nothing and logs `invalid parameter name for <function>: <name>`.
- **`assign=name`** puts the output into the Smarty variable `$name` instead of printing it.
- **How it runs.** Each call is a small generated `.tpl` holding just the call (`{node_view_gui content_node=$content_node
  view=$view}`) in `<cache directory>/smarty/functions/<function>-<md5 of the parameter names>.tpl`, compiled and
  cached by the template system like any template. So a function runs exactly as it does from a `.tpl`, and its own
  templates may be `.tpl` or `.stpl`.
- **Namespace.** The call runs in a namespace of its own (`ExpSmarty<n>`) that holds only the parameters you pass.
  The function's templates do not see the other variables of your `.stpl`: pass what they need as parameters (the
  `*_view_gui` functions hand every extra named parameter to their template as a variable). Global variables
  (`$#name` in a `.tpl`) stay global. A function that sets variables (the `.tpl` `{sequence}`) sets them in that
  namespace, so nothing comes back; use the Smarty equivalent (`expsequence`, Smarty's `{cycle}`).

### The functions of the kernel

| Function | Main parameter | Its templates | `FunctionList[]` |
|---|---|---|---|
| `attribute_view_gui` | `attribute` | `content/datatype/view/<datatype>` (by view, class and attribute) | yes |
| `attribute_edit_gui` | `attribute` | `content/datatype/edit/<datatype>` | yes |
| `attribute_result_gui` | `attribute` | `content/datatype/result/<datatype>` | yes |
| `attribute_pdf_gui` | `attribute` | `content/datatype/pdf/<datatype>` | yes |
| `attribute_diff_gui` | `attribute` | `content/datatype/diff/<datatype>` | no (bridged as a tag anyway) |
| `node_view_gui` | `content_node`, `view` | `node/view/<view>` | yes |
| `content_view_gui` | `content_object`, `view` | `content/view/<view>` | yes |
| `content_pdf_gui` | `content_object` | `content/pdf/<view>` | no |
| `content_version_view_gui` | `content_version` | `content/version/view/<view>` | yes |
| `related_view_gui` | `related_object` | `content/related/<view>` | yes |
| `shop_account_view_gui` | `order` | `shop/accounthandlers/<view>` | yes |
| `class_attribute_view_gui` | `class_attribute` | `class/datatype/view/<datatype>` | yes |
| `class_attribute_edit_gui` | `class_attribute` | `class/datatype/edit/<datatype>` | yes |
| `collaboration_view_gui`, `collaboration_icon` | `collaboration_item` | `collaboration/handlers/view`, `.../icon` | no |
| `collaboration_simple_message_view` | `collaboration_message` | `collaboration/message/view` | no |
| `collaboration_participation_view` | `collaboration_participant` | by its match rules (`template_root` of type `multi_match`) | no |
| `event_view_gui`, `event_edit_gui` | `event` | `workflow/eventtype/view`, `.../edit` | no |
| `tool_bar` | `name`, `view` | the tool bar of `toolbar.ini` | no |
| `menu` | `name` | the menu of `menu.ini` | no |

"no" in the last column changes nothing for these: every function the template system knows is bridged. Add a name
to `FunctionList[]` only to force a function with a body to be called as a plain tag.

Real lines from the media port:

```smarty
<div class="exp_richtext-field">{attribute_view_gui attribute=$af_map.body}</div>
{attribute_view_gui attribute=$node.object.data_map.billboard}
```

## Functions with a body as blocks

A template function that has a body in the `.tpl` (`hasChildren()` is true) is a Smarty block:

```smarty
{explblock name='content'}
<div class="content-view-full">
    {explblock name='article_body'}
    <div class="full-page-body">...</div>
    {/explblock}
</div>
{/explblock}
```

The block's body is rendered by Smarty first, then handed to the function as its children, one text node
(`expSmartyBlockFunction`). Functions that wrap or post-process their children (as `{explblock}` of Exponential
Layouts does: it marks a region of the output so a layout can take it out) behave as in a `.tpl`. A function that looks
at the structure of its children instead of their output sees only that text. The parameters are passed as values;
the function runs in the namespace of the `.stpl`.

Bridged blocks of the shipped extensions: `{explblock name=... [if=...] [wrap=...]}` (explayouts). Others
(`block_view_gui` and `block_edit_gui` of ezflow, the `powercontent_*` functions) are bridged by the same rules
when their extension is active.

## The bridge's tags

### {expinclude}: the template system's include

```smarty
{expinclude uri='design:pagelayout/head/title.tpl'}
{expinclude uri='design:parts/paging.stpl'}
{expinclude uri=(concat('design:explayouts/block/list/', $list_view_type, '.tpl')) block=$block}
{expinclude uri='design:navigator/google.tpl' name='navigator' page_uri=$node.url_alias item_count=$count view_parameters=$view_parameters item_limit=10}
```

| Parameter | Meaning |
|---|---|
| `uri` | required: a template URI (`design:...`, `file:...`), through the design resource and the override rules; `.tpl` or `.stpl` |
| `name` | optional: run the included template in a namespace of this name below the current one |
| any other | a variable of the included template, for that template only |

- **Without `name=`** the included template shares the namespace of the including one: it sees its variables, and
  what it defines, changes or removes is visible afterwards. When the URI resolves to a `.stpl`, it runs as a Smarty
  subtemplate directly (`expSmartyRenderer::resolveURI()`, `renderSubtemplate()`), with the same effect and without
  the detour through the template system. When it resolves to a `.tpl`, the `.stpl`'s variables are copied into the
  namespace first and read back afterwards.
- **With `name=`** it runs through the template system in that namespace; its variables do not come back.
- The other parameters replace variables of the same name for the included template only; afterwards the old values
  are back.
- A missing `uri` stops the render: `expinclude: missing parameter 'uri'`.
- The `.tpl` `{include}` maps to it one to one (the converter writes `{expinclude}` for every `{include}`).

### {expset}: set with a scope or a namespace

```smarty
{expset name='persistent_variable' value=['left_menu' => false] scope='global'}
{expset name='cache_ttl' value=0 scope='global'}
{expset name='title' value=$node.name scope='root'}
```

| Parameter | Meaning |
|---|---|
| `name` | required: the variable name |
| `value` | its value (`null` when left out) |
| `scope` | `relative` (default, the current namespace), `root` (the template's root namespace) or `global` |
| `namespace` | a namespace below the scope; with `scope='global'`, the global namespace of that name |

For a variable of the template itself, a plain Smarty assignment does the same (`{$title = $node.name}`); the
renderer writes it back when the template ends. `{expset}` is for the cases a `.tpl` writes `{set scope=global ...}`
or `{set name=Namespace ...}`. When the target is the template's own root namespace, the Smarty variable is set too.
`expset: missing parameter 'name'` when `name` is missing.

### {expundefall}: remove every variable

`{expundefall}` is the `.tpl` `{undef}` without parameters: it removes every variable of the template's root
namespace and sets every Smarty variable of the template to `null`. To remove one variable, assign `null`:
`{$tag = null}`.

### {expfetch}: a fetch as a tag

```smarty
{expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10 sort_by=$node.sort_array}
```

`assign`, `module` and `function` are required (`expfetch: missing parameter 'module'`); every other parameter is a
parameter of the fetch function. It is the same as
`{$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10, 'sort_by' => $node.sort_array])}`.

## The bridge's blocks

### {expsetblock} and {expappendblock}

```smarty
{expsetblock variable='cache_ttl' scope='global'}0{/expsetblock}
{expappendblock variable='scripts' scope='global'}<script src="/a.js"></script>{/expappendblock}
```

| Parameter | Meaning |
|---|---|
| `variable` | required: the variable name (`expsetblock: missing parameter 'variable'`) |
| `scope` | `relative` (default), `root` or `global` |
| `name` | a namespace below the scope |

`{expsetblock}` puts the block's output into the variable; `{expappendblock}` appends it to an array (a new array if
the variable is not one). For the template's own variable, Smarty's `{capture assign=x}...{/capture}` is the same as
`{set-block variable=x}` and is what the converter writes; it is written back to the calling template when the `.stpl`
ends.

Smarty drops the newline right after a tag that prints nothing. `{expsetblock ...}` followed by a line break and `1`
stores `1`, not a line break and `1`: the media pagelayout writes it that way on purpose.

### {expcacheblock}

```smarty
{expcacheblock id=1 keys=[$node.node_id, $view_parameters.offset] expiry=3600}
    ...
{/expcacheblock}
```

| Parameter | Meaning |
|---|---|
| `id` | a number unique in the file; with the file name it is the block's place (`stpl:<file>:<id>`), part of the key |
| `keys` | a value or an array of values added to the key |
| `expiry` | seconds the block lives; default `eZTemplateCacheFunction::DEFAULT_TTL` (two hours); `0` means no time limit |
| `ignore_content_expiry` | `true`: a content publish does not expire the block |
| `subtree_expiry` | a node id or a path: only a publish below that node expires the block (and the global content expiry is ignored) |

It is the template system's `{cache-block}`: stored in the template-block cache, keyed by the keys, its place and the
siteaccess (plus the administration design variant chosen by the UI switch), expired as a `.tpl` cache block is. When
the block is in the cache, its body does not run. With `[TemplateSettings] TemplateCache` not `enabled`, the body
always runs and nothing is stored. Everything about keys, expiry and the per-visitor parts is in
[Caching and performance](smarty-templates-caching.md).

### {exprunonce}

```smarty
{exprunonce id=1}<script src={'javascript/gallery.js'|ezdesign}></script>{/exprunonce}
```

The body is output the first time the block runs in a request only (the `.tpl` `{run-once}`); a template included many
times on a page loads its script once. `id` is unique in the file; with the file name it is the key.

## Smarty's own tags that matter here

| Tag | Use in a `.stpl` |
|---|---|
| `{$x = value}`, `{assign var='x' value=...}` | define or change a variable; written back to the calling template |
| `{capture assign=x}...{/capture}` | output into a variable (the `.tpl` `{set-block variable=x}`) |
| `{if}`, `{elseif}`, `{else}` | conditions |
| `{foreach $list as $k => $v}`, `{foreachelse}` | loops over a variable; `$v@index`, `@iteration`, `@first`, `@last`, `@total` |
| `{for $i = 1 to 10}` | counting up |
| `{while}`, `{break}`, `{continue}` | loops |
| `{include file='part.stpl' a=$b}` | another `.stpl` from the designs' template directories, without override rules; never a `.tpl` |
| `{literal}...{/literal}` | text with braces (JavaScript, CSS) |
| `{ldelim}`, `{rdelim}` | a single brace |
| `{function name=...}`, `{call}` | Smarty template functions defined in the file |
| `{cycle values='odd,even'}` | alternate values in a loop |
| `{section}` | **Smarty's** section, not the `.tpl` one: other parameters and variables. The `.tpl` `{section}` is converted to `{foreach}` |
| `{fetch file=...}` | **Smarty's** fetch of a file or URL, not the module fetch; use `fetch(...)` in an expression or `{expfetch}` |

## Related pages

- [Smarty templates: writing and running .stpl templates](smarty-templates.md)
- [Operator reference](smarty-templates-operator-reference.md)
- [Caching and performance](smarty-templates-caching.md)
- [Writing operators and functions for .stpl](smarty-templates-plugins.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
