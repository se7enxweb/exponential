---
title: The template operators builders use most
audience: developer, designer, app-creator
category: extension-development
order: 140
summary: A working set of about fifty template operators - escaping, strings, arrays, logic, numbers, dates, translation, URLs and settings - each with an example and the output the template engine produced.
keywords: template operators, wash, shorten, concat, upcase, count, implode, explode, contains, choose, cond, first_set, is_set, l10n, datetime, i18n, ezurl, ezimage, ezroot, ezdesign, ezini, sum, div
type: help
---
# The template operators builders use most

Part 2, chapter 14. The template language has hundreds of operators, but a few dozen do nearly all the work. This
chapter groups them by task. **Every line of output below was produced by the kernel's template engine** from the
input shown, with these variables defined:

```text
{def $text = '  Hello <b>World</b>  '
     $list = array( 3, 1, 2 )
     $when = 1760745600}
```

The complete list, with the extension each one comes from, is [Built-in template operators](../part-8-reference/template-operators-reference.md).

## Escaping: always

| Input | Output |
|---|---|
| `{$text\|wash}` | `  Hello &lt;b&gt;World&lt;/b&gt;  ` |
| `{$text\|wash( 'xhtml' )}` | the same (`xhtml` is the default) |
| `{'x"y'\|wash( 'javascript' )}` | `x\042y` |

`wash` turns `<`, `>`, `&` and quotes into entities, so text from people cannot become HTML. Use `wash( 'javascript' )`
for a value written into a JavaScript string in a data attribute or a JSON block.

## Strings

| Input | Output |
|---|---|
| `{$text\|trim\|upcase\|wash}` | `HELLO &lt;B&gt;WORLD&lt;/B&gt;` |
| `{'Some Title'\|downcase}` | `some title` |
| `{'some title'\|upfirst}` | `Some title` |
| `{'some title'\|upword}` | `Some Title` |
| `{'a long sentence that goes on'\|shorten( 12 )}` | `a long se...` |
| `{concat( 'a', '-', 'b' )}` | `a-b` |
| `{'x'\|append( 'y' )}` | `xy` |
| `{'a,b,c'\|explode( ',' )\|count}` | `3` |

`shorten( <length> )` cuts to that many characters including the `...`.

## Arrays

| Input | Output |
|---|---|
| `{$list\|count}` | `3` |
| `{$list\|implode( ',' )}` | `3,1,2` |
| `{$list\|sort\|implode( ',' )}` | `1,2,3` |
| `{array( 'x', 'y' )\|reverse\|implode( '' )}` | `yx` |
| `{array( 1, 2 )\|merge( array( 3 ) )\|implode( ',' )}` | `1,2,3` |
| `{array( 1, 2, 3 )\|extract( 1 )\|implode( ',' )}` | `2,3` |
| `{$list\|contains( 2 )\|choose( 'no', 'yes' )}` | `yes` |

`choose( <for false>, <for true> )` picks by a boolean, the first argument for false. `cond( <test>, <value>, ... )` is
the other way of choosing, test first.

## Logic and comparison

| Input | Output |
|---|---|
| `{'a'\|eq( 'a' )}` | `1` |
| `{and( true(), false() )}` | (empty: false prints nothing) |
| `{or( false(), true() )}` | `1` |
| `{not( true() )}` | (empty) |
| `{is_set( $nothing )\|choose( 'unset', 'set' )}` | `unset` |
| `{first_set( $nothing, 'fallback' )}` | `fallback` |

`first_set` returns the first argument that is defined: the clean way to give a default value.

## Numbers

| Input | Output |
|---|---|
| `{sum( 1, 2, 3 )}` | `6` |
| `{mul( 2, 3 )}` | `6` |
| `{div( 10, 4 )}` | `2.5` |
| `{mod( 10, 4 )}` | `2` |
| `{1234.5\|l10n( 'number' )}` | `1,234.50` (eng-US) |

`sub`, `inc`, `dec`, `min`, `max`, `round`, `floor`, `ceil` exist too.

## Dates

| Input | Output (eng-US) |
|---|---|
| `{$when\|l10n( 'shortdate' )}` | `10/17/2025` |
| `{$when\|datetime( 'custom', '%Y-%m-%d' )}` | `2025-10-17` |

`l10n` formats for the locale of the siteaccess (`shortdate`, `date`, `shorttime`, `time`, `shortdatetime`,
`datetime`, `number`, `currency`); `datetime( 'custom', <format> )` takes an explicit format. `currentdate()` is now.

## Translation

```text
{'Read more'|i18n( 'design/mysite/full/article' )}
```

prints the text in the user's language when a translation exists in a `.ts` file for that context, and the English
text otherwise. Every text a template prints should go through `i18n`; see
[Translations in an extension](../part-3-extension-developer/translations-in-an-extension.md).

## URLs and files

| Input | Output |
|---|---|
| `{'/content/view/full/2'\|ezurl( 'no' )}` | `/content/view/full/2` (plus the siteaccess path or host, when the site uses one) |
| `{'/content/view/full/2'\|ezurl}` | `"/content/view/full/2"`, with quotes, for an attribute |
| `{'stylesheets/site.css'\|ezdesign}` | the address of the first `stylesheets/site.css` in the design chain |
| `{'logo.png'\|ezimage}` | the address of `images/logo.png` in the design chain |
| `{'/'\|ezroot( 'no' )}` | `/`, the installation root without the siteaccess |

`ezimage` looks in the `images/` folder of the design bases; a missing file is reported in the debug output with every
path it tried.

## Settings

| Input | Output |
|---|---|
| `{ezini( 'SiteSettings', 'SiteName', 'site.ini' )\|wash}` | the site name, escaped |
| `{ezini( 'DebugSettings', 'DebugOutput' )}` | `enabled` or `disabled` (`site.ini` is the default file) |
| `{ezini_hasvariable( 'MySettings', 'Limit', 'mysite.ini' )}` | whether a setting exists |

## Debugging

| Input | Shows |
|---|---|
| `{$node\|attribute( 'show', 1 )}` | the attributes of a variable, one level deep, as a table |
| `{debug-log var=$node.name msg='name'}` | the value in the debug output, not in the page |

See [Debugging templates](debugging-templates.md).

## Related pages

- Previous: [Fetching content in templates](fetch-for-builders.md)
- Next: [Debugging templates](debugging-templates.md)
- [Built-in template operators](../part-8-reference/template-operators-reference.md)
- [String template operators](../../../features/6.0/string-template-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
