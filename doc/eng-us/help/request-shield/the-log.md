---
title: "Request shield: the log"
audience: admin, hosting
category: administration
order: 30
summary: The log page of Setup > Request shield - the periods, the decision groups, the columns, the client's network instead of a person's address, view parameters for a link to one view, and where the log file is.
keywords: request shield, log, requestshield/log, shield.log, var/request-shield/log, decision, refused, browser check, watched, monitor, other, period, hours, group, offset, client, network, masked, log-ip, rule ID, status, LogHours, LogPageSize
type: help
---
# Request shield: the log

**Setup > Request shield > Log** (`/requestshield/log`) lists what the shield wrote to its log: every request it
refused, sent to the browser check or, in monitor mode, would have refused. Newest first. You need the policy
`requestshield/read`.

## Contents

1. [Choosing what to see](#choosing-what-to-see)
2. [The columns](#the-columns)
3. [A link to one view](#a-link-to-one-view)
4. [The log file](#the-log-file)
5. [Settings of the log page](#settings-of-the-log-page)

## Choosing what to see

- **Period**: the last 1, 6, 24, 72 or 168 hours.
- **Decision**: **All**, **Refused**, **Browser check**, **Watched (monitor)** or **Other**, each with its count.
- **Newer** and **Older** page through the lines, 50 at a time.

## The columns

| Column | What it shows |
|---|---|
| **Time** | when the shield decided |
| **Client** | the client's network as the log wrote it |
| **Request** | the method, host and path |
| **Decision** | what the shield did (refused, browser check, a `monitor-...` decision in monitor mode ...) |
| **Status** | the HTTP status the client got |
| **Rule and reason** | the rule ID that decided (`EXP-SYSVIEW`, `EXP-LOGIN`, `SITE-DASH` ...) and why |

The **Client** column shows the address as the log wrote it: by default its network (IPv4 /24, IPv6 /48, the
library's `set log-ip masked`), never a person's full address.

The rule ID is the thread to pull. It tells whoever looks after the rules which line decided; `trace` on the server
shows the same decision step by step ([The rules](the-rules.md)).

## A link to one view

Every choice is a link with view parameters, so you can send a colleague exactly what you see:

```
/requestshield/log/(hours)/72/(group)/refused/(offset)/50
```

## The log file

The shield writes `var/request-shield/log/shield.log` (with the shipped rules), one line per decision, rotated at the
library's `log-max-size`. It is a plain file on the server; `tail -f` follows it. If the page says the log does not
exist or cannot be read, the rules name no log file, or the web server cannot read it
([Troubleshooting](troubleshooting.md)).

The record of who used the switches is a different file, `var/log/requestshield.log`, shown in **Setup > Log files**.

## Settings of the log page

In `settings/override/requestshield.ini.append.php`, `[AdminSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `LogHours[]` | 1, 6, 24, 72, 168 | the periods to choose from |
| `LogPageSize` | 50 | lines per page |
