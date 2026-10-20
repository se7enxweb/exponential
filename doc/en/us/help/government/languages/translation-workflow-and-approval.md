---
title: Translation workflow and approval
audience: government, admin, project-manager, editor
category: public-sector
order: 200
summary: How a public body organizes translation in Exponential: a backlog, translators per language, approval of translations with the Approve event limited to affected languages, publishing each language on its own, and what tooling (translation memory, machine translation) exists and does not.
keywords: translation workflow, approval, Approve event, Affected languages, translation backlog, review, machine translation, expai, translation memory, publication per language
type: help
---
# Translation workflow and approval

This page is for managers and administrators who organize the translation of a multilingual site. Exponential
versions each translation, publishes each language on its own, and can hold a translation for approval with a
workflow ([Workflows](../../../guides/workflows.md)).

## Contents

1. [A simple process](#a-simple-process)
2. [Finding what needs translation](#finding-what-needs-translation)
3. [Approval per language](#approval-per-language)
4. [Publishing each language on its own](#publishing-each-language-on-its-own)
5. [Tools that exist and tools that do not](#tools-that-exist-and-tools-that-do-not)

## A simple process

1. The author publishes in the source language.
2. A translator adds the translation (see "Translating an item step by step").
3. A reviewer for that language approves it.
4. It is published in that language; the other languages are not touched.

## Finding what needs translation

- The language's page in **Setup > Languages** counts objects with a translation in it and lists recent ones.
- An item's language badges show dashed badges for missing translations ([Languages (for editors)](../../languages.md)).
- A list kept as content, or an object state group "Translation" (Needed, In progress, Done) that translators set,
  gives a backlog ([Object states](../../../guides/object-states.md)).
- Notifications: translators can follow a section and get an e-mail when content in it changes
  ([Notifications](../../../features/6.0/notifications.md)).

## Approval per language

The *Approve* event has **Affected languages**: only versions whose initial language is one of these need approval.
A version created as a translation into French has French as its language, so a workflow with **Affected languages**
French and **Groups who approve content** "French reviewers" holds French translations only.

1. **Setup > Workflows**: a workflow per language, each with an *Approve* event for that language and its reviewers.
2. **Setup > Triggers**: one workflow per trigger, so combine them with a **Multiplexer** event in the workflow
   connected to **Before publishing content**.
3. The `frequent` cronjob group must run.

## Publishing each language on its own

Each translation is published on its own: publishing the German text does not change the English one. A correction
in the source language does not mark translations as outdated; your process (a state, a notice to translators) has
to do that.

## Tools that exist and tools that do not

| Tool | In Exponential |
|---|---|
| Translation memory, terminology database | not in the kernel; use your language service's tools and paste the result |
| Machine translation | not in the kernel; the optional `expai` assistant has a **Translate** action per field in the edit form, which writes a proposal into the form for review, with the provider you configure ([expai](../../../features/6.0/extensions/expai.md)) |
| Export for translation agencies | `xrowextract` can export one row per translation as CSV, JSON or XML, and import files back with a dry run ([xrowextract](../../../features/6.0/extensions/xrowextract.md)) |
| Side-by-side editing | **Translate based on** copies the source into the new translation's form |

Machine translation sends text to the provider you configure; review that under "Third-party services to review" in
the sovereignty section, and have every machine proposal reviewed by a person before publishing.

## Related pages

- [Workflows](../../../guides/workflows.md)
- [Translator roles and permissions](translator-roles-and-permissions.md)
- [Glossaries of terms](glossaries-of-terms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
