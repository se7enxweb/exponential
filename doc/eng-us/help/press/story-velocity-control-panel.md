---
title: "Story: the Velocity control panel"
audience: press
category: press-media
order: 93
summary: A feature story about the browser views every Exponential Velocity server has of itself - a live dashboard of requests, workers and memory, and a password-protected control panel for apps, domains, certificates, cache, logs and settings - with facts, sources and claims to avoid.
keywords: story, Velocity, control panel, dashboard, server management, certificates, monitoring, workers, WebSocket, operations, administrators
type: help
---
# Story: the Velocity control panel

This page is for journalists who cover operations and hosting. Velocity is not only a web server; it shows and manages
itself from the browser.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

"Every Velocity server has two built-in web views of itself": a **dashboard** at `/Q/dashboard` with live requests,
workers, memory, status codes and top paths over a WebSocket, and a **control panel** at `/Q/panel`, "a
password-protected admin page that manages apps, domains, certificates, the response cache, logs, workers and
settings" ([Velocity control panel and dashboard](../../features/6.0/velocity-control-panel.md)). "They answer to the
server, not to your application, so they keep working when the application is broken."

## Facts you can print

| Fact | Source |
|---|---|
| Dashboard and control panel are built into every Velocity server | [Velocity control panel and dashboard](../../features/6.0/velocity-control-panel.md) |
| A new installation forces a real password before any other panel action | [First sign-in](../../features/6.0/velocity-control-panel.md#first-sign-in) |
| Two-factor authentication is available for the panel | [Two-factor authentication](../../features/6.0/velocity-control-panel.md#two-factor-authentication-optional) |
| Administrators can renew a certificate, clear the cache or read filtered logs from a browser | [Velocity control panel and dashboard](../../features/6.0/velocity-control-panel.md) |
| The server's pages can be restyled without touching the engine | [Restyle the server's pages](../../features/6.0/velocity-control-panel.md#restyle-the-servers-pages-designs) |
| Applies to Exponential Velocity 0.0.4.x | same |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Replaces a hosting panel" | It manages the Velocity server itself |
| "Open to anyone" | Password-protected, optional two-factor |
| Screenshots with real domains or log lines | Use a demo server ([Keeping secrets off screen](keeping-secrets-off-screen.md)) |

## Angles and headline ideas

- "A web server that shows you what it is doing, live."
- "When the app is down, the server's own panel still works."

## Related pages

- [Story: Exponential Velocity](story-velocity.md)
- [Story: operations tools for administrators](story-maintenance-mode-and-operations.md)
- [Demo script: Velocity](demo-script-velocity.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
