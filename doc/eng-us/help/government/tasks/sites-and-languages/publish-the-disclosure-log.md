---
title: Publish the disclosure log
audience: government, website-creator, editor
category: public-sector
order: 260
summary: Task for site builders and editors: set up the public disclosure log (a class for entries without requester data, a folder per year, a list view, a feed) and publish the first entry.
keywords: disclosure log, FOI, released information, log entry class, year folders, RSS, transparency
type: help
---
# Publish the disclosure log

This task is for site builders and editors. Background: [Disclosure logs](../../transparency-and-foi/disclosure-logs.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Steps

1. **Setup > Classes**: create the class `disclosure_entry` with reference, title, date of response, subject, outcome,
   summary of the request, response and grounds; make it a container; object name pattern `<reference> <title>`.
2. Create the folder "Disclosure log" and a folder for the current year below it.
3. Give the class a line view showing reference, title, date and outcome (template override in your design).
4. **Setup > RSS**: an export of the log folder with subnodes (see "Publish a feed of new datasets" for the steps).
5. Publish the first entry (see "Publish a FOI response with redactions checked").

## Check

The log page lists entries newest first; no entry shows a requester's name or contact; the feed lists the entry.

## Problems

| Problem | Fix |
|---|---|
| Entries are not sorted by date | set the folder's sorting, or sort in the template by the date field |
| Visitors cannot open the log | the folder's section is not readable by Anonymous |

## Related pages

- [Proactive disclosure](../../transparency-and-foi/proactive-disclosure.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
