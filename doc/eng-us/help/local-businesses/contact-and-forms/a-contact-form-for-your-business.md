---
title: A contact form for your business
audience: local-business, businesses, customer
category: business-solutions
order: 51
summary: A contact form for a local business on Exponential from the owner's side - the fields worth asking for, where the mail goes, the thank-you page, what the builder sets up with the information collector, testing it logged out, and keeping it working.
keywords: contact form, feedback form, information collector, EmailReceiver, thank you page, fields, mail delivery, test, local business
type: help
---
# A contact form for your business

This page is for the owner of a local business who wants a contact form on the site. The step-by-step build for your
builder is in [Building a contact form, step by step](../../creators/website-creators/building-a-contact-form.md);
this page covers the decisions you make and the checks you do.

## Contents

1. [Ask for little](#ask-for-little)
2. [Where the message goes](#where-the-message-goes)
3. [After sending](#after-sending)
4. [What the builder does](#what-the-builder-does)
5. [Test it yourself](#test-it-yourself)
6. [Keep it working](#keep-it-working)

## Ask for little

Every extra field loses some visitors. For most local businesses:

| Field | Required | Why |
|---|---|---|
| Name | yes | to address the reply |
| E-mail | yes | to reply |
| Phone | no | some customers prefer a call back |
| Message | yes | the question |
| Spam check | yes, if you get spam | [Stopping spam](stopping-spam.md) |

Do not ask for date of birth, address or other details you do not need to answer a question. Less data also means
less to protect ([Handling messages and privacy](handling-messages-and-privacy.md)).

Above the form, say how fast you answer ("We reply within one working day") and give the phone number for urgent
matters.

## Where the message goes

Each submission is stored in Exponential (**Setup > Collected information**) and, when set up, mailed to a receiver
address (`site.ini [InformationCollectionSettings] EmailReceiver`)
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

- Use a shared business mailbox (`info@...`) that more than one person reads.
- The site must be able to send mail, and mail from the site must not land in spam. Ask your host to test it
  ([Choosing hosting for a small business](../getting-started/choosing-hosting-for-a-small-business.md#questions-to-ask-a-host)).

## After sending

The visitor should see a clear "Thank you, we will reply within one working day" page. The builder sets a redirect
to a thank-you page (`collect.ini [DisplaySettings] Display=redirect`). The thank-you page is a normal page you can
edit.

## What the builder does

From [Building a contact form, step by step](../../creators/website-creators/building-a-contact-form.md):

1. A class with collector fields, or the shipped **Feedback form** class (`feedback_form`).
2. A spam protection field (reCAPTCHA or hCaptcha).
3. The form object, for example "Contact" under the front page.
4. The form template, with a visible label for every field.
5. The receiver address and the mail template.
6. The thank-you page redirect.
7. Permission for visitors to send (normally already in place).
8. Clearing caches and a test.

## Test it yourself

Sign out (or use a private browser window) and:

- [ ] send the form with the message empty: an error appears next to the field;
- [ ] send it properly: the thank-you page appears;
- [ ] check the business mailbox: the message arrived, with the visitor's address;
- [ ] reply to it: the reply goes to the visitor, not to the website;
- [ ] in **Setup > Collected information**, the submission is listed;
- [ ] try the form on a phone.

## Keep it working

| How often | Check |
|---|---|
| Monthly | send a test message, see it arrive |
| After a host move or mail change | the same test |
| Quarterly | delete old submissions you no longer need ([Handling messages and privacy](handling-messages-and-privacy.md)) |

A form that silently stopped mailing is a common and expensive failure: customers think you ignored them.

## Related pages

- [Stopping spam](stopping-spam.md)
- [Reading and exporting form submissions](../../creators/website-creators/form-submissions.md)
- [Accessible forms](../../government/accessibility/accessible-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
