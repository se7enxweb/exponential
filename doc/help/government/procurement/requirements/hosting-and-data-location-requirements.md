---
title: Hosting and data location requirements
audience: government, project-manager, hosting, admin
category: public-sector
order: 80
summary: Requirement wording for where and how a public CMS is hosted - on premise or by a supplier, data location, databases, operating systems, outbound connections, backups and recovery - with what Exponential's documentation states about its technical options.
keywords: hosting, data location, data residency, on premise, cloud, sovereignty, database, operating system, backups, RPO, RTO, outbound connections, third parties, tender, requirements
type: help
---
# Hosting and data location requirements

This page is for procurement teams and IT managers who decide where a public website runs and where its data is
kept. Exponential is open source software that you can run on your own servers or have any supplier run, so the
hosting requirements are mostly about the supplier, not the product. This page gives requirement wording and the
technical facts from the installation book and the hosting help that bidders' answers should match.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Hosting options Exponential allows](#hosting-options-exponential-allows)
2. [Requirement wording](#requirement-wording)
3. [Technical facts to check answers against](#technical-facts-to-check-answers-against)
4. [Where personal data and keys live](#where-personal-data-and-keys-live)
5. [Pitfalls](#pitfalls)

## Hosting options Exponential allows

- **Any host that meets the requirements**: PHP 8.0 or later, a supported database and a web server
  ([Requirements](../../../../install/02-requirements.md), [Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md)).
- **Models**: shared hosting, VPS, dedicated servers, containers and Kubernetes are compared in
  [Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md).
- **Your own data center**: nothing in the license or the software ties it to a vendor's cloud. The license is the
  GNU GPL version 2 or later ([Licensing questions](../../../project/licensing-faq.md)).

## Requirement wording

| Id | Requirement |
|---|---|
| HOS-01 | All production data, backups and logs are stored in <country / region>. List every location, including backup and disaster recovery sites. |
| HOS-02 | List every sub-processor with access to the system or its data, and the country it operates from. |
| HOS-03 | List every outbound connection the delivered system makes (package servers, mail relays, search services, third-party APIs) and its purpose. |
| HOS-04 | Backups of database and files are taken together every <interval>, kept <period>, encrypted, and stored separately from production. |
| HOS-05 | The recovery point objective is <time> and the recovery time objective is <time>; a restore test is performed every <interval> and reported. |
| HOS-06 | The operating system, PHP version and database version are within their vendors' security support during the contract. |
| HOS-07 | The administration can be restricted to <networks / VPN>. |
| HOS-08 | The customer can obtain a full copy of database, files and configuration at any time without additional charge. |
| HOS-09 | The site is served over HTTPS only, with certificates renewed automatically. |

Fill the blanks from your own needs; this help does not suggest values.

## Technical facts to check answers against

| Topic | What the documentation says |
|---|---|
| Databases | In the official order of preference: SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Database support](../../../database-support.md)) |
| Web servers | Exponential Velocity, Apache or nginx with PHP-FPM, FrankenPHP ([Serving the site](../../../../install/08-serving-the-site.md), [Choosing a web server](../../../hosting/servers/choosing-a-web-server.md)) |
| Operating systems | Tiers from "tested here" to "not possible today" ([Operating system support](../../../os-support.md)) |
| PHP | 8.0 is the oldest supported; 8.5 the newest tested ([Requirements](../../../../install/02-requirements.md)) |
| Several web nodes | Need a database server and shared storage ([Several web nodes](../../../hosting/operations/several-web-nodes.md)) |
| HTTPS on Velocity | Certificate management including ACME ([Velocity HTTPS certificates](../../../../features/6.0/velocity-https-certificates.md)) |
| Restricting the administration | [Restricting the administration](../../../hosting/security/restricting-the-administration.md) |
| Outbound connections in the product | **Setup > Updates and packages** fetches Composer packages from package servers; mail goes to the configured transport; extensions such as analytics or CAPTCHA contact third parties when activated ([Privacy standards](../../../../standards/privacy.md)) |

## Where personal data and keys live

A bidder's answer to HOS-01 should cover all of these, from
[Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md):

- the database (content, user accounts, form submissions, consent records);
- `var/storage/` and `var/<site>/storage/` (uploaded files);
- `var/<site>/log/audit/` (the audit trail) and `var/log/` (logs);
- `settings/override/` (credentials, the audit signing keys, the e-mail preference secret);
- every backup copy of the above.

## Pitfalls

- Specifying a cloud product by name. Specify the outcome (location, control, recovery); let bidders propose.
- Forgetting the mail relay, the search service and the backup store when you ask about data location.
- Accepting a recovery time that was never tested. Ask for the date and duration of the last restore test
  ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)).

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: hosting and data location clause](../tender-templates/hosting-and-data-location-clause.md)
- [Operations and monitoring requirements](operations-and-monitoring-requirements.md)
- [Hosting companies: start here](../../../hosting/hosting-companies-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
