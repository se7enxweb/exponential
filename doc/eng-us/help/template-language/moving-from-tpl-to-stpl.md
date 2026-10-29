---
title: Moving from Exponential templates (.tpl) to Exponential Smarty templates (.stpl)
audience: developer, designer, app-creator
category: templates
order: 55
summary: Exponential renders Smarty 5 templates (.stpl) itself, next to .tpl - when to choose .stpl and when to stay with .tpl, what changes and what stays (operators, fetch, functions, overrides, caching, includes both ways, namespaces), the converters in both directions with every option, the marker, NOTE lines and exit codes, round trips, one template or a whole design, keeping both in step, worked examples and a side-by-side table.
keywords: Smarty, stpl, tpl, convert, converter, exp:template:convert, exp:template:convert:tpl-to-stpl, exp:template:convert:stpl-to-tpl, port, migrate, round trip, expinclude, expcacheblock, exp_ prefix
type: help
---
# Moving from Exponential templates (.tpl) to Exponential Smarty templates (.stpl)

The previous chapters compare Smarty with the template language as two different worlds. Exponential also runs Smarty
templates itself: a design can hold Smarty 5 templates with the suffix `.stpl` next to its `.tpl` templates, and the
kernel renders them with every operator, fetch function, template function, override rule and cache of the template
system. This chapter is for developers and designers who have `.tpl` templates and consider writing or converting them
to `.stpl`, or who have `.stpl` templates and need `.tpl` again. It explains when each language is the better choice,
what changes and what stays, and the two console commands that convert in either direction, with real output.

The examples of the conversion sections were produced by running the commands on a development installation; the
output is shown as the commands wrote it. Addresses use `https://www.example.com`.

## Contents

