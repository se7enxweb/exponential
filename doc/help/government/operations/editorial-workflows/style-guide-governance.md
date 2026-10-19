---
title: Style guide governance
audience: government, editor, project-manager, designer
category: public-sector
order: 130
summary: How a public body keeps its editorial and design style guide effective on an Exponential site - what the system enforces (classes, fields, the rich text editor, templates, approvals) and what the style guide and its owners must enforce themselves.
keywords: style guide, editorial standards, plain language, content design, governance, content classes, rich text, templates, accessibility, approval, training, web standards
type: help
---
# Style guide governance

This page is for the owners of a public body's editorial and design style guide. A style guide is only as good as
its enforcement; this page separates what Exponential can enforce through its structure from what people and
reviews must enforce. Plain-language guidance for public bodies is published, for example, at
[plainlanguage.gov](https://www.plainlanguage.gov/); accessibility rules are in [WCAG 2.2](https://www.w3.org/TR/WCAG22/).

## Contents
1. [What the system enforces](#what-the-system-enforces)
2. [What people enforce](#what-people-enforce)
3. [Who owns what](#who-owns-what)
4. [Changing the style guide](#changing-the-style-guide)
5. [Related pages](#related-pages)

## What the system enforces

| Style rule | Mechanism | Who changes it |
|---|---|---|
| Every page has a title, a summary, a contact | required fields on the content class | site builders, through change control |
| Only approved content types | the classes editors may create (`content/create` with a `Class` limitation) | administrators |
| Consistent layout and typography | templates and design, not editor formatting | designers and developers |
| Limited formatting in text | the configuration of the rich text editor | developers |
| Image alternative text | a field of the image class; required if you make it so | site builders |
| Every page reviewed | the approval workflow ([Approval workflows overview](approval-workflows-overview.md)) | administrators |

Changes to classes, templates and editor settings are configuration and code; on a live site with
[ManagedByDeployment](../upgrades-and-change/managed-by-deployment.md) they come through version control only, which
keeps the style decisions under change control.

The public designs shipped with Exponential have not been audited for accessibility
([Accessibility standard](../../../../standards/accessibility.md)); a style guide that requires WCAG conformance
needs its own check of your design.

## What people enforce

- Plain language, tone, reading level and terminology.
- Accurate, current information and named ownership ([Content ownership](content-ownership.md)).
- Meaningful link texts, headings in order, tables used for data only.
- Alternative texts that describe the image's purpose.

Approvers are the natural checkpoint: a style checklist next to the approval request helps them decide
consistently, and a **Deny** comment that names the rule teaches the author.

## Who owns what

| Role | Owns |
|---|---|
| Style guide owner (a function, for example the head of digital communications) | the text of the guide, exceptions |
| Web team | classes, required fields, editor settings proposals |
| Approvers | applying the guide before publication |
| Trainers | onboarding of new editors ([Editors 101 course](../../../trainers/editors-101/README.md)) |

## Changing the style guide

1. Propose the change with its reason and the content it affects.
2. If it changes a class, a template or the editor, raise a change for your change board
   ([Change management and advisory boards](../upgrades-and-change/change-management-and-advisory-boards.md)).
3. Tell approvers and editors; update the checklist.
4. Bring existing content in line on the next review cycle ([Review dates with object states](review-dates-with-object-states.md)).

## Related pages
- [Editorial calendar](editorial-calendar.md)
- [Legal review step](legal-review-step.md)
- [Accessibility standard](../../../../standards/accessibility.md)
- [Documentation and publishing standard](../../../../standards/documentation-and-publishing.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
