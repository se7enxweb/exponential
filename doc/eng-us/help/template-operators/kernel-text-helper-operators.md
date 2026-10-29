---
title: Text helper operators of the kernel
audience: developer, designer, app-creator
category: development
order: 340
summary: Five small kernel operators - alphabet, autolink, simpletags, eztoc and month_overview - with their parameters, the settings they read, what they escape and what they do not, and examples rendered on a development installation.
keywords: alphabet, autolink, simpletags, eztoc, month_overview, table of contents, calendar, archive, links, mailto, AlphabeticalFilterSettings, SimpleTagsOperator, AutoLinkOperator
type: help
---
# Text helper operators of the kernel

Five operators in `kernel/common/`, each in its own class and registered in `kernel/common/eztemplateautoload.php`.

| Operator | Class and file |
|---|---|
| `alphabet` | `eZAlphabetOperator`, `kernel/common/ezalphabetoperator.php` |
| `autolink` | `eZAutoLinkOperator`, `kernel/common/ezautolinkoperator.php` |
| `simpletags` | `eZSimpleTagsOperator`, `kernel/common/ezsimpletagsoperator.php` |
| `eztoc` | `eZTOCOperator`, `kernel/common/eztocoperator.php` |
| `month_overview` | `eZDateOperatorCollection`, `kernel/common/ezdateoperatorcollection.php` |

## Contents

