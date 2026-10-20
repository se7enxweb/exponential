---
title: Content model for courts
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit a court site and recipes for Decision, Court location and Guide classes with real datatypes, with the decision's publication status kept as an object state.
keywords: content model, court, tribunal, decision class, judgment, court location, guide class, case number, object state, anonymized, class editor
type: help
---
# Content model for courts

This page is for website creators and project managers who design the classes of a court or tribunal site. The
classes and datatypes named exist in Exponential ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md),
`kernel/classes/datatypes/`); the class editor is in [Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Decision](#class-to-add-decision)
3. [Publication status as an object state](#publication-status-as-an-object-state)
4. [Class to add: Court location](#class-to-add-court-location)
5. [Class to add: Guide](#class-to-add-guide)
6. [Rules for court content](#rules-for-court-content)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | guidance areas, decision collections by year or area of law |
| Article | `article` | news, explanations |
| File | `file` | forms, rules, practice directions, annual reports |
| Documentation page | `documentation_page` | multi-chapter guidance |
| Link | `link` | e-filing systems, legal aid services |
| Feedback form | `feedback_form` | general enquiries (never case documents) |

## Class to add: Decision

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | the case name as the rules allow it to be published (often initials or a neutral name) |
| Case number | `case_number` | Text line | the citable reference |
| Court or chamber | `chamber` | Selection (`ezselection`) | |
| Date of decision | `decision_date` | Date (`ezdate`) | |
| Area of law | `area` | Tags (`eztags`) | |
| Summary | `summary` | XML block (`ezxmltext`) | a short headnote, also anonymized |
| Decision text | `document` | File (`ezbinaryfile`) | the anonymized document |
| Publish until | `hide_date` | Date and time (`ezdatetime`) | only where rules limit the publication period; used by `hide.php` |

Keep no unanonymized text in any attribute: summaries and titles carry personal data as easily as the document.

## Publication status as an object state

A state group `decision_status` with *Under review*, *Published*, *Corrected*, *Withdrawn*:

| Use | How |
|---|---|
| Show the status on the page | templates read the state |
| Limit who may withdraw | `state/assign` with `NewState` = Withdrawn for the publishing lead only |
| Keep reviewers' rights to content under review | `content/read` limited by `StateGroup_decision_status` |

Setting a state makes no new version and changes no text ([Object states](../../../../guides/object-states.md)).

## Class to add: Court location

| Attribute | Identifier | Datatype |
|---|---|---|
| Name | `name` | Text line |
| Address | `address` | Text block (`eztext`) |
| Opening hours | `opening_hours` | XML block |
| Getting there and accessibility | `access` | XML block (step-free access, hearing loops, quiet rooms) |
| Security screening | `security` | XML block (what may be brought in) |
| Contact | `contact` | XML block (a registry mailbox and phone) |

## Class to add: Guide

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line | "Responding to a claim" |
| In short | `summary` | Text block | three to five sentences in plain language |
| Steps | `body` | XML block | numbered steps with headings |
| Forms | `forms` | Object relations (`ezobjectrelationlist`) | File objects |
| Applies from | `effective_from` | Date | when rules change |
| Next review | `review_date` | Date | |

## Rules for court content

| Rule | Reason |
|---|---|
| Decisions in their own class and section | separate permissions and review |
| One object per decision, never edited for content after publication except to correct, with a note | the published text is the court's text |
| Search-indexed attributes chosen with care | the search index holds what is searchable; mark only the summary and metadata searchable if documents must not be indexed |
| Forms as files, guidance as HTML | guidance must be readable without a download |

## Related pages

- [Courts](README.md)
- [Publishing decisions with care for personal data](publishing-decisions-with-care-for-personal-data.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
