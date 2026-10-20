---
title: How to write verifiable requirements
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 20
summary: For procurement officers and project managers who write the requirements of a tender for a content management system - how to phrase each requirement so a bidder can answer it with evidence and an evaluator can check it.
keywords: requirements, verifiable, testable, tender, RFP, specification, acceptance criteria, evidence, MoSCoW, must, should, procurement, public sector, CMS
type: help
---
# How to write verifiable requirements

This page is for procurement officers, project managers and the subject experts who help them write the requirements
section of a tender for a web content management system (CMS). It answers one question: how do you phrase a
requirement so that a bidder can answer it with evidence and your evaluators can check the answer? The examples use
Exponential and its documentation, because its standards pages show what good evidence looks like
([Standards](../../../../standards/README.md)).

## Contents
1. [What makes a requirement verifiable](#what-makes-a-requirement-verifiable)
2. [The parts of a good requirement](#the-parts-of-a-good-requirement)
3. [Weak and strong examples](#weak-and-strong-examples)
4. [Priorities: must, should, could](#priorities-must-should-could)
5. [Asking for evidence](#asking-for-evidence)
6. [Pitfalls](#pitfalls)

## What makes a requirement verifiable

A requirement is verifiable when two people who read the bidder's answer reach the same conclusion. That needs:

- **One subject per requirement.** "The system must be secure and accessible" cannot be scored; split it.
- **A named standard or a measurable outcome.** "WCAG 2.2 level AA" can be checked; "user friendly" cannot.
- **A scope.** Which part of the system: the public site, the administration, the editor, the API.
- **A form of evidence.** A document, a test, a demonstration, a setting, a log.
- **A neutral wording.** It describes what you need, not one product's feature names. Naming a product or a brand
  in a requirement usually limits competition; check your procurement rules.

## The parts of a good requirement

| Part | Question it answers | Example |
|---|---|---|
| Identifier | How do we refer to it? | `SEC-07` |
| Statement | What is needed? | "Administrators can require a second factor at sign-in." |
| Scope | Where does it apply? | "All accounts with access to the administration." |
| Priority | How important is it? | Must |
| Evidence asked | How does the bidder prove it? | "Name the component, link its documentation, show it in the demonstration." |
| Verification | How do we check it? | "Evaluator signs in during the demonstration with a second factor." |

## Weak and strong examples

| Weak | Strong | Why |
|---|---|---|
| "The CMS must be accessible." | "The administration interface used by editors meets WCAG 2.2 level AA. State your conformance status per criterion and the evidence (test reports, audit, conformance report)." | Names the standard, the level, the scope and the evidence. |
| "Good security." | "The system protects every form against cross-site request forgery. Name the mechanism and show where it is configured." | One control, a named mechanism. Exponential documents form tokens in [Security standards](../../../../standards/security.md). |
| "Supports many languages." | "Editors can publish the same page in at least <number> languages, including right-to-left scripts, and visitors are shown the language of the address they used." | Measurable; the number is yours to fill. |
| "Data can be exported." | "All published content, with its metadata and files, can be exported in an open, documented format without the bidder's involvement. Describe the tools and formats." | Testable in a proof of concept. Compare [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md). |
| "24/7 support." | "Critical incidents receive a first response within <time> at any hour; state the staffing that delivers this." | The number is a blank you fill from your own needs; the bidder must say how it delivers it. |

## Priorities: must, should, could

Most procurement frameworks separate mandatory requirements (pass or fail) from scored ones. Keep the mandatory list
short: every "must" that a good bidder cannot meet removes a bidder.

| Priority | Use for | Scoring |
|---|---|---|
| Must | legal obligations, security basics, things you cannot run without | pass or fail |
| Should | important, but a gap can be closed by the bidder or by you | scored |
| Could | useful extras | scored lightly or used as a tie-breaker |

A requirement that the market cannot meet today is a "should", with a request to describe the plan. Accessibility
conformance reports are an example: Exponential has none ([Accessibility standards](../../../../standards/accessibility.md)),
and neither do many other open source systems.

## Asking for evidence

Ask for evidence that exists and that you can check yourself:

- **Documentation links** to the exact page and section, not to a home page.
- **Settings** by file and key (for example `settings/site.ini [UserSettings] MinPasswordLength`), which you can see in
  a test installation.
- **Tests** that run in public CI. Exponential's test suites are listed in
  [Testing and continuous integration](../../../../standards/testing-and-ci.md).
- **A demonstration** on a system you can touch, or a proof-of-concept installation you run yourself. Exponential
  installs in one command on SQLite ([Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)).
- **Honest gaps.** Ask the bidder to list what is not met. A bidder who answers "fully compliant" to every line
  without evidence gives you less information than one who says "Partly complies" and shows the gap.

## Pitfalls

- **Copying requirements from another tender** without checking them against your needs.
- **Asking for a certificate the product category does not have.** Ask what exists, and score it.
- **Mixing product and service.** "The software supports backups" and "the supplier takes daily backups" are two
  requirements with two different owners.
- **Hidden numbers.** If you need a response time, write it in; do not leave bidders to guess.
- **No verification method.** Decide how you will check a requirement when you write it, not when you score it.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Mapping requirements to evidence](mapping-requirements-to-evidence.md)
- [What Exponential can and cannot evidence](what-exponential-can-and-cannot-evidence.md)
- [Standards](../../../../standards/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
