---
title: "Running a demo site"
audience: designer, sales-marketing
category: theme-distribution
order: 330
summary: Give people a live preview of a theme - options for hosting a demo, keeping it current with each release, resetting it automatically, and keeping it safe when the public can look at (or log in to) it.
keywords: demo site, live demo, preview, reset, rebuild, read-only, security, hosting, demo.exponential.earth
type: help
---
# Running a demo site

This page is for designers who want people to try a theme before installing it. A live demo answers "how does it feel?"
better than any screenshot.

## Examples from the project

The Exponential project runs public demonstration sites, for example `https://demo.exponential.earth/` and
`https://site.cjw.demo.exponential.earth/` (the CJW starter site, which shows the `cjw_themes_jumper` design). The
project rebuilds its demo installations on a schedule, so visitors always see a clean, current state.

## Options

| Option | Effort | Fits |
|---|---|---|
| Screenshots and a short video only | Low | A first release |
| A public read-only site | Medium | Most themes |
| A site that resets on a schedule, with a demo editor login | Higher | Themes sold to editors who want to try editing |
| A demo per customer, on request | Highest | Custom or premium work |

## Step by step: a read-only demo

1. Install Exponential on a server or hosting account you control, with the theme and demo content
   ([Demo content for a theme](demo-content.md)).
2. Give the anonymous role only read access; keep the administration on a separate siteaccess that is not linked.
3. Install every theme release there first; it doubles as your upgrade test
   ([Upgrade testing, step by step](../lifecycle/upgrade-test-step-by-step.md)).
4. Link the demo from the README, the project listing and your product page.

## Step by step: a demo that resets

1. Script a fresh install (database, demo content, theme) that runs without questions.
2. Run it on a schedule (for example every two hours) into a new directory, then switch the web root over, so
   visitors never see a half-built site.
3. Give a demo editor account rights to edit content only. Publish its password on the demo's front page if it is
   meant to be public; never reuse it anywhere.
4. Send mail of the demo to files or a test mailbox, never to real addresses.

## Safety

- Keep the server and Exponential updated like any production site
  ([Security hardening](../../security-hardening.md)).
- Do not put customer data or private material on a demo.

**Demo sites with a published sign-in.** Run them in demo mode (`EXP_DEMO_MODE=1`), so visitors cannot change the administrator password. A theme that overrides `user/forgotpassword.tpl` or `user/password.tpl` should show the kernel's notice (`demo_mode_text`, `demo_mode_locked`, read with `first_set()`). [Demo mode for developers](../../extension-development/part-8-reference/demo-mode-for-developers.md).

## Related pages

- [Demos that sell](../sales/demos-that-sell.md)
- [Demo scripts](../../business/demo-scripts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
