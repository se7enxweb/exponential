---
title: OAIS and handing content to an archive
audience: government, project-manager, admin
category: public-sector
order: 180
summary: For public bodies that transfer website content to a digital archive - the OAIS reference model (ISO 14721) framed generally, where Exponential sits (a producer, not an archive), and what a submission from Exponential can contain (content, metadata, files, fixity information, audit evidence).
keywords: OAIS, ISO 14721, open archival information system, digital preservation, submission information package, SIP, producer, archive, transfer, metadata, fixity, checksums, export, public sector, national archives
type: help
---
# OAIS and handing content to an archive

This page is for records managers and project managers who prepare transfers from a website to a digital archive, such
as a national or regional archive. The Open Archival Information System (OAIS) reference model, ISO 14721:2012
(<https://www.iso.org/standard/57284.html>), is the common vocabulary of such archives. This page frames it generally
and does not quote the standard.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Roles in the model](#roles-in-the-model)
2. [What a submission from Exponential can contain](#what-a-submission-from-exponential-can-contain)
3. [Preparing a transfer](#preparing-a-transfer)
4. [Related pages](#related-pages)

## Roles in the model

In general terms OAIS distinguishes the **producer** that submits information, the **archive** that preserves it and the
**consumers** who use it later. A website run with Exponential is on the producer side: it is not an archive and makes no
OAIS claim. Your archive's submission agreement says what it accepts.

## What a submission from Exponential can contain

| Part | Source in Exponential |
|---|---|
| Content | the objects: through the REST API (JSON), packages (`.ezpkg`), or the PDF export (human-readable) |
| Files | the uploaded files of file and image fields, from `var/storage` or through downloads |
| Descriptive metadata | the fields of your classes (title, dates, author fields), section, object states, locations in the tree |
| Provenance | versions with creators and dates; audit records (who published, if `content.object.publish` was enabled) |
| Fixity | checksums you compute on export; the audit export prints a sha256; audit archives carry signed manifests |
| Context | the class definitions (Setup > Classes) to explain the fields |

## Preparing a transfer

1. Agree the formats with the archive (see "File formats for preservation").
2. Decide what goes: by class, section, state or subtree, from your retention schedule.
3. Export with the chosen route and compute checksums for every file.
4. Add the metadata the archive requires; map your class fields to its schema.
5. Include an export log: who, when, which version of each object, which tool and settings.
6. After the archive confirms receipt, apply your schedule on the site (archive folder, hide, or removal).

## Related pages

- [Exporting content](exporting-content.md)
- [Records management and a web CMS](records-management-framing.md)
- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
