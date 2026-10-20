---
title: Forms and member feedback
audience: businesses, editor, website-creator
category: business-solutions
order: 40
summary: How a small group collects contact requests, feedback, poll answers and registrations on Exponential - the shipped feedback form, the form, feedback and poll types, star ratings, who receives and reads submissions, spam protection, and deleting what you no longer need.
keywords: feedback form, contact form, poll, survey, registration form, information collector, collected information, star rating, ezstarrating, CAPTCHA, member feedback, small group
type: help
---
# Forms and member feedback

This page is for the secretary, web coordinator and volunteers of a club, society or small group who want to hear from
members and visitors. It shows which kinds of form come with Exponential, how they reach the right person, and how
to keep the personal data in them tidy. The technical setup is in
[Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md).

## Contents

1. [Kinds of form](#kinds-of-form)
2. [The shipped feedback form](#the-shipped-feedback-form)
3. [Polls among members](#polls-among-members)
4. [Registrations for events](#registrations-for-events)
5. [Star ratings](#star-ratings)
6. [Who gets told, who may read](#who-gets-told-who-may-read)
7. [Spam](#spam)
8. [Tidying up](#tidying-up)
9. [Forms checklist](#forms-checklist)

## Kinds of form

`settings/collect.ini` knows three form types, `form`, `feedback` and `poll`, and decides per type whether a mail is
sent, what the visitor sees after sending, and whether anonymous visitors may send
([Forms with the information collector, Form types](../../creators/website-creators/forms-with-the-information-collector.md#form-types-form-feedback-poll)):

| Type | Mail on submission | Answers per person | Good for |
|---|---|---|---|
| `form` | Yes | Several | Contact, join requests, registrations |
| `feedback` | Yes | Several | Feedback on an event or the site |
| `poll` | No (`SendEmailList[poll]=disabled`) | Once per user (`CollectionUserDataList[poll]=unique`) | Quick member votes |

Anonymous visitors may send forms by default (`CollectAnonymousData=enabled`).

## The shipped feedback form

The class `feedback_form` has attributes marked **Information collector**: sender name, e-mail, subject and message.
An editor creates an object of the class, and its page shows the form
([How it works](../../creators/website-creators/forms-with-the-information-collector.md#how-it-works)). The
`feedback_form` package ships with `ezwebin` (`extension/ezwebin/packages/`). For a group this is usually the only
form needed at first: one on the Contact page, one on the Join page with its own receiver.

## Polls among members

A poll is a form of type `poll`: members choose an answer, each user can answer once, and the poll's result template
(`content/collectedinfo/poll.tpl`) shows the results
([Reading and exporting form submissions, Showing results on the site](../../creators/website-creators/form-submissions.md#showing-results-on-the-site)).
"Once per user" is counted per user account, and the documentation does not describe how it treats visitors who are
not signed in, so for a poll among members let members sign in (and test it). Use polls
for opinions, not for binding votes that your statutes regulate.

## Registrations for events

Put a form object on the event page with the fields you need (name, e-mail, number of people, dietary needs). Collect
only what you need for that event, and remove the submissions after the event
([Reading and exporting form submissions](../../creators/website-creators/form-submissions.md#removing-submissions)).
For event pages themselves see [Events and news for groups](events-and-news-for-groups.md).

## Star ratings

The `ezstarrating` extension adds a **Star Rating** datatype: visitors rate objects of a class "from 1 to 5 stars",
each user may vote once per session, and the average is stored per object
([ezstarrating](../../../features/6.0/extensions/ezstarrating.md)). It suits rating talks or recipes on a group site;
it is not a survey tool.

## Who gets told, who may read

- **Mail**: the default receiver is `site.ini [InformationCollectionSettings] EmailReceiver`; the ezwebin mail templates
  use a `recipient` attribute of the form object when it has one, "so each form can mail its own team"
  ([Mail to a receiver](../../creators/website-creators/forms-with-the-information-collector.md#mail-to-a-receiver)).
  Use a role address the group controls (secretary@...), not a volunteer's private address, so it survives a handover.
- **Reading**: every submission is kept in **Setup > Collected information**. "Everything needs `infocollector/read`.
  Give it to the people who process the forms, not to every editor"
  ([Who may see them](../../creators/website-creators/form-submissions.md#who-may-see-them)).
- **After sending**: a redirect to a thank-you page (`Display=redirect`) so a reload does not send again.

## Spam

Forms on a public site attract spam:

- the form token of `ezformtoken` refuses forged posts and is on by default;
- a CAPTCHA (`recaptcha` or `hcaptcha`) on the form class stops most automated spam;
- spam that gets through can be removed in bulk in **Setup > Collected information**.

Source: [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md). Mention the CAPTCHA
provider in your privacy notice.

## Tidying up

- **Export CSV** gives a spreadsheet of one form's submissions; formula-like values are made safe, and every export is
  recorded in the audit trail without the values.
- **Remove selected** deletes submissions after a confirmation; the forms stay.
- Remove old submissions on a schedule you can name, and delete exports when done.

Source: [Reading and exporting form submissions](../../creators/website-creators/form-submissions.md). The personal
data rules for forms are basics, not legal advice; see [Consent and forms](../security/consent-and-forms.md).

## Forms checklist

- [ ] Contact and join forms from the shipped `feedback_form` class, each with its receiver.
- [ ] Receivers are role addresses the group controls.
- [ ] Redirect to a thank-you page after sending.
- [ ] Token on, CAPTCHA added, tested while logged out.
- [ ] `infocollector/read` only for the people who answer.
- [ ] Polls used for opinions, with members signed in.
- [ ] Event registrations removed after the event; a deletion schedule for the rest.

## Related pages

- [Associations and small groups](README.md)
- [Volunteers and sign-up forms](../nonprofits/volunteers-and-sign-up-forms.md)
- [Building a contact form, step by step](../../creators/website-creators/building-a-contact-form.md)
- [Collected information (guide)](../../../guides/collected-information.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
