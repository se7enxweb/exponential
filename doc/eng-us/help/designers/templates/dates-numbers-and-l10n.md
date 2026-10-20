---
title: Dates, numbers and l10n
audience: designer, developer
category: templates
order: 260
summary: Showing dates, times, numbers and prices in the visitor's locale with l10n (time, shorttime, date, shortdate, datetime, shortdatetime, currency, clean_currency, number), custom formats with datetime, the time element, and currentdate, maketime and makedate.
keywords: l10n, datetime, date format, time format, locale, shortdate, shortdatetime, currency, number format, currentdate, maketime, makedate, time element, localization
type: help
---
# Dates, numbers and l10n

This page is for designers who print dates, times, numbers and prices. Formats differ per locale; the locale operators
(`eZTemplateLocaleOperator`, `lib/eztemplate/classes/eztemplatelocaleoperator.php`) use the formats of the current
locale file in `share/locale/`.

## Contents

1. [l10n](#l10n)
2. [Custom formats with datetime](#custom-formats-with-datetime)
3. [The time element](#the-time-element)
4. [Current and computed dates](#current-and-computed-dates)

## l10n

```tpl
{$node.object.published|l10n( 'shortdate' )}
{$node.object.published|l10n( 'datetime' )}
{$price|l10n( 'currency' )}
{$count|l10n( 'number' )}
```

The types, from `eZLocale`'s function map (`lib/ezlocale/classes/ezlocale.php`):

| Type | Uses the locale setting |
|---|---|
| `time`, `shorttime` | `[DateTime] TimeFormat`, `ShortTimeFormat` |
| `date`, `shortdate` | `DateFormat`, `ShortDateFormat` |
| `datetime`, `shortdatetime` | `DateTimeFormat`, `ShortDateTimeFormat` |
| `currency`, `clean_currency` | `[Currency]` settings, with and without the symbol |
| `number` | `[Numbers]` settings |

For `eng-GB`, `ShortDateFormat` is `%d/%m/%Y` (from `share/locale/eng-GB.ini`); each locale file has its own.

## Custom formats with datetime

When a design needs a fixed format (for example inside a `datetime=""` attribute):

```tpl
{$timestamp|datetime( 'custom', '%Y-%m-%d' )}
```

The format letters follow PHP's `date()` letters prefixed with `%` (`%Y`, `%m`, `%d`, `%H`, `%i`, `%s`; names of days and
months in the locale's language with `%l`, `%F`, `%D`, `%M`).

## The time element

Machine-readable date, human-readable text:

```tpl
<time datetime="{$node.object.published|datetime( 'custom', '%Y-%m-%dT%H:%i' )}">
    {$node.object.published|l10n( 'shortdate' )}
</time>
```

## Current and computed dates

```tpl
{currentdate()}                          {* now, as a timestamp *}
{maketime( 0, 0, 0, 12, 31, 2026 )}      {* hour, minute, second, month, day, year *}
{makedate( 12, 31, 2026 )}               {* month, day, year *}
```

Caution: `currentdate()` in a view-cached template or a cache-block is frozen at the time the cache was written. A
"days until" counter must sit outside cached output, or be computed by a script.

## Related pages

- [The i18n operator](i18n-operator.md)
- [Tutorial: internationalize a theme](../theme-design/tutorial-internationalize-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
