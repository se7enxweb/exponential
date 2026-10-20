---
title: Translation workflow
audience: businesses, editor, project-manager
category: business-solutions
order: 30
summary: How a business organizes translation in Exponential - translators limited to their language, translating in the editor, approval per language, keeping translations current, moving content between languages, and what deletes content.
keywords: translation workflow, translators, content/translate, language limitation, approval, Approve event, affected languages, main language, always available, remove translation, business
type: help
---
# Translation workflow

This page is for content managers, language owners and managers who organize translation work. It describes how
translation works in Exponential, how to give translators exactly the rights they need, how to have translations
approved before they go live, and which actions cannot be undone. The step-by-step reference for editors and
administrators is [Content languages](../../../guides/content-languages.md).

## Contents

1. [Roles in a translation team](#roles-in-a-translation-team)
2. [How a translation is made](#how-a-translation-is-made)
3. [Limiting translators to their language](#limiting-translators-to-their-language)
4. [Approval per language](#approval-per-language)
5. [Keeping translations up to date](#keeping-translations-up-to-date)
6. [Actions that delete content](#actions-that-delete-content)
7. [A weekly routine](#a-weekly-routine)

## Roles in a translation team

| Role | Does | Rights to plan |
|---|---|---|
| Author | Writes the source text | Edit and create in the source language |
| Translator | Translates into one language | Translate or edit, and create, limited to their language |
| Language owner | Approves translations, owns terminology | Approver in the approval workflow for that language |
| Content manager | Decides what is translated and when | Reads every language; manages the list of pending work |

Agencies and freelancers fit the translator role: they get an account limited to their language, nothing more.

## How a translation is made

From [7. Translating content in the editor](../../../guides/content-languages.md#7-translating-content-in-the-editor):

1. The translator opens the object and presses **Edit**. When it can be edited in more than one language, a page
   asks which translation to make.
2. Under **New translation** they choose the language; under **Translate based on** the existing translation to start
   from (its fields are copied in), or none to start empty.
3. They translate the fields and **Publish** (or send it for approval, see below).

Each object's **Translations** window lists its languages with the main one marked. The main language is the one an
object was created in, or one chosen later with **Set main**; the main translation cannot be removed while it is the
main one ([6. Main language and always available](../../../guides/content-languages.md#6-main-language-and-always-available)).

> **Tip**
> Translate the start page and the menu folders first. Visitors reach everything else through them.

## Limiting translators to their language

The policies to edit (`content/edit`), translate (`content/translate`) and create (`content/create`) content can each
be limited by **Language** in the role editor, so a translator may be allowed German and nothing else. Give them read
access to the content as well
([Translators](../../creators/website-creators/multilingual-sites.md#translators),
[Roles and policies](../../../guides/roles-and-policies.md)).

Combine the language limitation with a subtree limitation when a translator works for one brand only
([Keeping teams in their brand](../multisite/shared-content-across-brands.md#keeping-teams-in-their-brand)).

## Approval per language

Exponential's approval workflow can hold content until an approver accepts it. Its **Approve** event has a field
**Affected languages**: "only versions whose initial language is one of these"
([3.2 Set up the Approve event](../../../guides/workflows.md#32-set-up-the-approve-event)). That makes per-language
approval possible:

| Setup | Effect |
|---|---|
| Approve event with affected language German, approver group "German language owners" | German translations wait for a German reviewer |
| Approve event with all languages, approver group "Chief editors" | Every change waits for a chief editor |
| Excluded user group "Language owners" | Language owners publish their own corrections without waiting |

Approvers decide in their collaboration inbox; the workflow cronjob then publishes the approved version. If that
cronjob does not run, "approved content is never published"
([Workflows](../../../guides/workflows.md)). One workflow per trigger: to run several approval rules, the guide
describes a multiplexer event.

## Keeping translations up to date

When the source text changes, its translations do not change by themselves. Practical ways to keep track:

- **Setup > Languages** shows, per content language, how many objects and classes have a translation in it, which
  siteaccesses show it, and hints when few objects use it or no siteaccess lists it
  ([Content languages, In short](../../../guides/content-languages.md#in-short)).
- A rule in your editorial guide: whoever changes a source text tells the language owners, or uses the approval
  inbox to route the change.
- With Exponential Intelligence switched on, the **translation sync** finds what changed in the source and prepares
  updated translation drafts for review
  ([Assisted translation with Exponential Intelligence](assisted-translation-with-exponential-intelligence.md)).

## Actions that delete content

| Action | Effect |
|---|---|
| Removing a **translation** of an object | Deletes that translation "for good": all its fields in that language, in every version |
| Removing a **language** | Only possible when no object and no class uses it, so it never deletes content |
| Changing the default content language of a site with content | Can hide objects that do not exist in the new default language |

Sources: [9. Removing translations and languages safely](../../../guides/content-languages.md#9-removing-translations-and-languages-safely)
and [10.14 Multi-language sites](../../../install/10-after-installing.md#1014-multi-language-sites).

Content created in the wrong language is moved by translating it into the right one, making that the main language,
and only then removing the wrong translation. "No shipped console command moves translations in bulk"
([8. Moving content from one language into another](../../../guides/content-languages.md#8-moving-content-from-one-language-into-another)).

> **Warning**
> Give the right to remove translations only to people who understand that it cannot be undone. Keep backups that
> you have restored at least once ([Reliability](../reliability/README.md)).

## A weekly routine

- [ ] Check Setup > Languages for languages with unexpectedly few objects.
- [ ] Go through the approval inbox for each language.
- [ ] List source pages changed this week and confirm their translations were updated.
- [ ] Review machine-prepared drafts, if you use them, before they are published.
- [ ] Spot-check one page per language on the public site, including the language switcher.

## Related pages

- [Multilingual planning](multilingual-planning.md)
- [Assisted translation with Exponential Intelligence](assisted-translation-with-exponential-intelligence.md)
- [Multilingual checklist](multilingual-checklist.md)
- [Content languages](../../../guides/content-languages.md)
- [Languages for editors](../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
