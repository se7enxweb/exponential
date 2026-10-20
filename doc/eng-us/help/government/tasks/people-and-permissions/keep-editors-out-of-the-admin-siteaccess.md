---
title: Keep editors out of the admin siteaccess
audience: government, admin
category: public-sector
order: 250
summary: Step by step - limit the user/login policy of editor roles to the editor or public siteaccess with the SiteAccess limitation, so only the web team and administrators sign in to the administration.
keywords: siteaccess, user/login, SiteAccess limitation, administration, editor siteaccess, admin access, sign-in, separation, least privilege
type: help
---
# Keep editors out of the admin siteaccess

This task is for administrators. At the end, department editors sign in on the editor (or public) siteaccess only, and
the administration siteaccess refuses them. The policy `user/login` takes a `SiteAccess` limitation
(`kernel/user/module.php`); the role page writes it as, for example, *May log in, on the siteaccesses site and bold*
([Roles guide, section 2](../../../../guides/roles-and-policies.md#2-read-one-role)).

## Contents
1. [Before you start](#before-you-start)
2. [Find where editors get user/login](#find-where-editors-get-userlogin)
3. [Limit it](#limit-it)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- Your installation has a separate editing siteaccess, or editors work on the public siteaccess; see
  [Separating admin and public siteaccesses](../../operations/hosting/separating-admin-and-public-siteaccesses.md).
  The names are listed on **Setup > System information**, part *Siteaccesses*.
- An account with the `role` module.

## Find where editors get user/login

1. **Users > Roles and policies**; open each role the editors hold.
2. Look for the sentence *May log in* (module `user`, function `login`) and its siteaccesses. A policy *May do
   everything* in the user module (`user/*`) also includes sign-in everywhere.

## Limit it

1. In the role, **Edit**, then **Limitations** next to the `user/login` policy.
2. In **SiteAccess**, select only the editor and public siteaccesses (Ctrl or Cmd for more than one). **OK**.
3. If the role has `user/*`, replace it by the single functions the editors need (`login` limited, `password`,
   `preferences`).
4. **Save**.

The web team's and approvers' roles keep a `user/login` that includes the administration siteaccess.

## Check it worked

- Sign in as an editor on the administration address: the sign-in is refused.
- Sign in on the editor siteaccess: it works.
- The role page shows *May log in, on the siteaccesses ...* without the administration.

## Pitfalls

- A user holds every policy of every role: if any role (including one of a parent group) allows `user/login` on the
  administration, the editor still gets in. Check them all.
- Network restrictions on the administration's host name are a second, stronger layer
  ([Restricting the administration](../../../hosting/security/restricting-the-administration.md)).

## Related pages
- [Least-privilege principles](../../operations/people-and-permissions/least-privilege-principles.md)
- [Check what a role allows](check-what-a-role-allows.md)
- [Setup wizard and editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Security hardening chapter 13.5](../../../../install/13-security-hardening.md#135-the-administration-siteaccess)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
