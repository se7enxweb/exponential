---
title: Volunteers and sign-up forms
audience: businesses, editor, website-creator
category: business-solutions
order: 30
summary: How a non-profit collects volunteer sign-ups, event registrations and contact requests with Exponential's information collector - designing the form, mail to the coordinator, files with enhancedezbinaryfile, spam protection, who may read submissions, exports and deleting personal data.
keywords: volunteer sign-up, volunteer form, registration form, information collector, contact form, collected information, enhancedezbinaryfile, CAPTCHA, hCaptcha, form submissions, personal data, nonprofit
type: help
---
# Volunteers and sign-up forms

This page is for volunteer coordinators, staff and the website creator of a non-profit. It shows how to collect
volunteer sign-ups and similar requests with the forms that come with Exponential, who gets told, who may read them,
and how to keep the personal data in them under control.

## Contents

1. [How forms work, in one paragraph](#how-forms-work-in-one-paragraph)
2. [Designing a volunteer form](#designing-a-volunteer-form)
3. [Telling the coordinator](#telling-the-coordinator)
4. [Letting people attach a file](#letting-people-attach-a-file)
5. [Keeping spam out](#keeping-spam-out)
6. [Reading, exporting and removing submissions](#reading-exporting-and-removing-submissions)
7. [Other sign-ups](#other-sign-ups)
8. [Personal data](#personal-data)
9. [Form checklist](#form-checklist)

## How forms work, in one paragraph

A class has attributes marked **Information collector**; an editor creates an object of that class, and its page shows
the form. When a visitor sends it, each field is validated, one **collection** is stored, a mail may be sent, and the
visitor sees a result page or is redirected. Staff read and export the collections in **Setup > Collected
information** ([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md#how-it-works)).
The shipped `feedback_form` class is a ready example; a volunteer form is a class of your own built the same way
([Building a contact form, step by step](../../creators/website-creators/building-a-contact-form.md)).

## Designing a volunteer form

Ask only what you need to place a volunteer. A typical set:

| Field | Datatype | Required |
|---|---|---|
| Name | Text line (`ezstring`) | Yes |
| E-mail | `ezemail` | Yes |
| Phone | Text line | Optional |
| Areas of interest | Selection (`ezselection`), several allowed | Yes |
| Availability | Selection or text block | Optional |
| Message | Text block (`eztext`) | Optional |
| Consent to be contacted | Checkbox (`ezboolean`) with the wording next to it | Yes |

Each of these is marked **Information collector** in the class editor
([Content classes for websites, Attribute settings](../../creators/website-creators/content-classes-for-websites.md#attribute-settings)).
The description above the form (what volunteering involves, who reads the form, how long you keep it) is a normal
attribute that editors write.

> **Tip**
> Keep the form short. Every extra field is personal data you must protect and eventually delete, and a reason for a
> volunteer to give up.

## Telling the coordinator

- `site.ini [InformationCollectionSettings] EmailReceiver` is the default receiver of form mail.
- The ezwebin mail templates take the receiver from a `recipient` attribute of the form object when it has one, "so
  each form can mail its own team".
- `collect.ini [EmailSettings] SendEmail` is enabled for forms by default.

Source: [Forms with the information collector, Mail to a receiver](../../creators/website-creators/forms-with-the-information-collector.md#mail-to-a-receiver).
Test with the file transport before real mail is switched on (same page). After sending, prefer a redirect to a
thank-you page (`Display=redirect`), "so a reload does not resend" (same page, After sending).

## Letting people attach a file

If volunteers must send a document (a certificate, a CV), use the `enhancedezbinaryfile` datatype ("Enhanced File"),
built for information collection. It keeps uploaded files in a download folder with a limit, `MaxFiles` 25 by default,
and mails the collected information as a multi-part mail when a file is attached. "`0` keeps an unlimited number,
which is not advisable for a site with anonymous uploads"
([enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md)).

Ask whether you really need the file at sign-up; often it can be requested later, by a person, from the volunteers
you accept.

## Keeping spam out

| Measure | What it does |
|---|---|
| Form token (`ezformtoken`) | Refuses forged posts; active by default |
| CAPTCHA (`recaptcha` or `hcaptcha`) | Add the CAPTCHA attribute to the form class |
| Remove spam in bulk | **Setup > Collected information** |

Source: [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md). The CAPTCHA widget
loads a script from its provider; mention the provider in your privacy note. Both shipped CAPTCHA services offer an
accessible alternative ([Accessibility for site builders, Forms](../../creators/website-creators/accessibility-for-site-builders.md#forms)).

## Reading, exporting and removing submissions

- **Setup > Collected information** lists each form with figures, **Collections**, **Export CSV** and **Object**.
- **Export CSV** writes UTF-8 with a byte order mark; values a spreadsheet would read as a formula start with an
  apostrophe; at most 50 000 rows; every export is recorded in the audit trail without the values.
- **Remove selected** deletes submissions after a confirmation; the forms stay.
- "Everything needs `infocollector/read`. Give it to the people who process the forms, not to every editor."

Source: [Reading and exporting form submissions](../../creators/website-creators/form-submissions.md) and the guide
[Collected information](../../../guides/collected-information.md).

## Other sign-ups

| Need | Use |
|---|---|
| Event registration | A form object on the event page |
| A quick poll among supporters | The `poll` form type: "a poll can be answered once per user" (`CollectionUserDataList[poll]=unique`) |
| A supporter newsletter | `cjw_newsletter` with double opt-in, not a form ([Newsletters](../newsletters/README.md)) |
| Volunteer accounts with a members area | User registration ([Member registration and areas](../../creators/website-creators/member-registration-and-areas.md)) |

Form types are set in `settings/collect.ini`
([Forms with the information collector, Form types](../../creators/website-creators/forms-with-the-information-collector.md#form-types-form-feedback-poll)).

## Personal data

> **Note**
> Basics, not legal advice. Ask your data protection officer or lawyer which rules apply.

- Say next to the form what happens with the data, who reads it and how long you keep it.
- Limit `infocollector/read` to the people who process the forms.
- Store exports where personal data belongs, and delete them when done.
- Remove old submissions on a schedule you can name.

These points follow the personal data checklist of
[Reading and exporting form submissions](../../creators/website-creators/form-submissions.md#personal-data-checklist).
See also [Consent and forms](../security/consent-and-forms.md).

## Form checklist

- [ ] Fields limited to what you need; consent wording next to the checkbox.
- [ ] Receiver set per form; mail tested with the file transport, then for real.
- [ ] Redirect to a thank-you page.
- [ ] Token active; CAPTCHA added; tested logged out.
- [ ] File uploads only if needed, with `MaxFiles` set.
- [ ] `infocollector/read` only for coordinators.
- [ ] A deletion schedule for old submissions, written down.

## Related pages

- [Non-profits](README.md)
- [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)
- [Forms and member feedback](../associations/forms-and-member-feedback.md)
- [Collected information (guide)](../../../guides/collected-information.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
