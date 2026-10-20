---
title: A request form with the information collector
audience: government, website-creator, admin
category: public-sector
order: 60
summary: Build an online form for requests for information in Exponential with the information collector: the class, the fields to ask for and not to ask for, mail to the FOI team, the confirmation page, spam protection and privacy text.
keywords: request form, FOI request, information collector, collect.ini, feedback form, form, EmailReceiver, confirmation, privacy notice, spam protection, form token
type: help
---
# A request form with the information collector

This page is for website creators and administrators who give the public an online form to request information.
Exponential builds forms with the **information collector**: a class whose attributes are marked *Information
collector*, an object of it shown as a form, and a stored *collection* for every submission
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you build](#before-you-build)
2. [The form class](#the-form-class)
3. [Mail to the team](#mail-to-the-team)
4. [After sending](#after-sending)
5. [Spam and abuse](#spam-and-abuse)
6. [Privacy](#privacy)
7. [Test](#test)

## Before you build

- Check whether your law lets people request by any channel (mail, e-mail, paper). A web form is usually an
  additional channel; say so on the page.
- Decide what the minimum details are. Many laws only need a way to reply and a description of the information.
- Decide who receives requests and where the case is tracked: Exponential stores submissions but has no case
  tracking with deadlines (see "Handling requests from collected information").

## The form class

Create a class `information_request` in **Setup > Classes**, or adapt the shipped **Feedback form**
(`feedback_form`):

| Attribute | Datatype | Information collector | Required |
|---|---|---|---|
| Introduction | Rich text | no (written by editors) | |
| Recipient | E-mail | no (the team mailbox, used by the mail template) | |
| Your name | Text line | yes | as your law requires |
| E-mail address for the reply | E-mail | yes | yes |
| Postal address (optional) | Text block | yes | no |
| The information you are asking for | Text block | yes | yes |
| Preferred format | Selection | yes | no |

Do not ask for reasons, identity documents or phone numbers unless the law requires them.

Create one object of the class in the "Requesting information" area and write the introduction: what can be
requested, the response time your law sets, other channels, what happens to the data.

## Mail to the team

`site.ini [InformationCollectionSettings] EmailReceiver` is the default receiver. The ezwebin mail templates take the
receiver from a `recipient` attribute of the form object when it has one, so this form can mail the FOI team mailbox.
Mail settings and the file transport for testing: [10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending).

`settings/collect.ini` decides per form type whether mail is sent (`[EmailSettings] SendEmail`) and what the visitor
sees afterwards (`[DisplaySettings] Display`, `RedirectURL`).

## After sending

Use `Display=redirect` to a "Thank you" page, so a reload does not send twice. The thank-you page says when the
requester can expect an answer and how to follow up. If you give a reference number, it comes from your case system;
the collection ID is internal.

## Spam and abuse

- Keep the `ezformtoken` extension active; it protects POST forms.
- CAPTCHA extensions (`recaptcha`, `hcaptcha`) send visitor data to a third party and need a privacy notice
  ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md),
  [Privacy standards](../../../standards/privacy.md)).
- `[CollectionSettings] CollectAnonymousData=enabled` lets visitors who are not signed in send; leave it enabled for a
  public request form.

## Privacy

Next to the form say what is collected, why, who receives it, how long it is kept, and how to ask for its deletion.
Limit the policy `infocollector/read` to the team that handles requests. The privacy standard lists that forms
store what visitors type and that consent texts and retention rules are left to the site ([Privacy standards](../../../standards/privacy.md#known-gaps)).

## Test

1. Send a request signed out, with a test address on a domain you control.
2. Check the mail arrived (or the file in the mail directory with the file transport).
3. Check the submission in **Setup > Collected information**.
4. Remove the test submission there.

## Related pages

- [Building a contact form](../../creators/website-creators/building-a-contact-form.md)
- [Reading and exporting form submissions](../../creators/website-creators/form-submissions.md)
- [Collected information](../../../guides/collected-information.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
