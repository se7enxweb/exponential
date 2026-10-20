---
title: Intranet checklist
audience: government, admin, project-manager
category: public-sector
order: 90
summary: A launch and review checklist for a staff intranet built with Exponential: access, sections and roles, sign-in, sessions, network, search engines, audit, leavers and content ownership.
keywords: intranet checklist, launch, review, access control, sign-in, sessions, leavers, audit, internal content, security
type: help
---
# Intranet checklist

Use this checklist before an intranet goes live and twice a year after. Each item names the page of this section that
explains it.

## Contents

1. [Access](#access)
2. [Sign-in](#sign-in)
3. [Operations](#operations)
4. [Content](#content)

## Access

- [ ] The intranet siteaccess requires sign-in; `user/register` is not open there (see "A staff-only siteaccess").
- [ ] The intranet host is reachable only from the internal network or a VPN, if your policy says so.
- [ ] Internal content is in sections Anonymous cannot read, tested signed out (see "Sections and roles for intranet
  content").
- [ ] Restricted material in its own section and group.

## Sign-in

- [ ] The login handlers chosen and tested; LDAP tested against the real directory (see "LDAP sign-in").
- [ ] Two-step sign-in required for staff with administrative rights (see "Two-step sign-in for staff").
- [ ] Session timeout fits the content (see "Sessions, sign-out and shared computers").
- [ ] A lockout or alerting decision for failed sign-ins.

## Operations

- [ ] Leavers: directory account disabled and Exponential account disabled the same day; **Users > Check user
  accounts** reviewed monthly for accounts not seen in a long time.
- [ ] The audit trail on and read by someone ([Audit trail](../../../../features/6.0/audit-trail.md)).
- [ ] Backups include the intranet's content and files.

## Content

- [ ] Every intranet section has an owner and a review date.
- [ ] No personal data beyond what staff need; HR data stays in HR systems.
- [ ] Search works for staff, and internal pages do not appear in the public site's search.

## Related pages

- [Staff intranets with Exponential](staff-intranets-with-exponential.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
