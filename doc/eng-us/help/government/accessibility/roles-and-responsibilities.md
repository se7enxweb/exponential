---
title: Accessibility roles and responsibilities
audience: government, project-manager, admin, editor, trainer
category: public-sector
order: 340
summary: For public bodies organizing accessibility work around an Exponential site - who does what (owner, coordinator, editors, designers, developers, administrators, suppliers, trainers), with a responsibility matrix and the Exponential tools each role uses.
keywords: roles, responsibilities, RACI, accessibility coordinator, editors, designers, developers, administrators, suppliers, trainers, governance, accessibility plan, public sector, training
type: help
---
# Accessibility roles and responsibilities

This page is for the project manager or product owner who organizes accessibility for a public website. Accessibility
fails when it belongs to nobody. The matrix below assigns the recurring tasks to roles and names the Exponential tools
each role works with. Adapt the role names to your organization.

## Contents

1. [The roles](#the-roles)
2. [Responsibility matrix](#responsibility-matrix)
3. [Training](#training)
4. [Related pages](#related-pages)

## The roles

| Role | Typical person | Main tools in Exponential |
|---|---|---|
| Owner | head of digital, product owner | the accessibility statement page, reports |
| Accessibility coordinator | specialist or trained web manager | test tools, Setup > Collected information (feedback), findings table |
| Editors | communication staff, subject experts | edit form, Alternative image text, versions, preview |
| Administrators | web administrators | Setup > Classes ("Alternative text is required"), admin design settings, roles |
| Designers | in-house or supplier | the public design, color tokens, templates |
| Developers | in-house or supplier | templates, overrides, scripts, the `design` test suite for admin extensions |
| Suppliers | agency, hosting partner | as contracted (see "Procurement clauses for accessibility") |
| Trainers | in-house or partner | training courses |

## Responsibility matrix

R = does it, A = accountable, C = consulted.

| Task | Owner | Coordinator | Editors | Admins | Designers | Developers |
|---|---|---|---|---|---|---|
| Set the target (e.g. WCAG 2.2 AA) | A | R | | | C | C |
| Audit the public design | A | R | | | C | C |
| Fix template findings | A | C | | | R | R |
| Write accessible content | | C | R | | | |
| Require alternative text on image fields | | C | | R | | |
| Keep editors on admin4 | | C | | R | | |
| Write and publish the statement | A | R | C | | | |
| Answer feedback | A | R | C | | | C |
| Monthly content spot check | | R | C | | | |
| Test before each release | | C | | | R | R |
| Accessibility in tenders | A | C | | | | |

## Training

- Editors: a short course on alternative text, headings, links, tables and documents, with your own examples. The
  training section of this help has courses for editors ([Course catalogue](../../trainers/course-catalogue.md)).
- Administrators: class settings, design settings, roles.
- Designers and developers: [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
  and [admin4 styling](../../../bc/6.0/admin4-styling.md) for administration extensions.
- Trainers making their own materials accessible: the train-the-trainer course has a module on accessibility and
  inclusion ([Accessible course materials](../../trainers/train-the-trainer/module-05-accessibility-and-inclusion/accessible-course-materials.md)).

## Related pages

- [Accessible content for editors](accessible-content-for-editors.md)
- [What accessibility law asks of public bodies](laws-for-public-bodies.md)
- [Monitoring and feedback](monitoring-and-feedback.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
