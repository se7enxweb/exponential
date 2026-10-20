---
title: "Tutorial: internationalize a theme"
audience: designer
category: theme-design
order: 810
summary: Step by step, make every text of a theme translatable: i18n in templates with a context, placeholders, a translation.ts per language in the extension, TranslationExtensions, locale-aware dates and numbers, right-to-left languages, and caches.
keywords: tutorial, internationalize, i18n, translation, translation.ts, TranslationExtensions, theme translation, context, placeholders, l10n, multilingual theme, language
type: help
---
# Tutorial: internationalize a theme

This tutorial is for designers who want their theme in more than one language. You will make every visible text of the
design translatable and ship a German translation. Allow one to two hours for a small theme.

## Contents

1. [Step 1: every text through i18n](#step-1-every-text-through-i18n)
2. [Step 2: placeholders, not concatenation](#step-2-placeholders-not-concatenation)
3. [Step 3: the translation file](#step-3-the-translation-file)
4. [Step 4: tell the siteaccess to read it](#step-4-tell-the-siteaccess-to-read-it)
5. [Step 5: dates, numbers and direction](#step-5-dates-numbers-and-direction)
6. [Step 6: caches and checks](#step-6-caches-and-checks)

## Step 1: every text through i18n

Every text a template prints goes through the `i18n` operator with a **context**, a name that groups the texts of one
area. Use `design/<your theme>/<area>`:

```tpl
<a class="skip-link" href="#main">{'Skip to main content'|i18n( 'design/mytheme/pagelayout' )}</a>
<button type="button">{'Menu'|i18n( 'design/mytheme/pagelayout' )}</button>
```

Also attribute values (`title`, `aria-label`, `alt` of design images, `placeholder`): wash them when they go into an
attribute, `|i18n( ... )|wash`. See [The i18n operator](../templates/i18n-operator.md).

## Step 2: placeholders, not concatenation

Word order differs between languages. Put variable parts into the sentence as placeholders:

```tpl
{'Page %current of %total'|i18n( 'design/mytheme/navigator',, hash( '%current', $page, '%total', $pages ) )}
```

The second parameter (empty here) is a comment for translators.

## Step 3: the translation file

`extension/mytheme/translations/ger-DE/translation.ts`:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="ger-DE" sourcelanguage="en">
<context>
    <name>design/mytheme/pagelayout</name>
    <message>
        <source>Skip to main content</source>
        <translation>Zum Inhalt springen</translation>
    </message>
    <message>
        <source>Menu</source>
        <translation>Menü</translation>
    </message>
</context>
<context>
    <name>design/mytheme/navigator</name>
    <message>
        <source>Page %current of %total</source>
        <translation>Seite %current von %total</translation>
    </message>
</context>
</TS>
```

The starter theme's `translations/ger-DE/translation.ts` has exactly this form. The `source` must match the template
text character for character. A text without a translation is shown in English.

## Step 4: tell the siteaccess to read it

The kernel reads `share/translations/` and the extensions listed in `site.ini [RegionalSettings] TranslationExtensions[]`.
In the extension's siteaccess settings (as the media and starter themes do):

```ini
# extension/mytheme/settings/siteaccess/<siteaccess>/site.ini.append.php
[RegionalSettings]
TranslationExtensions[]=mytheme
```

The siteaccess's `[RegionalSettings] Locale` (for example `ger-DE`) chooses the language.

## Step 5: dates, numbers and direction

- Dates and numbers through `l10n`: `{$date|l10n( 'shortdate' )}`, `{$amount|l10n( 'number' )}`
  ([Dates, numbers and l10n](../templates/dates-numbers-and-l10n.md)).
- `lang` and `dir` on `<html>` from `$site` ([Right-to-left designs](right-to-left-designs.md)).
- Leave room: German and French texts are often a third longer than English. Buttons and menus must wrap or grow.

## Step 6: caches and checks

```bash
php bin/php/ezcache.php --clear-id=translation
./console exp:ezchecktranslation ger-DE    # statistics for one translation
```

- [ ] Open every page type in each language: no English left in the design's own texts.
- [ ] Long translations do not break buttons or menus at 375 px.
- [ ] The `translation.ts` file is valid XML (`xmllint --noout`).

## Related pages

- [Translating template strings](../templates/translating-template-strings.md)
- [Translations in a theme](../extensions/translations-in-a-theme.md)
- [Translations and languages](../../../features/6.0/translations-and-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
