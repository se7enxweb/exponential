---
title: Building forms
audience: government, website-creator, admin, project-manager
category: public-sector
order: 10
summary: Index of the guides for building public-sector forms in Exponential - the information collector, accessible forms, spam protection and its third-party implications, CSRF form tokens, file attachments, mail to receivers, retention of what was collected, and consultations and polls.
keywords: forms, information collector, accessible forms, spam, CAPTCHA, CSRF, form token, attachments, mail receiver, retention, consultation, poll
type: help
---
# Building forms

This folder is for website creators, administrators and project managers who build forms on a public-sector site:
contact forms, reports of problems, feedback, consultation responses, polls and simple requests. Exponential builds
forms with the **information collector**: attributes of a content class that visitors fill in, stored as collections
and optionally mailed. The guides cover the mechanism, how to make forms accessible and safe, and how to handle what
is collected for as long as you keep it, and no longer.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [Before you build a form](#before-you-build-a-form)

## The pages

| Page | The answer in one line |
|---|---|
| [Information collector basics](information-collector-basics.md) | Collector attributes, the form template, form types in `collect.ini`, where submissions go |
| [Accessible forms](accessible-forms.md) | Labels, instructions, errors, required fields and keyboard use for public forms |
| [Spam protection and third parties](spam-protection-and-third-parties.md) | Form tokens, reCAPTCHA or hCaptcha and what using a third party means, and alternatives |
| [CSRF form tokens](csrf-form-tokens.md) | How `ezformtoken` protects every POST, the "form expired" page, the secret to set |
| [File attachments in forms](file-attachments-in-forms.md) | The Enhanced File datatype for uploads, its limits and the risks of accepting files |
| [Mail to receivers](mail-to-receivers.md) | Who gets a mail for each form, per-form receivers, testing without sending real mail |
| [Retention of collected information](retention-of-collected-information.md) | Reading, exporting and removing submissions on a schedule, with the audit record |
| [Consultations and polls](consultations-and-polls.md) | A consultation class with a response form, and the poll type with one answer per user |

## Reading order

1. **Information collector basics**.
2. **Accessible forms**, **CSRF form tokens** and **Spam protection and third parties** before any form goes live.
3. **Mail to receivers** and **Retention of collected information** before you collect anything real.
4. **File attachments in forms** and **Consultations and polls** when you need them.

Up: [Building a public-sector site](../README.md), the index of all building guides, and [Governments: start here](../../government-start-here.md), the start of the Governments
section.

## Before you build a form

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

- [ ] Is a form the right tool, or does the request belong in a case or transaction system? See
  [Linking to online transactions](../service-catalog/linking-to-online-transactions.md#when-a-simple-form-is-enough).
- [ ] Which fields are needed for the purpose, and no more?
- [ ] Who reads the submissions, and who has `infocollector/read`?
- [ ] How long are submissions kept, and who removes them?
- [ ] What does the privacy notice next to the form say?

The general guides: [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md),
[Building a contact form](../../../creators/website-creators/building-a-contact-form.md),
[Reading and exporting form submissions](../../../creators/website-creators/form-submissions.md),
[Collected information](../../../../guides/collected-information.md).

## Related pages

- [Building a service catalog](../service-catalog/README.md)
- [Privacy standards](../../../../standards/privacy.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
