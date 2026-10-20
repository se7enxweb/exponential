---
title: Transparency in Exponential at a glance
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: One overview of the Exponential features a public body uses for transparency and freedom of information work (publishing, request forms, approval, version history, audit trail, exports), with what each does and the gaps to plan for.
keywords: transparency, freedom of information, FOI, FOIA, access to information, proactive disclosure, disclosure log, audit trail, version history, accountability, overview
type: help
---
# Transparency in Exponential at a glance

This page is for managers, records officers and administrators who use a website built with Exponential to publish
information proactively and to answer requests for information. It maps the usual transparency tasks to product
features and names the documentation behind each one. The other pages of this section go into each task.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Publishing information](#publishing-information)
2. [Receiving and answering requests](#receiving-and-answering-requests)
3. [Accountability](#accountability)
4. [Gaps to plan for](#gaps-to-plan-for)

## Publishing information

| Task | Feature | Documentation |
|---|---|---|
| A publication scheme or "information we publish" page | Folders and pages in the content tree | [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md) |
| Documents for download | File objects, downloads checked by `content/download` | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Registers (decisions, contracts, spending) | A content class of your own per register | [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md) |
| One PDF of a section | Setup > PDF export | [PDF exports](../../../guides/pdf-exports.md) |
| Publish at a set time | Workflow event *Wait until date* | [Workflows](../../../guides/workflows.md) |
| Review before publishing | Workflow event *Approve* | [Workflows](../../../guides/workflows.md) |
| Feeds of new publications | Setup > RSS | [RSS feeds](../../../guides/rss-feeds.md) |
| Bulk lists as CSV, JSON, XML | `xrowextract` Export | [xrowextract](../../../features/6.0/extensions/xrowextract.md) |

## Receiving and answering requests

| Task | Feature | Documentation |
|---|---|---|
| A request form | Information collector class and object | [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md) |
| Notify the team of a request | Mail to a receiver per form | same page, "Mail to a receiver" |
| Read and export requests | Setup > Collected information, CSV export | [Collected information](../../../guides/collected-information.md) |
| Spam protection | Form token, optional CAPTCHA extensions | [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md) |
| Publish a response | A page with the redacted documents | see "Publishing a FOI response" |

## Accountability

| Question | Feature | Documentation |
|---|---|---|
| What did the page say last month? | Versions, **Compare two versions** | [Content history](../../../guides/content-history.md) |
| Who changed or removed it? | Audit trail, on by default, hash-chained | [Audit trail](../../../features/6.0/audit-trail.md) |
| Who may change what? | Roles, sections, object states | [Roles and policies](../../../guides/roles-and-policies.md) |
| Was the record altered? | `./console exp:audit verify` | [Audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes) |

## Gaps to plan for

- **No case management.** Exponential stores form submissions; it has no request tracking with deadlines,
  assignments, clocks or reminders. Use a case system or a register you keep as content.
- **No redaction tool.** Redacting documents is a manual editorial step in the program that made the document;
  Exponential publishes the file you upload (see "Redaction is a manual step").
- **No records management certification.** Versions and the audit trail help; they do not make the site a records
  system in the sense of ISO 15489-1 ([ISO 15489-1:2016](https://www.iso.org/standard/62542.html)).
- **Version limits.** Old versions are removed beyond `content.ini [VersionManagement]` limits (see "Keeping
  versions longer").

## Related pages

- [Collected information](../../../guides/collected-information.md)
- [Content history](../../../guides/content-history.md)
- [Audit trail](../../../features/6.0/audit-trail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
