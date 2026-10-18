---
title: Comments
audience: designer, developer
category: templates
order: 160
summary: Template comments that never reach the browser, how to document a template's variables and purpose at its top as the shipped designs do, commenting out code, and why HTML comments are the wrong tool.
keywords: comments, template comments, {* *}, documentation in templates, header comment, comment out, html comments
type: help
---
# Comments

This page is for designers who document templates.

## Contents

1. [Template comments](#template-comments)
2. [A header comment](#a-header-comment)
3. [Commenting out](#commenting-out)
4. [Not HTML comments](#not-html-comments)

## Template comments

```tpl
{* One line *}
{* Several
   lines *}
```

Template comments are removed when the template is compiled. They cost nothing at runtime and never reach visitors.

## A header comment

The shipped designs start important templates with a comment that says what the template does, which variables it
expects and why it is written the way it is. The media design's `pagelayout/head/style.tpl` explains its file order and
the font decision; the starter pagelayout lists its `pl_` variables. Do the same:

```tpl
{* Teaser of a node, used in lists and layout blocks.
   Parameters: node (required), show_image (default false), image_class (default 'medium').
   Prints an <article>; the heading level is h3 because lists sit under an h2. *}
```

## Commenting out

Wrap code in `{* ... *}` to switch it off; the simple design keeps alternative menu includes this way. Remove dead code
before a release instead of leaving it commented out for long.

## Not HTML comments

`<!-- ... -->` is sent to every visitor, can reveal internal notes and adds bytes. Use it only when the comment is meant
for the HTML source (rare). For debugging which template drew what, use template path comments instead
(Template path comments).

## Related pages

- [Template style guide](template-style-guide.md)
- [TPL syntax basics](tpl-syntax-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
