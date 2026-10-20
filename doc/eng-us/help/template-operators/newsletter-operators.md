---
title: Newsletter operators of cjw_newsletter
audience: developer, designer, app-creator
category: development
order: 450
summary: The thirteen template operators of the cjw_newsletter extension - three general ones (cjw_newsletter_str_replace, cjw_newsletter_preg_replace, cjw_newsletter_variable) and ten rendering operators (conditions, plain text and rich text conversion, interests block, absolute URLs, underline, wrap, rendering data) - with parameters, return values, when the condition markers appear, and examples rendered on alpha.
keywords: cjw_newsletter, newsletter, cjw_newsletter_str_replace, cjw_newsletter_preg_replace, cjw_newsletter_variable, cjwnl_condition_open, cjwnl_condition_else, cjwnl_condition_close, cjwnl_plaintext, cjwnl_richtext, cjwnl_interests_block, cjwnl_abs_url, cjwnl_text_underline, cjwnl_text_wrap, cjwnl_rendering_data
type: help
---
# Newsletter operators of cjw_newsletter

The extension `cjw_newsletter` registers two operator classes in `extension/cjw_newsletter/autoloads/eztemplateautoload.php`
(and `ExtensionAutoloadPath[]=cjw_newsletter`). They exist only while the extension is active and are meant for the
templates that render newsletter editions: the HTML part, the plain text part and the web archive.

| Class | File | Operators |
|---|---|---|
| `CjwNewsletterOperators` | `autoloads/cjwnewsletteroperators.php` | `cjw_newsletter_str_replace`, `cjw_newsletter_preg_replace`, `cjw_newsletter_variable` |
| `CjwNewsletterRenderingOperators` | `classes/rendering/cjwnewsletterrenderingoperators.php` | the ten `cjwnl_*` operators |

## Contents

