---
title: How do I let a translator work in one language only?
audience: admin
category: manual
order: 570
summary: Make a role with content/read, plus content/edit and content/translate limited by Language to the translator's language, and assign it to the translators' group; they can then translate and edit that language but not change the others.
keywords: translator, role, policy, language limitation, content/translate, content/edit, content/create, grant limited access, translation rights
type: help
---
# How do I let a translator work in one language only?

The content functions `create`, `edit`, `translate` and `publish` can be limited by **Language**. A translator whose
edit and translate rights carry the limitation *German* can open any item in German, make or change its German text,
and nothing else. Reading is never limited by language.

## Who can do this

Users whose role has `role/*`, usually administrators only.

## Steps

1. Open **Users** in the top menu and choose **Roles and policies**.
2. Click **New role** and name it, for example "Translators German".
3. Click **New policy**, choose module **content**, function **read**, and **Grant full access**.
4. Click **New policy**, choose module **content**, function **edit**, and **Grant limited access**. Under **Language**,
   choose the translator's language. Add **Section** or **Subtree** limits too if they should work in one part only.
5. Repeat step 4 for the function **translate**, and for **publish** if your site limits publishing as well.
6. Click **Save**.
7. Assign the role to the translators' user group with **Assign**.
8. Sign in as a test translator and check: the language can be edited, the others cannot.

## What can go wrong

- **The translator cannot start a new translation.** The language is not one of the site's content languages yet. Add
  it first: see [How do I add a content language?](how-do-i-add-a-content-language.md)
- **The translator can still change other languages.** Another role of theirs grants `content/edit` without the
  limitation. Rights add up across all roles of the user and their groups.
- **The translator cannot see what to translate.** They lack `content/read` in that part of the tree, or for the
  section it is in.
- **Changes do not show at once.** Rights are cached per user and apply at the next sign-in.

## Related pages

- [Translator roles and permissions](../government/languages/translator-roles-and-permissions.md)
- [How do I give a group edit rights on one section only?](how-do-i-give-a-group-edit-rights-on-one-section.md)
- [Roles and policies](../../guides/roles-and-policies.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
