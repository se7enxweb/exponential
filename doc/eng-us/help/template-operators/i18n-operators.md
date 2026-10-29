---
title: Translation operators i18n, d18n and x18n
audience: developer, designer, app-creator
category: development
order: 310
summary: The three translation operators of eZi18nOperator, their parameters (context, comment, arguments, extension), how ezpI18n::tr() finds a translation and inserts arguments, how compiled templates translate at compile time, and why d18n exists, with examples rendered in English and German on a development installation.
keywords: i18n, d18n, x18n, translation, translate, ezpI18n, eZi18nOperator, context, arguments, translation.ts, ezlupdate, locale
type: help
---
# Translation operators i18n, d18n and x18n

`eZi18nOperator` (`kernel/common/ezi18noperator.php`) registers `i18n`, `x18n` and `d18n` in
`kernel/common/eztemplateautoload.php`. All three end in `ezpI18n::tr()` (`kernel/common/ezpi18n.php`), the same
function PHP code uses to translate.

## Contents

1. [The operators](#the-operators): [i18n](#i18n), [d18n](#d18n), [x18n](#x18n)
2. [How a text is translated](#how-a-text-is-translated)
3. [Examples](#examples)
4. [Compiled templates](#compiled-templates)

## The operators

### i18n

Generated page: [i18n](../../examples/template-operators/i18n.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `context` | string | no | `false` |
| `comment` | string | no | empty |
| `arguments` | hash | no | `false` |

- **Input:** the source text (American English).
- **Returns:** the translated text with the arguments inserted.

The context names the group of messages in the translation file (`design/admin/content/edit`). The comment tells
two identical source texts of one context apart. The arguments are replaced with `strtr()` after translation: a key
such as `%count` is replaced as written, an integer key `n` replaces `%1` to `%9` (`'%' . ( ( n % 9 ) + 1 )`), so
`array( 3, 7 )` fills `%1` and `%2`.

The `ezlupdate` tool reads templates for `|i18n(` with a constant text to build the translation files, which is why
the text should be a literal.

### d18n

Generated page: [d18n](../../examples/template-operators/d18n.md).

The same parameters and the same code as `i18n`. The constructor comment says why it exists: it is an alias "for use
with dynamic variables as input where you don't want ezlupdate to pick up the string", for example menu labels that
come from settings. The admin designs use it that way for the menu items of `menu.ini` (`design/admin4/templates/parts/ini_menu.tpl`).

### x18n

Generated page: [x18n](../../examples/template-operators/x18n.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `extension` | string | yes | `false` |
| `context` | string | no | `false` |
| `comment` | string | no | empty |
| `arguments` | hash | no | `false` |

The first parameter names an extension, then the parameters of `i18n` follow. In the current code `modify()` reads
the extension name but does not use it: the text is translated with `ezpI18n::tr()` exactly like `i18n`, from the
translations of all active extensions. `x18n` has no template hints, so it is never translated at compile time.

## How a text is translated

`ezpI18n::tr( $context, $source, $comment, $arguments )`:

1. With `[RegionalSettings] TextTranslation=disabled` (and no dynamic translations) only the arguments are inserted.
2. For the locale `eng-GB` (the source locale, no translation file) only the arguments are inserted.
3. Otherwise the `translation.ts` of the siteaccess locale is loaded (from `[RegionalSettings] TranslationRepository`, then from the `translations/` folder of each extension in `TranslationExtensions[]`),
   cached unless `TranslationCache=disabled`. With
   `[RegionalSettings] DevelopmentMode` enabled, missing texts are shown in "Bork" form between square brackets.
4. A text the file does not have comes back unchanged. For `eng-US` that is silent (the source texts are American
   English); for other locales a debug message "Missing translation for message in context" is written.

## Examples

The same template rendered twice: on the `site` siteaccess (locale eng-US) and on `bold_ger` (locale ger-DE).

```tpl
{def $label = 'Search'}
locale: {ezini( 'RegionalSettings', 'Locale' )}
1 {'Search'|i18n( 'design/standard/content/search' )}
2 {'%count items'|i18n( 'design/admin/node/view/full', '', hash( '%count', 12 ) )}
3 {'Page %1 of %2'|i18n( 'example/context', 'positional arguments', array( 3, 7 ) )}
4 {$label|d18n( 'design/standard/content/search' )}
5 {'Search'|x18n( 'myextension', 'design/standard/content/search' )}
```

Output:

```text
locale: eng-US
1 Search
2 12 items
3 Page 3 of 7
4 Search
5 Search
```

On `bold_ger`:

```text
locale: ger-DE
1 Suchen
2 12 items
3 Page 3 of 7
4 Suchen
5 Suchen
```

`Search` in the context `design/standard/content/search` has a German translation (`Suchen`); the other two texts are
not in any German file, so they come back as written, with their arguments inserted. `x18n` (line 5) translated like
`i18n`.

## Compiled templates

`operatorTemplateHints()` gives `i18n` and `d18n` the transformation `i18nTrans()`. When the text, the context and
the comment are constants, the compiler translates the text **while compiling** and writes the translated string into
the compiled file; with constant arguments as well, it writes code that only inserts the arguments. A compiled
template therefore keeps the translation of the time it was compiled: after changing a `translation.ts`, clear the
translation cache and the compiled templates (`php bin/php/ezcache.php --clear-id=template,translation`).

## Related pages

- [The i18n operator for designers](../designers/templates/i18n-operator.md)
- [Translating template strings](../designers/templates/translating-template-strings.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
