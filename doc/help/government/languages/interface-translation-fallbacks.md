---
title: Interface translation fallbacks
audience: government, admin, developer
category: public-sector
order: 180
summary: How Exponential decides which interface translation a locale shows when it has none of its own (configured fallbacks, rules, the automatic same-language fallback), how to see the chain with exp:i18n and Setup > Interface languages, and the settings a public body may change.
keywords: translation fallback, FallbackLanguages, i18n.ini, AutomaticLanguageFallback, FallbackMode, merge, exp:i18n, Setup Interface languages, regional locale, interface translation
type: help
---
# Interface translation fallbacks

This page is for administrators who run the administration or a public design in a regional locale, such as French
of Canada or Spanish of a Latin American country. A locale without its own translation does not fall back to English
at once: the translator tries the translations it falls back to, in order
([Languages in Exponential, section 13](../../../languages.md#13-translation-fallbacks)).

## Contents

1. [The chain](#the-chain)
2. [First or merge](#first-or-merge)
3. [Seeing the fallbacks](#seeing-the-fallbacks)
4. [Settings](#settings)
5. [Typical public sector cases](#typical-public-sector-cases)

## The chain

Each step is decided by the first rule that answers:

1. `i18n.ini [TranslationSettings] FallbackLanguages[]`: what is configured always wins.
2. The classes of `FallbackRules[]`, added by extensions.
3. With `AutomaticLanguageFallback=enabled` (the default), the most complete translation of the same language:
   `fre-CA` to `fre-FR`, `ger-AT` to `ger-DE`.

The answer is asked again, which makes a chain such as `esl-AR -> esl-MX -> esl-ES`. It ends at a locale nothing
answers for, at the English source texts, at a cycle (reported) or after `FallbackMaxDepth` steps.

## First or merge

`FallbackMode=first` (the default) loads the first file of the chain each translation root has. `FallbackMode=merge`
loads every file of the chain, the most general first, so a message the specific translation lacks comes from the
next one. Merge shows fewer English leftovers in regional locales.

## Seeing the fallbacks

```bash
./console exp:i18n fallbacks                          # every locale: coverage, extensions, chain
./console exp:i18n fallbacks --locale=fre-CA,ger-AT   # each step with its rule
./console exp:i18n check                              # exit 1 on a problem, for CI
```

**Setup > Interface languages** (`/setup/languages`, policy `setup/system_info`) shows the same in the
administration, read only.

## Settings

| `i18n.ini [TranslationSettings]` | Default | Meaning |
|---|---|---|
| `FallbackLanguages[<locale>]` | 100 entries | the translation a locale falls back to |
| `FallbackRules[]` | empty | classes asked after the configured fallbacks |
| `AutomaticLanguageFallback` | `enabled` | the same-language fallback |
| `AutomaticFallbackExclude[]` | `eng` | languages left alone |
| `FallbackMaxDepth` | `4` | most steps of a chain |
| `FallbackMode` | `first` | `first` or `merge` |

Put changes in `settings/override/i18n.ini.append.php` and clear the caches
(`php bin/php/ezcache.php --clear-tag=i18n` after changing translation files, `--clear-tag=ini` after settings).

## Typical public sector cases

| Case | Suggestion |
|---|---|
| A regional variety with its own spelling rules | add its own `.ts` translation for the strings that differ, `FallbackMode=merge` |
| A minority language without translation | the interface stays in the configured fallback or English; translate the public design's few strings |
| Staff in two official languages | each chooses; no fallback needed |

## Related pages

- [Administration interface languages](administration-interface-languages.md)
- [Interface language list](../../../features/6.0/ui-switch-languages.md)
- [Languages in Exponential](../../../languages.md#13-translation-fallbacks)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
