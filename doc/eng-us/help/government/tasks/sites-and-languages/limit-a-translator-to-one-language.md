---
title: Limit a translator to one language
audience: government, admin
category: public-sector
order: 80
summary: Task for administrators: create a role whose content policies are limited by Language, assign it to a translators' group, and test it with a test account.
keywords: translator role, Language limitation, content/translate, content/edit, roles and policies, user group, permissions
type: help
---
# Limit a translator to one language

This task is for administrators. Background: [Translator roles and permissions](../../languages/translator-roles-and-permissions.md).

## Before you start

The content language exists; you know which sections or subtrees the translator works in.

## Steps

1. **Users > Roles and policies** (`/role/list`): create a role "Translator French".
2. Add policies:
   - `content/read` (limited to the sections they translate);
   - `content/edit` with **Language** = French (and the section);
   - `content/translate` with **Language** = French;
   - `content/versionread` (the section);
   - `user/login` for the administration siteaccess.
3. Save the role. Create the user group "Translators French" and assign the role to it.
4. Put the translators' accounts in the group.

## Check

Sign in with a test account in the group (not a real person's account): the edit language list offers French only;
editing the English version is refused.

## Problems

| Problem | Fix |
|---|---|
| The translator cannot add a translation, only edit existing ones | `content/translate` (or `content/create`) is missing for the language |
| The translator sees nothing | `content/read` is limited to the wrong sections |

## Related pages

- [Roles and policies](../../../../guides/roles-and-policies.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
