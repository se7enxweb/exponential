# Smarty templates: the converter and porting a design

This guide is for site builders and template developers who want a whole design in Smarty, or a Smarty design back
in `.tpl`. The conversion commands of the `exp:template:convert` command space do the work:

- `./console exp:template:convert:tpl-to-stpl` turns every `.tpl` of a design into a `.stpl` that renders the same
  output, reports what it could not convert exactly, and writes the override rules that make a siteaccess use the
  converted templates (parts 1 to 5);
- `./console exp:template:convert:stpl-to-tpl` does the reverse: every `.stpl` becomes a `.tpl` (part 6).

Both are the general command `./console exp:template:convert` with the engines fixed (`--from-engine=tpl
--to-engine=stpl` and the reverse). Every option, every output line, the exit codes and the marker that keeps files
changed by hand are explained once for all engines in
[Converting templates between template engines](template-conversion.md); this guide is about Smarty: what the
converters write, their notes, and the siteaccess that serves a converted design.

You will run the conversion, read its notes, check the result, and set up a siteaccess that serves the converted
design, with the port of the media design (516 templates) and a `smarty` siteaccess as the worked example. Plan an
hour for the setup and as long as the checks of your design take.

Read [Smarty templates: writing and running .stpl templates](smarty-templates.md) first; the
[operators guide](smarty-templates-operators.md) explains the operator forms the converter writes.

## 1. Run the converter

```bash
./console exp:template:convert:tpl-to-stpl -s <siteaccess> \
    --from=<design directory with the .tpl templates> \
    [--to=<design directory for the .stpl templates>] \
    [--file=<one template>] [--override-ini=<file to write>] [--dry-run] [--force] --allow-root-user
```

