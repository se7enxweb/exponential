---
title: Literal and delimiters
audience: designer, developer
category: templates
order: 150
summary: Printing braces in template output with ldelim and rdelim, or a whole passage untouched with literal; why designs rarely need either now that CSS and JavaScript live in files.
keywords: literal, ldelim, rdelim, braces, curly braces, escape braces, raw output, template delimiters, javascript in template, css in template
type: help
---
# Literal and delimiters

This page is for designers who need a literal `{` or `}` in the output.

## Contents

1. [ldelim and rdelim](#ldelim-and-rdelim)
2. [literal](#literal)
3. [Usually: do not need them](#usually-do-not-need-them)

## ldelim and rdelim

`{ldelim}` prints `{` and `{rdelim}` prints `}` (`eZTemplateDelimitFunction`):

```tpl
<code>function x() {ldelim} return 1; {rdelim}</code>
```

## literal

Everything between `{literal}` and `{/literal}` is printed as it is; tags inside are not read (the parser handles
`literal` as a literal tag, `eZTemplate::registerLiteral( 'literal' )`):

```tpl
{literal}
<pre>{ "a": 1, "b": [1, 2] }</pre>
{/literal}
```

You cannot print a variable inside a literal block; close it first.

## Usually: do not need them

Most braces in old templates are CSS or JavaScript written into the template. In current designs that code lives in
files under `stylesheets/` and `javascript/` ([No inline CSS](../theme-design/no-inline-css.md),
[No inline JavaScript](../theme-design/no-inline-javascript.md)); JSON for scripts comes from `json_encode`. The kernel's
own `node/view/full.tpl` still shows the old style with `{literal}` around a script, which is exactly what new templates avoid.

## Related pages

- [TPL syntax basics](tpl-syntax-basics.md)
- [Comments](comments.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
