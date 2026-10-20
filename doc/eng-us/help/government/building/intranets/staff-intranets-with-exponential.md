---
title: Staff intranets with Exponential
audience: government, project-manager, admin
category: public-sector
order: 20
summary: What a public body needs for a staff intranet and how Exponential provides it: a siteaccess that requires sign-in, sections and roles for internal content, sign-in options (local accounts, LDAP handler, two-step sign-in), sessions, and the limits to plan for.
keywords: intranet, staff site, internal site, RequireUserLogin, sections, roles, LDAP, two-step sign-in, sessions, internal communication
type: help
---
# Staff intranets with Exponential

This page is for managers and administrators planning an intranet for staff. An intranet in Exponential is usually a
siteaccess whose visitors must sign in, showing content in sections only staff may read
([Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md)).

## Contents

1. [The building blocks](#the-building-blocks)
2. [Same installation as the public site, or separate](#same-installation-as-the-public-site-or-separate)
3. [Limits to plan for](#limits-to-plan-for)

## The building blocks

| Need | Exponential | Page |
|---|---|---|
| Only staff can open it | siteaccess with `RequireUserLogin=true` | "A staff-only siteaccess" |
| Internal content kept from the public site | sections the Anonymous role may not read | "Sections and roles for intranet content" |
| Staff sign in with their directory account | the kernel's LDAP login handler | "LDAP sign-in" |
| Stronger sign-in | two-step sign-in (`sevenx_authentication_2fa`) | "Two-step sign-in for staff" |
| Sessions that end | `site.ini [Session]` timeouts | "Sessions, sign-out and shared computers" |
| Who did what | the audit trail | [Audit trail](../../../../features/6.0/audit-trail.md) |

## Same installation as the public site, or separate

| Same installation | Separate installation |
|---|---|
| content can be shared with the public site in one step | internal data never in the public site's database |
| one set of updates | intranet can sit in the internal network only |
| a role mistake can expose internal content | more to operate |

Bodies with sensitive internal content often choose a separate installation inside the internal network; others use
one installation with strict sections. Decide with your security officer.

## Limits to plan for

- No built-in single sign-on with an identity provider beyond the login handlers the kernel and installed extensions
  offer (standard, LDAP, text file, and the 2FA extension's handlers); check your requirement against them.
- No built-in employee directory, chat or document co-editing; an intranet built with Exponential publishes content.
- The LDAP handler has no 6.x guide; test it with your directory before relying on it.

## Related pages

- [A staff-only siteaccess](a-staff-only-siteaccess.md)
- [Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
