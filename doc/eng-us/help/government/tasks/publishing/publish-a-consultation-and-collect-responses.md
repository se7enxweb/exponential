---
title: Publish a consultation and collect responses
audience: government, editor
category: public-sector
order: 130
summary: For policy and web editors - publish a public consultation with a response form built on the information collector, say how responses are used, close it on time, and read the responses in Setup > Collected information.
keywords: consultation, public consultation, response form, information collector, collected information, closing date, privacy note, responses, export
type: help
---
# Publish a consultation and collect responses

This page is for editors who open a consultation on the website. It uses a Consultation class with collector fields,
as described in [Consultations and polls](../../building/forms/consultations-and-polls.md); responses are stored in
**Setup > Collected information** ([Collected information](../../../../guides/collected-information.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Closing and reading responses](#closing-and-reading-responses)
5. [Pitfalls](#pitfalls)

## Before you start

- The proposal, the questions and the privacy note (who sees responses, whether they are published, how long they are
  kept) are approved.
- Your site has a Consultation class with response fields (ask the web team), or a feedback form you can use.
- You know the opening and closing dates.

## Steps

1. Open **Consultations**, **Create new**, choose **Consultation**.
2. Fill title, the proposal in short, the proposal, documents (**Upload new** or **Add existing**), opens and closes.
3. **How we will use your response**: the approved privacy note.
4. **Preview**: the form fields (your response, responding as, organization, e-mail) show below the proposal.
5. **Send for publishing**.
6. Send a test response signed out; check it in **Setup > Collected information**, then remove the test
   ([Remove form responses that are no longer needed](remove-form-responses-that-are-no-longer-needed.md)).

## Check the result

- [ ] Signed out, the consultation page shows the proposal, the closing date and the form.
- [ ] The form can be filled and sent with the keyboard only.
- [ ] A test response appears in **Setup > Collected information** under the consultation.

## Closing and reading responses

| When | Do |
|---|---|
| The closing date passes | the template shows "closed" instead of the form if it was built so; otherwise edit the page and say it is closed |
| Analysis | **Setup > Collected information**, the consultation's card, **Export CSV** ([Export form responses to CSV](export-form-responses-to-csv.md)) |
| Outcome | edit the consultation, fill **Outcome**, publish |
| Retention period ends | remove the responses |

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Asking for more personal data than needed | name and address only if the consultation requires them |
| A long response lost when the form expired | tell people to keep a copy of long answers; the "form expired" page says nothing was saved ([CSRF form tokens](../../building/forms/csrf-form-tokens.md)) |
| Publishing individual responses without permission | ask in the form whether a response may be published |
| Late responses after closing | note them; the collector itself does not enforce the date |

## Related pages

- [Publishing tasks](README.md)
- [Publish a poll](publish-a-poll.md)
- [Accessible forms](../../building/forms/accessible-forms.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
