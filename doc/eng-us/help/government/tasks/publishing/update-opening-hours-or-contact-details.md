---
title: Update opening hours or contact details
audience: government, editor
category: public-sector
order: 290
summary: For editors - change opening hours, a holiday closure or a phone number once in the contact point or office object, so every service page that relates it shows the change, and find pages that still type the old details.
keywords: opening hours, contact details, holiday closure, phone number, contact point, office, relations, reverse related objects, search, update once
type: help
---
# Update opening hours or contact details

This page is for editors who change opening hours, closures or contact details. When the site keeps contact
information in contact point objects related from service pages, you change it once
([Contact points and offices](../../building/service-catalog/contact-points-and-offices.md)). Where details were typed
into pages, you must find and change each.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You know the new hours or details and from when they apply.
- You know where contact points live on your site (a "Contact points" folder, or office pages).

## Steps

**Permanent change:**

1. Open the contact point (or office page), **Edit**.
2. Change **Opening hours**, **Phone** or **E-mail**.
3. **Send for publishing**.

**Temporary change (holiday, closure):**

1. **Edit** the contact point.
2. In **Temporary changes** (if the class has it), write "Closed on <date>" or "Open 10:00 to 14:00 on <date>".
3. **Send for publishing**; empty the field after the date.

**Find pages that still type the old details:**

1. Search for the old phone number or e-mail address with the administration search (`/content/search`).
2. Edit each result; replace the typed details with a relation to the contact point where the class allows it.

## Check the result

- [ ] The contact point's page shows the new details, signed out.
- [ ] A service page that relates it shows the new details. If it still shows the old ones, its cached view has not
  been renewed; ask the web team, or see [Refresh a page that shows old content](refresh-a-page-that-shows-old-content.md).
- [ ] On the contact point, the **Relations** tab, **Reverse related objects**, lists the services that use it.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Typing the hours into each service page | relate the contact point instead |
| A personal phone number or name | use team numbers and mailboxes |
| Forgetting to remove a temporary note | put a reminder in your calendar |
| Hours in an image | text only; images are not readable by everyone |

## Related pages

- [Publishing tasks](README.md)
- [Publish a service page](publish-a-service-page.md)
- [Searching](../../../searching.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
