---
title: Add a link to an external service
audience: government, editor
category: public-sector
order: 330
summary: For editors - link from a page to an online service on another system (an application portal, a payment service, a booking system) with a clear link text, a note that the visitor leaves the site, and a link that keeps working.
keywords: external link, online service, application portal, payment, booking, link text, URL field, ezurl, link check, Link management, leaving the site
type: help
---
# Add a link to an external service

This page is for editors who send visitors from the website to a system where they apply, pay or book. The page
explains, the external system does the transaction ([Linking to online transactions](../../building/service-catalog/linking-to-online-transactions.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You have the exact start address of the service, given by its owner, not copied from a browser after signing in
  (such addresses often contain session data).
- You know what visitors need before they start (documents, an ID, a payment card).

## Steps

**In a URL field** (for example **Apply online** of a Service, or the **Link** class):

1. **Edit** the page; paste the address in the URL field; fill its text field if it has one ("Apply for a resident
   parking permit").
2. **Send for publishing**.

**In rich text:**

1. Select the words that should be the link, choose the link button, choose an external address, paste it.
2. Write the link text so it makes sense out of context: "Apply for a resident parking permit (on the <service name>
   website)".

**Before the link**, in the text: what to have ready, how long it takes, the alternative for people who cannot use it
online.

## Check the result

- [ ] The link opens the start of the service, signed out, in a private window.
- [ ] The link text says where it goes.
- [ ] **Setup > Link management** (`/url/list`) lists the address; the link check marks it valid after its next run.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| "Click here" | the link text names the service |
| Opening in a new window without saying so | say "(opens in a new window)" or do not force it |
| An address with a session or tracking code | use the clean start address |
| The same address typed in many pages | use one URL field or one Link object; the URL table is shared, so fixing one entry fixes every page that uses it ([Find and fix broken links](find-and-fix-broken-links.md)) |

## Related pages

- [Publishing tasks](README.md)
- [Publish a service page](publish-a-service-page.md)
- [Link and content checks](../../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
