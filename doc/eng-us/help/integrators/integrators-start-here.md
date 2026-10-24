---
title: "Integrators: start here"
audience: integrator, developer, partner
category: integration
order: 1
summary: The landing page for system integrators and integration developers who connect Exponential with CRM, ERP, PIM, DAM, identity providers, search, mail, payment, analytics and other content systems. What integration surfaces Exponential really has, how this section is organized, reading paths by role and who to ask.
keywords: integrator, integration, system integrator, integration developer, API, REST, OAuth, API keys, expservices, ezjscore, webhook, import, export, sync, CRM, ERP, PIM, DAM, SSO, LDAP, search, mail, payment, start here, landing
featured: true
type: help
---
# Integrators: start here

This section of the help is for **system integrators**: integration developers, solution architects and the
integration companies that connect Exponential with other systems. Typical projects connect a CRM, an ERP or a
product information system (PIM), a digital asset manager (DAM), an identity provider, a search server, a mail or
newsletter service, a payment provider, an analytics tool, or another content management system that is being
replaced or kept alongside.

It answers four questions: which integration surfaces Exponential actually has (and which it does not), which one
fits a given job, how to build an integration that survives imports, failures and upgrades, and how to run an
integration project from discovery to handover.

> **Note**
> Every technical statement in this section names the file, setting, class or command it relies on, and was checked
> against the code of Exponential 6.0.15 and the extensions the root `composer.json` requires. Where something is not
> provided, the page says so plainly. Examples use `example.com`, "your company" and placeholder identifiers; no page
> describes a real customer or partner.

## Contents

