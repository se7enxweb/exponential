---
title: "Request shield: monitor and enforce"
audience: admin, hosting
category: administration
order: 50
summary: The modes of the request shield - enforce as installed, monitor to watch without refusing anybody, strict and off - when to start in monitor mode, how to read the watched decisions, and how to go back to enforce.
keywords: request shield, monitor mode, enforce mode, strict, off, set mode, monitor-reject, monitor-challenge, watched, EXP-BAN, EXP-STRICT, exception, expect, first days, false positive, page alias, recheck
type: help
---
# Request shield: monitor and enforce

The shield has a **mode**, written in the site's main rule file (`set mode ...`) and switched with the buttons on
the dashboard.

## Contents

1. [The modes](#the-modes)
2. [Enforce as installed](#enforce-as-installed)
3. [When to watch first](#when-to-watch-first)
4. [Going back to enforce](#going-back-to-enforce)

## The modes

| Mode | Effect |
|---|---|
| **enforce** | the decisions are carried out. Rules with `monitor` in front of them stay watched only |
| **monitor** | every decision is logged as it would be taken (`monitor-reject`, `monitor-challenge` ...); nobody is refused |
| **strict** | enforce, and the rules marked strict too. Only in the rule file, not on a button |
| **off** | nothing is decided |

A changed mode reaches every server within the rules' `recheck` seconds (10 by default); nothing has to be
restarted.

## Enforce as installed

The shield is installed in **enforce** mode: the protection is on from the first request. Scanners, floods, stray
forms and system URLs are refused, and the login and the forms of the administration ask for the browser check. A
few rules of the shipped set (`EXP-BAN`, `EXP-STRICT`) are marked `monitor` and only watch even then.

For most sites that is right from the start: the shipped rules were written for Exponential's own addresses and
tested with examples.

## When to watch first

Switch to **Monitor** for a few days when:

- the site has unusual addresses: page aliases whose first part is a module name (`content`, `user`, `shop` ...),
  forms of its own extensions, search pages of its own;
- you are looking into a complaint that a real visitor was refused, and want nobody else refused meanwhile.

While the shield monitors, read **Setup > Request shield > Log**, decision **Watched (monitor)**. Each line is a
request the shield *would* have refused or checked. For each that came from a real visitor or editor, have an
exception added to the site's rule file, with an `expect` example
([the guide](../../guides/request-shield.md#5-add-an-exception)).

## Going back to enforce

When the watched lines show only what you want refused (scanners, bots, requests for files nobody should ask for),
press **Enforce**. The dashboard's monitor notice disappears, and the figures show refusals again instead of watched
decisions.

Do not leave a site in monitor mode for good: it then logs attacks without stopping them.
