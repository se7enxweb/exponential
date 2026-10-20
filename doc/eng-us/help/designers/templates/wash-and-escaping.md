---
title: Wash and escaping
audience: designer, developer
category: templates
order: 230
summary: Why and how every printed value is escaped with wash: the xhtml, email, javascript and pdf modes, what does not need wash (rich text output, URL operators), escaping in attributes, JSON for scripts, and the mistakes that open cross-site scripting holes.
keywords: wash, escape, escaping, xss, cross site scripting, htmlspecialchars, wash email, wash javascript, attribute escaping, json_encode, output encoding, security
type: help
---
# Wash and escaping

This page is for everyone who writes templates. Anything a template prints that comes from content, settings, the
URL or a user must be **escaped**, or a visitor can inject HTML and scripts into the page. In Exponential templates the
escaping operator is `wash`.

## Contents

1. [The rule](#the-rule)
2. [The modes](#the-modes)
3. [What not to wash](#what-not-to-wash)
4. [In attributes](#in-attributes)
5. [Values for scripts](#values-for-scripts)
6. [Mistakes](#mistakes)

## The rule

```tpl
<h1>{$node.name|wash}</h1>
<p>{$search_text|wash}</p>
<input value="{$query|wash}">
```

Wash every value at the point it is printed into HTML. Shape it first, wash last:
`{$text|shorten( 100 )|wash}`.

## The modes

From `eZTemplateStringOperator::wash()` (`lib/eztemplate/classes/eztemplatestringoperator.php`):

| Call | Does |
|---|---|
| `\|wash` or `\|wash( 'xhtml' )` | `htmlspecialchars` with quotes: `<`, `>`, `&`, `"`, `'` become entities |
| `\|wash( 'email' )` | the same, then `.` and `@` replaced by `template.ini [WashSettings] EmailDotText` and `EmailAtText` (a simple spam guard) |
| `\|wash( 'javascript' )` | escapes for a JavaScript string |
| `\|wash( 'pdf' )` | prepares text for the PDF export |

## What not to wash

- **Rich text output**: `{attribute_view_gui attribute=$node.data_map.body}` renders through the datatype's templates,
  which escape text themselves; washing their HTML again would show tags as text.
- **URL operators**: `ezurl`, `ezroot`, `ezimage`, `ezdesign` escape what they return (the kernel's URL operator applies
  `htmlspecialchars` to the paths it builds).
- **Values you made yourself** from constants (a class name you wrote in the template).
- **Translated texts without user input**: `{'Search'|i18n( 'design/mytheme' )}` prints a translation; wash it when it
  goes into an attribute, or when it carries placeholders with user values.

## In attributes

Always quote attributes and wash the value:

```tpl
<a href={$node.url_alias|ezurl} title="{$node.name|wash}">
<img alt="{$image.alternative_text|wash}" ...>
<div data-label="{'Close'|i18n( 'design/mytheme' )|wash}">
```

## Values for scripts

Do not build JavaScript in templates. Hand values to scripts as JSON data, which `json_encode` (ezjscore) escapes
correctly for its context:

```tpl
<script type="application/json" id="mytheme-data">{json_encode( hash( 'title', $node.name ) )}</script>
```

([No inline JavaScript](../theme-design/no-inline-javascript.md)).

## Mistakes

- **Washing too early** and then using the value in a calculation or comparison: wash only at output.
- **Washing twice**: `&amp;amp;` on the page. Wash once.
- **Printing request values unwashed**: `{ezhttp( 'q', 'get' )}` must be `{ezhttp( 'q', 'get' )|wash}`.
- **Trusting settings**: values from INI files are printed by many templates; wash them too.

A Content Security Policy is the second line of defense when a template forgets to wash, not a replacement
([CSP-safe design](../theme-design/csp-safe-design.md)).

## Related pages

- [URL operators](url-operators.md)
- [The ezhttp operator](ezhttp-operator.md)
- [Common template mistakes](common-template-mistakes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
