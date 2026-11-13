---
title: "Request shield: under Velocity"
audience: admin, hosting, developer
category: administration
order: 60
summary: What differs for the request shield when Exponential Velocity serves the site - the prepend file in index.php, restarting after the hook changes, shared counters with set store file, updating the library safely - and why the answers are identical to PHP-FPM's.
keywords: request shield, Velocity, Exponential Velocity, vc, PHP-FPM, EXP_REQUEST_PREPEND_FILE, prepend.php, index.php, worker, restart, exp:velocity restart, store, set store file, APCu, exit signal, shield error, identical answers, update library
type: help
---
# Request shield: under Velocity

Exponential Velocity keeps PHP workers running between requests. The shield protects Velocity's requests just as
it protects those of PHP-FPM, with **identical answers**: the same status, the same headers, the same log line. A few
things work differently behind the scenes.

## Contents

1. [How the shield reaches each request](#how-the-shield-reaches-each-request)
2. [When to restart Velocity](#when-to-restart-velocity)
3. [Counters shared by every process](#counters-shared-by-every-process)
4. [Updating the extension](#updating-the-extension)
5. [Why the answers are the same](#why-the-answers-are-the-same)

## How the shield reaches each request

Under PHP-FPM, `config.php` runs on every request, so the shield runs there. Under Velocity, `config.php` runs once
per worker, when the worker starts. The hook therefore names its file in `EXP_REQUEST_PREPEND_FILE`, and
`index.php` runs that file at the start of every request.

This needs the **6.0.15** `index.php`. With an older one, Velocity's requests are not filtered, while PHP-FPM's still
are. The dashboard then says "The shield did not run for this page" when you open it through Velocity.

## When to restart Velocity

| Change | Restart? |
|---|---|
| hook lines added to or removed from `config.php` | yes: `./console exp:velocity restart` |
| a rule file changed, or a mode switched on the dashboard | no: read again within `recheck` seconds |
| **Pause** or **Switch on** | no: from the next request |
| the extension updated | yes, with a pause around it (below) |

## Counters shared by every process

Velocity runs several processes. With the store on APCu each process would count on its own, so a flood spread over
the workers would reach its limit later. `set store file` in the rule file makes all workers, and PHP-FPM if it
serves the same site, share the counters in `var/request-shield/store/`. The dashboard's **Setup** box shows the
store and its tier.

## Updating the extension

A worker that already loaded the old classes must not meet the new files. Update in this order:

1. **Pause** (or create `var/request-shield/PAUSED`).
2. Update the extension.
3. `./console exp:velocity restart`.
4. **Switch on** (or remove `PAUSED`).

## Why the answers are the same

In a Velocity worker `exit()` throws Velocity's exit signal, so that it ends the request and not the worker. The
extension lets that signal pass when the shield has answered (a refusal, the browser check, one of its own pages),
so nothing mistakes it for a failure of the shield. Should a future library change its inner steps, the extension
notices it and falls back to the library's plain call; the answers stay correct, and only the debug header and PHP's
error log would then show "shield error" again. The details are in the extension's
[doc/velocity.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/velocity.md).

Velocity in general: [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md).
