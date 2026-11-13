---
title: "Request shield: the switches"
audience: admin, hosting
category: administration
order: 20
summary: The switches at the top of the request shield's dashboard - Pause and Switch on, Enforce, Monitor and Off - what each changes on the server, when it takes effect, the backup and compile check of a mode switch, the policy requestshield/manage and the record of who switched what.
keywords: request shield, switches, requestshield/switch, requestshield/manage, pause, switch on, resume, PAUSED, var/request-shield/PAUSED, enforce, monitor, off, set mode, backup, var/request-shield/backup, compile check, recheck, requestshield.log, form token
type: help
---
# Request shield: the switches

The switches sit at the top of the dashboard (Setup > Request shield). They are shown to users with the policy
`requestshield/manage`; everyone else with `requestshield/read` sees the dashboard without them.

## Contents

1. [The switches at a glance](#the-switches-at-a-glance)
2. [Pause and Switch on](#pause-and-switch-on)
3. [Enforce, Monitor and Off](#enforce-monitor-and-off)
4. [When a switch is refused](#when-a-switch-is-refused)
5. [Who switched what](#who-switched-what)

## The switches at a glance

| Switch | What it changes | Takes effect |
|---|---|---|
| **Pause** | creates `var/request-shield/PAUSED` | on the next request, on every server |
| **Switch on** | removes `var/request-shield/PAUSED` | on the next request, on every server |
| **Enforce**, **Monitor**, **Off** | rewrites the `set mode` line of the site's main rule file | within the rules' `recheck` seconds (10 by default) |

Each switch is a button in a form posted with the form token, and ends back on the dashboard with a message that says
what was done.

## Pause and Switch on

**Pause** is the quickest way to take the shield out of the way: while `var/request-shield/PAUSED` exists, the
shield decides nothing and every request reaches the site as if it were not installed. Nothing in the rules changes.
Use it when you suspect the shield of something and want to know at once.

**Switch on** removes the file. The same works from a shell: create or remove `var/request-shield/PAUSED`.

## Enforce, Monitor and Off

The mode buttons change the mode of the rules:

- **Enforce**: the decisions are carried out. This is how the shield is installed.
- **Monitor**: every decision is only logged, nobody is refused. See [Monitor and enforce](monitor-and-enforce.md).
- **Off**: nothing is decided.

A mode switch:

1. uses the site's main rule file (`settings/request-shield/request-shield.rules`, or the file `config.php` names).
   While the extension's default file is in use, the **first** switch makes the site's own file from it and copies
   the three `exponential*.rules` next to it; from then on the site's file is used;
2. keeps the previous file in `var/request-shield/backup/` (its name, the date and a short hash);
3. compiles the new rules; when they do not compile, it puts the previous file back and says why.

The `strict` mode is not on a button: it stays a decision written in the rule file.

## When a switch is refused

A switch is refused, with the reason, when the web server cannot write the rule file or its directory (for a mode),
or `var/request-shield/` (for a pause). The buttons are then disabled. Ask whoever looks after the server to make the
files writable by the user the site's PHP runs as, or to make the change on the server.

## Who switched what

Every switch is written to `var/log/requestshield.log` with the user's login and id and the outcome. You can read it
in **Setup > Log files**.
