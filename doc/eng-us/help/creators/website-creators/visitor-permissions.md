---
title: "What visitors may see: the Anonymous role"
audience: website-creator, admin
category: website-creation
order: 590
summary: How permissions apply to visitors who are not logged in - the Anonymous user and role, what it grants by default, AnonymousAccessList, RequireUserLogin, testing as a visitor, and common mistakes that expose or hide content.
keywords: Anonymous, anonymous role, visitor permissions, content/read, Section Standard, AnonymousAccessList, RequireUserLogin, public site, permissions, roles, website creator
type: help
---
# What visitors may see: the Anonymous role

This page is for website creators who decide what the public can see and do. A visitor who is not logged in is the
**Anonymous** user, and everything they may do comes from the roles assigned to it.

## Contents

1. [The Anonymous user and role](#the-anonymous-user-and-role)
2. [What it grants by default](#what-it-grants-by-default)
3. [Siteaccess settings](#siteaccess-settings)
4. [Giving visitors more](#giving-visitors-more)
5. [Testing as a visitor](#testing-as-a-visitor)
6. [Common mistakes](#common-mistakes)

## The Anonymous user and role

Every request without a login runs as the Anonymous user, a member of the Anonymous user group. Its permissions come
from the role assigned to that group, usually called Anonymous ([Roles and policies](../../../guides/roles-and-policies.md)).

![Setup, Roles: the roles of the installation, Anonymous among them, with their policies and assignments](../../images/creators/roles-list.webp)

## What it grants by default

The default Anonymous role has `content/read` and `content/pdf` limited to Section Standard. That one limitation keeps
user accounts, the media library's own pages, setup objects and every other section away from visitors
([Sections](../../../guides/sections.md)). It also has `user/login` for the public siteaccesses.

## Siteaccess settings

`site.ini [SiteAccessSettings]`:

| Key | Meaning |
|---|---|
| `RequireUserLogin` | `true` in `site.ini`; a public siteaccess sets `false`. `true` sends every visitor to the login page |
| `AnonymousAccessList[]` | module views reachable without login even with `RequireUserLogin=true` (`user/register`, `user/success`, `user/activate`, `user/forgotpassword`, `csp/report` ...) |

## Giving visitors more

| Visitors should | Add to the Anonymous role |
|---|---|
| Read a new public section | `content/read` for that section |
| Post comments | `content/create` for the comment class, limited to the places that take comments |
| Search | nothing: search shows what they may read |
| Buy | `shop/buy` |
| See tag pages | `tags/read`, `tags/view` |
| Subscribe to a newsletter | the newsletter module's subscribe policies |

Keep the role small. Each addition is something every visitor on the internet may do.

## Testing as a visitor

- Use a private browser window, not your admin session.
- Check a page in each section, a hidden page (must be gone), a members page (must ask to log in) and a draft (must be
  invisible).
- The **access check** console command shows what a user may do: see `doc/eng-us/examples/commands/accesscheck.md` when the
  examples are installed, or the role pages in the administration.

## Common mistakes

| Mistake | Effect |
|---|---|
| `content/read` without a section limitation | visitors can open user accounts and setup objects |
| A new section forgotten in the Anonymous role | its pages give "access denied" to everyone |
| Media in a section visitors may not read | images in articles still show (served as files), but media pages do not |
| `RequireUserLogin=true` copied to the public siteaccess | everyone is sent to the login page |

## Related pages

- [Member registration and members-only areas](member-registration-and-areas.md)
- [Sections and object states for site builders](sections-and-object-states.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
