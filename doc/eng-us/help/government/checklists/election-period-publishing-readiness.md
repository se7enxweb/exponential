---
title: Election period publishing readiness
audience: government, editor, admin, project-manager
category: public-sector
order: 200
summary: Common practice for public bodies that restrict or review publishing during an election period, and how Exponential's roles, sections, object states, approval workflows and the audit trail can support those rules - as practice, not as a legal requirement.
keywords: election period, pre-election, purdah, caretaker period, publishing restrictions, approval, object states, workflows, roles, audit trail, neutrality, public sector
type: help
---
# Election period publishing readiness

This checklist is for communications leads, editors and site administrators of public bodies that apply special
publishing rules before an election (in some countries called a pre-election or caretaker period). The rules
themselves come from your jurisdiction and your organization; this page shows how Exponential's features can help you
follow them. It describes practice, not obligations.

> **Note** This page describes features and common practice; it is not legal advice. Check the law and guidance
> that apply to your organization with your legal counsel.

## Contents

1. [Know your rules](#know-your-rules)
2. [Permissions](#permissions)
3. [Review before publishing](#review-before-publishing)
4. [Existing content](#existing-content)
5. [Records of what was published](#records-of-what-was-published)
6. [After the period](#after-the-period)

## Know your rules

- [ ] The dates of the period and the rules that apply, in writing, from your legal or governance team.
- [ ] Which content is affected (for example announcements, campaigns, profiles of elected officials) and which is not (statutory notices, emergency information).
- [ ] Who decides on borderline cases.
- [ ] Editors briefed before the period starts.

## Permissions

- [ ] Sections or subtrees for affected content identified ([Sections](../../../guides/sections.md)).
- [ ] For the period, a role that cannot publish in those sections, or that can only create drafts there ([Roles and policies](../../../guides/roles-and-policies.md)).
- [ ] An object state such as "on hold for election period", with permissions that depend on it ([Object states](../../../guides/object-states.md)).
- [ ] The changes to roles written down so they can be reversed exactly afterwards.

## Review before publishing

- [ ] An approval workflow for affected sections: workflow with an Approve event and a trigger before publishing ([Workflows](../../../guides/workflows.md#3-worked-example-approval-before-publishing)).
- [ ] Approvers named and trained to check the rules ([Editors 201, Module 5](../../trainers/editors-201/module-05-approvals-and-collaboration/README.md)).
- [ ] The workflow cronjob runs, so approved items are published ([Workflows](../../../guides/workflows.md#34-make-sure-the-workflow-cronjob-runs)).
- [ ] Emergency information exempted by a separate path ([Emergency alert readiness](emergency-alert-readiness.md)).

## Existing content

- [ ] Content that must be hidden during the period identified; hide rather than delete, so it can return unchanged.
- [ ] Newsletters and feeds checked: scheduled sends paused if they contain affected content ([Sending newsletters](../../creators/website-creators/sending-newsletters.md)).
- [ ] Shared blocks (header, home page) reviewed ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)).

## Records of what was published

- [ ] The audit trail records publishing and permission changes; know how to search it ([Security and audit](../../../guides/security-and-audit.md)).
- [ ] Versions show what each page said and when ([Versions of an object](../../../guides/content-history.md)).
- [ ] Decisions on borderline cases recorded outside the site.

## After the period

- [ ] Roles and workflows restored as written down.
- [ ] Hidden content made visible again after review.
- [ ] Content updated for the outcome (for example new officials) ([Editorial quality](editorial-quality.md)).
- [ ] Lessons recorded for the next period.

## Related pages

- [Government checklists](README.md)
- [Sections and object states](../../creators/website-creators/sections-and-object-states.md)
- [Quarterly access review](quarterly-access-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
