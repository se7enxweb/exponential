---
title: Locale operators
audience: developer, designer, app-creator
category: development
order: 210
summary: The 7 operators of eZTemplateLocaleOperator - l10n with all nine formatting types, datetime with the datetime.ini classes and custom formats, locale, currentdate, maketime, makedate and gettime - with examples rendered on alpha and the compiled gettime difference.
keywords: l10n, datetime, locale, currentdate, maketime, makedate, gettime, date format, number format, currency, datetime.ini, eZLocale, eZTemplateLocaleOperator
type: help
---
# Locale operators

Dates, times, numbers and money are formatted for a locale, not with PHP's `date()`. The 7 operators on this page are
registered by `eZTemplateLocaleOperator` (`lib/eztemplate/classes/eztemplatelocaleoperator.php`) through
`lib/eztemplate/classes/eztemplateautoload.php`, and do their work with `eZLocale`
(`lib/ezlocale/classes/ezlocale.php`) and the locale files in `share/locale/`.

All examples use fixed timestamps made with `maketime`, so they do not change from day to day. Formats without a
locale code use the locale of the siteaccess (on alpha's site siteaccess: US English formats, as the output shows).

## Contents

1. [l10n](#l10n)
2. [datetime](#datetime)
3. [maketime, makedate and gettime](#maketime-makedate-and-gettime)
4. [currentdate](#currentdate)
5. [locale](#locale)
6. [Related pages](#related-pages)

## l10n

### l10n

Formats the piped value with one of the formatting functions of `eZLocale` (its `$functionMap`):

| Type | eZLocale method | Input |
|---|---|---|
| `time` | `formatTime()` | timestamp |
| `shorttime` | `formatShortTime()` | timestamp |
| `date` | `formatDate()` | timestamp |
| `shortdate` | `formatShortDate()` | timestamp |
| `datetime` | `formatDateTime()` | timestamp |
| `shortdatetime` | `formatShortDateTime()` | timestamp |
| `currency` | `formatCurrencyWithSymbol()` | number |
| `clean_currency` | `formatCleanCurrency()` | number |
| `number` | `formatNumber()` | number |

An unknown type logs "Unknown locale type".

| Parameter | Type | Required | Default |
|---|---|---|---|
| `type` | string | yes | `false` |
| `locale` | string, a locale code such as `ger-DE` | no | `false` (the siteaccess locale) |
| `param` | string, the currency symbol for `currency` | no | `false` (the locale's symbol) |

Returns a string. Generated reference: [l10n](../../examples/template-operators/l10n.md).

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
time          {$t|l10n( 'time' )}
shorttime     {$t|l10n( 'shorttime' )}
date          {$t|l10n( 'date' )}
shortdate     {$t|l10n( 'shortdate' )}
datetime      {$t|l10n( 'datetime' )}
shortdatetime {$t|l10n( 'shortdatetime' )}
number        {1234567.891|l10n( 'number' )}
currency      {1234.5|l10n( 'currency' )}
clean_currency {1234.5|l10n( 'clean_currency' )}
```

Output on alpha:

```text
time          02:05:00 pm
shorttime     02:05 pm
date          Monday October 19 2026
shortdate     10/19/2026
datetime      Monday October 19 2026 02:05:00 pm
shortdatetime 10/19/2026 02:05 pm
number        1,234,567.89
currency      $1,234.50
clean_currency 1,234.50
```

The same values for another locale, and a currency symbol of your own:

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
ger-DE date     {$t|l10n( 'date', 'ger-DE' )}
ger-DE number   {1234567.891|l10n( 'number', 'ger-DE' )}
ger-DE currency {1234.5|l10n( 'currency', 'ger-DE' )}
currency with symbol 'EUR' {1234.5|l10n( 'currency', 'eng-GB', 'EUR' )}
```

Output on alpha:

```text
ger-DE date     Montag, 19. Oktober 2026
ger-DE number   1.234.567,89
ger-DE currency € 1.234,50
currency with symbol 'EUR' EUR 1,234.50
```

## datetime

### datetime

Formats a timestamp with a named class from `settings/datetime.ini` `[ClassSettings] Formats[]`, or with a format of
your own when the class is `custom`. `eZLocale::formatDateTimeType()` turns the format into one for PHP's `date()`:
the letters are PHP's date letters with a `%` in front (`%Y` year, `%m` month with zero, `%n` month without zero, `%d`
and `%j` day, `%H` and `%h` hours, `%i` minutes, `%s` seconds), and other letters are kept as text. The names `%D`,
`%l` (day), `%M`, `%F` (month) and `%a`, `%A` (am/pm) are filled in by the locale, in its language.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `class` | string: a class name or `custom` | yes | `false` |
| `data` | string: the format, with `custom` | no | `false` |

The classes on alpha (kernel `datetime.ini`; ezfind adds `solr`, but ezfind is not active here):

| Class | Format |
|---|---|
| `short_date` | `%j.%n.%Y` |
| `long_date` | `%j %F %Y` |
| `short_date_usa` | `%n/%j/%Y` |
| `time_12` | `%h:%i:%s %a` |
| `time_24` | `%H:%i:%s` |
| `all` | every letter, for testing |

Add a class in your extension's `settings/datetime.ini.append.php` under `[ClassSettings]` and use it everywhere.
Returns a string. Generated reference: [datetime](../../examples/template-operators/datetime.md).

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
short_date      {$t|datetime( 'short_date' )}
long_date       {$t|datetime( 'long_date' )}
short_date_usa  {$t|datetime( 'short_date_usa' )}
time_12         {$t|datetime( 'time_12' )}
time_24         {$t|datetime( 'time_24' )}
custom ISO 8601 {$t|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}
custom words    {$t|datetime( 'custom', '%l %j %F %Y' )}
```

Output on alpha:

```text
short_date      19.10.2026
long_date       19 October 2026
short_date_usa  10/19/2026
time_12         02:05:00 pm
time_24         14:05:00
custom ISO 8601 2026-10-19T14:05:00
custom words    Monday 19 October 2026
```

A class that does not exist logs an error and leaves the timestamp as it was:

```tpl
[{maketime( 14, 5, 0, 10, 19, 2026 )|datetime( 'no_such_class' )}]
```

Output on alpha:

```text
[1792443900]
```

Logged by the template engine:

```text
ERROR datetime: DateTime class 'no_such_class' is not defined
```

## maketime, makedate and gettime

### maketime

PHP `mktime()` with the parameters given, in mktime's order. Parameters left out are not passed, so PHP uses the
current value for them; with none at all it returns `time()`. Returns a Unix timestamp.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `hour` | integer | no | now |
| `minute` | integer | no | now |
| `second` | integer | no | now |
| `month` | integer | no | now |
| `day` | integer | no | now |
| `year` | integer | no | now |
| `dst` | integer | no | not passed |

Generated reference: [maketime](../../examples/template-operators/maketime.md).

### makedate

`mktime( 0, 0, 0, month, day, year )`: midnight of a date.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `month` | integer | no | now |
| `day` | integer | no | now |
| `year` | integer | no | now |
| `dst` | integer | no | not passed |

Generated reference: [makedate](../../examples/template-operators/makedate.md).

### gettime

Splits a timestamp into a hash with the keys `seconds`, `minutes`, `hours`, `day`, `month`, `year`, `weeknumber`
(ISO 8601, weeks starting on Monday), `weekday` (0 is Sunday), `yearday` (0 is January 1) and `epoch`. Without a
timestamp it uses the current time.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `timestamp` | integer | no | now |

Generated reference: [gettime](../../examples/template-operators/gettime.md).

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )
     $d = makedate( 12, 25, 2026 )
     $info = gettime( $t )}
maketime( 14, 5, 0, 10, 19, 2026 ) = {$t}
makedate( 12, 25, 2026 ) = {$d} ({$d|datetime( 'custom', '%l %j %F %Y, %H:%i' )})
gettime( $t ): {foreach $info as $key => $value}{$key}={$value} {/foreach}

gettime( $d ).weekday = {gettime( $d ).weekday}
```

Output on alpha:

```text
maketime( 14, 5, 0, 10, 19, 2026 ) = 1792443900
makedate( 12, 25, 2026 ) = 1798185600 (Friday 25 December 2026, 00:00)
gettime( $t ): seconds=0 minutes=5 hours=14 day=19 month=10 year=2026 weeknumber=43 weekday=1 yearday=291 epoch=1792443900 
gettime( $d ).weekday = 5
```

**Pass the timestamp as a parameter.** The interpreter also takes it from the piped input:

```tpl
{def $t = maketime( 14, 5, 0, 10, 19, 2026 )}
$t|gettime.hours = {$t|gettime.hours}
```

Output on alpha:

```text
$t|gettime.hours = 14
```

but the compiled code (`getTimeTransformation()`) looks only at the parameters, so compiled on alpha the same line
used the current time and printed the current hour instead of 14. `gettime( $t )` gives the same answer in both.

Output on alpha with the template compiler (compiled into a scratch folder):

```text
$t|gettime.hours = 2
```

## currentdate

### currentdate

The current Unix timestamp (`time()`). No parameters, no input. Inside a cache-block or a view-cached page the value is
the time the cache was made. Generated reference: [currentdate](../../examples/template-operators/currentdate.md).

The example tests the value instead of printing it, so the page does not change every day; it also computes an age in
days from the publishing date of the Privacy Policy article on alpha (node 90):

```tpl
{def $now = currentdate()}
currentdate() is a Unix timestamp: {$now|is_integer|choose( 'no', 'yes' )}, after 2026-01-01: {$now|gt( makedate( 1, 1, 2026 ) )|choose( 'no', 'yes' )}
Published age of the Privacy Policy article in whole days is a number: {div( sub( $now, fetch( 'content', 'node', hash( 'node_id', 90 ) ).object.published ), 86400 )|floor|is_numeric|choose( 'no', 'yes' )}
```

Output on alpha:

```text
currentdate() is a Unix timestamp: yes, after 2026-01-01: yes
Published age of the Privacy Policy article in whole days is a number: yes
```

## locale

### locale

Returns the `eZLocale` object for a locale code, given piped or as the parameter. Its attributes include
`locale_code`, `language_name`, `country_name`, `currency_symbol`, `currency_name` and the formats. Without a code it
reports the missing parameter `localestring`; the current locale is reached through the formatting operators instead.

| Parameter | Type | Required | Default |
|---|---|---|---|
| locale code | string | yes, unless piped | none |

Returns an object. Generated reference: [locale](../../examples/template-operators/locale.md).

```tpl
{def $de = locale( 'ger-DE' )
     $no = 'nor-NO'|locale}
locale( 'ger-DE' ): {$de.locale_code}, {$de.language_name}, currency {$de.currency_symbol}
'nor-NO'|locale: {$no.locale_code}, {$no.language_name}
```

Output on alpha:

```text
locale( 'ger-DE' ): ger-DE, Deutsch (Deutschland), currency €
'nor-NO'|locale: nor-NO, Norsk (Bokmål)
```

## Related pages

- [Dates, numbers and localization for designers](../designers/templates/dates-numbers-and-l10n.md)
- [Arithmetic operators](arithmetic-operators.md): computing with timestamps
- [PHP string functions: encode and format](php-string-functions-encode-and-format.md): `number_format`, `sprintf`
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
