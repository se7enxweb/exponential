---
title: Publish a FOI response with redactions checked
audience: government, editor
category: public-sector
order: 270
summary: Task for editors: check the redactions of released documents outside Exponential, then publish the response as a disclosure log entry with its files, have it approved, and verify it signed out.
keywords: FOI response, redaction check, disclosure log, released documents, approval, freedom of information, publish
type: help
---
# Publish a FOI response with redactions checked

This task is for editors. Exponential has no redaction tool: redaction and its check happen in the program that made
the document, before upload. Background: [Redaction is a manual step](../../transparency-and-foi/redaction-is-a-manual-step.md)
and [Publishing a FOI response](../../transparency-and-foi/publishing-a-foi-response.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Steps

1. **Check each file** (outside Exponential, by a second person): select all text and paste it into a plain text
   editor, search for the redacted names and terms; check document properties, comments, hidden content; record the
   check in the case.
2. Rename files neutrally (no personal names: the file name appears in the download address).
3. In the disclosure log's year folder: **Create new**, the entry class; fill in the fields without requester data;
   **Store draft and exit** until the files are ready.
4. Below the entry: **Create new**, **File**, upload each checked file.
5. Open the entry, **Edit**, **Send for publishing**; an approver approves it in **Collaboration** if the workflow is on.

## Check

Signed out, open the entry and each file; search each file for the redacted terms once more.

## Problems

| Problem | Fix |
|---|---|
| An unredacted file was uploaded to a published entry | replace it and publish, then remove the old versions on the **Versions** page (`content/versionremove`), clear caches, tell your data protection officer |
| The entry waits | an approver must act; the workflow cronjob must run |

## Related pages

- [Withdrawing and correcting published information](../../transparency-and-foi/withdrawing-and-correcting-published-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
