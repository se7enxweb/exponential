---
title: Monitoring and feedback
audience: government, project-manager, editor, admin
category: public-sector
order: 290
summary: For accessibility coordinators - how to keep a live site accessible with a monthly content spot check, a feedback channel built from Exponential's own tools, a response routine, and the records a monitoring body or auditor will ask for.
keywords: monitoring, feedback, feedback mechanism, barrier report, contact form, information collector, spot check, content review, response time, records, accessibility statement, continuous improvement, public sector
type: help
---
# Monitoring and feedback

This page is for accessibility coordinators and project managers who keep a site accessible after launch, and the
editors and administrators who help them. Laws for public bodies usually ask for a way for people to report barriers
and request information in another form, and for regular checks. Exponential gives you the building blocks; the
routine is yours.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [A feedback channel](#a-feedback-channel)
2. [Answering reports](#answering-reports)
3. [A monthly content spot check](#a-monthly-content-spot-check)
4. [Watching for regressions](#watching-for-regressions)
5. [Records to keep](#records-to-keep)
6. [Related pages](#related-pages)

## A feedback channel

- Offer at least two ways: an e-mail address and a form (and a phone number where you have one).
- Build the form with the information collector: the shipped Feedback form class (`feedback_form`) or a class of your
  own, so each report is stored as a collection and can be mailed to a team
  ([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).
- Fields: what the person tried to do, the page address (prefilled where possible), what happened, how to reply. Make
  every field but the description optional.
- Link the channel from the accessibility statement and the footer.
- The form must itself be accessible (see "Accessible forms").

## Answering reports

1. Acknowledge each report within a fixed time and give a reference.
2. Provide the information in an accessible form at once if it is content (send the text of a PDF, describe an image).
3. Classify the cause: content (assign to the editor), template (to the developer), extension, third party.
4. Fix, reply, and add the case to your findings table.
5. Read reports in **Setup > Collected information**; access needs `infocollector/read`, so give it to the people who
   handle reports ([Collected information](../../../guides/collected-information.md)).

## A monthly content spot check

- Take ten pages published or changed in the month (the dashboard and the search can list recent content).
- For each: alternative text, headings, link texts, tables, linked documents, language.
- Where the Exponential Intelligence extension is active, the **Missing alt text** report (`/expai/alttext`) lists
  images without alternative text below a node ([Exponential Intelligence](../../../features/6.0/extensions/expai.md)).
- Feed problems back to the editors with an example of the better version.

## Watching for regressions

- After every release that changes templates: an automated check and a keyboard pass on the changed templates (see
  "Testing with axe and the keyboard").
- After an upgrade: read the release's changelog for accessibility entries (the project records fixes there, for
  example in the [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)).
- Re-check embedded third-party content (maps, video, forms of other services) when the provider changes it.

## Records to keep

| Record | Why |
|---|---|
| Audit reports with dates | the statement's assessment section; monitoring bodies |
| Findings table with fixes | the plan in the statement; trend over time |
| Feedback received and answers | proof the mechanism works |
| Statement versions | what was declared when (kept as versions of the statement object, within the version history limit) |
| Training of editors | evidence of measures taken |

## Related pages

- [Collected information](../../../guides/collected-information.md)
- [Form submissions](../../creators/website-creators/form-submissions.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
