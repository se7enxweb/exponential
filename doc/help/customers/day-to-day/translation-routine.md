---
title: Translation routine
audience: customer, editor, admin
category: customer-success
order: 25
summary: How a site owner keeps a multilingual Exponential site in step week by week - who translates and approves, translators limited to their language, per-language approval, finding translations that are missing or out of date, actions that delete content, and optional help from Exponential Intelligence.
keywords: translation, multilingual, routine, translators, language owner, approval per language, out of date translations, content languages, translation sync
type: help
---
# Translation routine

This page is for the owner and translation coordinator of a site in more than one language. The editor's clicks are on
[Languages](../../languages.md); the planning view is [Translation workflow](../../businesses/multilingual/translation-workflow.md).
Here you find the weekly routine and the checks that keep languages from drifting apart.

## Contents

1. [Who does what](#who-does-what)
2. [How a translation is made](#how-a-translation-is-made)
3. [Translations do not follow by themselves](#translations-do-not-follow-by-themselves)
4. [A weekly routine](#a-weekly-routine)
5. [Actions that delete content](#actions-that-delete-content)
6. [Help from the writing assistant](#help-from-the-writing-assistant)

## Who does what

From [Translation workflow](../../businesses/multilingual/translation-workflow.md):

| Role | Does |
|---|---|
| Author | writes the source text |
| Translator | translates into one language; their policies can be limited by **Language** |
| Language owner | approves translations, owns terminology |
| Content manager | decides what is translated and when |

Agencies and freelancers fit the translator role: an account limited to their language, nothing more. Per-language
approval uses the *Affected languages* field of the approval workflow
([Approvals and review](approvals-and-review.md)).

## How a translation is made

The translator opens the item, presses **Edit**, chooses the new language and the translation to start from, translates
and publishes or sends it for approval ([Content languages](../../../guides/content-languages.md#7-translating-content-in-the-editor)).
A translation is part of the same item, not a second item: it shares the item's location and version history.

> **Tip**
> Translate the start page and the menu folders first. Visitors reach everything else through them
> ([Translation workflow](../../businesses/multilingual/translation-workflow.md)).

## Translations do not follow by themselves

When the source text changes, its translations do not change by themselves
([Translation workflow](../../businesses/multilingual/translation-workflow.md)). Each siteaccess shows an item in the
first language of its language list that the item has; an item in none of them is hidden unless it is *always
available* ([Content languages](../../../guides/content-languages.md)). So a missing translation may mean visitors of
that language see another language, or nothing.

Ways to keep track:

- **Setup > Languages** shows per language how many items and classes have a translation in it and which
  siteaccesses show it.
- The versions page lets you compare the source's current version with the one the translation was made from
  ([Versions of an object](../../../guides/content-history.md)).
- A shared list (your [Content calendar](content-calendar.md)) with a "languages" column for every new item.

## A weekly routine

| Day | Task | Who |
|---|---|---|
| Monday | list source items published or changed last week that need translating | content manager |
| Monday | assign each to a translator, with a date | content manager |
| During the week | translate; send for approval | translators |
| Thursday | approve or deny with comments | language owners |
| Friday | check *My pending items* of language owners: nothing waits over the weekend that should go out | content manager |
| Monthly | look at **Setup > Languages** for a language falling behind | owner |

## Actions that delete content

> **Warning**
> Removing an item's **translation** deletes all its fields in that language, in every version, for good
> ([Content languages](../../../guides/content-languages.md)). To change the main language of an item, translate
> first, make the new language the main one, then remove the old translation. Removing a whole **language** is only
> possible when nothing has a translation in it, so that never deletes content.

Keep the right to remove translations with the content manager.

## Help from the writing assistant

Exponential Intelligence can prepare translations as **drafts** that a person reviews, and its translation sync can
bring existing translations up to date after the source changed, patching only the changed passages
([Assisted translation with Exponential Intelligence](../../businesses/multilingual/assisted-translation-with-exponential-intelligence.md)).
It never publishes on its own. See [Using AI drafts responsibly](../growth/using-ai-drafts-responsibly.md) before you
switch it on for translators.

## Related pages

- [Languages](../../languages.md)
- [Adding a language](../growth/adding-a-language.md)
- [Multilingual checklist](../../businesses/multilingual/multilingual-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
