---
title: Registrations for events
audience: government, website-creator, project-manager
category: public-sector
order: 60
summary: How to take sign-ups for public events with Exponential's information collector - collector attributes on the event, the confirmation and mail, what it cannot do (capacity, waiting lists, payments), the personal data involved, and when to link a booking system.
keywords: event registration, sign-up, booking, information collector, collector attributes, capacity, waiting list, confirmation mail, personal data, retention, booking system
type: help
---
# Registrations for events

This page is for website creators and project managers who need people to sign up for an event. Exponential has no
booking system; the **information collector** can take simple sign-ups. This page shows how, and where its limits
are. Sources: [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)
and [Collected information](../../../../guides/collected-information.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [A sign-up form on the event](#a-sign-up-form-on-the-event)
2. [What it cannot do](#what-it-cannot-do)
3. [Personal data](#personal-data)
4. [When to use a booking system](#when-to-use-a-booking-system)

## A sign-up form on the event

1. Add collector attributes to the Event class (or a class of your own for events with sign-up), each with
   **Information collector** ticked:

   | Attribute | Datatype |
   |---|---|
   | Name | Text line |
   | E-mail | Email |
   | Number of places | Integer |
   | Access needs (optional) | Text block |

2. Show the form in the event's full view template, posting to `content/action` with `ActionCollectInformation`
   ([Information collector basics](../forms/information-collector-basics.md)).
3. Set `collect.ini [DisplaySettings] Display=redirect` and a thank-you page that says what happens next.
4. Mail each sign-up to the organizer's mailbox ([Mail to receivers](../forms/mail-to-receivers.md)).
5. Read the list in **Setup > Collected information**; **Export CSV** for the attendance list.

Only events with sign-up need the collector attributes; on other events the template shows no form when the Booking
field says "free entry".

## What it cannot do

| Missing | Workaround |
|---|---|
| Capacity: it does not stop at N places | watch the count in **Setup > Collected information**; set the Booking field to "fully booked", which hides the form |
| A waiting list | sign-ups after "fully booked" go to a second form, or by e-mail |
| Confirmation mail to the person | the default mail goes to the receiver; mailing the person who typed an address can be abused to send spam to others, so confirm by a manual reply |
| Cancellation by the person | by e-mail to the organizer |
| Payments | a payment system; Exponential's shop is for products, not event fees in most public bodies |

## Personal data

| Practice | Why |
|---|---|
| Ask only what the event needs | data minimization |
| "Access needs" is optional and phrased so nobody must disclose health details | special categories of data |
| Say on the form who sees the data and when it is deleted | transparency |
| Remove the sign-ups after the event ([Retention of collected information](../forms/retention-of-collected-information.md)) | storage limitation |
| Limit `infocollector/read` | sign-ups of all events are visible to whoever has it |

The [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) applies in the EU ([Privacy standards](../../../../standards/privacy.md)).

## When to use a booking system

| Situation | Use |
|---|---|
| Limited places that fill fast | a booking system with capacity and waiting lists |
| Paid events | a booking or payment system |
| Recurring courses with many sessions | a course booking system |
| Small free events, information evenings | the information collector is enough |

Link the booking system from the event's **Booking link** attribute ([Modeling an event class](modeling-an-event-class.md)).

## Related pages

- [Events](README.md)
- [Building forms](../forms/README.md)
- [Accessible forms](../forms/accessible-forms.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
