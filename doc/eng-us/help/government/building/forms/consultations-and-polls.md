---
title: Consultations and polls
audience: government, website-creator, project-manager, editor
category: public-sector
order: 90
summary: How to run a public consultation with a Consultation class and response form in Exponential, close it on time, publish the outcome, and when the shipped Poll class (one answer per user) is the right tool instead.
keywords: consultation, public consultation, response form, information collector, closing date, outcome, poll, ezoption, CollectionUserData, unique, results
type: help
---
# Consultations and polls

This page is for website creators, project managers and editors who ask the public for views. A **consultation** asks
for considered responses to a proposal over weeks; a **poll** asks one quick question. Both use the information
collector, with different settings. Sources: [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)
and [Collected information](../../../../guides/collected-information.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [A Consultation class](#a-consultation-class)
2. [Opening and closing](#opening-and-closing)
3. [Reading and publishing the outcome](#reading-and-publishing-the-outcome)
4. [Polls](#polls)
5. [Consultation or poll?](#consultation-or-poll)

## A Consultation class

Create it in **Setup > Classes** ([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md)):

| Attribute | Identifier | Datatype | Collector |
|---|---|---|---|
| Title | `title` | Text line | no |
| The proposal in short | `summary` | Text block | no |
| The proposal | `proposal` | XML block | no |
| Documents | `documents` | Object relations (File) | no |
| Opens | `opens` | Date and time | no |
| Closes | `closes` | Date and time | no |
| How we will use your response | `privacy_note` | XML block | no |
| Your response | `response` | Text block | yes |
| You are responding as | `respondent_type` | Selection: an individual, an organization | yes |
| Organization (if any) | `organization` | Text line | yes |
| E-mail (optional, only to send a confirmation or follow-up) | `email` | Email | yes |
| Outcome | `outcome` | XML block | no |

Several questions can be several Text block collector attributes. Keep the number of personal fields low.

## Opening and closing

| Step | How |
|---|---|
| Prepare before the opening date | create and keep it in a staging section or as a draft ([Sections, 13.3](../../../../guides/sections.md#133-staging-content-before-it-goes-public)) |
| Open at a set time | publish it then; or a *Wait until date* workflow event on the class with the **Opens** attribute holds the publication until that date ([Workflows](../../../../guides/workflows.md)) |
| Close on time | the template compares **Closes** with the current time (`currentdate()`) and shows "This consultation closed on ..." instead of the form |

The information collector itself does not know a closing date: a visitor who kept the form open, or a script that
posts to `content/action`, could still send after the template stops showing the form. If late responses must be
refused strictly, remove the collector flag from the response attributes after closing, or move the object to a
section anonymous visitors cannot read; note late responses when analyzing.

## Reading and publishing the outcome

1. **Setup > Collected information**: the consultation's card shows the number of responses; **Export CSV** for the
   analysis (each export is recorded in the audit).
2. Analyze outside the CMS; do not publish individual responses unless people agreed to it.
3. Edit the consultation: fill **Outcome** with a summary of what was heard and what will change, and publish.
4. Remove the responses when your retention period ends ([Retention of collected information](retention-of-collected-information.md)).

## Polls

The shipped **Poll** class (`poll`) has an option list (`ezoption`) as its collector attribute.
`collect.ini` treats polls specially:

| Setting | Poll value | Effect |
|---|---|---|
| `CollectionUserDataList[poll]` | `unique` | one answer per user |
| `SendEmailList[poll]` | `disabled` | no mail per vote |
| `Display` | `result` | the result template shows the counts |

"One answer per user" means per account for signed-in users; anonymous visitors share one user account, so their answers
cannot be tied to a person, and a poll is not a reliable vote. Present results as indicative.

## Consultation or poll?

| Need | Use |
|---|---|
| Considered views on a proposal, with reasons | a consultation |
| A quick indication of preference | a poll |
| A formal vote or a referendum | neither; a dedicated system with identity checks |
| Structured surveys with many question types and logic | a survey tool, linked from the page |

## Related pages

- [Building forms](README.md)
- [Information collector basics](information-collector-basics.md)
- [Accessible forms](accessible-forms.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
