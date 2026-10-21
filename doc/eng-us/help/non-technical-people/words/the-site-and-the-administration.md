---
title: The site and the administration
audience: non-technical, editor, user
category: getting-started
order: 13
summary: One Exponential installation with several doors - the public site, the administration, often a simpler Editor, sometimes a second language or a second site - what each door is for, why signing in to one does not sign you in to another, and how to find your addresses.
keywords: site, administration, admin, editor, siteaccess, address, URL, sign in, front end, back end, multisite, language site, explained
type: help
---
# The site and the administration

## Contents

1. [One building, several doors](#one-building-several-doors)
2. [The doors you are likely to meet](#the-doors-you-are-likely-to-meet)
3. [Signing in is per door](#signing-in-is-per-door)
4. [Write down your addresses](#write-down-your-addresses)
5. [Common surprises](#common-surprises)

## One building, several doors

Picture a shop with a front door for customers and a back door for staff. It is one building with one stock room:
what staff put on the shelves through the back door is what customers see through the front door. An Exponential
installation is the same. The **site** is the front door. The **administration** is the staff entrance. Both show the
same content, stored once. Technically each door is called a **siteaccess**; you only need the word when talking to
your developer.

## The doors you are likely to meet

| Door | For | Typical address |
|---|---|---|
| **The site** | Visitors | `https://www.example.com` |
| **The administration** | Administrators and editors: every tool your role allows | `https://www.example.com/admin` ([Logging into the admin](../../logging-into-the-admin.md)) |
| **The Editor** | Editors only: a simpler administration with just the dashboard, the content and the media library | `https://www.example.com/editor` or `https://edit.example.com` ([Logging into the Editor](../../logging-into-the-editor.md)) |
| **A language version** | Visitors in another language | `https://www.example.com/de` or a host name of its own ([Languages](../../languages.md)) |
| **A second site** | A second brand or project on the same installation | its own host name ([Adding a site](../../customers/growth/adding-a-site.md)) |

Which of these your site has, and the exact addresses, depend on how it was set up. Your administrator knows.

## Signing in is per door

A sign-in belongs to one browser and to one door. Being signed in to the Editor does not sign you in to the
administration or to the public site, and the other way round
([Staying signed in and signing out](../../logging-into-the-editor.md#staying-signed-in-and-signing-out)). That is
on purpose: it keeps the staff entrance separate.

On the public site, some sites let visitors register and sign in as **members**, for example for a members-only
area. That is a different kind of account with a different role ([Roles and permissions explained](roles-and-permissions-explained.md)).

## Write down your addresses

Fill this in once and keep it with your passwords (never the passwords themselves on paper):

| | Address |
|---|---|
| Public site | |
| Administration | |
| Editor (if any) | |
| Other languages or sites | |
| Where to report a problem | |

[First day checklist](../checklists/first-day-checklist.md) includes this step.

## Common surprises

| Surprise | Explanation |
|---|---|
| A small browser window asks for a user name before the site appears | Some test servers have an extra browser password. It is not your Exponential account ([When signing in does not work](../../logging-into-the-editor.md#when-signing-in-does-not-work)). |
| I see fewer tabs than my colleague | Your role allows fewer tools, or you use the Editor door ([Roles and permissions explained](roles-and-permissions-explained.md)). |
| My change is in the administration but not on the site | Not published yet, or a cache ([Drafts, versions and publishing explained](drafts-versions-and-publishing-explained.md), [Caches explained](caches-explained.md)). |
| A yellow **Demo mode** banner | A public trial site; never enter anything private ([Demo mode](../../demo-mode.md)). |

## Related pages

- [Words explained](README.md)
- [Signing in for the first time](../first-steps/signing-in-for-the-first-time.md)
- [A tour of the administration](../../admin-tour.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
