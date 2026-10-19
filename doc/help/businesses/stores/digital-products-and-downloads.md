---
title: Digital products and downloads
audience: businesses, website-creator, developer
category: business-solutions
order: 170
summary: Selling files such as e-books, software, patterns or music with Exponential - what the built-in shop and the download system provide, what is missing for automatic delivery after payment, and three honest ways to close the gap.
keywords: digital products, downloads, e-books, software sales, file sales, ezbinaryfile, content/download, protected downloads, delivery after payment, licenses, digital goods VAT
type: help
---
# Digital products and downloads

This page is for business owners who want to sell files: e-books, software, templates, sewing patterns, music, course
material. It explains which parts Exponential already has, what is not included, and how a business can close the gap
with a small extension or a manual process.

## Contents

1. [The short answer](#the-short-answer)
2. [What already exists](#what-already-exists)
3. [What is missing](#what-is-missing)
4. [Three ways to deliver files](#three-ways-to-deliver-files)
5. [Setting up the products](#setting-up-the-products)
6. [Tax on digital products](#tax-on-digital-products)
7. [Checklist](#checklist)

## The short answer

Exponential can sell a product and it can protect a file so that only certain users may download it. What the kernel
does **not** do is connect the two: there is no built-in step that gives the buyer access to a file once the order is
paid. We searched the shop module (`kernel/shop/`) and the download view for such a link and found none. The gap is
small and well understood, and the options are below.

## What already exists

| Building block | What it does | Read |
|---|---|---|
| File field (`ezbinaryfile`) | Holds a file on any content object, including a product | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| `content/download` | Sends a file only after checking that the visitor may read the object | same page, "How a download works" |
| Sections and roles | Decide who may read which content | [Sections and object states](../../creators/website-creators/sections-and-object-states.md), [Roles and policies](../../../guides/roles-and-policies.md) |
| User groups | Collect customers who may download | [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md) |
| Workflow triggers on the shop | Run your own step at checkout | [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#workflow-triggers) |
| Permanent receipts | A signed link to the order, valid for years | [Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md) |

The download view's permission check means a file in a members-only section can only be downloaded by members, and
files must never be linked directly from the storage directory, which would bypass the check
([Files and downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).

## What is missing

- A step that grants the buyer access to the purchased file when payment is confirmed.
- Download links for **guests**, who have no account to grant access to.
- Limits on the number of downloads, or links that expire.
- License keys for software.

## Three ways to deliver files

### 1. Manual delivery

When an order reaches **Paid**, a staff member sends the file, or adds the customer's account to the user group that may
read it, and sets the order to **Completed** ([Orders and order statuses](orders-and-order-statuses.md)).

- Good for: a handful of orders a week.
- Cost: staff time; delay for the customer.

### 2. Membership access

Sell access instead of files: the product is a membership, the files sit in a protected section, and buying the
membership puts the customer in the group that may read it. Granting the group can be manual at first.

- Good for: libraries of patterns, courses, back issues.
- Read: Members-only content.

### 3. A small extension

A developer writes a workflow event for the checkout that, once payment is confirmed, either adds the buyer to a user
group with read access to the purchased product's files, or sends a signed, time-limited download link (similar in idea
to the signed receipt link). Guidance: [Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md),
[Workflows and events](../../creators/app-creators/workflows-and-events.md),
[Access control in code](../../creators/app-creators/access-control-in-code.md).

- Good for: stores where downloads are the main business.
- Ask the developer to publish it as an extension, so others can use and maintain it with you
  ([Contribute extensions back](../../contribute-extensions-back.md)).

## Setting up the products

- Use a product class with a price and, separately, a protected file object for the deliverable: the public product
  page must not carry the file itself, or anyone who can read the page can download it.
- Leave shipping out for digital-only orders ([Shipping and handling](shipping-and-handling.md)).
- Tell customers the format and size before they buy; it is also an accessibility requirement for many sites
  ([Files and downloads](../../creators/website-creators/files-and-downloads.md#download-links-in-templates)).
- Large files: PHP upload limits apply to editors, and very large files are better hosted on a service built for them
  ([Files and downloads](../../creators/website-creators/files-and-downloads.md#large-files)).
- The `enhancedezbinaryfile` extension adds options to the file field
  ([enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md)).

## Tax on digital products

Digital products are often taxed differently from physical ones, and sometimes by the buyer's country. The shop's VAT
rules can express a rate per customer country and product category ([VAT rules and VAT types](vat-rules-and-vat-types.md)),
so a "Digital" product category with its own rules is possible. Which rates apply is a question for your tax adviser.

## Checklist

- [ ] Delivery method chosen: manual, membership or extension.
- [ ] Files sit in a section that visitors cannot read; tested while signed out.
- [ ] Product pages do not carry the deliverable file.
- [ ] Customers know format, size and how they receive the file.
- [ ] Digital products have their own product category for VAT rules, if your adviser says so.
- [ ] A test purchase delivers the file within the time you promise.

## Related pages

- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Files and digital collections](../libraries/files-and-digital-collections.md)
- [What the built-in shop does](what-the-built-in-shop-does.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
