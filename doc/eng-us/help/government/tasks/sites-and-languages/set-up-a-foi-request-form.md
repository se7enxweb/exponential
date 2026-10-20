---
title: Set up a FOI request form
audience: government, website-creator, admin
category: public-sector
order: 280
summary: Task for site builders: build an online request form with the information collector, mail each request to the FOI team mailbox, redirect to a thank-you page, and test it.
keywords: request form, FOI, information collector, feedback form, recipient, collect.ini, redirect, form token, privacy text
type: help
---
# Set up a FOI request form

This task is for site builders and administrators. Background:
[A request form with the information collector](../../transparency-and-foi/a-request-form-with-the-information-collector.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Steps

1. **Setup > Classes**: create `information_request` (or adapt **Feedback form**) with collector attributes: name,
   e-mail for the reply, description of the information, preferred format; and editor fields: introduction, recipient
   (the team mailbox).
2. Create one object of the class on the "Requesting information" page; write the introduction (what can be asked,
   response times, other channels, privacy).
3. Send visitors to a thank-you page after sending, so a reload does not resend. The kernel reads two optional
   attributes of the form object for this (`collect.ini [DisplaySettings] DisplayAttribute=collection_display`,
   `RedirectURLAttribute=collection_redirect`, read by `eZInformationCollection::displayHandling()` and
   `redirectURL()`): add text line attributes with the identifiers `collection_display` and `collection_redirect` to
   the class (not collectors), and fill them with `redirect` and the thank-you page address in the form object.
   Alternatively set `DisplayList[<type>]=redirect` and `RedirectURLList[<type>]` for the form type in
   `settings/override/collect.ini.append.php`, which affects every form of that type.
4. Keep `ezformtoken` active.
5. Give `infocollector/read` only to the FOI team's role.

## Check

Send a request signed out with a test address on your own domain: the mail reaches the team mailbox (or the file
directory with the file transport), the submission appears in **Setup > Collected information**. Remove the test
submission.

## Problems

| Problem | Fix |
|---|---|
| No mail | `[EmailSettings] SendEmail` for the type, the mail transport, or the recipient field |
| Double submissions | use the redirect display |

## Related pages

- [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
