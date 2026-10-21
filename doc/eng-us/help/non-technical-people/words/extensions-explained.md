---
title: Extensions explained
audience: non-technical, customer, editor
category: getting-started
order: 16
summary: What extensions, designs, themes and templates are, a plain list of add-ons that ship with Exponential and what each does for a non-technical person, what is not available as an extension, and how to ask for one.
keywords: extension, add-on, plugin, design, theme, template, newsletter, maps, two-step sign-in, CAPTCHA, writing assistant, layouts, shop, what is not included, explained
type: help
---
# Extensions explained

## Contents

1. [The comparison: kitchen appliances](#the-comparison-kitchen-appliances)
2. [Add-ons that ship with Exponential](#add-ons-that-ship-with-exponential)
3. [What you will not find](#what-you-will-not-find)
4. [Designs, themes and templates](#designs-themes-and-templates)
5. [Asking for an extension](#asking-for-an-extension)

## The comparison: kitchen appliances

A kitchen works without a dishwasher, but adding one changes what you can do. An **extension** is such an appliance:
an add-on that gives the site an extra ability. A developer installs it and switches it on; after that, it shows up
as new buttons, tabs or fields in the administration. You never install one yourself.

## Add-ons that ship with Exponential

Exponential is distributed with many extensions (the list is in the file `composer.json` of the installation). Which
ones are **switched on** on your site is decided by whoever built it. Those that matter most to non-technical people:

| Extension | What it does for you | Read more |
|---|---|---|
| Exponential Layouts (`explayouts`) | Arrange pages with blocks by drag and drop | [Making a page with Layouts](../first-steps/making-a-page-with-layouts.md) |
| Newsletter (`cjw_newsletter`) | Lists of subscribers and sending newsletters | [Newsletters](../../customers/growth/newsletters.md) |
| Two-step sign-in (`sevenx_authentication_2fa`) | A code from your phone or e-mail after your password | [Your password and two-step sign-in](../first-steps/your-password-and-two-step-sign-in.md) |
| Exponential Intelligence (`expai`) | A writing assistant that suggests drafts | [Using the writing assistant](../first-steps/using-the-writing-assistant.md) |
| Maps (`ezgmaplocation`) | A map location field for an item | [Local businesses](../../local-businesses/local-businesses-start-here.md) |
| Spam protection (`hcaptcha`, `recaptcha`) | A check on forms that a person, not a robot, filled them in | [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md) |
| Tags (`eztags`) | Keywords for grouping content | [How do I tag content?](../../manual/how-do-i-tag-content.md) |
| Search engine sitemaps (`bcgooglesitemaps`) | A list of pages for search engines | [Sitemaps](../../creators/website-creators/sitemaps.md) |
| Shop payment (`ezpaypal`, `ezauthorize`) | Card or PayPal payment in the built-in shop | [Payment gateways](../../customers/commerce/payment-gateways.md) |
| Multiple upload (`ezmultiupload`) | Uploading many files at once | [Images, media and uploads](../../media-and-uploads.md#upload-files) |

**Setup > System information** (if your role allows it) lists the extensions that are switched on
([How do I see active extensions and system information?](../../manual/how-do-i-see-active-extensions-and-system-information.md)).

## What you will not find

Be wary of anyone who promises these "out of the box"; they need an outside service, or custom work by a developer:

- appointment booking, table reservations or event ticketing;
- a dedicated donation module with recurring giving
  ([Donations and fundraising pages](../../businesses/nonprofits/donations-and-fundraising-pages.md));
- a one-click importer from another blogging or website system;
- a ready-made reader comment system for every site: the demonstration installation has a *Comment* content type,
  but whether and how comments work depends on how your site was built
  ([Blog: start here](../../customers/types/blog-start-here.md)), so ask before you plan for them.

If you need one of these, ask a developer or [partner](../../partners/partners-start-here.md) to compare an outside
service you can link to or embed with custom work.

## Designs, themes and templates

How the site **looks** is not an extension setting you change in the edit form:

- A **design** or **theme** is the look of the whole site: colors, fonts, layout of the header and footer.
  Exponential ships with ready-made ones ([Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md)).
- **Templates** are the files that turn your content into web pages. A designer or developer edits them.

You change the **content**. A designer changes the **look**. If something looks wrong everywhere, for example the
footer, it is a design question.

## Asking for an extension

1. Describe the need, not the tool: "visitors should find our shop on a map".
2. Ask whether a shipped extension already does it (see the table above).
3. Ask what it changes for editors, and whether it needs an outside account (a map or CAPTCHA provider, a payment
   provider, an AI provider) with its own costs and terms.
4. Ask that it is tested on a copy of the site first.

## Related pages

- [Words explained](README.md)
- [What you never have to do](../what-you-never-have-to-do.md)
- [Features customers often miss](../../customers/value/features-customers-often-miss.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