1. [Integrating Exponential in one minute](#integrating-exponential-in-one-minute)
2. [What this section covers, and what it does not](#what-this-section-covers-and-what-it-does-not)
3. [Map of the integrator pages](#map-of-the-integrator-pages)
4. [Reading paths by role](#reading-paths-by-role)
5. [Your first week on an integration project](#your-first-week-on-an-integration-project)
6. [Who to ask](#who-to-ask)

## Integrating Exponential in one minute

- **Two HTTP APIs ship with the product.** The REST content API of the `ezprestapi` extension answers below
  `/api/ezp/v1/` and `/api/ezp/v2/` (reads at both versions, create and delete at v2), and the `expservices`
  extension offers about a thousand named services as JSON under `/ezjscore/call/exp<domain>::<method>`
  ([REST API for integrators](rest-api-for-integrators.md), [Remote services with expservices](remote-services-with-expservices.md)).
- **Three kinds of credentials.** OAuth 2.0 applications registered at Setup > oAuth admin, personal API keys
  (`expk_...`) for one program of one user, and `expservices` tokens (`expt_...`). None of them can do more than the
  user behind it ([Choosing credentials](choosing-credentials-for-an-integration.md)).
- **The PHP API is the strongest surface.** Imports that run on the server use `eZContentFunctions::createAndPublishObject()`
  and `updateAndPublishObject()` with a stable `remote_id`, so the publish operation, workflows, caches and the search
  index all stay in step ([Idempotent imports keyed by remote_id](idempotent-imports-keyed-by-remote-id.md)).
- **Hooks for "something happened".** Workflows on triggers (for example `content / publish / after`), event
  listeners of `ezpEvent`, notification handlers, and the audit trail's signed webhook sink
  ([Workflows and triggers as hooks](workflows-and-triggers-as-hooks.md),
  [Outgoing webhooks from the audit trail](outgoing-webhooks-from-the-audit-trail.md)).
- **Feeds, packages and files.** RSS 1.0, RSS 2.0, Atom and OPML exports, RSS imports by cronjob, `.ezpkg` packages,
  CSV import and export, and scheduled exchange to SFTP, S3, WebDAV or an HTTP endpoint with `xrowextract`.
- **Identity.** Login handlers (`standard`, `LDAP`, `textfile`, your own), single sign-on handlers, and social login
  through the `sevenx_authentication_2fa` extension ([Login handlers](login-handlers-ldap-and-textfile.md),
  [Single sign-on and social login](single-sign-on-and-social-login.md)).
- **Everything runs as an extension.** An integration is an extension: settings, classes, cronjob parts, console
  commands, workflow events. It can be versioned, tested, released and contributed back
  ([Building an integration as an extension](building-an-integration-as-an-extension.md)).
- **What is not there.** No GraphQL, no OpenAPI description of the content API, no XML-RPC, no SAML, no generic
  outgoing content webhook, no message broker connector in the kernel. Plan these as custom work
  ([Integration surfaces at a glance](integration-surfaces-at-a-glance.md#what-is-not-provided)).

## What this section covers, and what it does not

| Covered here | Read elsewhere |
|---|---|
| Choosing and using each integration surface for a job | Writing extensions in general: [Extension development: the book](../extension-development/README.md) |
| Integration patterns: push and pull, batch and real time, idempotency, mapping | Building applications on Exponential: [App creators: start here](../creators/app-creators/app-creators-start-here.md) |
| Security, testing, monitoring and upgrades of integrations | Hosting and operating servers: [Hosting companies: start here](../hosting/hosting-companies-start-here.md) |
| Running an integration project: estimates, statements of work, handover | The partner program itself: [Partners: start here](../partners/partners-start-here.md) |
| Worksheets, checklists, FAQ and glossary for integrators | Working on the code with Git: [Develop with Git](../develop-with-git.md) |

Two sibling sections are written for neighboring roles: [Resellers: start here](../resellers/resellers-start-here.md)
for companies that sell Exponential and services around it, and [Bundlers: start here](../bundlers/bundlers-start-here.md)
for companies that ship Exponential inside a product or a distribution.

## Map of the integrator pages

| Area | Pages |
|---|---|
| Orientation | [What integrators do with Exponential](what-integrators-do-with-exponential.md) · [Integration surfaces at a glance](integration-surfaces-at-a-glance.md) · [Choosing an integration surface](choosing-an-integration-surface.md) · [Choosing credentials](choosing-credentials-for-an-integration.md) |
| HTTP APIs | [REST API](rest-api-for-integrators.md) · [OAuth applications](oauth-applications-for-integrations.md) · [Personal API keys](personal-api-keys-for-integrations.md) · [Remote services with expservices](remote-services-with-expservices.md) · [ezjscore server functions](ezjscore-server-functions-for-integrations.md) |
| Feeds, files and protocols | [RSS, Atom and OPML](rss-atom-and-opml-feeds.md) · [Syndication between installations](syndication-between-installations.md) · [WebDAV and SOAP](webdav-and-soap.md) · [Packages](packages-import-and-export.md) · [Bulk exchange with xrowextract](bulk-data-exchange-with-xrowextract.md) |
| Server side | [CLI scripts and ezexec](cli-scripts-and-ezexec.md) · [Cronjobs](cronjobs-for-integrations.md) · [Workflows and triggers](workflows-and-triggers-as-hooks.md) · [Event listeners](event-listeners-for-integrations.md) · [Audit webhooks](outgoing-webhooks-from-the-audit-trail.md) |
| Systems around the site | [Mail and notifications](mail-and-notifications-integration.md) · [Forms and collected information](forms-and-collected-information.md) · [Login handlers](login-handlers-ldap-and-textfile.md) · [Single sign-on](single-sign-on-and-social-login.md) · [Search engines](search-engine-integration.md) · [Payment gateways](payment-gateways-and-the-shop.md) · [AI, job queue and MCP](ai-job-queue-and-mcp.md) |
| Patterns | [Push and pull, batch and real time](integration-patterns-push-pull-batch-real-time.md) · [Idempotent imports](idempotent-imports-keyed-by-remote-id.md) · [Mapping classes to external data](mapping-content-classes-to-external-data.md) · [Writing an import](writing-an-import-script.md) · [Writing an export](writing-an-export-or-outbound-sync.md) · [Migrating from another CMS](migrating-content-from-another-cms.md) |
| Quality and operations | [Error handling and logging](error-handling-and-logging.md) · [Security](security-for-integrations.md) · [Service accounts and roles](service-accounts-roles-and-policies.md) · [Caching after imports](caching-after-imports.md) · [Velocity and performance](velocity-and-performance-for-integrations.md) · [Testing](testing-integrations.md) · [Monitoring](monitoring-integrations.md) · [Upgrades](upgrades-without-breaking-integrations.md) |
| Building and sharing | [An integration as an extension](building-an-integration-as-an-extension.md) · [Contributing back](contributing-integrations-back.md) |
| Projects | [Project playbook](integration-project-playbook.md) · [Estimating](estimating-integration-work.md) · [Statements of work](statements-of-work-for-integrations.md) · [Handover](handover-of-an-integration.md) · [Integrators and the partner program](integrators-and-the-partner-program.md) |
| Worksheets and checklists | [Discovery worksheet](integration-discovery-worksheet.md) · [Interface specification template](interface-specification-template.md) · [Security review checklist](integration-security-checklist.md) · [Go-live checklist](integration-go-live-checklist.md) |
| Reference | [FAQ](integrators-faq.md) · [Glossary](integrators-glossary.md) · [Reading path](integrators-reading-path.md) |

## Reading paths by role

| You are | Read in this order |
|---|---|
| **Solution architect** deciding how systems connect | [Integration surfaces at a glance](integration-surfaces-at-a-glance.md), [Choosing an integration surface](choosing-an-integration-surface.md), [Push and pull, batch and real time](integration-patterns-push-pull-batch-real-time.md), [Mapping classes to external data](mapping-content-classes-to-external-data.md), [Security](security-for-integrations.md) |
| **Integration developer** writing the code | [CLI scripts and ezexec](cli-scripts-and-ezexec.md), [Idempotent imports](idempotent-imports-keyed-by-remote-id.md), [Writing an import](writing-an-import-script.md), [Workflows and triggers](workflows-and-triggers-as-hooks.md), [Error handling and logging](error-handling-and-logging.md), [Testing](testing-integrations.md), [An integration as an extension](building-an-integration-as-an-extension.md) |
| **Developer of an external app** calling Exponential over HTTP | [Choosing credentials](choosing-credentials-for-an-integration.md), [REST API](rest-api-for-integrators.md), [Personal API keys](personal-api-keys-for-integrations.md), [Remote services with expservices](remote-services-with-expservices.md) |
| **Identity and security engineer** | [Login handlers](login-handlers-ldap-and-textfile.md), [Single sign-on](single-sign-on-and-social-login.md), [Service accounts and roles](service-accounts-roles-and-policies.md), [Security review checklist](integration-security-checklist.md) |
| **Operations engineer** running the integration | [Cronjobs](cronjobs-for-integrations.md), [Monitoring](monitoring-integrations.md), [Caching after imports](caching-after-imports.md), [Velocity and performance](velocity-and-performance-for-integrations.md), [Upgrades](upgrades-without-breaking-integrations.md) |
| **Project lead or account manager** at an integration company | [Project playbook](integration-project-playbook.md), [Discovery worksheet](integration-discovery-worksheet.md), [Estimating](estimating-integration-work.md), [Statements of work](statements-of-work-for-integrations.md), [Handover](handover-of-an-integration.md), [Integrators and the partner program](integrators-and-the-partner-program.md) |

The whole section in one order is on [Integrators' reading path](integrators-reading-path.md).

## Your first week on an integration project

| Day | Do this | Page |
|---|---|---|
| 1 | Fill in the discovery worksheet with the customer: systems, data, direction, volume, timing | [Integration discovery worksheet](integration-discovery-worksheet.md) |
| 1 | Check which surfaces the installation has active (`ActiveExtensions`, `rest.ini`, `soap.ini`) | [Integration surfaces at a glance](integration-surfaces-at-a-glance.md) |
| 2 | Pick a surface and a credential per data flow, and write them down | [Choosing an integration surface](choosing-an-integration-surface.md), [Choosing credentials](choosing-credentials-for-an-integration.md) |
| 2 | Map each external record to a content class and a `remote_id` scheme | [Mapping classes to external data](mapping-content-classes-to-external-data.md) |
| 3 | Set up a development installation from Git and a service account with a narrow role | [Develop with Git](../develop-with-git.md), [Service accounts and roles](service-accounts-roles-and-policies.md) |
| 4 | Build a first import or call end to end, with a dry run | [Writing an import](writing-an-import-script.md) |
| 5 | Write the interface specification and agree on error handling and monitoring | [Interface specification template](interface-specification-template.md), [Monitoring](monitoring-integrations.md) |

## Who to ask

| Question | Where |
|---|---|
| Technical questions about the APIs and the kernel | The forums <https://share.exponential.earth/forums> and Telegram <https://t.me/exponentialcms> |
| Paid support, development or a review of your integration by 7x | <https://se7enx.com/contact> |
| The partner program, levels, referrals | The partner team: <partners@exponential.earth>, or the form at <https://exponential.earth/partner/contact-us> |
| A security problem you found while integrating | security@se7enx.com, privately, never in a public issue |
| A bug or a missing feature in an extension | The extension's repository on <https://github.com/se7enxweb> ([Writing good issues](../extension-development/part-5-extend-fork-contribute/writing-good-issues.md)) |

## Related pages

- [Develop with Git: grow from user to developer](../develop-with-git.md)
- [Extension development: the book (start here)](../extension-development/README.md)
- [Partners: start here](../partners/partners-start-here.md) and [Implementation partners](../partners/implementation-partners.md)
- [Resellers: start here](../resellers/resellers-start-here.md), [Bundlers: start here](../bundlers/bundlers-start-here.md)
- [App creators: start here](../creators/app-creators/app-creators-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
