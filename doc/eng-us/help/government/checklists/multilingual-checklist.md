---
title: Multilingual checklist
audience: government, admin, editor, project-manager
category: public-sector
order: 70
summary: A checklist for public bodies that publish in several languages with Exponential - content languages, addresses, the language switcher, right-to-left, the administration's interface language, translation workflow and quality.
keywords: multilingual, languages, translation, official languages, language switcher, right to left, RTL, interface language, hreflang, translation workflow, public sector
type: help
---
# Multilingual checklist

This checklist is for site administrators and editorial leads of public bodies that must publish in more than one
language, for example in official or minority languages. It follows [Content languages](../../../guides/content-languages.md),
[Translations and languages](../../../features/6.0/translations-and-languages.md) and
[Multilingual sites](../../creators/website-creators/multilingual-sites.md).

> **Note** This page describes features and common practice; it is not legal advice. Language obligations depend on
> your jurisdiction; check them with your legal counsel.

## Contents

1. [Plan](#plan)
2. [Set up](#set-up)
3. [Right-to-left languages](#right-to-left-languages)
4. [Editing and translating](#editing-and-translating)
5. [Check](#check)

## Plan

- [ ] List the languages you must publish in, and which content must exist in each.
- [ ] Decide what visitors see when a translation is missing: another language, or nothing.
- [ ] Decide the address pattern per language (path prefix, host) ([Language switcher and language URLs](../../creators/website-creators/language-switcher-and-language-urls.md), [Siteaccess host and language matching](../../../features/6.0/siteaccess-host-and-language-matching.md)).
- [ ] Decide who translates: staff, a service, or both, and how translations are approved.

## Set up

- [ ] Content languages added in **Setup > Languages** ([Content languages](../../../guides/content-languages.md)).
- [ ] One siteaccess per language where needed, with the right locale.
- [ ] A language switcher in the design ([Language switcher and language URLs](../../creators/website-creators/language-switcher-and-language-urls.md)).
- [ ] Interface languages for staff: **Setup > Interface languages** ([UI switch settings](../../../guides/ui-switch-settings.md)); check which administration translations are complete ([Languages](../../languages.md)).
- [ ] The test language Lorem Ipsum is not left enabled on production ([The test language Lorem Ipsum](../../../guides/test-language-lorem-ipsum.md)).

## Right-to-left languages

- [ ] Pages in right-to-left languages set `dir="rtl"` ([Right-to-left languages](../../../features/6.0/right-to-left.md)).
- [ ] The public design mirrors its layout correctly ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)).
- [ ] Mixed text (names, numbers, addresses) displays in the right order.

## Editing and translating

- [ ] Editors know how to add a translation and see which exist (the Translations tab of an object) ([Editors 201, Module 1](../../trainers/editors-201/module-01-translations-and-languages/README.md)).
- [ ] Translations go through approval where your rules require it ([Workflows](../../../guides/workflows.md)).
- [ ] Images with text have alternative text in each language.
- [ ] Documents attached in each language, or a note where only one language exists.
- [ ] Machine-assisted drafts, if used, are reviewed by a person; Exponential Intelligence writes drafts only and never publishes ([the Exponential glossary](../../../glossary.md#e)).

## Check

- [ ] Each language's home page, navigation and search work.
- [ ] `<html lang>` matches the language of each page.
- [ ] URL aliases exist in each language ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).
- [ ] Missing translations listed and assigned; reviewed every quarter.
- [ ] Accessibility checked in every language, including right-to-left ([Accessibility checklist](accessibility-checklist.md)).

## Related pages

- [Government checklists](README.md)
- [Internationalization standards](../../../standards/internationalization.md)
- The languages section of this government help (written by another author)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
