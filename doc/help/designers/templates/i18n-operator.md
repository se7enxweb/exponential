---
title: The i18n operator
audience: designer, developer
category: templates
order: 250
summary: Translating interface texts in templates with i18n: the context, the translator comment, placeholders with a hash or numbered with an array, washing, the x18n and d18n variants, and naming contexts for a theme.
keywords: i18n, translate, translation, context, placeholders, %1, hash arguments, x18n, d18n, interface text, translatable strings, ezpI18n
type: help
---
# The i18n operator

This page is for designers who make template texts translatable. Every interface text a design prints goes through
`i18n` (`kernel/common/ezi18noperator.php`, which calls `ezpI18n::tr()`).

## Contents

1. [The call](#the-call)
2. [Contexts](#contexts)
3. [Placeholders](#placeholders)
4. [Washing](#washing)
5. [Variants](#variants)

## The call

```tpl
{'Read more'|i18n( 'design/mytheme/line' )}
{'Read more'|i18n( 'design/mytheme/line', 'Link under a teaser' )}
```

| Parameter | Meaning |
|---|---|
| input | the English source text |
| `context` | the group of the text in the translation files |
| `comment` | a note for translators (optional) |
| `arguments` | values for placeholders (optional) |

If no translation exists for the current locale, the source text is shown.

## Contexts

Use one context per area of your design, beginning with `design/<theme>/`:
`design/mytheme/pagelayout`, `design/mytheme/full/article`, `design/mytheme/forms`. The same text in two contexts can have
two translations, which matters for short words ("Open" a menu versus "Open" as a state).

## Placeholders

Never build sentences by concatenating translated pieces: word order differs between languages. Put placeholders in the
text.

Named, with a hash:

```tpl
{'%count results for "%query"'|i18n( 'design/mytheme/search',, hash( '%count', $count, '%query', $query|wash ) )}
```

Numbered, with an array (the kernel's mail templates do this):

```tpl
{"Collected information from %1"|i18n( "design/standard/content/edit",, array( $collection.object.name|wash ) )}
```

The empty second parameter skips the comment.

## Washing

- The translated text is not washed by `i18n`. Wash the values you put into placeholders (as above) when they come from
  content or the request.
- In an attribute, wash the whole result: `title="{'Close'|i18n( 'design/mytheme' )|wash}"`.

## Variants

The kernel registers three names in the same class:

- `i18n`: the normal case.
- `x18n`: takes the extension name as an extra first parameter.
- `d18n`: an alias of `i18n` for a **variable** input, so string extraction tools do not pick it up
  (`{$label|d18n( 'design/mytheme' )}`); the text must still exist in the translation file to be translated.

## Related pages

- [Translating template strings](translating-template-strings.md)
- [Tutorial: internationalize a theme](../theme-design/tutorial-internationalize-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
