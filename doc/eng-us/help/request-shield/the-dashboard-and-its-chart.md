---
title: "Request shield: the dashboard and its chart"
audience: admin, hosting
category: administration
order: 10
summary: The dashboard of Setup > Request shield - the notices, the figures, the chart of decisions per hour, the setup box with the library version, rule file and store, the rules that decided most and the newest log lines - and how to read each.
keywords: request shield, dashboard, requestshield/dashboard, chart, decisions per hour, refused, browser check, watched, monitor, figures, notices, paused, did not run, rules cannot be read, store, tier, S0, S1, S2, APCu, library version, main rule file, DashboardHours, DashboardRecent, requestshield.ini
type: help
---
# Request shield: the dashboard and its chart

**Setup > Request shield** opens the dashboard (`/requestshield/dashboard`). It answers three questions at a glance:
is the shield running, in which mode, and what has it decided lately. You need the policy `requestshield/read`.

## Contents

1. [The switches](#the-switches)
2. [Notices](#notices)
3. [The figures](#the-figures)
4. [Decisions per hour](#decisions-per-hour)
5. [Setup](#setup)
6. [Rules that decided most](#rules-that-decided-most)
7. [Newest log lines](#newest-log-lines)
8. [Settings of the dashboard](#settings-of-the-dashboard)

## The switches

At the top, for a user with `requestshield/manage`: **Pause** or **Switch on**, and the modes **Enforce**,
**Monitor** and **Off**. [The switches](the-switches.md) explains each.

## Notices

A notice above the figures tells you when something needs attention:

| Notice | Meaning | What to do |
|---|---|---|
| **The rules cannot be read** | a rule file has a mistake; the shield lets every request through until it is fixed (it fails open) | open **Rules**, or run `bin/request-shield check` ([The rules](the-rules.md)) |
| **The shield is paused** | the file `var/request-shield/PAUSED` exists | **Switch on**, or remove the file |
| **The shield did not run for this page** | `config.php` does not load the extension's hook, or this server does not run it (under Velocity: not restarted since the hook was added) | add the hook; restart Velocity ([Troubleshooting](troubleshooting.md)) |
| **Monitor mode** | every decision is logged as it would be taken, and nobody is refused | go back to **Enforce** when the log looks right ([Monitor and enforce](monitor-and-enforce.md)) |

No notice means the shield ran for the page you are looking at, with readable rules, in enforce mode.

## The figures

For the period of the dashboard (24 hours by default): the **mode**, the **log lines** of the period, and how many
of them were **Refused**, sent to the **Browser check**, **Watched (monitor)** or **Other**.

With the shipped `set log-level flag` the shield writes only what it refuses, checks or would refuse, so the log
lines are the requests it acted on, not all requests of the site.

## Decisions per hour

A stacked bar per hour of the period, one color per decision group (refused, browser check, watched, other). Hover
or focus a bar for its hour and total; the caption names the most in one hour. The chart reads the shield's log,
so it works without the library's statistics (`set stats off`).

How to read it:

- **A steady low band** of refusals is normal: scanners and bots try every public site.
- **A sudden tall bar** is a scan or a flood that the shield turned away. Open the log for that period to see the
  clients and the rules.
- **Many browser checks** after a change mean a rule asks too often; check the rules named in **Rules that decided
  most**.
- **An empty chart** means no log lines: the rules name no log file, or `log-level` is `off`.

## Setup

What the shield is running with, read through the library's own classes, so what you see is what it decides:

- the **library**'s version and build (request-shield 0.4.0, build `v0.4.0`) and the **extension**'s version;
- the **main rule file** and where it comes from: the site's own file in `settings/request-shield/`, a file
  named in `config.php` (`EXP_REQUEST_SHIELD_RULES`), or the extension's default (no site file yet);
- the **store** and its tier: S0 stateless, S1 files, S2 APCu, with what is off and why;
- the **log** file, the **host names**, and the **parts loaded** (the core, statistics, API, firewall pages, HTTP
  cache).

## Rules that decided most

The rule IDs that decided most in the period, with the time of their last decision. `EXP-...` rules come from the
shipped Exponential rules, `SITE-...` rules from the site's own file. A rule you do not expect near the top is the
first thing to look at after a complaint.

## Newest log lines

The newest lines (15 by default), linked to the whole log. [The log](the-log.md) explains the columns.

## Settings of the dashboard

In `settings/override/requestshield.ini.append.php`, `[AdminSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `DashboardHours` | 24 | hours the dashboard sums up and draws (1 to 168) |
| `DashboardRecent` | 15 | newest log lines on the dashboard |

These settings change only the administration pages. The shield itself is configured in its rule files.
