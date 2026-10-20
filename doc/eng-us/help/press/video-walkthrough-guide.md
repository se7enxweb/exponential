---
title: Video walkthrough guide
audience: press
category: press-media
order: 154
summary: How to plan, script, record, edit and publish a video walkthrough of Exponential - one task per video, the exact names of buttons, a script template, recording settings for readability, the editing pass that catches secrets, and keeping the video correct after the next release.
keywords: video, walkthrough, screencast, tutorial video, recording, script, editing, YouTube, screen recording, voice-over, creator
type: help
---
# Video walkthrough guide

This page is for creators who record a walkthrough of Exponential: a tutorial, an overview, a "first look". It adds
the creator's workflow to [Recording a screencast](../advocates/recording-a-screencast.md), which covers the basics.
A good walkthrough shows one task well, uses the real names of what is on screen, and says which version it shows.

## Contents

1. [Plan: one video, one promise](#plan-one-video-one-promise)
2. [Script it with the real names](#script-it-with-the-real-names)
3. [Record for a phone screen](#record-for-a-phone-screen)
4. [Edit: the safety pass and the accuracy pass](#edit-the-safety-pass-and-the-accuracy-pass)
5. [Publish](#publish)
6. [Keep it correct](#keep-it-correct)

## Plan: one video, one promise

Pick a task the help already explains, so viewers can read on, and a matching script from this section:

| Video | Length | Script | Help page to link |
|---|---|---|---|
| What Exponential is, shown in five minutes | 5 min | [Five-minute overview](demo-script-five-minute-overview.md) | [Getting started](../getting-started.md) |
| Editing, drafts, preview and versions | 8 to 10 min | [Editor tour](demo-script-editor-tour.md) | [A tour of the administration](../admin-tour.md) |
| Building a page with the page builder | 6 to 8 min | [Exponential Layouts](demo-script-layouts.md) | [The layouts editor](../layouts-editor.md) |
| The writing assistant that writes drafts only | 6 min | [Exponential Intelligence](demo-script-exponential-intelligence.md) | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| Running a site under Velocity | 8 min | [Velocity](demo-script-velocity.md) | [Velocity](../../features/6.0/velocity-persistent-worker-server.md) |
| From an empty folder to a running site | 10 min | [Install from zero](demo-script-install-from-zero.md) | [Install in one command](../../features/6.0/install-in-one-command.md) |
| What an administrator sees | 10 min | [Administrator tour](demo-script-administrator-tour.md) | [Security hardening](../security-hardening.md) |

Write the promise in one sentence before anything else: "After this video you can <task>."

## Script it with the real names

Use the names exactly as the screen shows them, in **bold** in your script and transcript: **Send for publishing**,
**Store draft and exit**, **Discard draft**, **Manage versions**, **Publish layout**. The names on this page are taken
from [A tour of the administration](../admin-tour.md) and [The layouts editor](../layouts-editor.md).

```text
WALKTHROUGH SCRIPT
Title:        <the task, as a viewer would search it>
Promise:      After this video you can <task>.
Version:      Exponential <6.0.14 | 6.0.15 development line, checked on <date>>, design admin4
Disclosure:   <line from the disclosure page>
Help page:    <link>

0:00  Disclosure and promise (10 seconds)
0:10  Where we start: <screen>
0:20  Step 1  Click <name>            Say: <one sentence, from the help>
...
      Result: show what changed, on the site if it shows there
      Outro: "The help page is linked below. Questions: the forums or Telegram."
```

## Record for a phone screen

- Record one browser window, not the whole screen, at a zoom that is readable on a phone.
- Use the light color mode for recordings unless you show dark mode on purpose (the sun button switches it).
- Move the pointer slowly; pause a second on each click. Say what you press: "I press **Edit**".
- Record the voice separately, or in a quiet room; fix it in post.
- Keep a printed copy of the script beside you; never read keys or passwords from the screen.

## Edit: the safety pass and the accuracy pass

Watch the whole recording twice before publishing.

```text
SAFETY PASS (frame by frame where a sign-in, a summary or an error page appears)
[ ] No password, key, token, cookie, server address, settings file
[ ] No real person's name, e-mail, order or message
[ ] No other tabs, notifications, bookmarks or file names
[ ] The exp:install summary is cut or blurred (it prints the password)

ACCURACY PASS
[ ] Every button name matches the screen and the help
[ ] Every number and date has a source I can link
[ ] The version is said and shown
[ ] Nothing claims an endorsement, a program, a release date or a speed multiple
```

The full list of what must not appear is on [Keeping secrets off screen](keeping-secrets-off-screen.md).

## Publish

- Title, thumbnail and description: [Titles, thumbnails and descriptions](titles-thumbnails-and-descriptions.md).
- Captions and a transcript: [Captions and accessible video](captions-and-accessible-video.md).
- State the license of your video. If you read help pages aloud or show them, they are under the GNU FDL 1.3; quoting
  with a link is the simple way ([Citing sources and licensing your writing](../advocates/citing-sources-and-licensing-your-writing.md)).
- Share it where the community looks: the forums and Share ([Publishing on Share and Projects](../advocates/publishing-on-share-and-projects.md)).

## Keep it correct

- After each release, read the changelog for changes to the screens you showed
  ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)).
- When a step no longer works, pin a correction and fix the description; re-record when it matters
  ([Correcting a video or post](correcting-a-video-or-post.md)).
- When the video is obsolete, say so in the title or description.

## Related pages

- [Recording a screencast](../advocates/recording-a-screencast.md)
- [Setting up a demo installation](setting-up-a-demo-installation.md)
- [Captions and accessible video](captions-and-accessible-video.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
