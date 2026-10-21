---
title: What integrators do with Exponential
audience: integrator, developer, project-manager
category: integration
order: 10
summary: The typical integration jobs around an Exponential site (CRM, ERP, PIM, DAM, identity, search, mail, payment, analytics, other CMSs), what Exponential plays in each, and which surface usually carries the data.
keywords: integrator, integration jobs, CRM, ERP, PIM, DAM, identity provider, LDAP, search, Solr, mail, newsletter, payment, analytics, CMS migration, headless, content hub
type: help
---
# What integrators do with Exponential

This page is for integrators who are new to Exponential and want to know what kind of work to expect. It lists the
systems that are usually connected to a content management system, says which role Exponential plays in each
connection, and names the surface that normally carries the data. The details of each surface are on its own page.

## Contents

1. [Exponential's place in a system landscape](#exponentials-place-in-a-system-landscape)
2. [Typical integration jobs](#typical-integration-jobs)
3. [Who is the master of which data](#who-is-the-master-of-which-data)
4. [What an integrator delivers](#what-an-integrator-delivers)

## Exponential's place in a system landscape

Exponential is a content management system with a content repository (classes, objects, versions, locations in a
tree), users and roles, a template engine, a shop module, forms that collect information, and an administration.
In a landscape of business systems it usually takes one or more of these roles:

| Role | What it means | Typical surfaces |
|---|---|---|
| **Publisher** | Receives data from other systems and publishes it as pages | PHP import scripts with `eZContentFunctions`, the REST create route, `xrowextract` imports, RSS imports |
| **Content hub** | Is the master of editorial content that other channels read | The REST content API, `expservices`, RSS and Atom exports, `syndication` |
| **Collector** | Gathers form answers, registrations and orders that other systems process | Information collection, the shop, notification and audit hooks, CSV exports |
| **Relying party** | Lets people sign in with an account that lives elsewhere | Login handlers (LDAP, textfile, your own), single sign-on handlers, social login |
| **Consumer of services** | Uses a mail server, a search server, a payment provider, an AI provider | `[MailSettings]`, a search plugin, payment gateways, `expai` |

## Typical integration jobs

| System | Typical job | Direction | Usual surface | Page |
|---|---|---|---|---|
| CRM | Leads from contact forms go to the CRM; account data shows on member pages | Out, sometimes in | Workflow event or cronjob reading collected information; a login or SSO handler for members | [Forms and collected information](forms-and-collected-information.md) |
| ERP | Product data and prices come in; orders go out | Both | Import script keyed by `remote_id`; shop triggers (`shop / checkout`, `confirmorder`) | [Payment gateways and the shop](payment-gateways-and-the-shop.md) |
| PIM | Product pages are created and updated from the PIM | In | Scheduled import (cronjob part) or `xrowextract` schedule | [Writing an import script](writing-an-import-script.md) |
| DAM | Images and files arrive from the asset system | In | Import with image and file attributes given by path (`storage_dir`) | [Mapping classes to external data](mapping-content-classes-to-external-data.md) |
| Identity provider | Staff or members sign in with their central account | In (identity) | `LoginHandler[]` (LDAP or your own), `SingleSignOnHandlerArray[]` | [Login handlers](login-handlers-ldap-and-textfile.md) |
| Search server | Site search with facets and relevance | Out (index) | A search engine plugin (`[SearchSettings] SearchEngine`), `ezfind` for Solr | [Search engine integration](search-engine-integration.md) |
| Mail and newsletter | Transactional mail through a provider; newsletters | Out | `[MailSettings] Transport` or your own transport class; `cjw_newsletter` | [Mail and notifications](mail-and-notifications-integration.md) |
| Payment provider | Payments at checkout | Both | A payment gateway (`paymentgateways.ini`), `ezpaypal` | [Payment gateways and the shop](payment-gateways-and-the-shop.md) |
| Analytics | Page tracking, statistics | Out | Templates (tracking snippet, mind the Content Security Policy); `bcwebsitestatistics` | [Security for integrations](security-for-integrations.md) |
| Another CMS | Migrate content in, or exchange content both ways | In or both | Import scripts, packages, `syndication`, RSS | [Migrating content from another CMS](migrating-content-from-another-cms.md) |
| Mobile or JavaScript app | Reads content, sometimes publishes | Both | REST API with OAuth or API keys; `expservices` | [REST API for integrators](rest-api-for-integrators.md) |
| Monitoring and SIEM | Security and change events into a log platform | Out | Audit trail sinks: syslog, signed webhook, mail | [Outgoing webhooks from the audit trail](outgoing-webhooks-from-the-audit-trail.md) |

> **Note**
> The "usual surface" column is a starting point, not a rule. A daily product import of 50 000 records is a
> server-side script, even though the REST API could create the same objects one by one.
> [Choosing an integration surface](choosing-an-integration-surface.md) explains the trade-offs.

## Who is the master of which data

Every integration needs one clear answer per kind of data: which system is the **master** (the source of truth), and
which systems hold **copies**. Write it down before writing code.

| Question | Why it matters in Exponential |
|---|---|
| Is the external system or Exponential the master of this record? | If the external system is the master, editors must not change imported fields, or their changes are overwritten by the next run. Mark such fields in the class description or restrict editing with roles. |
| What identifies the record in both systems? | Use the external ID in the object's `remote_id` (unique, up to 100 characters), not the Exponential object ID, which differs between installations. |
| What happens when the record is deleted at the master? | Decide between removing to the trash, hiding, or keeping with a state; each has different consequences for URLs and search. |
| Who may publish? | An import publishes through the publish operation; if an approval workflow is on that trigger, imported versions wait for approval. |

## What an integrator delivers

A complete integration is more than the code that moves data:

- [ ] An interface specification per data flow ([template](interface-specification-template.md)).
- [ ] The integration as an extension in its own Git repository ([how](building-an-integration-as-an-extension.md)).
- [ ] A service account with a narrow role ([how](service-accounts-roles-and-policies.md)).
- [ ] Credentials kept out of Git, in `settings/override` or the environment ([how](security-for-integrations.md)).
- [ ] A dry-run mode, logging and a monitoring check ([how](monitoring-integrations.md)).
- [ ] Tests that run without the external system ([how](testing-integrations.md)).
- [ ] A runbook and a handover to the people who operate it ([how](handover-of-an-integration.md)).

## Related pages

- [Integrators: start here](integrators-start-here.md)
- [Integration surfaces at a glance](integration-surfaces-at-a-glance.md)
- [Implementation partners](../partners/implementation-partners.md)
- [Headless Exponential](../creators/app-creators/headless-exponential.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
