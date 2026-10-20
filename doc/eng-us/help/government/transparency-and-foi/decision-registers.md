---
title: Decision registers
audience: government, editor, website-creator
category: public-sector
order: 130
summary: How to keep a public register of decisions (resolutions, delegated decisions, executive decisions) as content in Exponential, with stable references, links to meetings and documents, states for the decision's life, and an export as open data.
keywords: decision register, resolutions, delegated decisions, executive decisions, register, object states, references, transparency, accountability, open data
type: help
---
# Decision registers

This page is for clerks and site builders who publish the decisions a public body takes. A register kept as content
gives each decision a stable address, a version history and an audit trail, and can be exported as a dataset.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The decision class](#the-decision-class)
2. [The life of a decision with object states](#the-life-of-a-decision-with-object-states)
3. [Links to meetings and documents](#links-to-meetings-and-documents)
4. [The register page](#the-register-page)
5. [Export as open data](#export-as-open-data)
6. [Checklist](#checklist)

## The decision class

| Attribute | Datatype | Note |
|---|---|---|
| Reference | Text line | your numbering, never reused |
| Title | Text line | what was decided, in plain language |
| Decided by | Selection | Council, a committee, an officer function |
| Date of decision | Date | |
| Decision text | Rich text | the decision as taken |
| Reasons | Rich text | |
| Meeting | Object relation | the meeting object, when taken in a meeting |
| Documents | Object relation list | reports, consultations |
| Subject | Tags or Selection | theme |

Object name pattern `<reference> <title>`. Put decisions in a folder per year.

## The life of a decision with object states

Object states are labels every object carries, one from each state group; roles can use them
([Object states](../../../guides/object-states.md)). Create a group "Decision status" in **Setup > States** with
states such as *Taken*, *Called in*, *In force*, *Revoked*, *Superseded*.

- Show the state on the page with your template.
- Limit `state/assign` with the `NewState` limitation so only the clerk team changes it.
- A change of state is recorded in the audit trail ([Audit trail](../../../features/6.0/audit-trail.md)).

Never remove a revoked decision; set its state and link the decision that revoked it.

## Links to meetings and documents

Object relations point to objects, not to addresses, so they keep working when meetings or documents move
([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)). **Broken relations** in Setup
(`/contentcheck/brokenrelations`) lists relations to content that was removed.

## The register page

The year folders list decisions newest first; a line view shows reference, title, date and state. The site search
finds decisions by their text when the fields are **Searchable** ([Site search](../../creators/website-creators/site-search.md)).

## Export as open data

Export the class with `xrowextract` to CSV or JSON (one row per decision) and publish the file as a dataset; or let
re-users read the register through the REST API (see the open data section).

## Checklist

- [ ] References are unique and never reused.
- [ ] Decision text is the text as taken; corrections are dated notes, not silent edits.
- [ ] States are set only by the clerk team.
- [ ] Revoked decisions stay, with a link to what replaced them.

## Related pages

- [Meeting records, agendas and minutes](meeting-records-agendas-and-minutes.md)
- [Sections and object states for site builders](../../creators/website-creators/sections-and-object-states.md)
- [Version history as accountability](version-history-as-accountability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
