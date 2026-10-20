---
title: Live demo safety
audience: advocate
category: advocacy
order: 45
summary: How to show Exponential live without exposing secrets, personal data or someone else's site - what never appears on screen, how to prepare a clean demo installation, what to do when something leaks, and a recovery table for demos that go wrong.
keywords: live demo, safety, secrets, passwords, keys, personal data, screen sharing, recording, streaming, demo installation, recovery, leak, rotation
type: help
---
# Live demo safety

This page is for every advocate who shares a screen: on stage, in a video call, in a recording or a stream. A demo can
leak a password, an API key, a server address or someone's personal data in one second, and recordings keep it
forever. Prepare so that nothing secret is on the machine you show.

## Contents

1. [What must never be on screen](#what-must-never-be-on-screen)
2. [Prepare a clean demo installation](#prepare-a-clean-demo-installation)
3. [Prepare the machine](#prepare-the-machine)
4. [During the demo](#during-the-demo)
5. [If something leaks](#if-something-leaks)
6. [When the demo goes wrong](#when-the-demo-goes-wrong)

## What must never be on screen

- Passwords, including the generated administrator password and the file `var/log/initial-admin-password`.
- Provider keys (for example of Exponential Intelligence), API keys, tokens, session cookies.
- The contents of `settings/override/` and any file with database credentials.
- Server addresses, database host names, internal paths beyond the installation.
- Real people's names, e-mail addresses, orders or messages.
- A customer's or employer's site, unless they agreed in writing.
- Other browser tabs, chat notifications, e-mail previews, bookmarks bar.

The demo scripts carry the same warning ([Demo scripts](../business/demo-scripts.md#the-checklist-one-day-before)).

## Prepare a clean demo installation

1. Install a fresh copy with invented content ([Install in one command](../../features/6.0/install-in-one-command.md)).
2. Change the generated password; delete `var/log/initial-admin-password`
   ([The default administrator password](../default-administrator-password.md)).
3. Create demo accounts with invented names (for example "Demo Editor"), never real people.
4. Use invented sample content. Do not copy real articles, customer data or private texts.
5. If you show Exponential Intelligence, use a key with a low spending limit that you can revoke afterwards, and
   never open the screen that shows it.
6. Run it on your own machine or a test server you control.
7. If the audience signs in to the demo themselves with a sign-in you show on screen, run it as a public demo with
   the environment variable `EXP_DEMO_MODE=1`: nobody can then change or reset the administrator password during the
   session, and a **Demo mode** banner says so on every page. It protects only the administrator password, so
   reinstall the demo afterwards ([Demo mode](../demo-mode.md)).

## Prepare the machine

```text
[ ] A separate browser profile or private window for the demo
[ ] Bookmarks bar hidden; other tabs closed; autocomplete cleared or off
[ ] Notifications off (system, chat, e-mail)
[ ] Desktop clean; no files with customer names visible
[ ] Terminal: history does not show secrets; prompt shows no host or user names you mind
[ ] Password manager locked; do not type passwords while sharing
[ ] Screen sharing: share one window, not the whole screen
```

## During the demo

- If a sign-in screen appears, **pause the screen share**, type the password, resume.
- Do not open settings files on screen. Show the documentation page instead.
- If an error page appears, read it before you show it: error details may contain paths.
- If someone asks to "just quickly look at my site", decline politely during the session; offer to help afterwards in
  private, without recording.

## If something leaks

Act at once, even during the talk:

| What leaked | Do now |
|---|---|
| A password | Change it immediately after (or during a break); sign out other sessions |
| A provider or API key | Revoke it at the provider and create a new one |
| A server address or path | Tell the server's owner; review what is exposed |
| Personal data | Stop the recording if possible; tell the organizer; follow your organization's process for data incidents |
| A recording with any of the above | Do not publish it; edit the part out or re-record |

Never assume "nobody noticed". Recordings and screenshots spread.

## When the demo goes wrong

| Problem | Recovery |
|---|---|
| The network fails | Your demo runs locally on SQLite; if not, switch to the screenshot backup |
| The laptop fails | Slides as PDF on a stick or in your e-mail |
| A page shows an error | "Live demos - let me show you the same step from my notes." Move on; investigate later, and report it if it is a bug ([Reporting bugs well](reporting-bugs-well.md)) |
| A draft from a previous run is in the way | **Dashboard > My drafts**, discard it |
| You forgot what comes next | Keep the script on a printed card |

More recoveries: [Demo scripts, What can go wrong](../business/demo-scripts.md#what-can-go-wrong-and-how-to-recover).

## Related pages

- [Giving demos as an advocate](giving-demos-as-an-advocate.md)
- [Recording a screencast](recording-a-screencast.md)
- [Protecting people's privacy](protecting-peoples-privacy.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
