---
title: Forms, polls and sign-ups
audience: individual, website-creator, customer
category: personal-sites
order: 1715
summary: How a volunteer collects sign-ups, answers and votes on an Exponential club or family site with the information collector - the shipped Feedback form and Poll classes, form types in collect.ini, one vote per user for polls, where submissions are stored, mail to a receiver, spam protection and privacy.
keywords: forms, sign-up, registration form, poll, vote, survey, Feedback form, Poll class, information collector, collect.ini, form types, submissions, collected information, mail receiver, CAPTCHA, privacy, club
type: help
---
# Forms, polls and sign-ups

This page is for individuals who need people to answer something on their site: "Who comes to the summer outing?",
"Which date suits you?", "Send us your question". Exponential's **information collector** does this: fields of a class
can be marked so that visitors fill them in, and the answers are stored and can be mailed
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

## Contents

1. [What ships](#what-ships)
2. [Form types](#form-types)
3. [A sign-up form for an event](#a-sign-up-form-for-an-event)
4. [A poll](#a-poll)
5. [Reading the answers](#reading-the-answers)
6. [Spam](#spam)
7. [Privacy](#privacy)
8. [Limits](#limits)

## What ships

| Class | Visitor fields | Use |
|---|---|---|
| *Feedback form* | sender name, subject, message, e-mail (information collectors); recipient set by you | Contact, questions, simple sign-ups |
| *Poll* | one question with options (`ezoption`, information collector) | One question, several answers |

Sources: `class-feedback_form.xml` and `class-poll.xml` in the `sevenx_classes` package.

## Form types

`settings/collect.ini` knows three types, `form`, `feedback` and `poll`, and decides per type whether a mail is sent
and whether one user may answer once or many times. Shipped: mail on submission is enabled, but disabled for polls;
a poll can be answered once per user ([Form types](../../creators/website-creators/forms-with-the-information-collector.md#form-types-form-feedback-poll)).

## A sign-up form for an event

The quickest way without changing any class:

1. Create a *Feedback form* next to the event (or inside it), named "Sign up: summer outing".
2. In the description, say what to write in the message: number of people, food preferences.
3. Set the recipient to the organizer's address.
4. Publish, and link it from the event.

If you need proper fields (number of guests, a choice of meal), a class of your own with information-collector
fields is the clean solution; that is website-creator work
([Building a contact form](../../creators/website-creators/building-a-contact-form.md),
[Designing your content model](../../creators/website-creators/designing-your-content-model.md)).

For members-only sign-ups, put the form in the members' area: only signed-in members can read and send it.

## A poll

1. Create a *Poll* where it should appear.
2. Write the question and the options.
3. Publish. Visitors choose an option and see the result page.

Because polls are answered once per user by default, a poll in the members' area counts one vote per member. A
public poll is answered by anonymous visitors, so treat its result as an impression, not a decision.

## Reading the answers

| Where | What |
|---|---|
| The recipient's mailbox | A mail per submission (forms and feedback, if mail works) |
| The administration | Every submission, stored ([Form submissions](../../creators/website-creators/form-submissions.md), [Collected information](../../../guides/collected-information.md)) |

Export or copy the answers you need, then delete old submissions on a schedule.

## Spam

Public forms attract bots. Use the form token (`ezformtoken`, in the default installation), add a CAPTCHA field
(`recaptcha` or `hcaptcha`) to the class, or keep the form in the members' area
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## Privacy

- Ask only for what you need.
- Say next to the form what is kept, why and for how long ([Consent and forms](../../businesses/security/consent-and-forms.md)).
- Delete submissions after the event.
- Sign-up lists with names are personal data; do not publish them without consent.

## Limits

- No payment on forms.
- No limit on the number of places (first come, first served) without custom work.
- No automatic confirmation mail to the person who signed up in the shipped feedback form; the mail goes to the recipient.
- No conditional questions or multi-page forms out of the box.

## Related pages

- [Club sites](README.md)
- [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)
- [Forms and member feedback](../../businesses/associations/forms-and-member-feedback.md)
- [About page and contact form](../personal-sites/about-page-and-contact-form.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