1. [When to choose .stpl, and when to stay with .tpl](#when-to-choose-stpl-and-when-to-stay-with-tpl)
2. [What stays the same](#what-stays-the-same)
3. [What changes](#what-changes)
4. [Side by side](#side-by-side)
5. [The two commands](#the-two-commands)
6. [Every option](#every-option)
7. [Output, NOTE lines and exit codes](#output-note-lines-and-exit-codes)
8. [The marker: files changed by hand](#the-marker-files-changed-by-hand)
9. [Worked example: .tpl to .stpl](#worked-example-tpl-to-stpl)
10. [Worked example: .stpl to .tpl](#worked-example-stpl-to-tpl)
11. [Round trips: what converts exactly](#round-trips-what-converts-exactly)
12. [One template or a whole design](#one-template-or-a-whole-design)
13. [Keeping both in step, and going back](#keeping-both-in-step-and-going-back)
14. [Related pages](#related-pages)

## When to choose .stpl, and when to stay with .tpl

Both languages are complete and render the same pages. Neither has to be activated, and both can be used in the same
design, template by template.

| Choose `.stpl` when | Stay with `.tpl` when |
|---|---|
| your team knows Smarty, or you hire for it | your team knows the template language |
| you want infix expressions (`$a + 1`, `$a == 2 && $b`), `{foreachelse}`, `@first`, `@iteration` | your templates are written and work; a conversion is work without a visible result |
| pages must be as fast as possible: the measured `.stpl` pages were 18 to 25 % faster on Apache with PHP-FPM and 32 to 60 % faster on Velocity ([Speed](../../guides/smarty-templates-performance.md)) | you depend on an extension that ships `.tpl` templates you override often (you can still mix) |
| a new design is started from scratch | the templates are edited by people who know only the template language |
| | you need nothing that Smarty adds |

Mixing is normal: a `.stpl` pagelayout with `.tpl` view templates, or the other way round. A `.stpl` includes a `.tpl`
with `{expinclude uri='design:...'}`, a `.tpl` includes a `.stpl` with `{include uri='design:x.stpl'}`.

## What stays the same

| Area | In a `.stpl` |
|---|---|
| Finding templates | the design fallback and `override.ini` rules work as for `.tpl`; a rule's `MatchFile` may name a `.stpl` |
| Variables | a `.stpl` sees the variables a `.tpl` in its place sees (`$node`, `$view_parameters`, `$module_result` ...); what it assigns goes back to the calling template, as `{def}` and `{set}` do |
| Objects | `$node.data_map.body.content`, `$node.path_array`: the same dot syntax, the same attributes |
| Operators | every operator of the kernel and the active extensions, as a Smarty modifier or function, running the template system's own code |
| Fetch | `fetch('content', 'list', [...])`, every module fetch function, the same permission checks |
| Template functions | `{attribute_view_gui}`, `{node_view_gui}`, `{content_view_gui}` and every other function, as tags; functions with a body (`{explblock}`) as blocks |
| Translations | `i18n` with the same contexts and translation files |
| Caching | the content view cache, the template-block cache (`{expcacheblock}` is `{cache-block}`), the full-page caches |
| Includes | both directions, with and without a namespace (`name=`) |
| Namespaces | the root, current and global namespaces are the template system's; `{expset scope=...}` and `expvar()` reach them |

## What changes

| Area | `.tpl` | `.stpl` |
|---|---|---|
| Modifier parameters | `$x\|op(a, b)` | `$x\|op:a:b` |
| Literals | `array(...)`, `hash(...)`, `true()`, `false()` | `[...]`, `['k' => v]`, `true`, `false`, `null` |
| Expressions | operators only: `sum($a, 1)`, `eq($a, 2)` | operators or Smarty's infix: `$a + 1`, `$a == 2` |
| Operators Smarty also has | `count`, `implode`, `explode`, `is_array` ... | with `exp_`: `exp_count`, `exp_implode`, `exp_explode`, `exp_is_array` |
| Assignment | `{def}`, `{set}`, `{undef}`, `{let}`, `{default}` | `{$x = ...}`, `{$x = null}`; `{expset}` for a scope or namespace |
| Output into a variable | `{set-block}`, `{append-block}` | `{capture assign=x}`, `{expsetblock}`, `{expappendblock}` |
| Includes | `{include uri=...}` | `{expinclude uri=...}` (Smarty's `{include file=...}` is Smarty's own, without overrides) |
| Loops | `{foreach}` with `offset`, `max`, `reverse`, `sequence`, `{delimiter}`; `{section}`; `{for}`; `{do}`; `{switch}` | `{foreach}` with `@first`, `@iteration`, `{foreachelse}`; helpers `expslice`, `expsequence`, `exprange`; `{if}` chains for `{switch}` |
| A loop over an expression | `{foreach fetch(...) as $x}` | assign it first: `{$list = fetch(...)}{foreach $list as $x}` |
| Caching a block | `{cache-block}` | `{expcacheblock id=N}` |
| Run once | `{run-once}` | `{exprunonce id=N}` |
| Braces in CSS and JavaScript | `{ldelim}`, `{literal}` | the same; a `{` followed by white space is text |
| Comments | `{* ... *}` | the same |
| Escaping | none automatically; `\|wash` | the same; Smarty's `\|escape` exists too, `\|wash` keeps both languages alike |
| What a template can reach | settings, content, the request through operators | the same, plus Smarty's own features (`{fetch file=...}`, `$smarty.server` ...), which `[SmartySettings] Security=enabled` restricts |

Every operator with its `.stpl` form: [operator reference](../../guides/smarty-templates-operator-reference.md). Every
tag: [template functions and blocks](../../guides/smarty-templates-functions.md).

## Side by side

| Construct | `.tpl` | `.stpl` |
|---|---|---|
| Output, washed | `{$node.name\|wash}` | `{$node.name\|wash}` |
| Translation with arguments | `{'%count items'\|i18n( 'design/mysite', , hash( '%count', $n ) )}` | `{'%count items'\|i18n:'design/mysite':expvoid():(['%count' => $n])}` |
| Link | `<a href={$node.url_alias\|ezurl}>` | `<a href={$node.url_alias\|ezurl}>` |
| Link from parts | `<a href={concat( '/content/edit/', $id )\|ezurl}>` | `<a href={(concat('/content/edit/', $id))\|ezurl}>` |
| Define | `{def $limit = 10}` | `{$limit = 10}` |
| Change | `{set $limit = 20}` | `{$limit = 20}` |
| Remove | `{undef $limit}` | `{$limit = null}` |
| Global variable | `{set scope=global persistent_variable=hash( 'left_menu', false() )}` | `{expset name='persistent_variable' value=['left_menu' => false] scope='global'}` |
| Read a global variable | `$#exp_dynamic_parts` | `expvar('exp_dynamic_parts', '')` |
| Fetch | `{def $list = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 10 ) )}` | `{$list = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10])}` |
| Condition | `{if and( $a, $b\|gt( 0 ) )}` | `{if and($a, $b\|gt:0)}` or `{if $a && $b > 0}` |
| Count | `{$list\|count}` | `{$list\|exp_count}` |
| Loop with separator | `{foreach $list as $x}{delimiter}, {/delimiter}{$x}{/foreach}` | `{foreach $list as $x}{if !$x@first}, {/if}{$x}{/foreach}` |
| Loop, empty case | `{if $list\|count}{foreach ...}...{/foreach}{else}None{/if}` | `{foreach $list as $x}...{foreachelse}None{/foreach}` |
| Counting loop | `{for 1 to 5 as $i}...{/for}` | `{foreach exprange(1, 5) as $i}...{/foreach}` |
| Include | `{include uri='design:parts/paging.tpl' item_limit=10}` | `{expinclude uri='design:parts/paging.tpl' item_limit=10}` |
| Template function | `{attribute_view_gui attribute=$node.data_map.body}` | `{attribute_view_gui attribute=$node.data_map.body}` |
| Output into a variable | `{set-block variable=subject}...{/set-block}` | `{capture assign=subject}...{/capture}` |
| Cache block | `{cache-block keys=array( $node.node_id )}...{/cache-block}` | `{expcacheblock id=1 keys=[$node.node_id]}...{/expcacheblock}` |
| Attribute of an expression | `{fetch( 'shop', 'basket' ).items}` | `{(fetch('shop', 'basket'))\|expattr:'items'}` |

## The two commands

| Command | Converts | Script | Work done by |
|---|---|---|---|
| `./console exp:template:convert:tpl-to-stpl` | every `.tpl` to a `.stpl` that prints the same | `bin/php/exptemplateconverttpltostpl.php` | `expSmartyConverter`, driven by `expTemplateDesignConverter` |
| `./console exp:template:convert:stpl-to-tpl` | every `.stpl` to a `.tpl` that prints the same | `bin/php/exptemplateconvertstpltotpl.php` | `expSmartyReverseConverter`, driven by `expTemplateDesignConverter` |

Both are the general command `./console exp:template:convert` with the engines fixed: `--from-engine=tpl
--to-engine=stpl` and the reverse (`exp` and `exponential` are other names of `tpl`, `smarty` of `stpl`). The two
converters are in `lib/smarty/classes/`, `expTemplateDesignConverter` in `lib/templatebridge/classes/`; the command
classes (`kernel/private/classes/commands/exptemplateconvert*.php`) only hand their options to it, so the same work can
be called from PHP: `( new expTemplateDesignConverter( 'tpl', 'stpl' ) )->run( $options, $out, $err )`. The commands
for every engine are described once in [Converting templates between template engines](../../guides/template-conversion.md).

**tpl to stpl** parses each `.tpl` with the template system's own parser (`eZTemplateMultiPassParser`), so it sees the
tree the template system would run, with its whitespace rules applied, and writes every node as Smarty. Before it
writes a result, it compiles it with Smarty 5 (when Smarty is installed); a result that does not compile is an error
and is not written.

**stpl to tpl** splits a `.stpl` into text, comments and tags with a tokenizer that follows Smarty's own rules, parses
every tag into an expression tree and writes it as `.tpl`. It reads what the other command writes and the common
hand-written Smarty. What has no exact `.tpl` form is never guessed: it is kept as a `{* SMARTY ... *}` comment that
prints nothing, and reported. Before it writes a result, the template system's parser must read it without an error.

Both write the newlines the other language needs to print exactly the same text: Smarty drops the newline after a tag
that prints nothing, the template system drops the newline after and before every function tag.

## Every option

The two commands take the same options; the general command `exp:template:convert` takes `--from-engine` and
`--to-engine` as well.

| Option | Meaning |
|---|---|
| `--from-engine=<tpl\|stpl>`, `--to-engine=<tpl\|stpl>` | `exp:template:convert` only: the engine of the sources and the engine to convert them to |
| `-s <siteaccess>` | the siteaccess whose extensions decide which operators and template functions exist and how each operator takes its input, and whose `override.ini` `--override-ini` copies. Use the siteaccess that serves the design |
| `--from=<dir>` | the design directory to read; every source below it is converted (`templates/` and `override/templates/` alike) |
| `--to=<dir>` | the design directory to write; each target goes to the same place below it. Default: `--from`, so each target is written next to its source |
| `--file=<file>` | convert one template: a path below `--from` (`--file=templates/pagelayout.tpl`), or, without `--from`, a file of its own, written next to it, or to `--to`, which then names the target file |
| `--override-ini=<file>` | write an `override.ini.append.php` for a siteaccess that uses the `--to` design: every override rule of the `-s` siteaccess with `MatchFile` pointing at the converted template where one was converted; tpl to stpl adds one rule per converted `templates/*.tpl` that puts the `.stpl` in its place. Needs `--from` |
| `--dry-run` | show what would be written (`WRITE` new, `REPLACE` changed, `SKIP` kept); nothing is written |
| `--force` | also replace a target that was changed or written by hand |
| `--allow-root-user` | run as root (a general option of every script; add it when you are logged in as root) |

## Output, NOTE lines and exit codes

| Line | Means |
|---|---|
| `NOTE <template>: <note>` | a construct that could not be converted exactly, or an error the template system's parser found in the source (`the source has an error: ...`) |
| `WRITE <file>`, `REPLACE <file>` | a target written new, a generated target replaced because its source changed; with `--dry-run`: would be |
| `SKIP <template>: <file> was changed after it was generated; --force replaces it` | the target's marker no longer matches its content |
| `SKIP <template>: <file> has no conversion marker (written by hand); --force replaces it` | the target was not written by the command |
| `ERROR <template>: ...` | the source could not be read or converted, or the result does not compile (Smarty) or parse (template system); nothing written for it |
| `PASS tpl->stpl <n> templates: <w> written, <u> unchanged, <s> skipped, <e> errors; <m> notes in <k> templates` | the summary, with the direction (`stpl->tpl` the other way); `to write` instead of `written` with `--dry-run` |
| `PASS wrote <file>: <a> rules pointed at .stpl, <b> rules for the design's templates` | `--override-ini` was written |
| `FAIL ...` | errors, or wrong options |

| Exit code | When |
|---|---|
| 0 | `PASS`, also when files were skipped or notes were printed |
| 1 | `FAIL`: a template could not be read, converted, checked or written, or the override file could not be written |
| 2 | wrong options: `FAIL --from must be a design directory: ...`, `FAIL --file must be an existing .tpl file: ...`, `FAIL --override-ini needs --from (the design directory)`, `FAIL --from-engine is needed: tpl, stpl` ... |
| 3 | `exp:template:convert` with two engines the installation has no converter between |

The notes of tpl to stpl, and what to do about each, are listed in
[the converter guide](../../guides/smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting). A
note of stpl to tpl names the line, the Smarty source and the reason, for example:

```
NOTE news_article.stpl: line 1: {$title = $node.data_map.title.content|default:$node.name}: Smarty's default has no exact template operator; kept as a comment
```

## The marker: files changed by hand

Every file either command writes ends with a marker comment that names the direction, the checksum of everything
before it and the source:

```
{* exp:template:convert tpl->stpl md5:c456cf0887833e135d9cc40a0ca25f7d from tags.tpl -- generated; a file changed by hand is not replaced without --force *}
```

A target is replaced only while its marker still matches its content. Running the command again on an unchanged source
leaves the file alone:

```bash
./console exp:template:convert:tpl-to-stpl --file=design/mysite/override/templates/tags.tpl --allow-root-user
```

```
PASS tpl->stpl 1 templates: 0 written, 1 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

after a hand edit of `tags.stpl`, it is skipped:

```
SKIP tags.tpl: design/mysite/override/templates/tags.stpl was changed after it was generated; --force replaces it
PASS tpl->stpl 1 templates: 0 written, 0 unchanged, 1 skipped, 0 errors; 0 notes in 0 templates (a skipped file was changed or written by hand: --force replaces it)
```

with `--dry-run --force` you see that it would be replaced:

```
Dry run: nothing is written.
REPLACE design/mysite/override/templates/tags.stpl
PASS tpl->stpl 1 templates: 1 to write, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

and with `--force` alone it is:

```
REPLACE design/mysite/override/templates/tags.stpl
PASS tpl->stpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

The marker is a comment, so it prints nothing, and it is removed from a source before converting. Delete the marker
line of a file to make it yours for good: it then counts as written by hand.

## Worked example: .tpl to .stpl

### A full view with a fetch, a function, a loop and an include

The source, `full_folder.tpl`:

```
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                  'limit', 10,
                                                  'sort_by', $node.sort_array ) )}
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    <p>{'%count items'|i18n( 'design/mysite', , hash( '%count', $children|count ) )}</p>
    {foreach $children as $child}
        {node_view_gui view='line' content_node=$child}
    {/foreach}
    {include uri='design:parts/paging.tpl' item_count=$node.children_count item_limit=10}
</div>
{undef $children}
```

```bash
./console exp:template:convert:tpl-to-stpl --file=design/mysite/override/templates/full_folder.tpl --allow-root-user
```

```
WRITE design/mysite/override/templates/full_folder.stpl
PASS tpl->stpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

The result, `full_folder.stpl`:

```smarty
{$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10, 'sort_by' => $node.sort_array])}
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    <p>{'%count items'|i18n:'design/mysite':expvoid():(['%count' => $children|exp_count])}</p>
    {foreach $children as $child}
        {node_view_gui view='line' content_node=$child}    {/foreach}
    {expinclude uri='design:parts/paging.tpl' item_count=$node.children_count item_limit=10}</div>{$children = null}
{* exp:template:convert tpl->stpl md5:bd6e0a97ddf340e97a9b76fccb0ec0e2 from full_folder.tpl -- generated; a file changed by hand is not replaced without --force *}
```

What happened:

- `{def}` became an assignment, `hash()` an array literal, `{undef}` an assignment of `null`.
- The empty comment parameter of `i18n` became `expvoid()`, `count` became `exp_count` (Smarty has a `count`).
- `{include}` became `{expinclude}`; the URI keeps its `.tpl` name, so the override rules decide which file is used.
- The layout of the lines follows the whitespace the template system prints: it drops the newline around function
  tags, so `{/foreach}` follows the line view on the same line, and the converter writes exactly what the `.tpl`
  printed.

### A loop with a delimiter, and a section with show=

The source, `tags.tpl`:

```
<p class="tags">{foreach $node.data_map.tags.content.keywords as $index => $keyword}{delimiter}, {/delimiter}<a href={concat( '/content/keyword/', $keyword )|ezurl}>{$keyword|wash}</a>{/foreach}</p>
{section show=$node.can_edit}<a href={concat( '/content/edit/', $node.contentobject_id )|ezurl}>{'Edit'|i18n( 'design/mysite' )}</a>{/section}
```

The result:

```smarty
<p class="tags">{foreach $node.data_map.tags.content.keywords as $index => $keyword}
{if !$keyword@first}
, {/if}
<a href={(concat('/content/keyword/', $keyword))|ezurl}>{$keyword|wash}</a>{/foreach}
</p>{if $node.can_edit}
<a href={(concat('/content/edit/', $node.contentobject_id))|ezurl}>{'Edit'|i18n:'design/mysite'}</a>{/if}
```

The `{delimiter}` became an `{if !$keyword@first}` at the top of the body; the `{section}` with only `show=` became
an `{if}`; an operator applied to the result of `concat()` got parentheses. The newlines after `{foreach ...}` and
`{if ...}` are dropped by Smarty, so the output is the one line of the `.tpl`.

## Worked example: .stpl to .tpl

The other command converts its own output back. Into a file of its own, so the `.tpl` source stays as it is:

```bash
./console exp:template:convert:stpl-to-tpl --file=design/mysite/override/templates/full_folder.stpl \
    --to=var/tmp/full_folder.tpl --allow-root-user
```

```
WRITE var/tmp/full_folder.tpl
PASS stpl->tpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

`full_folder.stpl` above gives (the marker line left out):

```
{def $children = fetch('content', 'list', hash('parent_node_id', $node.node_id, 'limit', 10, 'sort_by', $node.sort_array))}
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    <p>{'%count items'|i18n('design/mysite', , hash('%count', $children|count))}</p>
    {foreach $children as $child}
        {node_view_gui view='line' content_node=$child}    {/foreach}
    {include uri='design:parts/paging.tpl' item_count=$node.children_count item_limit=10}</div>{undef $children}
```

and `tags.stpl` gives the `{delimiter}` back:

```
<p class="tags">{foreach $node.data_map.tags.content.keywords as $index => $keyword}{delimiter}
, {/delimiter}
<a href={concat('/content/keyword/', $keyword)|ezurl}>{$keyword|wash}</a>{/foreach}
</p>{if $node.can_edit}
<a href={concat('/content/edit/', $node.contentobject_id)|ezurl}>{'Edit'|i18n('design/mysite')}</a>{/if}
```

### Hand-written Smarty

A `.stpl` written by hand, `news_article.stpl`, with infix operators, `{foreachelse}`, `@first`, an `{expinclude}` and Smarty's `default`:

```smarty
{$title = $node.data_map.title.content|default:$node.name}
<article class="article">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.image.has_content && $node.data_map.image.content.is_valid}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}
    {/if}
    <ul class="related">
    {foreach $node.object.related_contentobject_array as $related}
        {if !$related@first}<li class="sep"></li>{/if}
        <li><a href={$related.main_node.url_alias|ezurl}>{$related.name|wash}</a></li>
    {foreachelse}
        <li>{'Nothing related.'|i18n:'design/mysite'}</li>
    {/foreach}
    </ul>
    {expinclude uri='design:parts/share.stpl' node=$node}
    <p>{$node.children_count + 1} items, {$node.object.published|l10n:'shortdate'}</p>
</article>
```

```bash
./console exp:template:convert:stpl-to-tpl --file=design/mysite/override/templates/news_article.stpl --allow-root-user
```

```
NOTE news_article.stpl: line 1: {$title = $node.data_map.title.content|default:$node.name}: Smarty's default has no exact template operator; kept as a comment
WRITE design/mysite/override/templates/news_article.tpl
PASS stpl->tpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 1 notes in 1 templates
```

The result, `news_article.tpl` (the marker line left out):

```
{* SMARTY (not converted: Smarty's default has no exact template operator): {$title = $node.data_map.title.content|default:$node.name} *}<article class="article">
    <h1>{$node.name|wash}</h1>
    {if and($node.data_map.image.has_content, $node.data_map.image.content.is_valid)}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}

    {/if}
    <ul class="related">
    {if $node.object.related_contentobject_array|count}{def $__index_related = -1}{foreach $node.object.related_contentobject_array as $related}{set $__index_related = $__index_related|inc}
        {if not(eq($__index_related, 0))}<li class="sep"></li>{/if}
        <li><a href={$related.main_node.url_alias|ezurl}>{$related.name|wash}</a></li>
    {/foreach}{undef $__index_related}{else}
        <li>{'Nothing related.'|i18n('design/mysite')}</li>
    {/if}
    </ul>
    {include uri='design:parts/share.stpl' node=$node}

    <p>{sum($node.children_count, 1)} items, {$node.object.published|l10n('shortdate')}</p>
</article>
```

- `&&` became `and()`, `+` became `sum()`, `{foreachelse}` an `{if ...|count}` around the loop with `{else}`.
- `@first` became a counter the `.tpl` keeps itself (`$__index_related`), since that `{if}` is not the first thing in
  the body (the converter turns `{if !$x@first}` into `{delimiter}` only when it is).
- `{expinclude}` became `{include}`; the included file is still the `.stpl`, which a `.tpl` may include.
- Smarty's `default` has no exact operator in the template language: the line is kept as a comment and reported.
  Rewrite it by hand: `{def $title = first_set( $node.data_map.title.content, $node.name )}` is close, but not the
  same (`default` also replaces an empty string).

## Round trips: what converts exactly

The media design (516 templates, 58 of which contain errors the template system's parser reports in the source) was
converted `.tpl` to `.stpl` to `.tpl`:

| Check | Templates |
|---|---|
| the `.tpl` parses without an error | 516 |
| converting the `.tpl` again gives the first `.stpl` again | 512 |
| converting that `.stpl` back again gives the same `.tpl` | 512 |
| the same template tree as the original, up to equivalent spellings | 403 |
| the same template tree as the original | 209 |
| byte for byte the original | 15 |

The differences are spelling (spaces inside parentheses, `{def}` where the original had `{set}`), a `{section}` with
only `show=` that comes back as an `{if}`, and the source errors the parser drops; the output is the same. Every case
of the template engine's own tests (453 cases) renders the same after `.tpl` to `.stpl` to `.tpl`, and every engine and
operator case gives the same output as a `.stpl` (`tests/tests/lib/smarty/expSmartyOperatorParityTest.php`).

Never converted from `.stpl` to `.tpl`: Smarty's own modifiers without an exact operator (`escape`, `default`,
`truncate`, `date_format`, `replace`, `json_encode` ...), `$smarty`, `===` and `!==`, object method calls, static class
members, constants, an assignment to an array element, Smarty's `{include file=...}`, `{section}`, `{strip}`,
`{capture name=...}`, `{for}` with `step` or `max`. The full mapping in both directions:
[the converter guide](../../guides/smarty-templates-converter.md#2-what-it-writes).

## One template or a whole design

**One template** is the way to start: convert the template you work on, point an override rule at it, compare the
page.

```bash
./console exp:template:convert:tpl-to-stpl -s site --from=design/mysite --file=override/templates/folder.tpl --dry-run --allow-root-user
```

```
Dry run: nothing is written.
WRITE design/mysite/override/templates/folder.stpl
PASS tpl->stpl 1 templates: 1 to write, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Without `--dry-run` it writes `design/mysite/override/templates/folder.stpl` next to the `.tpl`. Change the rule's
`MatchFile` to `folder.stpl` and clear the template caches.

**A whole design** goes into a design of its own, served by a siteaccess of its own whose design list keeps the
original as the fallback:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --override-ini=var/tmp/smarty-override.ini.append.php --allow-root-user
```

```
PASS wrote var/tmp/smarty-override.ini.append.php: 74 rules pointed at .stpl, 444 rules for the design's templates
PASS tpl->stpl 516 templates: 516 written, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Then copy the override file into the new siteaccess, set its `[DesignSettings]`, make it known in `SiteList[]` and
`AvailableSiteAccessList[]`, give the Anonymous role's `user/login` policy the new siteaccess, and clear the caches.
The whole procedure, with the checks page by page: [Migrating a design](../../guides/smarty-templates-migrating.md).

## Keeping both in step, and going back

While both exist, one of them is the source:

- **The `.tpl` design is the source.** Change only `.tpl` files and convert again after each change. Unchanged
  targets stay as they are, changed sources replace their generated targets, hand-edited targets are skipped. A dry run
  lists the `.stpl` files that are behind their `.tpl` (`REPLACE`).
- **The `.stpl` design is the source.** Change only `.stpl` files. Where a `.tpl` is still needed, convert back with
  `exp:template:convert:stpl-to-tpl`; what has no `.tpl` form is reported and kept as a comment.

Going back from `.stpl` to `.tpl` for good is the same command on the whole design, into a design of its own or next to
the `.stpl` files, followed by override rules that point at the `.tpl` (`--override-ini` writes them). Writing into the
original `.tpl` design is safe: its own files have no marker, so every one is skipped unless `--force` is given.

## Related pages

- Previous: [Moving a template from Smarty](moving-from-smarty.md)
- Next: [Lexical structure](lexical-structure.md)
- [Smarty templates (.stpl) next to .tpl](../../features/6.0/smarty-templates.md), the feature page
- Guides: [a first .stpl, step by step](../../guides/smarty-templates-tutorial.md),
  [the converter and porting a design](../../guides/smarty-templates-converter.md),
  [migrating a design](../../guides/smarty-templates-migrating.md),
  [operators and fetch functions](../../guides/smarty-templates-operators.md),
  [operator reference](../../guides/smarty-templates-operator-reference.md),
  [template functions and blocks](../../guides/smarty-templates-functions.md),
  [caching and performance](../../guides/smarty-templates-caching.md),
  [performance (Speed)](../../guides/smarty-templates-performance.md),
  [troubleshooting and FAQ](../../guides/smarty-templates-troubleshooting.md),
  [security and escaping](../../guides/smarty-templates-security.md)
- [Converting templates between template engines](../../guides/template-conversion.md): the `exp:template:convert` commands, every option, output line and exit code
- Command reference: [exp:template:convert](../../examples/commands/exptemplateconvert.md),
  [exp:template:convert:tpl-to-stpl](../../examples/commands/exptemplateconverttpltostpl.md),
  [exp:template:convert:stpl-to-tpl](../../examples/commands/exptemplateconvertstpltotpl.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
