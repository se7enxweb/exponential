# The test language Lorem Ipsum: enabling and removing it

Exponential ships a language that exists only for testing: **Lorem Ipsum**, code `qlo-ZZ`. Use it to try out
content translations (a second or third language, the editor's language choice, fallbacks, language filters, URL
aliases per language, a removal) on a real installation without touching a language your content uses.

It is shipped as a locale file only, `share/locale/qlo-ZZ.ini`. **No installation adds it**: not the clean data,
not the installer's seeds, not a package, not the kickstart. It takes no content language slot until you add it
(see [the limit of 62 content languages](../languages.md#11-the-limit-of-62-content-languages)), and you should
remove it again when your test is done.

## Contents

1. [What it is](#1-what-it-is)
2. [Enabling it](#2-enabling-it)
3. [Filling content with lorem ipsum](#3-filling-content-with-lorem-ipsum)
4. [Removing it again](#4-removing-it-again)
5. [Where it is not offered](#5-where-it-is-not-offered)

## 1. What it is

| | |
|---|---|
| Code | `qlo-ZZ`. `qlo` lies in the ISO 639-2 range `qaa`-`qtz`, reserved for local use, so no real language will ever get this code; `ZZ` is the code of an unknown region. |
| Name | *Lorem Ipsum*, as the language list and the editor show it. |
| Formats | English: `1,234.50`, 24-hour time, the week starts on Monday, ISO short dates (`2023-11-14`), currency `¤` (ISO 4217 `XXX`, no currency). |
| Month and day names | Lorem-style, so a date in this language is recognized at a glance: *Ipsumdies 14 Temporember 2023*. |
| Marker | `[LanguageSettings] TestLanguage=true` in the locale file. The interface language choice and the `exp:loremipsum` command read it. |

The marker is what makes a language a test language; you can mark a locale file of your own the same way. Keep such
files in the reserved range `qaa`-`qtz`.

## 2. Enabling it

1. **Setup > Languages > Add a language.** Type `lorem` or `qlo` in the search field, choose *Lorem Ipsum* and
   click *Add language*. It is listed among the other languages by its name.
2. **Show it on a siteaccess (optional).** Only if you want to see the translations on a site: add
   `SiteLanguageList[]=qlo-ZZ` to that siteaccess's `site.ini.append.php`, behind the languages it lists now, and
   clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`). Use a test siteaccess, not a live one.

The editor now offers *Lorem Ipsum* when you edit an object; translate by hand or fill it as below.

## 3. Filling content with lorem ipsum

`exp:loremipsum` (`bin/php/exploremipsumfill.php`) adds or fills the Lorem Ipsum translation of objects: text lines,
text blocks, rich text and keywords get lorem ipsum text; every other attribute (numbers, images, relations, dates)
keeps the value of the language it is copied from, and attributes that are not translatable are left alone. Each
object gets one new published version. The same object and attribute always get the same text.

```bash
php bin/php/exploremipsumfill.php 123 --dry-run              # what would be written, nothing changes
php bin/php/exploremipsumfill.php 123,124                    # two objects
php bin/php/exploremipsumfill.php --subtree=2 --limit=20     # a node and the objects below it, at most 20
./console exp:loremipsum 123 --from=ger-DE                   # copy the other attributes from German
```

It runs only when you start it, and it refuses:

- a language that is not a test language (`--language=ger-DE` is refused: it never writes lorem ipsum into a real
  language);
- a test language that is not a content language yet: it never adds one, add it as in section 2 first.

Add `--allow-root-user` when you run it as root. Exit code 0 done (or a dry run), 1 refused or failed, 2 usage error.

## 4. Removing it again

1. **Remove the translations.** A language with content cannot be removed. For each object that has a Lorem Ipsum
   translation: open it, *Translations*, select *Lorem Ipsum* and remove it; or remove the test objects themselves.
   Setup > Languages, the Lorem Ipsum card, says how many objects and classes still use it.
2. **Remove `qlo-ZZ` from every `SiteLanguageList[]`** you added it to, and clear the INI cache.
3. **Setup > Languages**: select *Lorem Ipsum* and click *Remove selected*. Its slot is free again.
4. Clear the content cache (`php bin/php/ezcache.php --clear-tag=content`) and, on Velocity,
   `./console exp:velocity cache clear`.

Compare the list of content languages before and after: it should be the same as before you started. See
[removing translations and languages safely](content-languages.md#9-removing-translations-and-languages-safely).

## 5. Where it is not offered

- **The interface language choice** (the UI switch, `uiswitch.ini [UISwitch_language]`) leaves a test language out of
  its ranked groups and out of the A-Z list of `AllLanguages=enabled`, even when a group names it. An installation
  that wants it there names it in `AllowedValues[]`: it is then listed A-Z, never ranked. Its interface stays English
  (there is no translation), with its own date formats.
- **Installation**: none of the shipped data, seeds, packages or kickstart settings adds it.

Reference: [Languages in Exponential](../languages.md#12-test-languages-lorem-ipsum),
[Content languages](content-languages.md).