`php bin/php/exptemplateconverttpltostpl.php` with the same options is the same command. The work is done by
`expTemplateDesignConverter` (`lib/templatebridge/classes/`) with `expSmartyConverter` (`lib/smarty/classes/`) for each
template; [part 7 of the conversion guide](template-conversion.md#7-from-php-and-the-converter-registry) shows how to
call it from PHP.

| Option | Meaning |
|---|---|
| `-s <siteaccess>` | the siteaccess that uses the design today. Its extensions decide which operators and template functions exist, and how each operator takes its input; its `override.ini` is the one `--override-ini` copies. |
| `--from` | the design directory to read, for example `extension/sevenx_themes_media/design/media` |
| `--to` | the design directory to write (default: `--from`, so each `.stpl` is written next to its `.tpl`). Every `*.tpl` below `--from` (in `templates/` and `override/templates/` alike) becomes the `*.stpl` at the same place below `--to`. |
| `--file` | convert one template only: a path below `--from` (`templates/pagelayout.tpl`), or, without `--from`, a file of its own, written next to it (or to `--to`, then the target file) |
| `--override-ini` | the `override.ini.append.php` to write for a siteaccess that uses the `--to` design (part 4) |
| `--dry-run` | show what would be written (`WRITE`, `REPLACE`, `SKIP`); nothing is written |
| `--force` | also replace a `.stpl` that was changed or written by hand (see "Files changed by hand" below) |

Run it from the installation root. The media design converted into a Smarty design of its own:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --override-ini=var/tmp/smarty-override.ini.append.php --allow-root-user
```

Expected output: a `WRITE <file>` line for each template written, a `NOTE <template>: <note>` line for each construct
that could not be converted exactly (and for each error the template system's parser finds in a source), then:

```
PASS wrote var/tmp/smarty-override.ini.append.php: 74 rules pointed at .stpl, 444 rules for the design's templates
PASS tpl->stpl 516 templates: 516 written, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Every result is checked before it is written: a `.stpl` must compile with Smarty 5 (when Smarty is installed). A
template that cannot be read, converted, checked or written is an `ERROR <template>: ...` line, nothing is written
for it, and the command ends with `FAIL ...` and exit code 1. Wrong options end with `FAIL ...` and exit code 2
(`FAIL --from must be a design directory: ...`). Run it again and the summary says `516 unchanged`: a file whose
content would not change is not touched.

### Files changed by hand

Every file the command writes ends with a marker comment, on the last line:

```
{* exp:template:convert tpl->stpl md5:<checksum> from templates/pagelayout.tpl -- generated; a file changed by hand is not replaced without --force *}
```

The checksum is that of everything before the marker. When the command meets a target that already exists, it
replaces it only when the marker is there and still matches the content, that is when nobody changed the file after
it was written. Otherwise it prints

```
SKIP templates/pagelayout.tpl: extension/sevenx_themes_media/design/smarty_media/templates/pagelayout.stpl was changed after it was generated; --force replaces it
SKIP override/templates/content/view/embed/embed_file.stpl: extension/sevenx_themes_media/design/media/override/templates/content/view/embed/embed_file.tpl has no conversion marker (written by hand); --force replaces it
```

and leaves the file alone (the summary counts it as skipped; the exit code stays 0). In the media port,
`templates/pagelayout.stpl` was changed by hand after it was converted (the whole page is kept in one cache block, see
[caching and performance](smarty-templates-caching.md)), so converting the design again keeps it. The marker is a
comment at the very end of the file, so it prints nothing in either template language, and it is removed from a source
before converting. An editor that adds a newline after the marker makes the file count as changed by hand, which is
safe.

Run it with `-s` set to a siteaccess where every extension the design needs is active: a template function the
converter does not know is reported (`unknown function {explblock}: it prints nothing, as in the .tpl`) and left out.
An operator the converter does not know is written with the `exp_` prefix and as if it read its input; that still
runs when the operator exists at render time, but an operator that reads only its parameters would then get a `null`
first parameter.

## 2. What it writes

The converter parses each `.tpl` with the template system's own parser, so it sees the tree the template system
would run, with its whitespace rules already applied, and writes every node as Smarty:

| `.tpl` | `.stpl` |
|---|---|
| text | the same text; a `{` that is not followed by white space becomes `{ldelim}` |
| `{* comment *}` | nothing (the parser drops comments; a blank line can remain) |
| `{$node.data_map.title.content}`, `{$list[$i]}` | the same: dot lookups, bracket lookups for computed keys |
| an attribute of an expression | `(expression)\|expattr:'name'` |
| `$#name`, `$#Namespace:name` | `expvar('name', '')`, `expvar('name', 'Namespace')` |
| `$:item` in `{section name=Items}` | `$Items__item` |
| `$x\|op(a, b)` | `$x\|op:a:b`, or `$x\|exp_op:a:b` where Smarty has the name |
| `op(a, b)` without a pipe | `op(a, b)` for an operator that reads its parameters, `op(null, a, b)` for one that reads its input |
| `$x\|first_set($y)`, `$x\|cond(...)` | `first_set($y)`, `cond(...)`: the input is left out, since the `.tpl` ignores it |
| `hash('a', 1)`, `array(1, 2)` | `['a' => 1]`, `[1, 2]` |
| `true()`, `false()` | `true`, `false` |
| an empty parameter | `expvoid()` |
| `{if}`, `{elseif}`, `{else}`, `{while}`, `{break}` | the same; `{continue}` and `{skip}` become `{continue}` |
| `{def $a = 1}` | `{$a = 1}` |
| `{set $a = 1}` | `{$a = 1}`, in the current namespace (`$Items__a` inside a named section) |
| `{set}` or `{def}` with `scope=` other than relative, or `name=` | `{expset name='a' value=1 scope='global' namespace='...'}` |
| `{undef $a}`, `{undef}` | `{$a = null}`, `{expundefall}` |
| `{let a=1}...{/let}` | `{$a = 1}...{$a = null}` |
| `{default a=1}...{/default}` | `{if !isset($a)}{$a = 1}{/if}...` |
| `{foreach $list as $k => $v}` | the same; with `offset`, `max` or `reverse` the source is `(...)\|expslice:offset:max:reverse` |
| a `{foreach}` over an expression | `{$__loop1 = expression}{foreach $__loop1 as $v}` |
| `sequence_array=` and `sequence_var=` | `{$__sequence1 = ...}` and `{$row = $__sequence1\|expsequence:$v@index}` in the loop |
| `{delimiter}...{/delimiter}` | `{if !$v@first}...{/if}` at the top of the loop |
| `{for 1 to 5 as $i}` | `{foreach exprange(1, 5) as $i}` (counts down as well as up, as the `.tpl` does) |
| `{do}...{/do while ...}` | a `{while}` loop with a flag |
| `{section loop=$items}` | `{$__loop1 = ($items)\|expsectionloop:offset:max:reverse}{foreach $__loop1 as $key => $item}` with `$index`, `$number` (and `$sequence`), prefixed with the section's name when it has one |
| `{section show=...}` without `loop` | `{if ...}`; `{section-else}` becomes `{else}` |
| `{section-else}` in a loop | `{foreachelse}` |
| `{section-exclude}`, `{section-include}` | a skip flag set by each `match` |
| `{switch match=...}{case match=...}`, `{case in=...}` | an `{if}`/`{elseif}` chain on `$__switch1`: `==`, or `in_array` (with `expcolumn` for `key=`) |
| `{set-block variable=x}` (relative or `scope=root`) | `{capture assign=x}...{/capture}` |
| `{set-block scope=global ...}`, `{set-block name=...}` | `{expsetblock variable='x' scope='global'}...{/expsetblock}` |
| `{append-block}` | `{expappendblock ...}` |
| `{cache-block ...}` | `{expcacheblock id=<n> ...}`, numbered per file |
| `{run-once}` | `{exprunonce id=<n>}`, numbered per file |
| `{include uri=... a=$b}` | `{expinclude uri=... a=$b}`; the URI keeps its `.tpl` name |
| `{literal}...{/literal}` | its text, with braces written as `{ldelim}` |
| `{ldelim}`, `{rdelim}` | the same |
| any other template function | a tag of the same name, or a block when it has a body (`{explblock}...{/explblock}`) |
| an unknown function | nothing, with a note: the `.tpl` prints nothing for it either |

Smarty drops the newline right after a tag that prints nothing (`{if}`, `{foreach}`, an assignment, a block). The
converter writes a newline after each such tag, so the `.stpl` stays readable and prints the newlines the template
system would have printed. That is why converted files have tags on lines of their own:

```smarty
{if $block.values.title}
    {if $block.values.tag|ne:''}
{$tag = $block.values.tag}
{else}
{$tag = concat('h', $block.values.level)}
{/if}
    <{$tag} class="section-title text-center">{$block.values.title|wash}</{$tag}>
    {$tag = null}
{/if}
```

from

```
{if $block.values.title}
    {if $block.values.tag|ne('')}{def $tag = $block.values.tag}{else}{def $tag = concat('h', $block.values.level)}{/if}
    <{$tag} class="section-title text-center">{$block.values.title|wash}</{$tag}>
    {undef $tag}
{/if}
```

(`templates/explayouts/block/title/centered.tpl` of the media design and its `.stpl`.)

## 3. Its notes, and what to check after converting

Each `NOTE` names the template and what was not exact:

| Note | What to do |
|---|---|
| `unknown function {x}: it prints nothing, as in the .tpl` | an extension that registers `x` is not active in the `-s` siteaccess, or the `.tpl` has a mistake; convert with the right siteaccess, or remove the call |
| `function {x}: the name is not a Smarty tag name` | a function with a hyphen in its name (`debug-log` ...): nothing is written; remove it or replace it |
| `{if} without a condition (a parse error in the .tpl): it prints nothing, as in the .tpl` | fix the `.tpl` and convert again |
| `condition missing` | an `{elseif}` or loop without a condition: `false` is written |
| `{x} outside of its loop or switch` | a `{delimiter}`, `{case}` or `{section-else}` in the wrong place: dropped |
| `{set-block} without a variable` | the body is written as plain output |
| `a constant value was expected` | a `scope=`, `name=` or similar parameter that is not a constant |
| `parameter name 'x' is not a Smarty name` | the parameter is left out of the tag |
| `hash() with an odd number of parameters` | the last key has no value: fix the `.tpl` |
| `attribute lookup without a value`, `unknown element type`, `unknown node type` | a construct the converter cannot read: convert that part by hand |

Then check the things the converter keeps on purpose, because the `.tpl` did them:

- **`first_set` and `cond` with a pipe.** `{$x|first_set($y)}` becomes `{first_set($y)}`, because the `.tpl` ignores
  `$x`. If the author meant "`$x`, or `$y` as the default", the `.tpl` had a bug; write `first_set($x, $y)`.
- **Bare words are strings.** A `.tpl` reads an identifier without quotes or parentheses, such as `null`, as the text
  `'null'`, and so does the converted template: `{image($content_fields, $content, null, 'i320')}` became
  `{image(null, $content_fields, $content, 'null', 'i320')}` in `node/view/embed/embed_ng_article.stpl`. Where a real
  null was meant, write `null` without quotes.
- **Operators without an input** are written `op(null, ...)` (`exp_count(null, $validation.attributes)`), which is
  what the template system does; usually `$x|exp_count` reads better and gives the same result.
- **Includes keep their `.tpl` names.** `{expinclude uri='design:pagelayout/head/title.tpl'}` reaches the `.stpl`
  through the override rules of part 4; without them it includes the `.tpl` of the fallback design.
- **Inline CSS and JavaScript.** Braces became `{ldelim}`; long scripts read better in `{literal}...{/literal}`.
- **Blank lines** where `{* comments *}` were.

Smarty compiles every `.stpl` on its first render, so open the pages that use them: a compile error appears in the
debug output (label `smarty`) and as an HTML comment with debug output on ([debugging](smarty-templates.md#11-debugging)).
Compare each page with the same page on the `.tpl` siteaccess.

## 4. A siteaccess that serves the converted design

A converted design does not replace the original: a siteaccess of its own uses it, and the original design stays in
its fallback list for everything the port does not have (stylesheets, JavaScript, images, fonts and any template not
converted). In the worked example this is the siteaccess `smarty`, a copy of the media site's public siteaccess `site`, served at
`https://www.example.com/smarty/`. Nothing has to be activated for `.stpl` templates: the kernel renders them for
every siteaccess.

### 4.1 The siteaccess directory

Copy the siteaccess the design was converted for, here `site`, to `settings/siteaccess/smarty/`, then change its
`[DesignSettings]` in `settings/siteaccess/smarty/site.ini.append.php`:

```ini
# The .stpl port first; the media design stays in the chain after it, for its stylesheets,
# javascript, images and fonts, and for any template the port does not have.
[DesignSettings]
SiteDesign=smarty_media
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=media
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

The empty `AdditionalSiteDesignList[]` line clears the list the copy had before the new one is built. Keep the rest
of the copy (database, site name and URL, languages, the related siteaccesses) as it is.

In the worked example, `extension/sevenx_themes_media` adds the settings it gives the `site` siteaccess to `smarty` as well, in
`extension/sevenx_themes_media/settings/siteaccess/smarty/` (`site.ini.append.php` with the path prefix and the
theme's translations, `ezjscore.ini.append.php`, `menu.ini.append.php`). The design list and the override rules stay
in the installation's `settings/siteaccess/smarty/`, because a siteaccess's own files outrank an extension's.

### 4.2 The override rules

Put the file `--override-ini` wrote in the siteaccess:

```bash
cp var/tmp/smarty-override.ini.append.php settings/siteaccess/smarty/override.ini.append.php
```

The extension ships the file of its port as `extension/sevenx_themes_media/doc/smarty/override.ini.append.php`. It
holds two kinds of rules:

1. Every override rule of the `-s` siteaccess, with `MatchFile` pointing at the converted `.stpl` where one was
   converted, in the same order and with the same `Priority` (a lower priority is tried first):

   ```ini
   [node/view/full#frontpage]
   Source=node/view/full.tpl
   MatchFile=node/view/full/full_frontpage.stpl
   Subdir=templates
   Match[class_identifier]=frontpage
   Priority=1
   ```

2. One rule per converted template of the design's `templates/` directory, named `stpl_<path>`, that puts the `.stpl`
   in the place of the `.tpl`. The template system and the includes ask for `pagelayout.tpl` or
   `content/search.tpl`; these rules answer with the `.stpl`:

   ```ini
   [stpl_pagelayout]
   Source=pagelayout.tpl
   MatchFile=../../templates/pagelayout.stpl
   Subdir=templates
   ```

   `MatchFile` is looked up below `override/templates/` of each design, so `../../templates/` is the design's own
   `templates/` directory, where the `.stpl` sits next to where the `.tpl` was.

The media port's file has 637 rules.

### 4.3 Make the siteaccess known

In `settings/override/site.ini.append.php`, add the siteaccess to the list of sites and of available siteaccesses
(next to the entries that are already there; do not repeat the empty `[]` line):

```ini
[SiteSettings]
SiteList[]=smarty

[SiteAccessSettings]
AvailableSiteAccessList[]=smarty
```

How a request reaches the siteaccess follows `[SiteAccessSettings] MatchOrder`. With `uri;host` (as in the worked example),
`/smarty/` at the start of the address selects it. With host matching, add a `HostMatchMapItems[]` line for a host of
its own instead.

### 4.4 Let visitors in without signing in

A visitor may use a siteaccess only when the Anonymous role allows `user/login` for it; otherwise every page of the
new siteaccess asks for a sign-in. In the administration: **Users > Roles and policies**, open **Anonymous**, edit it,
and edit the policy `user/login`: add `smarty` to its **SiteAccess** limitation (or add a policy `user/login` with the
limitation SiteAccess `smarty`), and save. Users who sign in need the same for their roles if they are limited by
siteaccess.

### 4.5 Clear the caches and look

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

Or deploy in one step, which clears these caches, reloads PHP-FPM and restarts Velocity:

```bash
./console exp:velocity deploy --allow-root-user
```

Open the siteaccess (`https://www.example.com/smarty/` in the worked example) and the same pages on the original siteaccess, side
by side. A page rendered from `.stpl` is the same page; the template path comments
([Template path comments](../features/6.0/template-path-comments.md)) and the debug output's template list show which
file wrote what.

## 5. Working on a ported design

- Edit the `.stpl` files from now on. Running the converter again leaves every file you changed alone (`SKIP`) and
  only rewrites the files it wrote itself and nobody touched; `--force` starts over. Run with `-s` set to the
  siteaccess that serves the port, `--override-ini` writes its override file again with the same rules.
- A template added to the original design later is used through the fallback until it is converted: convert it on its
  own (`--from=<design> --file=templates/<name>.tpl --to=<ported design>`) or write it by hand, and add its override
  rule.
- Keep the design rules of the project: no inline styles, the shared components and tokens of the design.

## 6. Converting Smarty templates back to .tpl

```bash
./console exp:template:convert:stpl-to-tpl -s <siteaccess> \
    --from=<design directory with the .stpl templates> \
    [--to=<design directory for the .tpl templates>] \
    [--file=<one template>] [--override-ini=<file to write>] [--dry-run] [--force] --allow-root-user
```

The options are those of part 1 with the directions swapped (`php bin/php/exptemplateconvertstpltotpl.php` and
`./console exp:template:convert --from-engine=stpl --to-engine=tpl` are the same command). The work is done by
`expSmartyReverseConverter`. It reads what the tpl -> stpl converter writes and the common hand-written Smarty, and
writes a `.tpl` that prints what the `.stpl` printed. Every result must parse in the template system's parser before
it is written. `--override-ini` writes the `-s` siteaccess's override rules with `MatchFile` pointing at the converted
`.tpl` where one was converted.

Writing back into the original design is safe: its own `.tpl` files have no marker, so every one of them is skipped
unless you give `--force`. A dry run of the media port back into the media design:

```bash
./console exp:template:convert:stpl-to-tpl -s smarty --from=<the converted design> \
    --to=extension/sevenx_themes_media/design/media --dry-run --allow-root-user
```

```
Dry run: nothing is written.
SKIP override/templates/content/view/embed/embed_file.stpl: extension/sevenx_themes_media/design/media/override/templates/content/view/embed/embed_file.tpl has no conversion marker (written by hand); --force replaces it
...
PASS stpl->tpl 516 templates: 0 to write, 0 unchanged, 516 skipped, 0 errors; 8 notes in 4 templates (a skipped file was changed or written by hand: --force replaces it)
```

### What becomes what

| Smarty | .tpl |
|---|---|
| `{$a.b}`, `{$a['b']}`, `{$a[0]}`, `{$a->b}`, `{$a[$k]}` | `{$a.b}`, `{$a.b}`, `{$a[0]}`, `{$a.b}`, `{$a[$k]}` |
| `$Ns__name` (a section namespace, as the converter writes it) | `$Ns:name` |
| `expvar('name', 'ns')` | `$#ns:name` |
| `{$x\|op:a:b}`, `{op(a, b)}` | `{$x\|op(a, b)}`, `{op(a, b)}`; `exp_op` is `op` (the prefix removed) |
| `op(null, a)` of an operator that reads its input | `op(a)` |
| `X\|expattr:'k'`, `X\|expattr:0` | `X.k`, `X[0]` |
| `i18n('c', expvoid(), ...)` | `i18n('c',, ...)` (the empty parameter) |
| `==` `!=` `<` `>` `<=` `>=` (and `eq`, `ne`, `gt`, ...) | `eq()` `ne()` `lt()` `gt()` `le()` `ge()` |
| `&&` `\|\|` `!`, `+` `-` `*` `/` `%`, `c ? a : b` | `and()` `or()` `not()`, `sum()` `sub()` `mul()` `div()` `mod()`, `cond(c, a, b)` |
| `true` `false` `null`, `[a, b]`, `['k' => v]`, `"a $b"` | `true()` `false()` `null()`, `array(a, b)`, `hash('k', v)`, `concat('a ', $b)` |
| `isset($x)`, `$x\|in_array:$l`, Smarty's `upper` `lower` `count` `cat` | `is_set($x)`, `$l\|contains($x)`, `upcase` `downcase` `count` `concat` |
| `{$x = v}` | `{def $x = v}` the first time in its block, `{set $x = v}` when it is certainly defined already |
| `{$x = null}`, `{expundefall}` | `{undef $x}`, `{undef}`; `{undef $a}{undef $b}` is written `{undef $a $b}` |
| `{$x[] = v}` | `{def $x = array(v)}` or `{set $x = $x\|append(v)}` |
| `{if}{elseif}{else}` | the same, with the condition as above |
| `{foreach $l as $k => $v}`, `{foreach from=$l item=v key=k}` | `{foreach $l as $k => $v}` |
| `{foreachelse}` | `{if $l\|count}{foreach ...}...{/foreach}{else}...{/if}` |
| `$v@index` `@iteration` `@first` `@last` `@total` `@key` | a counter: `{def $__index_v = -1}`, `{set $__index_v = $__index_v\|inc}` first in the body, `{undef $__index_v}` after the loop |
| `{if !$v@first}...{/if}` first in the body | `{delimiter}...{/delimiter}` |
| `{$__loopN = X}{foreach $__loopN as ...}`, `X\|expslice:o:m:r` | `{foreach X as ... offset o max m reverse}` |
| `{$__sequenceN = S}` and `$s = $__sequenceN\|expsequence:$v@index` | `sequence S as $s` |
| `{$__delimN = false}`, `{if $__delimN}...{/if}{$__delimN = true}`, `{$__delimN = false}{continue}` | `{delimiter}...{/delimiter}`, `{skip}` |
| `{foreach exprange(a, b) as $i}` | `{for a to b as $i}` |
| `{for $i = a to b}` | `{for a to b as $i}`, inside `{if le(a, b)}` unless a <= b are numbers (the .tpl `{for}` also counts down) |
| `{while}`, `{break}`, `{continue}` | the same |
| `{$__doN = true}{while $__doN}...{$__doN = c}{/while}` | `{do}...{/do while c}` |
| `{$__switchN = X}{if $__switchN == A}...{elseif $__switchN\|in_array:B}...{else}...{/if}` | `{switch match=X}{case match=A}...{/case}{case in=B}...{/case}{case}...{/case}{/switch}` |
| `{foreach (L)\|expsectionloop:o:m:r as $Ns__key => $Ns__item}` with `$Ns__index`, `$Ns__number`, `$Ns__sequence`, `$__skipN` | `{section name=Ns loop=L offset=o max=m reverse=r sequence=S}` with `{section-exclude}`, `{section-include}`, `{delimiter}`, `{section-else}` |
| `{if !isset($v)}{$v = X}{/if}` | `{default v=X}` with the rest of the block as its body |
| `{capture assign=x}`, `{expsetblock}`, `{expappendblock}` | `{set-block variable=x scope=root}`, `{set-block}`, `{append-block}` |
| `{expcacheblock id=N ...}`, `{exprunonce id=N}` | `{cache-block ...}`, `{run-once}` |
| `{expinclude uri=...}`, `{expset name='x' value=v scope='global'}`, `{expfetch module= function= assign=x ...}` | `{include uri=...}`, `{set scope=global x=v}`, `{def $x = fetch(module, function, hash(...))}` |
| `{node_view_gui ...}` and every other template function | the same tag (and block) |
| `{ldelim}` `{rdelim}`, a brace Smarty prints as text (`{ `), `{literal}` | `{ldelim}` `{rdelim}`, `{literal}...{/literal}` |
| `{* comment *}` | the same |

Whitespace is converted exactly: Smarty drops the newline right after a tag that prints nothing, after a comment and
at the start of the file; the template system drops the newline right after and right before every function tag. The
converter reduces each text to what Smarty prints and writes the newlines the template system needs to print exactly
that.

### What it does not convert

What has no exact `.tpl` form is never guessed. It is printed as a `NOTE <template>: line <n>: <the Smarty source>:
<why>; kept as a comment` line and kept in the `.tpl` as a comment that prints nothing:

```
{* SMARTY (not converted: Smarty's escape has no exact template operator): {$title|escape} *}
```

A block (`{if}`, `{foreach}` ...) whose head cannot be converted is kept whole in one such comment. Not converted:
Smarty's own modifiers other than those of the table (`escape`, `default`, `truncate`, `date_format`, `replace`,
`json_encode` ...), `$smarty`, `===` and `!==`, object method calls, static class members, constants, config
variables, an assignment to an array element (`{$a.b = 1}`), `{include file=...}`, Smarty's `{section}`, `{strip}`,
`{capture name=...}`, `{for}` with `step` or `max`, `@last` and `@total` of a `{for}`, a variable of a section
namespace assigned outside of that section, and a top-level `expvoid()` (what the tpl -> stpl converter writes for an
expression its source could not parse). Rewrite those by hand, using the comment as the starting point.

### How far a round trip goes

The media design (516 templates, 58 of them with errors the template system's parser reports in the source) was
converted tpl -> stpl -> tpl:

| Check | Templates |
|---|---|
| the `.tpl` parses without an error | 516 |
| converting the `.tpl` again gives the first `.stpl` again (fixpoint) | 512 |
| converting that `.stpl` back again gives the same `.tpl` (idempotent) | 512 |
| the same template tree as the original, up to equivalent spellings (a name and the same string, one `{def}` per variable, `{def}` for `{set}`) | 403 |
| the same template tree as the original | 209 |
| byte for byte the original | 15 |
| with notes (8 notes, all a top-level `expvoid()` of a source that does not parse) | 4 |

The four that are not a fixpoint are those four with notes. The rest of the differences from the original are
spelling (spaces, `fieldValue($a, 'b')` for `fieldValue( $a, 'b' )`), a `{section}` with only `show=` that came back
as an `{if}`, the source errors the parser drops, and, in an installation where an extension's template function is
not active, the tags of that function (convert with `-s` set to a siteaccess that has them). Every case of the
template engine's own tests (eZTemplateEngineRenderTest, eZTemplateOperatorMatrixTest: 453 cases) renders the same
after tpl -> stpl -> tpl; `tests/tests/lib/smarty/expSmartyReverseConverterTest.php` checks that and the hand-written
Smarty of the table, and `tests/tests/kernel/private/classes/commands/expTemplateConvertCommandTest.php` checks the
commands, both directions on a design and the round trip of the whole media design.

## Related pages

- [Converting templates between template engines](template-conversion.md): the `exp:template:convert` commands for every engine, every option, the output and exit codes, the marker, the override settings, the same from PHP
- [Moving from .tpl to .stpl](../help/template-language/moving-from-tpl-to-stpl.md): the template-language manual's chapter, with both commands, worked examples and a side-by-side table
- Command reference: [exp:template:convert](../examples/commands/exptemplateconvert.md), [exp:template:convert:tpl-to-stpl](../examples/commands/exptemplateconverttpltostpl.md), [exp:template:convert:stpl-to-tpl](../examples/commands/exptemplateconvertstpltotpl.md)
- New to `.stpl`: [a first .stpl, step by step](smarty-templates-tutorial.md)
- Reference: [every operator](smarty-templates-operator-reference.md), [template functions and blocks](smarty-templates-functions.md), [troubleshooting and FAQ](smarty-templates-troubleshooting.md)
- Topics: [caching and performance](smarty-templates-caching.md), [performance (Speed)](smarty-templates-performance.md), [migrating a design](smarty-templates-migrating.md), [writing operators and functions](smarty-templates-plugins.md), [security and escaping](smarty-templates-security.md)
- [Smarty templates: writing and running .stpl templates](smarty-templates.md)
- [Smarty templates: operators and fetch functions](smarty-templates-operators.md)
- [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md), the feature page
- [Templates and design](templates-and-design.md), [Roles, policies and unactivated users](roles-and-policies.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
