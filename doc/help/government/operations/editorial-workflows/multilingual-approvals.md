---
title: Multilingual approvals
audience: government, admin, editor
category: public-sector
order: 80
summary: How to approve content in several official languages - one approval for all languages, or approvers per language with the Affected languages field and Language-limited roles - and how translators and approvers fit the version model.
keywords: multilingual, translations, official languages, bilingual, language approval, Affected languages, translators, Language limitation, content/translate, initial language, approval workflow
type: help
---
# Multilingual approvals

This page is for web managers of public websites published in more than one official language. It shows how the
approval workflow treats translations and how to give each language team its own approvers. Sources:
[Workflows, section 3.2](../../../../guides/workflows.md#32-set-up-the-approve-event),
[Content languages](../../../../guides/content-languages.md) and the `Language` limitation in
`kernel/content/module.php`.

## Contents
1. [How a translation reaches the approval](#how-a-translation-reaches-the-approval)
2. [Pattern 1: one approval for every language](#pattern-1-one-approval-for-every-language)
3. [Pattern 2: approvers per language](#pattern-2-approvers-per-language)
4. [Translator roles](#translator-roles)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## How a translation reaches the approval

Each save of an object is a version, and a version is made in one **initial language**: translating a page into
French creates a new version whose initial language is French. Publishing it runs the same trigger, **Before
publishing content**, as any other version. The Approve event's field **Affected languages** decides which initial
languages need approval ("only versions whose initial language is one of these").

## Pattern 1: one approval for every language

**Affected languages** = *All languages*; one approvers' group that reads every language. Simple, and suitable when
the approvers are fluent in all official languages or the translation is done by a trusted service.

## Pattern 2: approvers per language

When each language has its own reviewers:

1. Create one approvers' group per language ("Approvers - English", "Approvers - French").
2. Create one workflow per language, each with an Approve event: **Affected languages** = that language, **Groups who
   approve content** = that language's group.
3. Because a trigger runs one workflow, create a third workflow with a **Multiplexer** event that runs the language
   workflows, and connect that one to **Before publishing content**.
4. Test with a version in each language: each waits for its own group only, because the other language's Approve
   event lets it through.

Exponential does not translate content or check that every language exists before publishing; a page can be public
in one language while its translation is still a draft. If your rules require simultaneous publication, keep the
first language unpublished until the translation is approved, by process.

## Translator roles

| Module | Function | Limitations | Why |
|---|---|---|---|
| content | read | the area | see the source |
| content | translate | Language: the target language | translate |
| content | edit | Language: the target language, Subtree | change only their language |
| content | versionread, diff | the area | compare with the source version |

`content/create`, `edit`, `publish`, `translate` and `hide` accept the `Language` limitation. Approvers per language
get the same Language limitation on any edit right they have.

## Pitfalls

- **Which languages a site shows** is a siteaccess setting (`SiteLanguageList`), separate from approvals
  ([Content languages, section 5](../../../../guides/content-languages.md#5-showing-a-language-sitelanguagelist-and-siteaccesses)).
- **Always available**: a page marked always available can be shown in its main language on a siteaccess for another
  language; that is a display rule, not an approval.
- **Removing a language** removes content; read [Content languages, section 9](../../../../guides/content-languages.md#9-removing-translations-and-languages-safely)
  first.
- **Right-to-left languages** are supported in the administration and designs that handle them; see
  [Right-to-left](../../../../features/6.0/right-to-left.md).

## Related pages
- [Setting up the Approve event](setting-up-the-approve-event.md)
- [Approval workflows overview](approval-workflows-overview.md)
- [Languages for public bodies (help)](../../../languages.md)
- [Content languages guide](../../../../guides/content-languages.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
