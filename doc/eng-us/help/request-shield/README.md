---
title: "Request shield: start here"
audience: admin, hosting, developer
category: administration
order: 1
summary: What the request shield (exp_request_shield) is - the filter in front of the site that decides every request before the kernel - who looks after it, what Setup > Request shield shows, and where to read on.
keywords: request shield, request-shield, exp_request_shield, web application firewall, WAF, firewall, filter, scanners, bots, floods, browser check, pass cookie, rule file, monitor mode, enforce mode, pause, Setup, Request shield, requestshield/dashboard, requestshield/read, requestshield/manage, Velocity, PHP-FPM
type: help
---
# Request shield: start here

The **request shield** (the extension `exp_request_shield`) is a filter in front of the site. It looks at every
request **before Exponential starts** and decides: let it through, refuse it, or ask the browser to prove it is one
(the **browser check**). It turns away scanners that look for weak spots, floods from one network, form posts that
did not come from the site's pages and requests for internal files, for a few microseconds each.

You look after it in **Setup > Request shield**, right after **Log files** in the Setup menu.

## Contents

1. [Who it is for](#who-it-is-for)
2. [What you see in the administration](#what-you-see-in-the-administration)
3. [What visitors and editors notice](#what-visitors-and-editors-notice)
4. [The pages of this section](#the-pages-of-this-section)
5. [Related pages](#related-pages)

## Who it is for

- **Administrators** read the dashboard and the log, pause the shield or switch between monitor and enforce, and
  answer when someone says the site refused them.
- **Hosting staff and developers** install it, give the site its own rule file and add exceptions on the server.
  Their guide is [Setting up the request shield](../../guides/request-shield.md).

You do not need to edit anything to use the administration pages: they show what the shield does, and the switches
change exactly the files described in these pages.

## What you see in the administration

| Page | What it is for |
|---|---|
| **Dashboard** | Is the shield running, in which mode, what did it decide in the last 24 hours, which rules decided most. The switches are at the top. [The dashboard and its chart](the-dashboard-and-its-chart.md) |
| **Log** | Every line the shield wrote, by period and decision. [The log](the-log.md) |
| **Rules** | Whether the rule files are in order, and their text. [The rules](the-rules.md) |

Two policies decide who sees what: `requestshield/read` for the pages, `requestshield/manage` for the switches.
In a new installation only the Administrator role holds them.

## What visitors and editors notice

Usually nothing. A real visitor's requests go through. At the login, and in the administration once per pass,
the browser solves the browser check by itself in a fraction of a second; there is no puzzle to solve. A browser
without JavaScript or cookies is told what is missing.

A scanner, a flood or a request for an internal file gets a short refusal (mostly 404 or 403, 429 for too many
requests) and never reaches the site.

## The pages of this section

| Page | What it answers |
|---|---|
| [The dashboard and its chart](the-dashboard-and-its-chart.md) | The notices, the figures, the chart of decisions per hour, the setup box, the rules that decided most |
| [The switches](the-switches.md) | Pause and Switch on, Enforce, Monitor, Off: what each changes, when it takes effect, who may use them |
| [The log](the-log.md) | Reading the log by period and decision, the columns, the client's network, a link to a view |
| [The rules](the-rules.md) | Which rule file is used, its includes, whether it compiles, the commands that check it |
| [Monitor and enforce](monitor-and-enforce.md) | The modes, when to watch first, going back to enforce |
| [Under Velocity](under-velocity.md) | What differs when Exponential Velocity serves the site |
| [Troubleshooting](troubleshooting.md) | Symptoms, causes and fixes |
| [FAQ](faq.md) | Short answers to the usual questions |

## Related pages

- [The request shield](../../features/6.0/request-shield.md) (feature reference: how it works, files, licensing,
  who gets it when)
- [Setting up the request shield](../../guides/request-shield.md) (install, own rules, check and test, update,
  uninstall)
- [Upgrading to 6.0.15: the request shield](../../bc/6.0/request-shield.md)
- [Security hardening](../security-hardening.md)
- The library's documentation: [request-shield](https://github.com/cjw-network/request-shield/tree/main/docs)
