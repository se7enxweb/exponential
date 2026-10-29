---
title: "FAQ: languages and right-to-left"
audience: government, editor, admin, project-manager
category: public-sector
order: 90
summary: Answers to the language questions public bodies ask about Exponential - multilingual content, official and minority languages, right-to-left scripts, the administration in other languages, translation workflow, missing translations and machine-assisted drafts.
keywords: languages FAQ, multilingual, translation, official languages, minority languages, right to left, RTL, Arabic, Hebrew, interface language, locales, translation workflow, public sector
type: help
---
# FAQ: languages and right-to-left

This page answers the language questions public bodies ask about Exponential. The answers come from
[Content languages](../../../guides/content-languages.md), [Translations and languages](../../../features/6.0/translations-and-languages.md),
[Right-to-left languages](../../../features/6.0/right-to-left.md), [Languages](../../languages.md) and
[Internationalization standards](../../../standards/internationalization.md).

## Contents

1. [Can we publish in several languages?](#can-we-publish-in-several-languages)
2. [Which languages are possible?](#which-languages-are-possible)
3. [Are right-to-left languages supported?](#are-right-to-left-languages-supported)
4. [Can staff use the administration in their language?](#can-staff-use-the-administration-in-their-language)
5. [What happens when a translation is missing?](#what-happens-when-a-translation-is-missing)
6. [Can translations be approved before publishing?](#can-translations-be-approved-before-publishing)
7. [Is there machine translation?](#is-there-machine-translation)

## Can we publish in several languages?

Yes. Each content object can have a translation per content language, and each language can have its own siteaccess,
address and design settings ([Content languages](../../../guides/content-languages.md),
[Multilingual sites](../../creators/website-creators/multilingual-sites.md)).

## Which languages are possible?

Any language with a locale. The project counts 368 locale files, 43 of them right to left
([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#languages-and-accessibility)). More than
30 languages on one site need a 64-bit PHP, because the languages of an object are kept as bits of one integer
([2. Requirements](../../../install/02-requirements.md#versions)).

## Are right-to-left languages supported?

Yes, in content and in the administration: right-to-left languages set `dir="rtl"` on the page, names are isolated so
mixed text keeps its order ([Right-to-left languages](../../../features/6.0/right-to-left.md)). Your public design must
also handle mirrored layout ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)).

## Can staff use the administration in their language?

Yes, where a translation exists. The Foundation page counts administration translations (the current totals are at the top of [Languages in Exponential](../../../languages.md))
([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#languages-and-accessibility)); other
pages give slightly different counts, so check the current state in the language list, which shows how complete each
translation is ([Languages](../../languages.md)). Staff choose their interface language
([UI switch settings](../../../guides/ui-switch-settings.md)).

## What happens when a translation is missing?

Your settings decide whether visitors see the object in another language or not at all
([Content languages](../../../guides/content-languages.md)). Decide this per site and tell editors.

## Can translations be approved before publishing?

Yes, with an approval workflow on publishing, which applies to translations as to any version
([Workflows](../../../guides/workflows.md)).

## Is there machine translation?

Not in the kernel. Exponential Intelligence, an optional extension, can write drafts that an editor reviews; it never
publishes on its own ([Exponential Intelligence](../../../features/6.0/extensions/expai.md)). A translator should review
any machine-assisted text, especially legal and official content.

## Related pages

- [Government FAQ](README.md)
- [Multilingual checklist](../checklists/multilingual-checklist.md)
- [Internationalization standards](../../../standards/internationalization.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
