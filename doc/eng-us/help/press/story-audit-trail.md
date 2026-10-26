---
title: "Story: the audit trail"
audience: press
category: press-media
order: 61
summary: A feature story about the tamper-evident audit trail of Exponential - who did what, a hash chain that proves nobody changed the record, alerts, archives and privacy rules - with printable facts, sources and the claims to avoid.
keywords: story, audit trail, audit log, tamper-evident, hash chain, accountability, alerts, privacy, exp:audit, press
type: help
---
# Story: the audit trail

This page is for journalists who cover accountability, compliance work and security operations. Exponential records
who did what in a log that can prove it was not edited afterwards. This page gives the story, the facts with
sources, and the wording to avoid.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Explaining it to readers](#explaining-it-to-readers)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"Exponential 6.0.15 records security-relevant events in a tamper-evident log: sign-ins and refusals, content changes,
settings writes, cache clears, deploys, orders and VAT changes. The log is on by default, needs no setup, and can be
read from the admin interface or the command line" ([The audit trail](../../features/6.0/audit-trail.md)). "Every
record carries the hash of the record before it (a hash chain)", and a `verify` command tells whether anything was
edited or cut. The trail is also careful about the people it records: addresses are truncated and passwords are never
written down. (6.0.15 is in development and not released; the feature was added on 2 October 2026.)

## What is new, and when

| What | When | Source |
|---|---|---|
| The audit trail: hash-chained, signed, searchable, with the `exp:audit` command, the Audit tab, sinks, alert rules and archives; on by default | 2 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |
| Extension sources in the audit console, streamed and background exports | 12 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#12-october-2026-the-audit-consoles-extension-sources-a-streamed-and-background-export-repeated-records) |
| A timeline for admin4's audit events | 14 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#14-october-2026-a-timeline-for-admin4s-audit-events) |
| Exponential Intelligence logs its requests as audit events | 12 October 2026 | [expai, For administrators](../../features/6.0/extensions/expai.md#for-administrators) |

## Facts you can print

| Fact | Source |
|---|---|
| It is on by default and needs no setup. | [The audit trail](../../features/6.0/audit-trail.md) |
| Every record carries the hash of the one before; daily files are closed, archived and listed in signed manifests. | [The audit trail, Why you want it](../../features/6.0/audit-trail.md#why-you-want-it) |
| Alert rules watch for brute-force sign-ins, an administrator role being granted, settings written out of hours, mass deletes, the audit being switched off and a broken chain. | same |
| Addresses are truncated, sessions hashed, user agents shortened, query values removed. | same |
| Passwords, tokens, repair keys and secrets are never recorded. | same |
| A subtree removal is one parent event with one child per node below it. | same |
| Sinks include syslog, journald, a signed webhook and mail; archives in five formats. | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |
| Exponential Intelligence requests are audit events, but never a prompt, an answer or a key. | [README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety) |

## What not to claim

- "The audit trail makes a site compliant" with any regulation. It is evidence an organization can use; compliance
  depends on the organization.
- "Nobody can change the log." The source says changes can be **detected** (`verify` exits 1 when a chain is broken).
- "It records everything users do." It records the events listed in its catalog; read the
  [audit guide](../../bc/6.0/audit.md) before saying more.

## Explaining it to readers

```text
Every entry in the log contains a fingerprint of the entry before it. Change or delete one old entry and the
fingerprints no longer match, so a check finds the gap. The log does not stop someone with access to the server
from editing it - it makes the edit visible.
```

## Angles and headline ideas

```text
"Who removed this page? An open source CMS that can answer - and prove it"
"A log with fingerprints: Exponential's tamper-evident audit trail"
"Recording the actions, not the people: privacy rules inside an audit log"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Design and use cases | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| How administrators use it | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Story: security](story-security.md)
- [The audit trail (feature page)](../../features/6.0/audit-trail.md)
- [Story: an assistant that writes drafts only](story-ai-that-writes-drafts-only.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
