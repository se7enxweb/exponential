---
title: Check interface translation coverage
audience: government, admin
category: public-sector
order: 130
summary: Task for administrators: find out how complete the administration's translation into a language is, which translation a regional locale really shows, and what is untranslated, with ezchecktranslation, exp:i18n and Setup > Interface languages.
keywords: translation coverage, ezchecktranslation, exp:i18n, Setup Interface languages, fallbacks, untranslated strings, interface translation
type: help
---
# Check interface translation coverage

This task is for administrators deciding whether staff can work in their language. Background:
[Administration interface languages](../../languages/administration-interface-languages.md) and
[Interface translation fallbacks](../../languages/interface-translation-fallbacks.md).

## Steps

1. In the administration, **Setup > Interface languages** (`/setup/languages`, policy `setup/system_info`): every
   locale with the translation it shows, the coverage of the kernel and the extensions, and the fallback chain.
2. On the command line, statistics of one translation file:

   ```bash
   php bin/php/ezchecktranslation.php fre-FR
   ```

3. The fallback chain of a regional locale:

   ```bash
   ./console exp:i18n fallbacks --locale=fre-CA
   ./console exp:i18n check
   ```

4. Compare with the published table in [Languages in Exponential, section 4](../../../../languages.md#4-every-administration-translation).

## Check

You know the percentage of current strings translated, whether the locale uses its own file or a fallback, and which
extensions show English.

## Next steps

- Low coverage: plan a translation or review with your language service, and contribute it back.
- Machine-assisted translations: have a native speaker review them before staff rely on them.

## Related pages

- [Languages in Exponential](../../../../languages.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
