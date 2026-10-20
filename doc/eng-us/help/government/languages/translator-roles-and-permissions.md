---
title: Translator roles and permissions
audience: government, admin
category: public-sector
order: 190
summary: How to give translators in a public body exactly the rights they need in Exponential: a role with content/read, content/edit and content/translate limited by Language (and section), why content/create matters, and how to check what a translator can do.
keywords: translator role, content/translate, content/edit, Language limitation, roles and policies, permissions, translators, language services, external translators
type: help
---
# Translator roles and permissions

This page is for administrators who set up accounts for translators, staff of a language service or contracted
translators. Exponential's policies can be limited by **Language**, so a translator may edit one language and
nothing else ([Building a multilingual site, Translators](../../creators/website-creators/multilingual-sites.md#translators),
[Roles and policies](../../../guides/roles-and-policies.md)).

## Contents

1. [The role](#the-role)
2. [Creating it](#creating-it)
3. [External translators](#external-translators)
4. [Checking](#checking)
5. [Pitfalls](#pitfalls)

## The role

| Module / function | Limitation | Why |
|---|---|---|
| `content/read` | the sections they translate | to see the source |
| `content/edit` | Language: their language; Section as needed | to edit their translation |
| `content/translate` | Language: their language | to add a new translation |
| `content/versionread` | Section as needed | to compare versions |
| `user/login` | the administration siteaccess | to sign in |

Editors need `content/edit` (or `content/translate`) and `content/create` for a language to add it to an object,
and all three can be limited by **Language** ([Content languages, section 7](../../../guides/content-languages.md#7-translating-content-in-the-editor)).
Give `content/create` only if translators must create new objects.

## Creating it

1. **Users > Roles and policies** (`/role/list`), create a role, for example "Translator French".
2. Add the policies of the table; for each, set the **Language** limitation to the language.
3. Create a user group "Translators French" and assign the role to it.
4. Put the translators' accounts in that group.

## External translators

- Personal accounts, never shared; an end date noted in your records. Exponential has no account expiry date:
  disable accounts when the contract ends, and use **Users > Check user accounts** to find accounts not used for a
  while ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- Two-factor sign-in where your policy asks for it ([Two-factor login](../../../guides/two-factor-login.md)).
- An approval step before translations go live, if the translator is not the final reviewer (see "Translation
  workflow and approval").

## Checking

Sign in as a test translator (not with a real person's account) and check: the edit language list offers only their
language; editing another language is refused. After adding a language the editor does not offer it if the
**Language** limitation is missing ([Content languages, section 12](../../../guides/content-languages.md#12-troubleshooting)).

## Pitfalls

- Translators with `content/remove` could remove whole objects; do not give it.
- Removing a translation deletes it in every version; keep that right with administrators (see "Removing a
  translation safely").
- Role assignments are recorded in the audit trail's `access` channel.

## Related pages

- [Roles and policies](../../../guides/roles-and-policies.md)
- [Translation workflow and approval](translation-workflow-and-approval.md)
- [Translating an item step by step](translating-an-item-step-by-step.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
