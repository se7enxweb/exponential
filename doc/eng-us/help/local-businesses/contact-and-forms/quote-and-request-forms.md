---
title: Quote and request forms
audience: local-business, businesses, customer
category: local-business
order: 53
summary: Forms beyond "contact us" for local businesses on Exponential - quote requests for trades, catering and cake orders, callback requests, event and course sign-ups, appointment requests (not bookings) - with the fields to use, the information collector's form types, and where a form is not enough.
keywords: quote request, request form, callback, catering, custom order, sign-up, appointment request, information collector, poll, feedback, file upload, trades, local business
type: help
---
# Quote and request forms

This page is for local businesses that need more than a general contact form: a roofer asking for photos of the
damage, a bakery taking cake orders, a cafe offering catering, a course with a sign-up list. All of these are
**information collector** forms: a class with collector fields, a form object, and submissions stored and mailed
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

## Contents

1. [Examples by trade](#examples-by-trade)
2. [Useful field types](#useful-field-types)
3. [Appointment requests are not bookings](#appointment-requests-are-not-bookings)
4. [One form or several](#one-form-or-several)
5. [What the owner does with a request](#what-the-owner-does-with-a-request)
6. [Checklist](#checklist)

## Examples by trade

| Business | Form | Fields beyond name and contact |
|---|---|---|
| Trades | quote request | type of job (selection), address of the job, preferred days, description, photos (if file upload is set up) |
| Bakery, cake shop | custom order | occasion, date needed, number of people, flavor (selection), text for the cake, allergies |
| Restaurant | catering or group request | date, number of guests, budget range, dietary needs |
| Salon | callback request | preferred time of day (selection), service of interest |
| Practice | appointment request | preferred days, reason (short), new or existing patient |
| Shop | special order | product description, quantity, by when |
| Course or workshop | sign-up | which date (selection), number of places |

Keep each form as short as the job allows. You can always ask more by phone.

## Useful field types

Collector fields use ordinary datatypes ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)):

| For | Datatype |
|---|---|
| Short text (name, address) | Text line |
| Long text (description) | Text block |
| One choice from a list | Selection |
| Yes or no ("I agree to be called") | Checkbox |
| Number of people | Integer |
| E-mail | E-mail |
| Spam check | reCAPTCHA or hCaptcha field ([Stopping spam](stopping-spam.md)) |

Ask your builder whether file uploads in forms are possible on your site before promising "send us photos"; otherwise
ask customers to reply to your mail with photos.

The information collector knows three form types, **form**, **feedback** and **poll**
([Form types](../../creators/website-creators/forms-with-the-information-collector.md#form-types-form-feedback-poll)).
A quick customer survey ("Which new opening day would you prefer?") can be a poll.

## Appointment requests are not bookings

A request form collects wishes; you confirm by phone or mail. It does **not** show free times, prevent double
bookings or send reminders. No booking or appointment module was found in `composer.json` or `extension/`
(20 October 2026). If you need real-time booking, use a booking service and link to it, or ask a partner for a quote
([What needs an outside service](../budget/what-needs-an-outside-service.md)).

For course places with a limit, the same applies: the event pages and sign-up forms described in
[Events and calendars](../../businesses/members/events-and-calendars.md#signing-up-for-an-event) work, but seat
limits and waiting lists are not built in.

## One form or several

One form per purpose is easier for visitors and for you: mails arrive with a clear subject and the fields fit the
request. Each form is its own object, placed where visitors look for it (the quote form on the Services page, the cake
form on the Cakes page).

## What the owner does with a request

1. The mail arrives in the shared mailbox; the submission is also stored.
2. Answer within the time you promised on the form.
3. Note the outcome in your own system (diary, order book, accounting).
4. Delete the stored submission when you no longer need it ([Handling messages and privacy](handling-messages-and-privacy.md)).

## Checklist

- [ ] One form per purpose, each short.
- [ ] Every public form has spam protection.
- [ ] The text above each form says what happens next and how fast.
- [ ] Appointment and course forms say clearly that they are requests.
- [ ] Each form tested signed out.

## Related pages

- [A contact form for your business](a-contact-form-for-your-business.md)
- [Form submissions](../../creators/website-creators/form-submissions.md)
- [Restaurant orders and what is not built in](../simple-shop/restaurant-orders-and-what-is-not-built-in.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
