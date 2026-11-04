---
title: Indigenous languages of the Americas
audience: government, project-manager, admin, editor
category: public-sector
order: 135
summary: How to publish in an Indigenous language of the Americas with Exponential: the 22 locales that ship (Alaska Native languages, Navajo, Cherokee, Lakota, Ojibwe, Cree, Inuktitut, Mohawk, Nahuatl, Maya, Guarani, Aymara and more), adding the language, translating content, URL aliases, the interface language, and fonts for syllabics and the Cherokee syllabary.
keywords: indigenous languages, Native American languages, Alaska Native languages, First Nations, Inuit, Aleut, Yup'ik, Inupiaq, Alutiiq, Tlingit, Haida, Gwich'in, Tsimshian, Navajo, Cherokee, Lakota, Ojibwe, Cree, Inuktitut, Mohawk, Nahuatl, Yucatec Maya, K'iche', Guarani, Aymara, Mapudungun, syllabics, locale
type: help
---
# Indigenous languages of the Americas

This page is for tribal governments, school districts, public bodies and organizations that publish in an
Indigenous language of the Americas. Exponential 6.0.15 ships a locale for 22 of them, so each can be a content
language without any file of your own. The general advice of
[Minority and regional languages](minority-and-regional-languages.md) applies as well.

## Contents

1. [The languages that ship](#the-languages-that-ship)
2. [Add the language](#add-the-language)
3. [Translate content](#translate-content)
4. [Addresses](#addresses)
5. [The interface language](#the-interface-language)
6. [Fonts for syllabics and the Cherokee syllabary](#fonts-for-syllabics-and-the-cherokee-syllabary)
7. [A language that is not in the list](#a-language-that-is-not-in-the-list)

## The languages that ship

| Region | Languages (locale code) |
|---|---|
| Alaska | Central Yup'ik (`esu-US`), Inupiaq (`ipk-US`), Aleut (`ale-US`), Alutiiq (`ems-US`), Tlingit (`tli-US`), Haida (`hai-US`), Gwich'in (`gwi-US`), Tsimshian (`tsi-US`) |
| United States | Navajo (`nav-US`), Cherokee (`chr-US`), Lakota (`lkt-US`), Ojibwe (`oji-US`) |
| Canada | Cree (`cre-CA`), Inuktitut (`iku-CA`), Ojibwe (`oji-CA`), Mohawk (`moh-CA`) |
| Mexico and Central America | Nahuatl (`nah-MX`), Yucatec Maya (`yua-MX`), K'iche' (`quc-GT`) |
| South America | Guarani (`grn-PY`), Aymara (`aym-BO`), Mapudungun (`arn-CL`), Quechua (`que-PE`) |

Greenlandic (`kal-GL`) and, from the Pacific, Hawaiian (`haw-US`) ship as well. Each locale gives the language its
own name (for example *Diné Bizaad*, *ᏣᎳᎩ*, *Unangam Tunuu*) and the dates, numbers and currency of its country.
Where a language has no established month and day names in the sources the locale was built from, the file uses the
English or Spanish names and says so in its header; [Languages in Exponential](../../../languages.md#indigenous-languages-of-the-americas)
lists which.

## Add the language

1. Open **Setup > Languages** (`/content/translations`) and press **Add language**.
2. Type part of the name, the own name or the code into **Find a language**: `navajo`, `diné` and `nav` all find
   *Navajo · Diné Bizaad · nav-US*.
3. Press **Add language**. The steps are the same as for any language; see
   [Adding a content language](adding-a-content-language.md).

## Translate content

Translate an item into the new language as any other ([Translating an item step by step](translating-an-item-step-by-step.md)).
Most sites publish only part of their content in the Indigenous language. A siteaccess that lists the language
first and English (or Spanish, or French) second shows the translated pages in the Indigenous language and the rest
in the other:

```ini
# settings/siteaccess/site_nv/site.ini.append.php
[RegionalSettings]
Locale=nav-US
ContentObjectLocale=nav-US
SiteLanguageList[]
SiteLanguageList[]=nav-US
SiteLanguageList[]=eng-US
TextTranslation=enabled
```

Ojibwe has two locales: use `oji-US` for a community in the United States and `oji-CA` in Canada. They differ only
in country, currency and date order.

## Addresses

The URL alias of an item is made from its name. With the shipped transformation `urlalias`, Latin letters with
diacritics become plain ASCII letters (ñ becomes n); a name written entirely in Cherokee or in syllabics keeps
its script, because a title that would lose every letter falls back to the IRI transformation
([URLs and aliases, section 10](../../../guides/urls-and-aliases.md#10-titles-in-scripts-without-a-transliteration-table)).
To keep every letter in every alias of a siteaccess, set `[URLTranslator] TransformationGroup=urlalias_iri` in its
`site.ini`. Decide this before you publish; existing aliases change only when items are published again.

## The interface language

Content, page titles and the language switcher use the language; the administration and the fixed texts of the
public design show in the siteaccess's interface language, usually English. Complete administration translations
exist for Quechua, Greenlandic and Hawaiian, and Guarani is in progress; for the other languages the administration
stays in English, or Spanish (`esl-ES`) or French (`fre-CA`) if your staff prefer it
([Administration interface languages](administration-interface-languages.md)). The few fixed texts of a public design
(menu labels, "Search", the footer) can be translated in your design's own `.ts` file.

## Fonts for syllabics and the Cherokee syllabary

Cherokee is written in its own syllabary (U+13A0 to U+13FF and U+AB70 to U+ABBF), Inuktitut and, where a community
prefers them, Cree and Ojibwe in Canadian syllabics (U+1400 to U+167F). Windows, macOS, iOS and Android ship fonts
for both. A public design that sets its own web font must name a fallback that has the characters, for example:

```css
:root {
    --site-font: "Your Brand Font", "Noto Sans Cherokee", "Noto Sans Canadian Aboriginal", system-ui, sans-serif;
}
```

Latin-script languages use special letters such as ł, ŋ, x̱, ẽ and the glottal stop ʼ (U+02BC); check that your
font has them. Characters above U+FFFF need `utf8mb4` on MySQL and MariaDB; none of these languages needs them, but an
emoji in a title does.

## A language that is not in the list

Many more Indigenous languages are spoken in the Americas than ship with a locale. A missing one is a single file
in `share/locale` ([Custom locales for unlisted languages](custom-locales-for-unlisted-languages.md)); copy the file
of a language of the same country, change the names and the code, and add it like any other.

## Related pages

- [Minority and regional languages](minority-and-regional-languages.md)
- [Languages in Exponential](../../../languages.md#indigenous-languages-of-the-americas)
- [Adding a content language](adding-a-content-language.md)
- [Custom locales for unlisted languages](custom-locales-for-unlisted-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