1. [General operators](#general-operators): [cjw_newsletter_str_replace](#cjw_newsletter_str_replace),
   [cjw_newsletter_preg_replace](#cjw_newsletter_preg_replace), [cjw_newsletter_variable](#cjw_newsletter_variable)
2. [Text rendering](#text-rendering): [cjwnl_plaintext](#cjwnl_plaintext), [cjwnl_richtext](#cjwnl_richtext),
   [cjwnl_text_underline](#cjwnl_text_underline), [cjwnl_text_wrap](#cjwnl_text_wrap), [cjwnl_abs_url](#cjwnl_abs_url)
3. [Markers and data](#markers-and-data): [cjwnl_condition_open](#cjwnl_condition_open),
   [cjwnl_condition_else](#cjwnl_condition_else), [cjwnl_condition_close](#cjwnl_condition_close),
   [cjwnl_interests_block](#cjwnl_interests_block), [cjwnl_rendering_data](#cjwnl_rendering_data)

## General operators

### cjw_newsletter_str_replace

Parameters `string_search` and `string_replace` (strings, required). PHP `str_replace()` on the input.

### cjw_newsletter_preg_replace

Parameters `string_search` (a regular expression) and `string_replace` (required). PHP `preg_replace()` on the input.
Use constant patterns only.

### cjw_newsletter_variable

Parameter `variable_name` (required). The only name it knows is `available_subscription_status_id_name_array`: the
subscription status ids and their names (`CjwNewsletterSubscription::availableStatusIdNameArray()`). Any other name
gives `false`. Line 7 of the second example.

## Text rendering

### cjwnl_plaintext

No parameters. Input: an `ezxmltext` or rich text attribute (or its stored XML). Returns plain text for the text part
of a mail (`CjwNewsletterPlainText::xmlText()`): DocBook rich text is converted by `CjwNewsletterRichText::toText()`,
the older XML format by the extension's plain-text output handler; headings are underlined, emphasis marked with `*`.

### cjwnl_richtext

| Parameter | Type | Required | Default |
|---|---|---|---|
| `format` | string | no | `html` |

Input: DocBook rich text (the `ezrichtext` format). Returns HTML with inline styles for mail clients, or plain text
with `format` `text`. Every text is escaped; links are kept only with `http`, `https`, `mailto` and the `ez*` schemes,
and a link to content becomes the absolute address of its node.

### cjwnl_text_underline

| Parameter | Type | Required | Default |
|---|---|---|---|
| `char` | string | no | `=` |

Returns the trimmed input followed by a line of `char` as long as the text, at least 3 and at most 72 characters.

### cjwnl_text_wrap

| Parameter | Type | Required | Default |
|---|---|---|---|
| `width` | integer | no | 72 (at least 20 is used) |

Wraps the text at `width` characters (`CjwNewsletterPlainText::wrap()`).

### cjwnl_abs_url

No parameters. Makes a path absolute: with the scheme and host of the site (`eZURI::transformURI( ..., 'full' )`),
or with the base address the output command sets for an edition. Addresses with a scheme, `//` or `#` are returned
unchanged.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
{def $docbook = '<section xmlns="http://docbook.org/ns/docbook" version="5.0-variant ezpublish-1.0"><para>Hello <emphasis role="strong">world</emphasis></para><itemizedlist><listitem><para>one</para></listitem><listitem><para>two</para></listitem></itemizedlist></section>'}

1 cjw_newsletter_str_replace: {'Hello [name]'|cjw_newsletter_str_replace( '[name]', 'reader' )}
2 cjw_newsletter_preg_replace: {'Order 2026-10-19'|cjw_newsletter_preg_replace( '/(\d+)-(\d+)-(\d+)/', '$3.$2.$1' )}
3 cjwnl_text_underline: {'Monthly news'|cjwnl_text_underline}
4 cjwnl_text_underline with -: {'Ok'|cjwnl_text_underline( '-' )}
5 cjwnl_text_wrap 30: {'Exponential sends newsletters as HTML and as plain text, wrapped for mail clients.'|cjwnl_text_wrap( 30 )}
6 cjwnl_abs_url: {'/privacy-policy'|cjwnl_abs_url} / {'mailto:info@example.com'|cjwnl_abs_url}
7 cjwnl_richtext text: {$docbook|cjwnl_richtext( 'text' )}
7b cjwnl_richtext html: {$docbook|cjwnl_richtext}
8 cjwnl_plaintext of node 90 body: {$node.data_map.body|cjwnl_plaintext|extract_left( 160 )}
```

Output on alpha:

```text

1 cjw_newsletter_str_replace: Hello reader
2 cjw_newsletter_preg_replace: Order 19.10.2026
3 cjwnl_text_underline: Monthly news
============
4 cjwnl_text_underline with -: Ok
---
5 cjwnl_text_wrap 30: Exponential sends newsletters
as HTML and as plain text,
wrapped for mail clients.
6 cjwnl_abs_url: http://alpha.se7enx.com/privacy-policy / mailto:info@example.com
7 cjwnl_richtext text: Hello *world*

- one
- two
7b cjwnl_richtext html: <p style="margin:0 0 1em 0;">Hello <strong>world</strong></p>
<ul style="margin:0 0 1em 0;padding:0 0 0 1.5em;"><li style="margin:0 0 .3em 0;">one</li><li style="margin:0 0 .3em 0;">two</li></ul>
8 cjwnl_plaintext of node 90 body: Effective date: [Date]

Last updated: [Date]

Summary
=======

- *Who we are*: this website is operated from the United States by [Legal name of the operator] (
```

The address in line 6 uses `http` because the example ran on the command line.

## Markers and data

### cjwnl_condition_open

| Parameter | Type | Required | Default |
|---|---|---|---|
| `settings` | array (hash) | no | empty |

Starts a part of the text that only some subscribers get. While an edition's output is rendered it returns the
marker `[[cjwnl:if:<id>:<settings as base64url JSON>]]`; the send step keeps or drops the part per subscriber. The
settings: `field` (`salutation`, `first_name`, `last_name`, `organisation`, `email`, `language`,
`custom_data_text_1` to `4`), `operator` (`eq`, `ne`, `contains`, `starts`, `empty`, `not_empty`, `in`), `value`,
`list`, `language`, `interest`, `negate` (documented in `classes/rendering/cjwnewsletterconditions.php`).

**Outside the output command the markers are empty strings**, so on the web site both branches show; that is what
happened in line 1 below.

### cjwnl_condition_else

No parameters. The else marker of the innermost open condition, or an empty string.

### cjwnl_condition_close

No parameters. The end marker of the innermost open condition, or an empty string.

### cjwnl_interests_block

| Parameter | Type | Required | Default |
|---|---|---|---|
| `limit` | integer | no | 0 |
| `heading` | string | no | empty |
| `accent` | string | no | empty |

Returns a marker `[[cjwnl:interests:<limit>:<options>]]` (limit kept between 0 and 20, `accent` used only when it is a
`#rrggbb` color) that is replaced, per subscriber, by the list of the
subscriber's interests.

### cjwnl_rendering_data

| Parameter | Type | Required | Default |
|---|---|---|---|
| `name` | string | yes | empty |
| `param` | mixed | no | `false` |

Gives the edit and output templates the data they need. Names: `available_skins`, `skin_settings` (param: skin),
`allowed_skins` and `list_of_node` (param: node id), `list_languages` and `main_language` (param: list object id),
`content_languages`, `interests` (param: list), `all_interests`, `interest_user_count` (param: interest id),
`placeholder_names`, `condition_fields`, `user_choices` (param: user id) and `plaintext_template` (param: datatype
string; the `design:` path of a plain-text override template, or `false`). Unknown names give `false`.

```tpl
1 condition: {cjwnl_condition_open( hash( 'field', 'interest', 'value', 'running' ) )}A{cjwnl_condition_else()}B{cjwnl_condition_close()}
2 interests block: {cjwnl_interests_block( 3, 'Your topics' )}
3 skins: {cjwnl_rendering_data( 'available_skins' )|implode( ', ' )}
4 placeholders: {cjwnl_rendering_data( 'placeholder_names' )|implode( ', ' )}
5 condition fields: {foreach cjwnl_rendering_data( 'condition_fields' ) as $k => $v}{$k}{delimiter}, {/delimiter}{/foreach}

6 unknown name: {cjwnl_rendering_data( 'nosuchname' )|get_type}
7 subscription statuses: {foreach cjw_newsletter_variable( 'available_subscription_status_id_name_array' ) as $id => $name}{$id}={$name}{delimiter}, {/delimiter}{/foreach}
```

Output on alpha:

```text
1 condition: AB
2 interests block: [[cjwnl:interests:3:eyJoIjoiWW91ciB0b3BpY3MiLCJhIjoiIn0]]
3 skins: default, company, news, shop
4 placeholders: [[name]], [[salutation_name]], [[first_name]], [[last_name]], [[email]], [[organisation]], [[custom_1]], [[custom_2]], [[custom_3]], [[custom_4]], [[list_name]], [[unsubscribe_url]], [[configure_url]], [[manage_url]]
5 condition fields: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9
6 unknown name: boolean[false]
7 subscription statuses: 0=Pending, 1=Confirmed, 2=Approved, 3=Removed by user, 4=Removed by admin, 6=Bounced soft, 7=Bounced hard, 8=Blacklisted
```

## Related pages

- [Notifications and mail for applications](../creators/app-creators/notifications-and-mail.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
