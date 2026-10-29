---
title: Lexical structure
audience: developer, designer, app-creator
category: development
order: 60
summary: How the parser reads a template - the delimiters, the kinds of tags (output tags, function tags, end tags, single tags), comments, literal blocks, ldelim and rdelim, escaping a brace inside a tag, the rule that removes one newline around each function tag, and how parser errors report line and column.
keywords: lexical structure, parser, delimiters, braces, tags, function tag, end tag, single tag, comments, literal, ldelim, rdelim, whitespace, newline, parser error, line, column
type: help
---
# Lexical structure

Before anything is evaluated, the parser in `lib/eztemplate/classes/eztemplatemultipassparser.php` cuts the template
into pieces in three passes: text elements and tags (`parseIntoTextElements()`), whitespace removal around tags
(`parseWhitespaceRemoval()`), and the node tree (`parseIntoTree()`). This page describes the first two, which decide
what is text, what is a tag and which newlines reach the output.

## Contents

1. [Delimiters](#delimiters)
2. [Kinds of tags](#kinds-of-tags)
3. [Comments](#comments)
4. [Literal text, ldelim and rdelim](#literal-text-ldelim-and-rdelim)
5. [A brace inside a tag](#a-brace-inside-a-tag)
6. [Whitespace around tags](#whitespace-around-tags)
7. [Single tags with a slash](#single-tags-with-a-slash)
8. [Parser errors: line and column](#parser-errors-line-and-column)
9. [Unknown function tags](#unknown-function-tags)
10. [Related pages](#related-pages)

## Delimiters

A tag starts at `{` and ends at the next `}`. The delimiters are set in the constructor of `eZTemplate`
(`$this->LDelim = "{"`, `$this->RDelim = "}"`); there is no setting to change them.

## Kinds of tags

The parser looks at the first characters inside the braces:

| The tag starts with | It is | Example |
|---|---|---|
| `$` | an output tag: a variable, possibly with attributes and operators | `{$node.name\|wash}` |
| `'` or `"` | an output tag with a string | `{'text'\|upcase}` |
| a digit, or `-` and a digit | an output tag with a number | `{42}`, `{-7}` |
| lowercase letters, digits, `_` or `-` followed by `(` | an output tag with an operator call | `{concat( 'a', 'b' )}` |
| `*` | a comment, up to `*}` | `{* note *}` |
| `/` | an end tag | `{/if}` |
| anything else | a function tag; it is a single tag when it ends with `/` | `{if ...}`, `{include ... /}` |

Every kind in one template:

```tpl
{def $who = 'world'}
text | {$who} | {'literal string'} | {42} | {$who|upcase} | {concat( 'a', 'b' )} | {if true()}block{/if}
```

Output:

```text
text | world | literal string | 42 | WORLD | ab | block
```

## Comments

`{* ... *}` may span lines. The comment itself is dropped, but the text around it is untouched: the newlines before
and after a comment stay, unlike those around function tags.

```tpl
line 1
{* a comment
   over two lines *}
line 3 {* inline *} still line 3
```

Output:

```text
line 1

line 3  still line 3
```

## Literal text, ldelim and rdelim

`{literal}` is registered as a literal tag (`registerLiteral( 'literal' )` in the constructor). Everything up to
`{/literal}` is plain text, braces included. `{ldelim}` and `{rdelim}` (`eZTemplateDelimitFunction`) print a single
brace. Both are shown in [the differences page](smarty-differences-in-depth.md#braces-in-css-and-javascript), which
also shows what an unprotected CSS rule does to a template.

## A brace inside a tag

The tag ends at the first `}` that is not preceded by a backslash. Inside a tag, `\}` is a literal brace. Without the
backslash the string is cut, the tag ends early and the rest is printed as text:

```tpl
[{'a\}b'}]
[{'a}b'}]
```

Output:

```text
[a}b]
[ab'}]
```

## Whitespace around tags

The second pass removes **one** newline right before a function tag (normal, single or end tag) and **one** newline
right after it. Text and output tags (`{$x}`, `{'...'}`) are not touched. In this template every function tag sits on its
own line, so the newlines around `{def}`, `{if}` and `{/if}` disappear, while those around `{$x}` stay:

```tpl
{def $x = 'X'}
A
{if true()}
B
{/if}
C
{$x}
D
```

Output:

```text
ABC
X
D
```

Only one newline is removed on each side. A blank line before or after a function tag therefore still leaves one
newline:

```tpl
A

{if true()}

B

{/if}

C
```

Output:

```text
A

B

C
```

Spaces and tabs are never removed. Indentation in front of a function tag stays in the output.

## Single tags with a slash

A tag ending in `/` is a single tag: it has no end tag. Functions with a special syntax (`if`, `elseif`, `foreach`, `def`, `undef`, `set`,
`let`, `default`, `set-block`, `append-block`, `section`, `while`, `for`) are parsed with their own grammar only in the
normal form. Written with a slash, `{set $n = 2 /}` goes to the generic parameter parser, which does not accept it, and
the variable keeps its value:

```tpl
{def $n = 1}[{set $n = 2 /}{$n}]
```

Output:

```text
[1]
```

Logged by the template engine:

```text
ERROR : parser error @ tl-lex/08-single-tag-slash.tpl:1[15]
Invalid parameter characters in function 'set': '$n = 2'
```

Write these functions without the slash. The slash works for functions that take plain `name=value` parameters, such as
`include`:

```tpl
[{include uri='file:var/tmp/examples/tl-smarty/parts/greeting.tpl' who='single tag' /}]
```

Output:

```text
[<p>Hello, single tag!</p>
]
```

## Parser errors: line and column

Parser errors name the template, the line and the column in brackets, then the problem. An unterminated tag:

```tpl
first line
second {$broken
```

Output:

```text
first line
second {$broken
```

Logged by the template engine:

```text
ERROR : parser error @ tl-lex/06-unterminated-tag.tpl:1[0]
Unterminated tag, needs a } to end the tag.
{$broken
```

The text from the broken tag to the end of the file is printed as it is. The position is where the parser was when it
gave up, which for an unterminated tag is the start of the text before it; look at the quoted tag after the message.

## Unknown function tags

A function tag with a name the engine does not know produces a warning ("Function ... is not registered") and no
output. Because a normal tag opens a block, everything up to its end tag, or to the end of the file when there is none,
belongs to that block and is lost with it:

```tpl
[before {nosuchfunction param=1} after]
```

Output:

```text
[before
```

Logged by the template engine:

```text
WARNING : Function "nosuchfunction" is not registered
```

A misspelled `{fi}` or `{foreahc ...}` therefore swallows the rest of the template. If a page ends early, look for such a
warning in the debug output.

## Related pages

- Previous: [Moving from .tpl to .stpl](moving-from-tpl-to-stpl.md)
- Next: [Values and literals](values-and-literals.md)
- [TPL syntax basics](../designers/templates/tpl-syntax-basics.md)
- [Comments](../designers/templates/comments.md)
- [Literal and delimiters](../designers/templates/literal-and-delimiters.md)
- [Template debugging](../designers/templates/template-debugging.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
