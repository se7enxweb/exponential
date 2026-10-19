---
title: Publishing a FOI response
audience: government, editor
category: public-sector
order: 90
summary: Step by step for editors: turn an answered request for information into a public disclosure log entry with its redacted documents, have it approved, check it as a visitor, and link it from related pages.
keywords: FOI response, publish response, disclosure log entry, released documents, redaction check, approval, freedom of information, FOIA, transparency
type: help
---
# Publishing a FOI response

This page is for editors who publish an answered request for information on the website. It assumes a disclosure
log exists (see "Disclosure logs") and the documents have been redacted and checked outside Exponential (see
"Redaction is a manual step").

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Create the entry](#create-the-entry)
3. [Attach the released documents](#attach-the-released-documents)
4. [Approval and publication](#approval-and-publication)
5. [Check as a visitor](#check-as-a-visitor)
6. [Afterwards](#afterwards)

## Before you start

- The response was sent to the requester; the decision to publish it in the log was taken.
- The redaction check of every file is recorded in the case.
- The summary of the request contains nothing that identifies the requester.

## Create the entry

1. In **Content structure**, open the disclosure log and the folder of the current year.
2. Under **Sub items**, press **Create new** and choose the log entry class.
3. Fill in reference, title, date of response, subject, outcome, summary of the request, response and grounds.
4. Press **Store draft and exit** if the documents are not ready yet; the draft is not public.

## Attach the released documents

1. Open the entry; under **Sub items** press **Create new** and choose **File**.
2. Give a descriptive name ("Released correspondence, redacted") and upload the checked file.
3. Repeat per document. Several at once: **Media library > Upload files**, then move them below the entry.

Tell visitors format and size near each link; the file datatype provides both
([Files and downloads](../../creators/website-creators/files-and-downloads.md#download-links-in-templates)).

## Approval and publication

Press **Send for publishing**. With an approval workflow on the transparency section, the version waits as
*pending* until an approver presses **Approve** in **Collaboration**; the workflow cronjob then publishes it
([Workflows](../../../guides/workflows.md)). If publication must wait for a date, the *Wait until date* event does
that (see "Scheduled publication and embargoes").

## Check as a visitor

1. Open the entry in a private browser window, signed out.
2. Open every file; search each for the redacted terms once more.
3. Check the entry appears in the log list and, after the cache time, in the log's feed.

## Afterwards

- Link the entry from related pages (the policy page, the news item) with an object relation or embed, so the link
  follows the entry if it moves.
- If a mistake is found later, correct it as described in "Withdrawing and correcting published information".

## Related pages

- [Disclosure logs](disclosure-logs.md)
- [Approval before publishing](approval-before-publishing.md)
- [Editing content](../../editing-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
