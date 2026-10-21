---
title: "Contact and forms for local businesses"
audience: local-business, businesses, customer
category: business-solutions
order: 50
summary: The pages for how customers reach a local business through its Exponential site - a contact form with the information collector, spam protection with form tokens, reCAPTCHA or hCaptcha, quote and request forms, star ratings and comments, and handling messages with care for privacy.
keywords: contact form, information collector, feedback form, spam, CAPTCHA, recaptcha, hcaptcha, ezformtoken, quote request, ratings, ezstarrating, comments, privacy, local business
type: help
---
# Contact and forms for local businesses

This group of pages is for local businesses that want customers to get in touch through the website, and to keep
spam out. Forms in Exponential are built with the **information collector**: fields of a class are marked to collect
input, visitors fill them in on the page, and each submission is stored and can be mailed to you
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

## Contents

1. [The pages in this group](#the-pages-in-this-group)
2. [What ships](#what-ships)
3. [Before you add a form](#before-you-add-a-form)

## The pages in this group

| Page | One line |
|---|---|
| [A contact form for your business](a-contact-form-for-your-business.md) | the fields, the mail, the thank-you page, the test |
| [Stopping spam](stopping-spam.md) | form tokens, reCAPTCHA or hCaptcha, and other measures |
| [Quote and request forms](quote-and-request-forms.md) | quote requests, catering, callbacks, simple sign-ups |
| [Ratings and reviews](ratings-and-reviews.md) | star ratings with `ezstarrating`, comments, and reviews on outside platforms |
| [Handling messages and privacy](handling-messages-and-privacy.md) | reading, answering, exporting and deleting submissions |

## What ships

| Part | Built in or extension | In `composer.json` | Source |
|---|---|---|---|
| Information collector | built in | kernel | [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md) |
| Feedback form class | `ezwebin` site package (`feedback_form`) | `se7enxweb/ezwebin` | `extension/ezwebin/packages/feedback_form-1.0-1.ezpkg` |
| Form tokens against forged posts | `ezformtoken` | shipped in the kernel repository | [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md) |
| reCAPTCHA | `recaptcha` | `se7enxweb/recaptcha` | [recaptcha](../../../features/6.0/extensions/recaptcha.md) |
| hCaptcha | `hcaptcha` | `se7enxweb/hcaptcha` | [hcaptcha](../../../features/6.0/extensions/hcaptcha.md) |
| Star ratings | `ezstarrating` | `se7enxweb/ezstarrating` | [ezstarrating](../../../features/6.0/extensions/ezstarrating.md) |

## Before you add a form

- Who reads the messages, and how fast do you answer? A form that is answered after a week does more harm than a
  phone number.
- Which address receives the mail? Use a shared business address, not one person's private mailbox.
- What does your privacy note say about the data? ([Handling messages and privacy](handling-messages-and-privacy.md))

## Related pages

- [Local businesses: start here](../local-businesses-start-here.md)
- [Forms and collected information](../../customers/growth/forms-and-collected-information.md)
- [Consent and forms](../../businesses/security/consent-and-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
