---
title: Records management and a web CMS
audience: government, project-manager, editor, admin
category: public-sector
order: 20
summary: For records managers of public bodies - how a website run with Exponential relates to records management practice (ISO 15489-1 framed generally), which website content and system data may be records, what Exponential keeps on its own and what belongs in your records system.
keywords: records management, ISO 15489, records, web records, record keeping, archives, retention, authenticity, integrity, reliability, usability, versions, audit trail, records system, public sector
type: help
---
# Records management and a web CMS

This page is for records managers, archivists and project managers of public bodies. A website publishes official
information; some of it, and some of the system's own data, may be records your organization must keep. Exponential
is a publishing system, not a records management system; it has features that help, and it hands records to your
records system where needed.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The framing](#the-framing)
2. [What may be a record](#what-may-be-a-record)
3. [What Exponential keeps by itself](#what-exponential-keeps-by-itself)
4. [What it does not do](#what-it-does-not-do)
5. [Related pages](#related-pages)

## The framing

ISO 15489-1:2016, "Records management - Concepts and principles" (<https://www.iso.org/standard/62542.html>), is the
international reference for records management. In general terms it describes records as evidence of business
activity, with characteristics such as authenticity, reliability, integrity and usability, kept under controls for
their whole life. This page does not quote the standard; it relates its general ideas to a website.

## What may be a record

| Item | Why it may be a record | Where it lives in Exponential |
|---|---|---|
| Published notices, decisions, announcements | the official publication of a decision | content objects and their versions |
| The state of a page at a date | what the public was told, and when | versions (within the version history limit) |
| Form submissions (requests, complaints) | the start of a case | information collections |
| Consent records for mail | proof of consent | consent log |
| Who changed what in the system | accountability | audit trail |

Your records schedule decides; this list helps you look in the right places.

## What Exponential keeps by itself

| Characteristic (general) | Feature |
|---|---|
| Integrity of system records | hash-chained audit trail, signed checkpoints and archive manifests |
| History of content | versions with creator and dates; compare; new drafts from old versions |
| Context | sections, object states, locations in the tree; metadata fields of your classes |
| Stable references | URL aliases; old aliases redirect after a rename or move |
| Controlled change | roles and policies; approval workflows; the trash |

## What it does not do

- It has **no records management functions**: no declaration of records, no disposition authority, no records
  classification scheme of its own, no certified archive.
- **Versions are not kept forever**: the oldest archived version is removed when an object reaches the version history
  limit (45 by default). See "The version history limit".
- **Audit records are removed** at the end of their retention (730 days archived by default) unless you extend it.

Plan the hand-over: export what is a record to your records system or archive (see "Exporting content").

## Related pages

- [Versions of an object](../../../guides/content-history.md)
- [Audit trail](../../../features/6.0/audit-trail.md)
- [Object states](../../../guides/object-states.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
