---
title: Function reference
audience: designer, developer
category: templates
order: 330
summary: Every template function (the brace tags that control templates) of the template library and the kernel, with its syntax in one line and the page that explains it: def, set, if, foreach, for, while, do, switch, include, cache-block, set-block, the gui functions and more.
keywords: function reference, template functions, def, undef, set, let, default, if, foreach, for, while, do, switch, section, include, cache-block, set-block, append-block, run-once, ldelim, rdelim, sequence, attribute_view_gui, node_view_gui, content_view_gui, debug functions
type: help
---
# Function reference

Template **functions** are the brace tags that control a template, as opposed to operators that compute values. This
page lists them by where they are registered.

## Contents

1. [The template library](#the-template-library)
2. [The kernel: gui functions](#the-kernel-gui-functions)
3. [Extensions](#extensions)

## The template library

Registered in `lib/eztemplate/classes/eztemplateautoload.php`:

| Function | Syntax | Page |
|---|---|---|
| `def`, `undef` | `{def $a=1 $b=2}`, `{undef $a}` | [Variables and def](variables-and-def.md) |
| `set`, `let`, `default` | `{set $a=2}`, `{default name=value}` | [Variables and def](variables-and-def.md) |
| `if` (`elseif`, `else`) | `{if cond}...{elseif cond}...{else}...{/if}` | [If and conditions](if-and-conditions.md) |
| `foreach` | `{foreach $list as $key => $item offset 1 max 3 reverse}...{/foreach}` | [Foreach loops](foreach-loops.md) |
| `for` | `{for 1 to 5 as $i}...{/for}` | [For loops](for-loops.md) |
| `while` | `{while cond}...{/while}` | [While and do loops](while-and-do-loops.md) |
| `do` | `{do}...{/do while cond}` | [While and do loops](while-and-do-loops.md) |
| `delimiter`, `break`, `continue`, `skip` | inside loops | [Foreach loops](foreach-loops.md) |
| `switch` (`case`) | `{switch match=$x}{case match=1}...{/case}{case}...{/case}{/switch}` | [Switch and case](switch-case.md) |
| `section` (`section-else`, `section-include`, `section-exclude`) | `{section loop=$list}...{/section}` | [Section (legacy)](section-legacy.md) |
| `include` | `{include uri='design:path.tpl' name=value}` | [Include](include.md) |
| `cache-block` | `{cache-block keys=... expiry=...}...{/cache-block}` | [Cache-block](cache-block.md) |
| `set-block`, `append-block` | `{set-block variable=$x}...{/set-block}` | [Set-block and append-block](set-block-and-append-block.md) |
| `run-once` | `{run-once}...{/run-once}` | [Run-once](run-once.md) |
| `ldelim`, `rdelim` | `{ldelim}` prints `{` | [Literal and delimiters](literal-and-delimiters.md) |
| `literal` | `{literal}...{/literal}` | [Literal and delimiters](literal-and-delimiters.md) |
| `sequence` | `{sequence name=seq loop=array( ... )}` | older alternating values; prefer `foreach ... sequence` |
| `debug-timing-point`, `debug-accumulator`, `debug-log`, `debug-trace` | `{debug-log msg='...'}` | Template debugging |
| `tool_bar` | `{tool_bar name=... view=...}` | toolbars of `toolbar.ini` |
| `menu` | `{menu name=...}` | menus of `menu.ini` |

## The kernel: gui functions

Registered in `kernel/common/eztemplateautoload.php` (`eZObjectForwardInit`); each forwards to a template chosen by
the object it receives:

| Function | Draws | Template folder |
|---|---|---|
| `attribute_view_gui` | a field for viewing | `content/datatype/view/` (`collect/` for information collectors) (attribute_view_gui) |
| `attribute_edit_gui` | a field's edit form | `content/datatype/edit/` |
| `attribute_result_gui`, `attribute_pdf_gui`, `attribute_diff_gui` | collected results, PDF, version differences | `content/datatype/result/`, `pdf/`, `diff/` |
| `node_view_gui` | a node in a view mode | `node/view/<view>.tpl` (node_view_gui) |
| `content_view_gui` | an object in a view mode | `content/view/<view>.tpl` (content_view_gui) |
| `related_view_gui`, `content_pdf_gui`, `content_version_view_gui` | related objects, PDF, versions | |
| `class_attribute_view_gui`, `class_attribute_edit_gui` | class definitions (administration) | |
| `event_view_gui`, `event_edit_gui` | workflow events (administration) | |
| `collaboration_*`, `shop_account_view_gui` | collaboration and shop account parts | |

## Extensions

- `explblock` (explayouts) draws a layouts block.
- `powercontent_create_gui`, `powercontent_attribute_create_gui` (nxc_powercontent, where active).

## Related pages

- [Operator reference](operator-reference.md)
- [TPL syntax basics](tpl-syntax-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
