---
title: Transparency checklist
audience: government, project-manager, admin, editor
category: public-sector
order: 210
summary: A checklist for running transparency and freedom of information publishing on Exponential, from structure and roles to request handling, accountability settings and yearly review, linking the page that explains each item.
keywords: checklist, transparency, freedom of information, FOI, disclosure log, publication scheme, audit, versions, review, request form
type: help
---
# Transparency checklist

This checklist is for the people who run transparency publishing. Review it at launch and once a year. Each item
names the page of this section that explains it.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Structure and roles](#structure-and-roles)
2. [Requests](#requests)
3. [Publishing](#publishing)
4. [Accountability settings](#accountability-settings)
5. [Yearly review](#yearly-review)

## Structure and roles

- [ ] Categories of proactive publication, owners and frequencies decided (see "Proactive disclosure").
- [ ] Publication scheme published and current (see "Publication schemes").
- [ ] One section per owning team; roles limited to their sections.
- [ ] Approval workflow on record-relevant sections; workflow cronjob scheduled (see "Approval before publishing").

## Requests

- [ ] A request form with minimal fields, a privacy text and spam protection (see "A request form with the
  information collector").
- [ ] `infocollector/read` only for the request team (see "Handling requests from collected information").
- [ ] Submissions moved into the case process and removed from the website per retention rule.
- [ ] Deadlines tracked outside Exponential, which has no case management.

## Publishing

- [ ] Disclosure log without requester data (see "Disclosure logs").
- [ ] Redaction done outside Exponential and checked by a second person (see "Redaction is a manual step").
- [ ] Meetings, decisions, spending and contracts published in agreed structures (see their pages).
- [ ] Short content as page text; tables also as CSV (see "PDF and document formats for disclosures").
- [ ] Release times handled with *Wait until date*; never the unpublish cronjob for records (see "Scheduled
  publication and embargoes").

## Accountability settings

- [ ] Version limits raised for record-relevant classes (see "Keeping versions longer").
- [ ] `content/versionremove` and `content/remove` restricted for record-relevant sections.
- [ ] Audit trail on, retention matched to the records schedule, `exp:audit verify` run regularly (see "The audit
  trail for who changed what").
- [ ] Audit keys in `settings/override/audit.ini.append.php` backed up.

## Yearly review

- [ ] Search statistics checked for unmet needs (see "Search and findability of disclosures").
- [ ] Link management checked for broken links.
- [ ] Roles and group memberships reviewed; leavers removed.
- [ ] Corrections and withdrawals reviewed for visible notes (see "Withdrawing and correcting published
  information").

## Related pages

- [Transparency in Exponential at a glance](transparency-in-exponential-at-a-glance.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
