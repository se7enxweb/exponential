---
title: Template debugging
audience: designer, developer
category: templates
order: 740
summary: Tools for finding template problems: the debug output and its template list, template warnings with file and line, dump and attribute to look into variables, the debug functions, the template syntax checker, and the error log.
keywords: template debugging, debug output, DebugOutput, ShowUsedTemplates, template warnings, dump, attribute operator, debug-log, debug-timing-point, eztemplatecheck, error log, unknown operator
type: help
---
# Template debugging

This page is for designers whose template does not do what they expect. It lists the tools from quickest to deepest.
For "my change does not show", start with [Debugging a design](../theme-design/debugging-a-design.md) (caches); for "which
template is this", [Template path comments](template-path-comments.md).

## Contents

1. [The debug output](#the-debug-output)
2. [Looking into a variable](#looking-into-a-variable)
3. [Debug functions](#debug-functions)
4. [Syntax check](#syntax-check)
5. [Typical messages](#typical-messages)

## The debug output

On a test siteaccess, for your address only:

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10

[TemplateSettings]
ShowUsedTemplates=enabled
```

Under the page appear the warnings and errors of the request, timing, database queries and, with `ShowUsedTemplates`,
a table of every template used with the file it came from. Template warnings name the template and the line
(compiler warnings carry the placement since the path-comments work of August 2026).

## Looking into a variable

```tpl
{$node|attribute( show, 1 )}           {* the attributes of $node and their values, one level deep *}
{$module_result.content_info|dump( show, 2 )}
```

Remove these before committing: they print internal data to the page.

## Debug functions

From `lib/eztemplate/classes/eztemplatedebugfunction.php`, shown in the debug output, not in the page:

```tpl
{debug-log msg='teaser part reached'}
{debug-log var=$items msg='items of the list'}
{debug-timing-point id='menu'} ... {/debug-timing-point}
{debug-accumulator} ... {/debug-accumulator}
```

`debug-timing-point` measures how long a part of the template takes.

## Syntax check

```bash
./console exp:eztemplatecheck extension/mytheme/design/mytheme/templates/
./console exp:eztemplatecheck -s mysite
```

reports each file as valid or with its parse error. It does not run templates, so it cannot find unknown operators or
missing variables.

## Typical messages

| Message (debug output or `var/log/error.log`) | Meaning |
|---|---|
| Operator '...' is not registered | the operator does not exist, or its extension is not active here |
| Variable '...' is already defined | a `{def}` of an existing variable ([Variables and def](variables-and-def.md)) |
| Variable '...' is not defined with {def} | `{undef}` of a variable that was not defined |
| Custom match file: path '...' not found in any resource | an override's `MatchFile` does not exist |
| Image '...' does not exist in any design | an `ezimage` file is missing from the cascade |
| Could not find: ... | a stylesheet or script given to the packer is missing |

## Related pages

- [Template path comments](template-path-comments.md)
- [Common template mistakes](common-template-mistakes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
