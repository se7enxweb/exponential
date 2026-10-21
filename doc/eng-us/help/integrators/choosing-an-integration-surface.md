---
title: Choosing an integration surface
audience: integrator, developer
category: integration
order: 30
summary: A decision guide for picking the right surface for each data flow - where the code runs, how much data moves, how fast it must arrive, and which surfaces fit - with worked decisions for common jobs.
keywords: choosing, decision, integration surface, REST, PHP API, cronjob, workflow, event listener, expservices, RSS, packages, xrowextract, volume, latency, trade-offs
type: help
---
# Choosing an integration surface

This page is for solution architects and integration developers who have a data flow to build and several ways to
build it. Exponential offers HTTP APIs, server-side PHP, feeds, files and hooks; most flows can be built with more
than one. Four questions narrow the choice quickly.

## Contents

1. [Four questions](#four-questions)
2. [The decision table](#the-decision-table)
3. [Worked decisions](#worked-decisions)
4. [Combining surfaces](#combining-surfaces)
5. [Red flags](#red-flags)

## Four questions

1. **Where can your code run?** On the Exponential server (as an extension, a CLI script or a cronjob) or only on
   another machine? Code on the server can use the PHP content API, which is the most complete surface; code
   elsewhere must use HTTP, feeds or files.
2. **How much data moves, and how often?** A handful of records per hour is fine over HTTP. Tens of thousands of
   records a night belong in a server-side batch with progress and a dry run.
3. **How fast must a change arrive?** "Within seconds of publishing" points to a workflow event or an event listener;
   "by tomorrow morning" points to a cronjob.
4. **Who starts the exchange?** Exponential pushing to the other system, the other system pulling from Exponential,
   or the other system pushing into it ([Push and pull, batch and real time](integration-patterns-push-pull-batch-real-time.md)).

## The decision table

| Your situation | First choice | Also possible | Avoid |
|---|---|---|---|
| Import many records on a schedule, code may run on the server | Cronjob part or CLI script with `eZContentFunctions` and `remote_id` | `xrowextract` scheduled import | Many single REST creates |
| Another system creates a few items now and then, cannot run code on the server | REST `POST /api/ezp/v2/content/node/create` with an API key | `expservices` write services | Writing to database tables directly |
| An app reads content | REST reads (`/api/ezp/v1/...` or `/v2/...`) | `expservices` reads, RSS or Atom feeds | Scraping HTML pages |
| Another system must know at once when something is published | A workflow event on `content / publish / after` | Audit webhook sink with `content.object.publish` switched on | Polling the REST API every minute for everything |
| Another system must approve or reject a publication | A workflow event on `content / publish / before` that returns accepted or rejected, or defers to the cronjob | The *Approve* event for human approval | Checking after publishing |
| Form answers go to a CRM | A cronjob that reads new collections and sends them | `bccie` exports, the CSV export | Mailing the CRM's inbox from `collect.ini` and parsing mail |
| Staff sign in with the company directory | `LoginHandler[]=LDAP` | A login handler of your own | Copying passwords into Exponential |
| Two Exponential installations share content | `syndication` | RSS export and import; packages | Database copies |
| Move a site or a subtree between installations | Packages (`ezpm.php`) | `xrowextract` content packages | Database dumps of single tables |
| A spreadsheet round trip for editors | `xrowextract` | `ezcsvimport.php`, `ezcsvexport.php` | Hand-made SQL |
| Send mail through a provider's API | A mail transport class of your own (`TransportAlias[]`) | SMTP to the provider | Calling the provider from templates |

## Worked decisions

### Product data from a PIM, 20 000 products, nightly

The PIM exports a file at night. Code may run on the server. The decision: a cronjob part of your extension that
reads the file, finds each product with `eZContentObject::fetchByRemoteID()`, creates it with
`eZContentFunctions::createAndPublishObject()` or updates it with `updateAndPublishObject()` only when a field
changed, and logs a summary. Publishing without notification (`$notify = false`) keeps subscribers from receiving
20 000 mails. See [Writing an import script](writing-an-import-script.md).

### A mobile app shows news and lets reporters post drafts

The app runs on phones; nothing runs on the server. Reads use the REST API; reporters sign in through an OAuth
application so the app acts as each reporter. Creating content uses the v2 create route, which publishes under the
reporter's own `content/create` rights. See [OAuth applications](oauth-applications-for-integrations.md).

### Search index in an external service, updated within a minute

A workflow event on `content / publish / after` queues the object id; a frequent cronjob sends the queued ids to the
search service. The event stays fast, and a failure of the search service does not block editors. See
[Workflows and triggers as hooks](workflows-and-triggers-as-hooks.md).

## Combining surfaces

Most real integrations combine two surfaces: a **hook** that notices a change cheaply and a **batch** that does the
heavy work and can retry. Typical pairs:

| Hook | Batch |
|---|---|
| Workflow event on publish writes an id to your own table | Cronjob part sends pending ids and marks them done |
| `content/cache` event listener notes cleared nodes | Cronjob purges them at the CDN |
| Audit webhook delivers `content.node.remove.*` | The receiving system removes its copy |

## Red flags

- **Writing to Exponential's database tables directly.** It bypasses versions, the publish operation, workflows,
  caches, the search index and the audit trail. Always use the kernel's functions
  ([Creating content from PHP](../extension-development/part-8-reference/content-functions-from-php.md)).
- **Running imports as the administrator.** Use a service account with the rights the import needs
  ([Service accounts and roles](service-accounts-roles-and-policies.md)).
- **Doing slow remote calls inside the publishing request.** Editors wait, and a timeout at the other end breaks
  publishing. Defer to a cronjob.
- **Relying on object or node IDs across installations.** They differ between installations; use `remote_id`.

## Related pages

- [Integration surfaces at a glance](integration-surfaces-at-a-glance.md)
- [Push and pull, batch and real time](integration-patterns-push-pull-batch-real-time.md)
- [Choosing credentials for an integration](choosing-credentials-for-an-integration.md)
- [Background work patterns](../creators/app-creators/background-work-patterns.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
