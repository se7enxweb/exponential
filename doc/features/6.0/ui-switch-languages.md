# The interface language list: every language, the most needed first

Since 6.0.15 (#516). Extends the [interface language](ui-switch.md) choice of #506.

The *Interface language* choice in the right toolbar (admin4, admin4l, the classic admin design) and in the AdminUI
side bar offers every language the kernel has a locale for, not only the few that have a translation. It is one list
box with headings:

1. **English**: the English locales administrators use most.
2. **Major world languages**: the 19 languages most needed by administrators worldwide, ranked (see below).
3. **Regional variants**: 10 regional forms of those languages with their own formats.
4. **All languages (A–Z)**: every language of `share/locale` (about 130), in alphabetical order.

Each entry names the language in itself and in the language the interface is shown in now, and says how complete
the interface translation is:

```
Deutsch (Deutschland) — German (Germany) (99%)
Français (France) — French (France) (46%)
Kiswahili (Kenya) — Swahili (Kenya) (no translation yet)
```

A language without a translation is offered honestly: its texts stay English, but dates, times and numbers follow
its locale (`share/locale/<locale>.ini`). Choosing one is also the first step towards translating the interface:
the formats are right from the start, and every message translated later appears at once.

All of it is set in `settings/uiswitch.ini [UISwitch_language]`; the settings are described in
[UI switch settings](../../guides/ui-switch-settings.md#uiswitch_language).

## The list box

A native `<select>` with an `<optgroup>` per group and an **Apply** button, in a POST form to `uiswitch/set`
(template `design/standard/templates/uiswitch/grouped.tpl`, used by every administration design, which all fall back to the standard design; providers without
groups keep their list of buttons).

- **Type-ahead**: with the list box focused, typing the first letters of a name ("Kis") selects the first entry that
  starts with them, as in every native list box. Arrow keys, Home, End, Page Up and Page Down move through it.
- **Nothing happens until Apply**: moving through the list never reloads the page (WCAG 3.2.2, on input).
- **Screen readers** announce the label (*Interface language*), the entry and its group.
- The entry in effect is selected where it first appears (a language of a group is also in the A–Z list).
- No script is needed, and none is added. The styles are the shared component classes (`a4-input`, `a4-btn`,
  `a4-stack`, `a4-meta`) with the design's tokens and their dark values; admin4.css adds only that the list box is
  never wider than its column.

## The A–Z order and the names

- The **own name** comes from the locale file (`LanguageName`). Where a file only repeats the English name
  ("Indonesian", "Croatian (Hrvatski)", "Serbian (Српски)": the first word is that of `InternationalLanguageName`),
  PHP's Intl extension gives the own name instead ("Indonesia (Indonesia)", "Hrvatski (Hrvatska)").
- The **second name** is the language's name in the reader's current interface language (Intl,
  `Locale::getDisplayName()`), else the English name of the locale file. It is left out when both are the same.
- Two locales with the same names get their codes added (`[ser-SR]`, `[srp-RS]`).
- The order is the **collation of the reader's interface language** (Intl's `Collator`): Č after C in Czech, Ä with A
  in English. Without Intl the fallback folds accents (Unicode decomposition when `Normalizer` exists) and case, and
  sorts other scripts after Latin, in code point order.

## Translation coverage

`expUISwitchLanguageCatalogue` counts, for each locale with `share/translations/<locale>/translation.ts`, the source
messages of `share/translations/untranslated/translation.ts` (context, source and comment) that the file translates:
a translation that is empty, `unfinished` or `obsolete` does not count, nor does a message the kernel no longer has.
The percentage is rounded down, so only a complete translation says 100%. English (`eng-*`) is the language of the
source texts and always 100%. Only the kernel's translation is counted, not the extensions' own.

Reading the 25 translation files (1 to 3 MB each) takes 2 to 3 seconds, so the result is cached: in memory for the
process (Velocity workers) and in `<cache directory>/uiswitch/language-catalogue.json` (and the names per reader
language in `language-names-<code>.json`). Both are keyed by a signature of the names, sizes and times of every
locale and translation file, so a new or changed file builds the catalogue again on its next use; nothing needs
clearing. With the cache, the list costs about 2 ms per page.

A regional variant uses only its own translation file: `ger-AT` has none and shows English texts, not German ones.
To give it German texts, copy `share/translations/ger-DE/` to `share/translations/ger-AT/`.

## Untranslated languages: listed by default

`ListUntranslated=enabled` is shipped, so every language is offered, the untranslated ones marked as such. The
reasons: the owner's aim is that the list is useful to administrators worldwide, the formats of a locale are useful
on their own, and the mark makes the state clear before anyone chooses. `ListUntranslated=disabled` offers English
and the translated languages only (and is what applies when the setting is missing, as in #506).

## Security

- The allow-list is the siteaccess's own `Locale`, the groups' locales, `AllowedValues[]` and, with
  `AllLanguages=enabled`, every `share/locale/<code>.ini` whose name is a locale code (`/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/`),
  without the format variants (`eng-GB@euro`) unless one is named. With `ListUntranslated=disabled` a locale also
  needs a translation file (or to be English).
- A value from the request is compared with that list as it is (`in_array(..., true)`): nothing from the request is
  normalised, and nothing reaches a path. Paths are built only from codes read from the directory listing or the
  settings, after the pattern check.
- The cache files hold only data (JSON), no code, and a file whose signature does not match is ignored.

## Why the default ranking is what it is

The groups rank languages by how many administrators of a CMS are likely to need them: the number of people who
speak the language (first or second language) **and** how much of the web's content and business is in it. Neither
alone is enough: Hindi and Bengali have hundreds of millions of speakers but little web content, and many of their
administrators work in English; Dutch, Polish and Italian have far fewer speakers but a large share of websites.

| Language | Speakers, millions (L1+L2) | Websites in it | Place |
|---|---|---|---|
| English | 1,493 | 49.5% | English group |
| Spanish | 561 | 6.1% | 1 |
| Chinese (Mandarin) | 1,183 | 1.3% | 2 |
| French | 334 | 4.5% | 3 |
| German | 133 | 5.9% | 4 |
| Portuguese | 269 | 4.1% | 5 |
| Japanese | 126 | 4.9% | 6 |
| Russian | 210 | 3.4% | 7 |
| Arabic (Standard) | 335 (L2) | 0.6% | 8 |
| Hindi | 611 | < 0.1% | 9 |
| Italian | 66 | 2.7% | 10 |
| Indonesian | 255 | 1.3% | 11 |
| Turkish | 94 | 1.6% | 12 |
| Korean | 82 | 0.9% | 13 |
| Dutch | — | 2.1% | 14 |
| Polish | — | 1.8% | 15 |
| Vietnamese | 97 | 0.9% | 16 |
| Ukrainian | — | 0.6% | 17 |
| Persian | 82 | 1.0% | 18 |
| Hebrew | 10 | 0.4% | 19 |

Speakers: Ethnologue, as summarised on Wikipedia's *List of languages by total number of speakers* (2026 edition;
"—" where the summary has no figure). Websites: W3Techs, *Usage Statistics and Market Share of Content Languages for
Websites*, October 2026. Figures change every year; the order matters, not the digits.

- **English** gets its own group because it is the source language (always complete) and the one most
  administrators use: `eng-US` first (the most common default), `eng-GB` (the kernel's own default), then `eng-AU`,
  `eng-CA` and `eng-IN`, the largest other English-speaking administrations with their own date formats.
- **Major world languages**: the top of both lists, Spanish to Russian first (large on both), then Arabic and Hindi
  (very many speakers), then the languages with a large share of websites and business software (Italian, Turkish,
  Korean, Dutch, Polish) and the large markets of Indonesian, Vietnamese, Ukrainian and Persian, then Hebrew
  (0.4% of websites, more than Thai, and the right-to-left language that has an interface translation and a
  right-to-left administration layout since #521). Bengali, Urdu, Thai
  and Swahili are in the A–Z list: many speakers, little web content (Thai 0.3%, the others under 0.1%).
- **Regional variants**: the regional forms with the most users and their own formats: Mexican and US Spanish,
  Canadian and Swiss French, Austrian and Swiss German, European Portuguese, Traditional Chinese of Taiwan and Hong
  Kong, and Egyptian Arabic, the largest Arabic-speaking country.

In all 34 entries, inside the 14 to 35 the list should hold so that the groups stay quick to scan. An installation
with other needs ranks its own: see [UI switch settings](../../guides/ui-switch-settings.md#change-the-groups).

Sources:

- W3Techs, Usage Statistics and Market Share of Content Languages for Websites:
  https://w3techs.com/technologies/overview/content_language
- Wikipedia, List of languages by total number of speakers (from Ethnologue):
  https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers
- Ethnologue, the 200 most spoken languages: https://www.ethnologue.com/insights/ethnologue200/
- Unicode CLDR, Territory-Language Information (speakers of each language per country, from census data and
  Ethnologue): https://www.unicode.org/cldr/charts/latest/supplemental/territory_language_information.html

## For developers

- `expUISwitchLanguageProvider::groups()`: the groups, each `identifier`, `label` and `values`.
  `displayLabel( $value )` is the text of an entry, `coverage( $value )` the percentage or null, `nativeName()` and
  `readerName()` the two names. `label()` stays the own name (the flat list, `uiswitch/state`).
- `expUISwitch::toolbar()` adds `groups` to a provider's entry when the provider has `groups()` and
  `displayLabel()`; each group's values are hashes `value`, `label`, `current`, `lang`. `uiswitch/state` returns them
  too. A provider of an extension gets the same list box by implementing both methods.
- `expUISwitchLanguageCatalogue`: `entries()`, `entry( $code )`, `readerNames( $httpCode )`, `localeCodes()`,
  `coveragePercent()`, `sortValuesByLabel( $labels, $httpCode, $useIntl )`.
- Tests: `tests/tests/kernel/classes/uiswitch/expUISwitchLanguageProviderTest.php` (grouping, the A–Z order with and
  without Intl, the allow-list, untranslated languages, coverage and its cache), without a database.
