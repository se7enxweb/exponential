---
title: "Integration patterns: push and pull, batch and real time"
audience: integrator, developer
category: integration
order: 270
summary: The basic integration patterns applied to Exponential - who starts the exchange (push or pull), how often (batch or near real time), delta and full runs, outbox and inbox, polling with a watermark, and which Exponential surface implements each pattern.
keywords: integration patterns, push, pull, batch, real time, near real time, delta, full sync, watermark, outbox, inbox, polling, webhook, eventual consistency, reconciliation
type: help
---
# Integration patterns: push and pull, batch and real time

This page is for architects and developers who design data flows between Exponential and other systems. It names the
basic patterns, says when each fits, and maps each to the surface that implements it in Exponential. The examples
assume a product feed from an ERP and a lead flow to a CRM, the two most common jobs.

## Contents

1. [Two questions per flow](#two-questions-per-flow)
2. [The four combinations](#the-four-combinations)
3. [Delta runs and watermarks](#delta-runs-and-watermarks)
4. [Outbox and inbox](#outbox-and-inbox)
5. [Reconciliation](#reconciliation)
6. [Choosing per flow](#choosing-per-flow)

## Two questions per flow

1. **Who starts the exchange?** In a **push**, the system that has the change sends it. In a **pull**, the system that
   needs the change asks for it.
2. **When?** In a **batch**, changes are collected and moved together on a schedule. In **near real time**, each change
   moves within seconds or minutes of happening.

## The four combinations

| | Into Exponential | Out of Exponential |
|---|---|---|
| **Push, near real time** | The other system calls the REST create route or `expservices` writes for each change | A workflow event on `content / publish / after` records the change; a frequent cronjob sends it (outbox). The audit webhook sink for event feeds |
| **Push, batch** | The other system drops a file (SFTP, a folder); a cronjob part imports it; or `xrowextract` schedules an import | A cronjob part or an `xrowextract` schedule writes a file and delivers it to SFTP, S3, WebDAV or an HTTP endpoint |
| **Pull, near real time** | A frequent cronjob part asks the other system's API for changes since the last watermark | The other system polls the REST API or `expservices` (recently modified objects), or an RSS export |
| **Pull, batch** | A nightly cronjob part downloads the full export of the other system | The other system downloads a nightly export or a feed |

Push from Exponential needs code on the Exponential side; pull by the other system needs only credentials. Pull into
Exponential needs the other system's API and a cronjob part.

## Delta runs and watermarks

A **full run** moves everything every time: simple, slow, and it finds removals by absence. A **delta run** moves only
what changed since the last successful run, identified by a **watermark**:

| Watermark | Where it comes from in Exponential |
|---|---|
| Modification time | `eZContentObject` `modified`; the `expservices` service `expobject::recentlyModified`; `xrowextract --changed-since` |
| An increasing id | `ezinfocollection.id` for form answers; your outbox table's id |
| The other system's change token | Stored by your integration after each run |

Rules: store the watermark only after the run succeeded; overlap the window a little (for example five minutes) and
rely on idempotency to absorb duplicates; run a full reconciliation now and then to catch what deltas miss, such as
removals.

## Outbox and inbox

| Pattern | What it is | In Exponential |
|---|---|---|
| **Outbox** | The change is recorded in a table inside the same system and transaction; a separate process sends it and marks it sent | A workflow event or event listener writes a row to your extension's table; a cronjob part or an `expqueue` worker sends it |
| **Inbox** | Incoming messages are stored first, then processed; duplicates are recognized by message id | A module view or server function stores the payload in your table and answers at once; a cronjob part processes it |

Both decouple the editor's request from the other system's availability, and both make retries safe.

## Reconciliation

Even well-built syncs drift: a message lost while a system was down, a record changed by hand. Plan a **reconciliation**
job that compares both sides by key (the `remote_id`) and reports or repairs differences:

- [ ] Count of records per class on each side.
- [ ] Records present on one side only (created or removed outside the sync).
- [ ] A checksum or modification time per record to find changed ones.
- [ ] A report to the operators, and a decision rule for who wins.

## Choosing per flow

| Flow | Typical choice |
|---|---|
| ERP products into product pages | Push batch (file) or pull batch (API) nightly, with a delta during the day |
| Price and stock | Pull near real time, or a server function reading a cache your import keeps |
| Leads from forms to the CRM | Pull near real time by a cronjob from Exponential's side, pushing to the CRM API |
| Publications to a search or archive system | Outbox, near real time |
| Editorial content to a mobile app | Pull by the app over REST, cached on the app side |
| Users from a directory | Login handler (on demand) plus a nightly de-provisioning job |

## Related pages

- [Choosing an integration surface](choosing-an-integration-surface.md)
- [Idempotent imports keyed by remote_id](idempotent-imports-keyed-by-remote-id.md)
- [Workflows and triggers as integration hooks](workflows-and-triggers-as-hooks.md)
- [Background work patterns](../creators/app-creators/background-work-patterns.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
