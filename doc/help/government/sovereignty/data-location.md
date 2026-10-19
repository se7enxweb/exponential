---
title: Data location
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Where an Exponential installation keeps data (database, storage files, caches, logs, audit records, backups, mail, search index) so a public body can state its data location, and which optional features send data elsewhere.
keywords: data location, data residency, where is the data, storage, var directory, database, logs, audit, backups, Solr, mail, third party, sovereignty
type: help
---
# Data location

This page is for administrators, data protection officers and managers who must state where the body's data is
stored and processed. Exponential keeps everything on the servers and services you choose; this list helps you
account for every place.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Where an installation keeps data](#where-an-installation-keeps-data)
2. [Data that leaves only when you choose](#data-that-leaves-only-when-you-choose)
3. [Writing the statement](#writing-the-statement)

## Where an installation keeps data

| Data | Location | Notes |
|---|---|---|
| Content, users, settings stored in the database | the database server or SQLite file | [Database support](../../database-support.md) |
| Uploaded files and images | `var/<site>/storage/` | can be on shared or mounted storage |
| Caches (pages, templates, INI) | `var/<site>/cache/`, or another path | can be moved per site ([Logs, cache and expiry per site](../../../features/6.0/multi-site-log-and-cache-paths.md)) |
| Logs | `var/log/` or per site | rotation and masking in [Log rotation](../../../features/6.0/log-rotation.md), [Log viewer](../../../features/6.0/log-viewer.md) |
| Audit records | `var/<site>/log/audit/`, plus an index in the database | sinks can copy them to syslog or a webhook you choose ([Audit trail](../../../features/6.0/audit-trail.md)) |
| Sessions | the database or the session store configured | [Sessions](../../../guides/sessions.md) |
| Search index | the database (built-in engine) or the Solr server | [Site search](../../creators/website-creators/site-search.md) |
| Mail | your mail server; the file transport writes mail to files in the installation | [Mail delivery](../../hosting/operations/mail-delivery.md) |
| Backups | wherever your backup process puts them | often the forgotten copy |
| Exports | files written by exports and jobs | delete them when done |

## Data that leaves only when you choose

| Feature | Where data goes |
|---|---|
| Analytics, CAPTCHA, maps, social sign-in extensions | their providers (see "Third-party services to review") |
| AI assistant (`expai`) | the provider configured; region recorded per provider; local models possible |
| CDN or HTTP cache in front of the site | the CDN's locations ([HTTP caching and CDNs](../../creators/website-creators/http-caching-and-cdns.md)) |
| Audit sinks | the syslog or webhook receiver you configure |
| Scheduled exports (`xrowextract`) | the destinations you set (SFTP, S3, WebDAV, ...) |

## Writing the statement

1. List every server and service with its operator and country: web, database, storage, Solr, mail, backups, CDN.
2. List the active extensions that contact third parties (**Setup > Extensions**).
3. Note the retention per place: audit (90 days live, 730 archived by default), logs, backups, exports.
4. Review it when hosting, extensions or contracts change.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Government cloud and hosting models](government-cloud-and-hosting-models.md)
- [Storage layout](../../hosting/operations/storage-layout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
