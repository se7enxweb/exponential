---
title: Right-to-left markets
audience: businesses, sales-marketing, website-creator
category: business-solutions
order: 50
summary: What a business needs to know before selling or publishing in Arabic, Hebrew, Persian, Urdu and other right-to-left languages with Exponential - which locales exist, what the administration and content do by themselves, what the public design must do, and how to test.
keywords: right to left, RTL, Arabic, Hebrew, Persian, Urdu, Yiddish, dir, text direction, TextDirection, administration, design, testing, Middle East, business
type: help
---
# Right-to-left markets

This page is for owners, marketing leads and project managers who plan a site, a store or a language version in a
script written from right to left. It sets expectations: what Exponential does by itself, what your design team has
to build, and what a native reader must check before launch. The technical guides are
[Right-to-left sites](../../creators/website-creators/right-to-left-sites.md) for site builders and
[Right-to-left languages in the administration](../../../features/6.0/right-to-left.md).

![Interface languages of the administration](../../images/help/bu-rtl.webp)

## Contents

1. [Which languages](#which-languages)
2. [What works by itself](#what-works-by-itself)
3. [What your design must do](#what-your-design-must-do)
4. [Editors who work right to left](#editors-who-work-right-to-left)
5. [Budget and schedule](#budget-and-schedule)
6. [Testing before launch](#testing-before-launch)

## Which languages

[Languages in Exponential, section 9](../../../languages.md#9-right-to-left-languages) lists 43 locale files written
right to left. Among them:

| Language | Locales | Script |
|---|---|---|
| Arabic | 24 country locales, from `ara-SA` and `ara-EG` to `ara-MA` and `ara-AE` | Arabic |
| Hebrew | `heb-IL` | Hebrew |
| Yiddish | `yid-US` | Hebrew |
| Persian, Dari | `fas-IR`, `fas-AF` | Arabic |
| Urdu | `urd-PK`, `urd-IN` | Arabic |
| Pashto, Sindhi, Kashmiri, Uyghur, Central Kurdish, Western Punjabi | one locale each, or more | Arabic |
| Syriac, N'Ko, Dhivehi | `syr-IQ`, `syr-SY`, `nqo-GN`, `div-MV` | Syriac, N'Ko, Thaana |

The direction of a locale comes from one line in its locale file, `[LanguageSettings] TextDirection=rtl`; a file
without it is left to right. Kurdish (Kurmanji) and Uzbek in Latin script are left to right.

> **Note**
> The count grows when locales are added. Quote the reference page
> [Languages in Exponential](../../../languages.md#9-right-to-left-languages) and give the date of its figures.

## What works by itself

| Area | Behavior | Source |
|---|---|---|
| The administration | Turns around when the interface language is right to left: `dir="rtl"`, side columns swapped, text from the right | [Right to left (administration)](../../../features/6.0/right-to-left.md) |
| Left-to-right pages of the administration | "The same markup byte for byte" as before | same |
| Content in a right-to-left language | Marked `dir="rtl"` where it is shown and edited, also on a left-to-right page | [Languages, section 9](../../../languages.md#9-right-to-left-languages) |
| The online editors | Switch their direction for right-to-left texts | [Right-to-left sites](../../creators/website-creators/right-to-left-sites.md#content-in-a-right-to-left-language) |
| Formats | Dates, numbers and currency follow the locale | [Currencies, dates and formats](currencies-dates-and-formats.md) |

So a Hebrew translation reads correctly inside an English site, and Arabic-speaking editors can use a mirrored
administration.

## What your design must do

The public site's design is your project's: it "does what its templates and stylesheets say"
([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)). For the design team that means:

- Print the direction on the page frame from the site's locale, as the administration pagelayouts do.
- Write stylesheets with logical properties (start and end instead of left and right), so "one stylesheet serves
  both directions; no `-rtl.css` copy to maintain".
- Flip icons that point along the line, such as "next" arrows.
- Check menus that collapse on narrow screens; the guide notes they "often break first".

If you start from a shipped theme, check its stylesheets for right to left before you choose it
([Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md#decision-checklist)).

## Editors who work right to left

Two right-to-left languages have a complete administration translation: Hebrew (`heb-IL`) and Arabic (`ara-SA`,
Modern Standard Arabic, which every Arabic country falls back to). Both are machine-assisted, and the reference page
says they "need a review by a native speaker before a site relies on them"
([Languages, section 9](../../../languages.md#9-right-to-left-languages)). Editors in Persian, Urdu and the other
right-to-left languages work in an English (or other) administration while writing content in their language.

## Budget and schedule

Plan these items explicitly; they are easy to forget:

| Item | Why |
|---|---|
| Design work for right to left | Your public design, not the product, decides how pages look |
| A native-speaker review of the pages | Mixed text (numbers, Latin product names inside Arabic) needs a reader to confirm |
| A review of the administration translation | If editors work in Hebrew or Arabic |
| Fonts | The script must be covered by the fonts your design loads ([Languages, section 3](../../../languages.md#3-every-locale) covers scripts and fonts) |
| Translated legal texts | Checked by someone qualified for that market |

## Testing before launch

From the site builders' checklist, in business terms:

- [ ] The right-to-left site's pages are mirrored: columns, menus and text start at the right.
- [ ] Mixed text (numbers, Latin words in Arabic or Hebrew) reads correctly.
- [ ] Forms (checkout, sign-up, contact) align labels, inputs and error messages to the start side.
- [ ] Icons that point along the line point the right way.
- [ ] The left-to-right sites look exactly as before.
- [ ] Everything was checked at phone width too.
- [ ] A native reader signed off on the content and the checkout.

## Related pages

- [Languages and locales](languages-and-locales.md)
- [Multilingual checklist](multilingual-checklist.md)
- [Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)
- [Right-to-left languages in the administration](../../../features/6.0/right-to-left.md)
- [Languages in Exponential](../../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
