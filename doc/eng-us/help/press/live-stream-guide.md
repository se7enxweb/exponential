---
title: Live stream guide
audience: press
category: press-media
order: 162
summary: How to stream Exponential live without leaking secrets or personal data - a run sheet for a 30 or 60 minute stream, the delay and scene setup, handling chat questions and requests to "look at my site", what to do the second something leaks, and how to keep the recording.
keywords: live stream, streaming, Twitch, YouTube Live, run sheet, scenes, stream delay, chat, moderation, leaks, recording, VOD, creator
type: help
---
# Live stream guide

This page is for creators who show Exponential live: a build-along, a first look, a question-and-answer session. A
live stream cannot be edited before people see it, so everything that keeps a recorded video safe has to be done in
advance. Read [Live demo safety](../advocates/live-demo-safety.md) first; this page adds what is specific to
streaming.

## Contents

1. [Plan the stream](#plan-the-stream)
2. [Set up scenes and a safety net](#set-up-scenes-and-a-safety-net)
3. [A run sheet to copy](#a-run-sheet-to-copy)
4. [Chat, questions and requests](#chat-questions-and-requests)
5. [When something leaks live](#when-something-leaks-live)
6. [After the stream](#after-the-stream)

## Plan the stream

| Decision | Recommendation |
|---|---|
| Topic | One theme per stream: editing, Layouts, Exponential Intelligence, Velocity, an install |
| Installation | Your own demo installation with invented content ([Setting up a demo installation](setting-up-a-demo-installation.md)) |
| Version | Say it in the title and at the start: a release, or the 6.0.15 development line with the date |
| Script | One of the demo scripts in this section, with time for questions |
| Disclosure | In the title or panel, and spoken at the start ([Disclosure and sponsorship ethics](disclosure-and-sponsorship-ethics.md)) |
| Co-host | A second person who watches the output and the chat helps catch leaks |

## Set up scenes and a safety net

```text
SCENES
[ ] "Starting soon" and "Be right back" scenes with no screen capture at all
[ ] A "Demo" scene that captures one browser window (a separate profile), never the whole desktop
[ ] A "Terminal" scene that captures one terminal window with a large font and a plain prompt
[ ] A hotkey that switches to "Be right back" instantly - practice it

SAFETY NET
[ ] A stream delay, if your platform offers one, so you can cut before viewers see a mistake
[ ] Notifications off on the machine and the phone near the microphone
[ ] Password manager locked; no sign-in needed during the stream (sign in before going live)
[ ] Provider keys for Exponential Intelligence: a separate key with a low limit, revoked after the stream
[ ] The exp:install summary and settings files are never shown (they contain passwords)
```

The full list of what must never be on screen is on [Keeping secrets off screen](keeping-secrets-off-screen.md).

## A run sheet to copy

```text
RUN SHEET - <title> - <date>                        Version: <Exponential version>   Design: admin4
00:00  Starting soon scene; check audio
00:03  Hello, disclosure, what we will build today, the version
00:06  Part 1: <demo script part>                    Help page in chat: <link>
00:18  Questions from chat (5 minutes)
00:23  Part 2: <demo script part>                    Help page in chat: <link>
00:40  Questions from chat
00:50  Recap: what we did, where to read more, where to ask (forums, Telegram)
00:55  Clean up on stream or after: discard drafts, revoke keys
```

Paste the help links into the chat at each part; viewers can read along while you talk.

## Chat, questions and requests

- **Answer with a source.** If the help answers it, paste the link. If not: "The documentation does not say. I will
  check and post the source." Write it down ([Handling comments and questions](handling-comments-and-questions.md)).
- **"Look at my site, it's broken."** Decline on stream, kindly. A real site on screen can show its owner's data and
  settings. Point them to the forums or the help's **Report an error** page.
- **A viewer posts a password, a key or personal data in chat.** Delete the message, tell them to change it, never read
  it aloud.
- **A viewer reports a security problem.** Stop the topic: "Please send that privately to the address in
  `SECURITY.md`." Do not discuss details live.
- **Off-topic attacks on other projects or people.** Moderate; keep the stream respectful
  ([Respecting other projects and companies](../advocates/respecting-other-projects-and-companies.md)).

## When something leaks live

| What leaked | Do now |
|---|---|
| A password | Switch to "Be right back"; change it at once; sign out other sessions |
| An API or provider key | Switch scene; revoke the key at the provider; create a new one off stream |
| A server address or path | Note it; review after the stream what it exposes |
| Personal data | Switch scene; remove it from the recording before it stays online |

Then tell viewers plainly what happened and that it is fixed. Never assume nobody noticed: streams are recorded and
clipped.

## After the stream

1. Watch the recording before you keep it public; cut what leaked, or keep it private.
2. Add chapters, captions and the links you posted in chat
   ([Captions and accessible video](captions-and-accessible-video.md)).
3. Post the answers you promised, with sources.
4. Reset the demo installation: discard drafts, revoke keys, switch the interface back to English.

## Related pages

- [Live demo safety](../advocates/live-demo-safety.md)
- [Running an online meetup](../advocates/running-an-online-meetup.md)
- [Keeping secrets off screen](keeping-secrets-off-screen.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
