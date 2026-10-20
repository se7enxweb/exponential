---
title: Translations in an extension
audience: developer, app-creator, designer
category: extension-development
order: 250
summary: Every text in every language: i18n in templates and ezpI18n::tr() in PHP, translation.ts files, TranslationExtensions, the cache, fallbacks between locales, and coverage checks.
keywords: translation, i18n, ezpI18n, tr, context, translation.ts, TS file, TranslationExtensions, locale, eng-US, ger-DE, untranslated, fallback, translation cache, ezlupdate, placeholders
type: help
---
# Translations in an extension

Part 3, chapter 25. Exponential runs in many languages, and an extension's texts should follow the user's language
like the kernel's do. This chapter shows how to mark texts, where translations live, and how they are found.

## Mark every text

In templates, the `i18n` operator with a **context**:

```text
{'Read more'|i18n( 'extension/mysite_hello' )}
{'Hello %name'|i18n( 'extension/mysite_hello',, hash( '%name', $name|wash ) )}
```

The second line, rendered by the template engine with `Ada`, prints `Hello Ada`. The empty second argument is the
optional **comment**, a hint for translators.

In PHP, `ezpI18n::tr( $context, $source, $comment = null, $arguments = null )` (`kernel/common/ezpi18n.php`):

```php
<?php
$text = ezpI18n::tr( 'extension/mysite_hello', 'There are %count greetings.', null, array( '%count' => $count ) );
```

| Rule | Why |
|---|---|
| One context per extension, or per area of it: `extension/mysite_hello`, `extension/mysite_hello/admin` | translators see the texts together |
| Whole sentences with placeholders, never glued pieces | word order differs between languages |
| Placeholders start with `%` | `%name`, `%count` |
| American English as the source text | the project's source language; `license`, `color` |
| Escape placeholders that come from people | `hash( '%name', $name\|wash )` |

## The files

```text
extension/mysite_hello/translations/
    untranslated/translation.ts   every source text, no translations (a template for translators)
    eng-US/translation.ts         English, when it differs from the source
    ger-DE/translation.ts
    fre-FR/translation.ts
```

A `.ts` file is Qt Linguist XML. From the `expqueue` extension's German file:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="de_DE">
<context>
    <name>extension/expqueue</name>
    <message>
        <source>Job queue</source>
        <translation>Auftragswarteschlange</translation>
    </message>
</context>
</TS>
```

## Register them

`extension/mysite_hello/settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[RegionalSettings]
TranslationExtensions[]=mysite_hello

*/ ?>
```

`expqueue` registers itself exactly this way. Then clear the translation cache:

```bash
php bin/php/ezcache.php --clear-id=translation
```

## Fallbacks

A locale without its own translation can fall back to a related one (Austrian German to German, for example). The
chains are in `i18n.ini [TranslationSettings]`; the console shows them and how much of the kernel and of each
extension is covered:

```bash
./console exp:i18n fallbacks --locale=ger-AT
./console exp:i18n check
```

See [Translations and languages](../../../features/6.0/translations-and-languages.md) and
[Languages](../../../languages.md).

## Producing the files

- **By hand**: for a small extension, writing the `.ts` files directly is fine; keep `untranslated/` complete.
- **ezlupdate**: the kernel ships the source of `ezlupdate` (a Qt based tool that extracts `i18n` texts from templates
  and PHP) in `support/ezlupdate-qt4.5/`; its `README.txt` explains how to build it. The `.ts` files can then be
  edited in Qt Linguist or any XML editor.

## Check

```bash
./console exp:ezchecktranslation ger-DE
```

shows statistics for one translation. Switch the administration to another language (**My account**, or a siteaccess
with another `Locale`) and look at your pages: any English text left is a text not marked with `i18n`, or missing from
the file, or a cache not cleared.

## Related pages

- Previous: [Ajax with ezjscore server functions](ajax-with-ezjscore-server-functions.md)
- Next: [Persistent objects and your own tables](persistent-objects-and-tables.md)
- [By hand: translating an extension](tutorials/by-hand-translation.md)
- [Translations and languages](../../../features/6.0/translations-and-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
