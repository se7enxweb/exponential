---
title: Test translations with Lorem Ipsum
audience: government, admin, website-creator
category: public-sector
order: 390
summary: Task for administrators and site builders: add the test language Lorem Ipsum (qlo-ZZ), fill translations with exp:loremipsum, check fallbacks and untranslated texts on a test siteaccess, and remove it again.
keywords: Lorem Ipsum, qlo-ZZ, test language, exp:loremipsum, exploremipsumfill.php, translation testing, fallback test
type: help
---
# Test translations with Lorem Ipsum

This task is for administrators and site builders, on a test installation. Background:
[The test language Lorem Ipsum](../../../../guides/test-language-lorem-ipsum.md) and
[Testing a multilingual site](../../languages/testing-a-multilingual-site.md).

## Steps

1. **Setup > Languages**, **Add language**, choose *Lorem Ipsum* (`qlo-ZZ`).
2. Give a test siteaccess `SiteLanguageList[]=qlo-ZZ` followed by your main language; clear the INI cache.
3. Fill translations, first as a dry run:

   ```bash
   php bin/php/exploremipsumfill.php --subtree=2 --limit=20 --dry-run
   php bin/php/exploremipsumfill.php --apply --subtree=2 --limit=20
   ```

   (`./console exp:loremipsum` is the same command.) It fills only test languages.
4. Browse the test siteaccess: everything translated is lorem ipsum; anything in the main language is a fallback or a
   fixed text of the design that is not yet translatable.

## Check

You have a list of fixed texts to translate in the design and of pages that fall back.

## Clean up

Remove the Lorem Ipsum translations (or the test objects), then the language in **Setup > Languages**, so it does not
keep one of the 62 content language places.

## Related pages

- [Languages in Exponential, section 12](../../../../languages.md#12-test-languages-lorem-ipsum)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
