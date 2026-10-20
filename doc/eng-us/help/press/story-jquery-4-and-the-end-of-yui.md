---
title: "Story: jQuery 4 and the end of YUI"
audience: press
category: press-media
order: 86
summary: A feature story about how Exponential's administration moved to jQuery 4 and replaced the discontinued YUI library with its own Exponential UI components in October 2026 - what changed, why it matters for security and maintenance, and the claims to avoid.
keywords: story, jQuery 4, YUI, JavaScript, Exponential UI, expui, modernization, technical debt, security, administration, ezjscore
type: help
---
# Story: jQuery 4 and the end of YUI

This page is for technical journalists who cover maintenance of long-lived software. Replacing an old JavaScript
library across a large administration is unglamorous work, and a good example of how a code base from the 2000s is
kept current.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

Exponential's administration used YUI, a JavaScript library from the 2000s, for tables, dialogs, uploads and date
fields. On 1 and 2 October 2026 the administration moved to jQuery 4, and Exponential UI (`exp::*` components)
replaced YUI in the sub-items table, upload dialogs, date fields, collapsing menus, the sticky edit toolbar, autosave
and preview. YUI was removed from the administration designs and from the bundled ezjscore; "The admin loads no YUI
request any more" ([jQuery 4 in the admin, and YUI gone](../../features/6.0/jquery4-and-yui-removal.md)). Older
jQuery and jQuery UI files were removed on 14 October 2026; the page notes that jQuery UI 1.10.3 "has known vulnerabilities".

## What is new, and when

| What | When | Source |
|---|---|---|
| jQuery 3.7.1 in the administration | August 2026 | [How it began](../../features/6.0/jquery4-and-yui-removal.md#how-it-began-jquery-371-in-august) |
| jQuery 4, Exponential UI, YUI removed | 1 and 2 October 2026 | [jQuery 4 in the admin, and YUI gone](../../features/6.0/jquery4-and-yui-removal.md) |
| Old jQuery and jQuery UI files removed | 14 October 2026 | [What you get](../../features/6.0/jquery4-and-yui-removal.md#what-you-get) |

## Facts you can print

| Fact | Source |
|---|---|
| jQuery 4.0.0, jQuery Migrate 4.0.2, jQuery UI 1.14.2 | [What you get](../../features/6.0/jquery4-and-yui-removal.md#what-you-get) |
| YUI removed from the admin, admin3, admin4 and standard designs | same |
| Exponential UI components replace YUI widgets | same |
| A two-minute check for administrators is documented | [Check an installation in two minutes](../../features/6.0/jquery4-and-yui-removal.md#check-an-installation-in-two-minutes) |
| Sites and extensions that still use YUI have upgrade steps | [YUI removal](../../bc/6.0/yui-removal.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Rewritten from scratch" | The libraries were replaced; the administration's templates remain |
| "No more JavaScript vulnerabilities" | Old files with known vulnerabilities were removed |
| "Every extension works unchanged" | Extensions that used YUI need the documented upgrade steps |

## Angles and headline ideas

- "Paying down twenty years of technical debt, one widget at a time."
- "Why an old CMS dropped YUI."

## Related pages

- [Story: admin4, the administration design](story-admin4.md)
- [Story: a code base kept alive](story-longevity.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
