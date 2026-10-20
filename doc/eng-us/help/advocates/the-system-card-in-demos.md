---
title: The system card in demos
audience: advocate, press
category: advocacy
order: 46
summary: Open a demo, a talk or a video with ./console exp:about - one screen with the Exponential "e", the facts of the installation and what the project is, its license and its links, all taken from the documentation. How to show it well and what it never reveals.
keywords: exp:about, exp:expfetch, system card, demo, talk, screenshot, video, screencast, terminal, logo, wordmark, theme, light theme, animate, press, advocate
type: help
---
# The system card in demos

This page is for advocates and creators who show Exponential in a talk, a stream or a video. When someone asks
"what is this?", one command answers on one screen:

```bash
./console exp:about
```

It draws the orange Exponential "e" (with the rainbow wordmark EXPONENTIAL on a wide terminal), the facts of the
installation (version, PHP, the engine, the database) and then what Exponential is, the Exponential Foundation and 7x,
Exponential Velocity, the license (GPL 2.0 or later for the code, GFDL 1.3 or later for the documentation), the links
and how to contribute or become a partner. Every sentence of the project part comes from the documentation, so you
can say it out loud without checking it again. Reference: [The system card](../../features/6.0/system-card.md).

## Showing it well

- **Width.** Make the terminal at least 82 columns wide for the big "e" with the wordmark; zoom the font, not the
  window, so the layout stays.
- **Projector or light background.** `--theme=light` has darker colors; `--theme=high-contrast` reads best on a weak
  projector.
- **An opening flourish.** `--animate` reveals the logo and moves its gradient in under a second.
- **Only the project.** `./console exp:about --section=about,links --no-logo` for a slide-like screen.
- **A still for a slide.** `./console exp:about --plain > about.txt` gives text you can paste into slides, with no
  escape codes.
- **The machine instead.** `./console exp:expfetch` is the same card without the project part: good for "this is the
  server we just installed".

## What it never shows

The card never prints a password, key, token, database user or full database address, nor the values of the
Velocity envvars. It does show the host name and the operating system: on a stream, use a demo machine with a
neutral host name ([Live demo safety](live-demo-safety.md)). Hints like "demo mode is on" are expected on a demo
installation; say so if someone asks.

## Related pages

- [The system card](../../features/6.0/system-card.md)
- [Giving demos as an advocate](giving-demos-as-an-advocate.md)
- [Live demo safety](live-demo-safety.md)
- [Demo script: five-minute overview](../press/demo-script-five-minute-overview.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