1. [The operators](#the-operators): [alphabet](#alphabet), [autolink](#autolink), [simpletags](#simpletags),
   [eztoc](#eztoc), [month_overview](#month_overview)
2. [Escaping summary](#escaping-summary)

## The operators

### alphabet

Generated page: [alphabet](../../examples/template-operators/alphabet.md).

- **Input:** none. **Parameters:** none.
- **Returns:** the list of letters for alphabetical navigation, or `false` when it is switched off.

`eZAlphabetOperator::fetchAlphabet()` reads `content.ini` `[AlphabeticalFilterSettings]`: for each name in
`ContentFilterList[]` it takes the ranges of `AlphabetList[<name>]` (Unicode code points such as `97-122`, or single
letters, separated by commas), builds the letters and converts them to the charset of `i18n.ini`. An empty
`ContentFilterList[]` returns `false`; a list whose names have no ranges falls back to `a` to `z`. On a development installation the list is
empty:

```tpl
{def $letters = alphabet()}
ContentFilterList: {ezini( 'AlphabeticalFilterSettings', 'ContentFilterList', 'content.ini' )|count} entries
alphabet() gives {$letters|get_type}
```

Output:

```text
ContentFilterList: 0 entries
alphabet() gives boolean[false]
```

### autolink

Generated page: [autolink](../../examples/template-operators/autolink.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `max_chars` | integer | no | `template.ini` `[AutoLinkOperator] MaxCharacters` (72) |

- **Input:** a text. **Returns:** the text with links.

It first turns every e-mail address into `<a href='mailto:...'>`, then every address of the schemes in
`[AutoLinkOperator] Methods[]` into `<a href="..." title="...">`. A link text longer than `max_chars` is shortened in
the middle with `...`. Addresses already inside `href=`, `src=` or `value=` attributes are skipped. The text is **not
escaped**: wash user input first (`|wash|autolink`), otherwise markup in it passes through.

```tpl
{def $text = 'Mail info@example.com or read https://doc.exponential.earth/en/latest/guide/templates/operators/index.html today.'}
{$text|autolink}

{$text|autolink( 30 )}
```

Output:

```text
Mail <a href='mailto:info@example.com'>info@example.com</a> or read <a href="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html" title="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html">https://doc.exponential.earth/en/...guide/templates/operators/index.html</a> today.

Mail <a href='mailto:info@example.com'>info@example.com</a> or read <a href="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html" title="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html">https://doc....tors/index.html</a> today.
```

### simpletags

Generated page: [simpletags](../../examples/template-operators/simpletags.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `listname` | string | no | `false`: use `TagList[]`; a name uses `TagList_<name>[]` |

- **Input:** a text with simple tags such as `<strong>`. **Returns:** HTML.

The tag list lives in `template.ini` `[SimpleTagsOperator]`. Each entry is `TagList[<tag>]=<before>;<after>;<php
functions>`: the text between the start and end tag is passed through the listed PHP functions and wrapped in
`<before>` and `<after>`. The entry `text` handles everything outside known tags; without it, plain text is passed
through `htmlspecialchars`. Unknown tags are treated as text, so they are escaped. The shipped list:

```ini
TagList[text]=;;htmlspecialchars,nl2br
TagList[literal]=<pre>;</pre>;htmlspecialchars
TagList[code]=<pre class="code">;</pre>;htmlspecialchars
TagList[strong]=<b>;</b>;htmlspecialchars,nl2br
TagList[emphasize]=<i>;</i>;htmlspecialchars,nl2br
```

`IncludeList[]` names PHP files to include before the first use (looked up in the `simpletags/` folder of the
extensions in `Extensions[]`), so a tag can use a PHP function of your own. Any function name that exists is
called, so only trusted settings belong there.

```tpl
{def $text = "Plain <b>&</b> text\n<strong>bold</strong> and <emphasize>italic</emphasize>\n<literal><a href=\"x\"></literal>"}
{$text|simpletags}
```

Output:

```text
Plain &lt;b&gt;&amp;&lt;/b&gt; text<br />
<b>bold</b> and <i>italic</i><br />
<pre>&lt;a href=&quot;x&quot;&gt;</pre>
```

### eztoc

Generated page: [eztoc](../../examples/template-operators/eztoc.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `dom` | object | yes | 0 |

- **Input:** none. **Parameter:** an `ezxmltext` content object attribute (anything else gives an empty string).
- **Returns:** nested `<ul><li><a href="#eztoc<attribute id>_<n>">` lists of the headings of the rich text, with the
  heading text HTML-escaped.

The anchor names follow the section numbering: `eztoc498_3_1` is the first heading in the third top-level section
of attribute 498. The counter of a level is not reset under a new parent, so the second level keeps counting across
parents (`3_3` is followed by `5_4` below). The rich text output of the same attribute must contain matching anchors
for the links to work.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
{eztoc( $node.data_map.body )}
```

Output:

```text

<ul><li><a href="#eztoc498_1">Summary</a></li>
<li><a href="#eztoc498_2">1. Who we are and what this policy covers</a></li>
<li><a href="#eztoc498_3">2. Information we collect</a>
<ul><li><a href="#eztoc498_3_1">Information you provide to us</a></li>
<li><a href="#eztoc498_3_2">Information collected automatically</a></li>
<li><a href="#eztoc498_3_3">Information from third parties</a></li>
</ul></li>
<li><a href="#eztoc498_4">3. How we use your information, our legal bases and how long we keep it</a></li>
<li><a href="#eztoc498_5">4. Cookies and similar technologies</a>
<ul><li><a href="#eztoc498_5_4">Embedded content from other websites</a></li>
</ul></li>
<li><a href="#eztoc498_6">5. How we disclose your information</a>
<ul><li><a href="#eztoc498_6_5">Service providers and processors</a></li>
<li><a href="#eztoc498_6_6">Other disclosures</a></li>
<li><a href="#eztoc498_6_7">&quot;Sale&quot; and &quot;sharing&quot; under US state laws</a></li>
</ul></li>
<li><a href="#eztoc498_7">6. International data transfers</a></li>
<li><a href="#eztoc498_8">7. Retention</a></li>
<li><a href="#eztoc498_9">8. Security</a></li>
<li><a href="#eztoc498_10">9. Your privacy rights</a>
<ul><li><a href="#eztoc498_10_8">9.1 Rights we offer everyone</a></li>
<li><a href="#eztoc498_10_9">9.2 United States</a>
<ul><li><a href="#eztoc498_10_9_1">Federal laws</a></li>
<li><a href="#eztoc498_10_9_2">California: CCPA as amended by the CPRA, CalOPPA and &quot;Shine the Light&quot;</a></li>
<li><a href="#eztoc498_10_9_3">Other US states with comprehensive privacy laws</a></li>
<li><a href="#eztoc498_10_9_4">Nevada</a></li>
<li><a href="#eztoc498_10_9_5">Washington: My Health My Data Act</a></li>
</ul></li>
<li><a href="#eztoc498_10_10">9.3 European Union and European Economic Area</a></li>
<li><a href="#eztoc498_10_11">9.4 United Kingdom</a></li>
<li><a href="#eztoc498_10_12">9.5 Switzerland</a></li>
<li><a href="#eztoc498_10_13">9.6 Canada</a></li>
<li><a href="#eztoc498_10_14">9.7 Brazil</a></li>
<li><a href="#eztoc498_10_15">9.8 China</a></li>
<li><a href="#eztoc498_10_16">9.9 Japan</a></li>
<li><a href="#eztoc498_10_17">9.10 South Korea</a></li>
<li><a href="#eztoc498_10_18">9.11 India</a></li>
<li><a href="#eztoc498_10_19">9.12 Singapore, Thailand and the wider Asia-Pacific region</a></li>
<li><a href="#eztoc498_10_20">9.13 Australia and New Zealand</a></li>
<li><a href="#eztoc498_10_21">9.14 Africa</a></li>
<li><a href="#eztoc498_10_22">9.15 Middle East and Turkey</a></li>
<li><a href="#eztoc498_10_23">9.16 Latin America</a></li>
<li><a href="#eztoc498_10_24">9.17 Other jurisdictions</a></li>
</ul></li>
<li><a href="#eztoc498_11">10. Children&#039;s privacy</a></li>
<li><a href="#eztoc498_12">11. Do Not Track, Global Privacy Control and third-party tracking</a></li>
<li><a href="#eztoc498_13">12. Changes to this policy</a></li>
<li><a href="#eztoc498_14">13. How to contact us and exercise your rights</a>
<ul><li><a href="#eztoc498_14_25">Verifying your identity</a></li>
<li><a href="#eztoc498_14_26">Response times</a></li>
</ul>
</li>
</ul>
```

### month_overview

Generated page: [month_overview](../../examples/template-operators/month_overview.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `field` | string | yes | `false` (missing: warning "Missing parameter field") |
| `date` | integer (timestamp) | yes | `false` (missing: warning "Missing parameter date") |
| `optional` | array (hash) | no | `false` |

- **Input:** a list of objects (anything with `hasAttribute()`/`attribute()`) or hashes. The value of `field` in each
  item is a timestamp; the days of the month of `date` that have an item get a link.
- **Returns:** a hash with `year`, `month` (localized name), `weekdays` (list of `day` short name and `class`
  `sun`..`sat`, in the locale's week order), `weeks` (week number => seven slots, `false` or a hash with `day`,
  `link`, `class`, `highlight`), `current`, `next` and `previous` (each `month`, `year`, `link`, or `false`).
  Every `month` is the name a month has on its own (`eZLocale::longMonthNameStandalone()`), not the form used inside a
  date: `październik` in Polish, `октябрь` in Russian, where a date reads `15 października`, `15 октября`.
- **optional keys:** `link` (base of the day links, which get `/(year)/Y/(month)/M/(day)/D` appended), `current`
  (timestamp of a day to highlight) and `current_class`, `day_class`, `today_class`, `next` and `previous` (hashes
  with a `link`; the month links get `/(year)/Y/(month)/M`). `year_link`, `month_link` and `day_link` are read but not
  used.

The field must be an attribute of the items themselves: `published` is an attribute of the content object, not of
the node, so the example collects the objects first.

```tpl
{def $recipes = fetch( 'content', 'list', hash( 'parent_node_id', 144, 'limit', 50, 'sort_by', array( 'published', false() ) ) )
     $objects = array()}
{foreach $recipes as $r}{set $objects = $objects|append( $r.object )}{/foreach}
{def $first = $objects[0].published
     $month = $objects|month_overview( 'published', $first, hash( 'link', '/content/view/archive/144', 'current', $first, 'current_class', 'is-current', 'day_class', 'day', 'previous', hash( 'link', '/content/view/archive/144' ) ) )}
{$month.current.month} {$month.current.year} (previous: {$month.previous.month} {$month.previous.link}; next: {$month.next|choose( 'false', 'set' )})

{foreach $month.weekdays as $wd}{$wd.day} {/foreach}


{foreach $month.weeks as $number => $week}
week {$number}: {foreach $week as $d}{if $d}{$d.day}{if $d.link}*{/if}{if $d.highlight}!{/if} {else}- {/if}{/foreach}


{/foreach}

a day with content links to {foreach $month.weeks as $week}{foreach $week as $d}{if and( $d, $d.link )}{$d.link} {/if}{/foreach}{/foreach}
```

Output:

```text
August 2018 (previous: July /content/view/archive/144/(year)/2018/(month)/7; next: false)
Sun Mon Tue Wed Thu Fri Sat 
week 31: - - - 1 2 3 4 
week 32: 5 6 7 8* 9 10 11 
week 33: 12 13*! 14 15 16 17 18 
week 34: 19 20 21 22 23 24 25 
week 35: 26 27 28 29 30 31 - 

a day with content links to /content/view/archive/144/(year)/2018/(month)/8/(day)/8 /content/view/archive/144/(year)/2018/(month)/8/(day)/13
```

`*` marks the two days with a recipe (they got a `link`), `!` the highlighted `current` day.

## Escaping summary

| Operator | Escapes its input? |
|---|---|
| `autolink` | no: wash first |
| `simpletags` | yes, through the PHP functions of each tag (`htmlspecialchars` in the shipped list) |
| `eztoc` | heading texts are escaped |
| `month_overview`, `alphabet` | return data; escape when printing |

## Related pages

- [String operators for designers](../designers/templates/string-operators.md)
- [Wash and escaping](../designers/templates/wash-and-escaping.md)
- [Rich text templates](../designers/templates/rich-text-templates.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
