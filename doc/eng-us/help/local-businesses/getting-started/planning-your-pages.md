---
title: Planning your pages
audience: local-business, businesses, customer, website-creator
category: business-solutions
order: 12
summary: A page list for a local business site - shop, restaurant, trades, salon or practice - with the content class behind each page, where opening hours and contact details live so they are written once, and how to keep the tree small.
keywords: page plan, site map, content tree, content classes, restaurant menu, services page, team page, opening hours, contact page, local business, folder, article
type: help
---
# Planning your pages

This page is for the owner, or the person building the site, who wants a page list before work starts. In Exponential
every page is a **content object** of a **content class** (folder, article, product, feedback form and so on), placed
in a **content tree**. A good plan is a short tree where each fact lives in one place.

## Contents

1. [A page list by kind of business](#a-page-list-by-kind-of-business)
2. [Which class for which page](#which-class-for-which-page)
3. [Write each fact once](#write-each-fact-once)
4. [Keep the tree small](#keep-the-tree-small)
5. [Worksheet](#worksheet)

## A page list by kind of business

| Page | Shop | Restaurant or cafe | Trades | Salon | Practice |
|---|---|---|---|---|---|
| Home | yes | yes | yes | yes | yes |
| What we offer | product range | menu | services and areas served | treatments and prices | treatments, insurance notes |
| Opening hours and location | yes | yes | service hours, area | yes | yes |
| About us, team | yes | yes | yes, with qualifications | stylists | practitioners, with qualifications |
| Contact | form | form, reservations link | quote request form | booking link | appointment link, emergency note |
| News or specials | offers | daily specials, events | - | offers | holiday closures |
| Shop | if you sell online | vouchers | - | gift cards, products | - |
| Legal pages | imprint, privacy | same | same | same | same, plus professional rules |

Legal pages differ from country to country; ask your adviser what you must publish. This guide is not legal advice.

## Which class for which page

The shipped site packages bring the classes most small sites need. The `ezwebin` packages include `folder`, `article`,
`frontpage`, `feedback_form`, `event`, `event_calendar`, `product` and others (`extension/ezwebin/packages/`;
[ezwebin](../../../features/6.0/extensions/ezwebin.md)).

| Page | Class | Note |
|---|---|---|
| Home | `frontpage`, or a layout from the layouts editor | [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md) |
| Section pages (Services, Menu) | `folder` | a folder lists what is below it |
| One service, one treatment | `article` | title, intro, body, image |
| Team member | `article`, or a class of your own | ask the person before publishing their photo |
| Contact | `feedback_form` | [A contact form for your business](../contact-and-forms/a-contact-form-for-your-business.md) |
| Events, specials | `event` in an `event_calendar` | [Events and specials](../marketing/events-and-specials.md) |
| Products, vouchers | `product` | [A small catalog](../simple-shop/a-small-catalog.md) |
| Opening hours | a small class of your own, or a text block | [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md) |

Choosing fields is explained in [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md) and
[Content classes for websites](../../creators/website-creators/content-classes-for-websites.md).

## Write each fact once

Your address, phone number and opening hours appear in many places: the footer, the contact page, the map, perhaps a
banner. If they are typed in each place, one of them will be wrong after the next change. Ask whoever builds the site
to:

- keep the address, phone and hours in **one content object** and show it in the footer and on the contact page
  through the templates ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md));
- put the map on the same object as the address, so moving premises means one edit;
- use **relations** where a page needs to show something kept elsewhere
  ([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)).

## Keep the tree small

- Two levels are enough for most local businesses: Home, then sections, then single pages.
- The names of folders become the addresses (`/Services/Boiler-repair`), so pick short, clear names before launch
  ([Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)).
- One page per service, not one page per keyword. Search engines and visitors both prefer one clear page.
- Old pages are moved to the trash or hidden, not left to rot ([Archiving and the trash](../../customers/day-to-day/archiving-and-the-trash.md)).

## Worksheet

Copy this table into your [website brief](../templates/website-brief-for-a-local-business.md):

| Page name | Address | Class | Who writes it | Who updates it, how often | Ready by |
|---|---|---|---|---|---|
| Home | `/` | frontpage | | | |
| Services | `/Services` | folder | | | |
| Opening hours and location | `/Visit-us` | opening hours class | | | |
| Contact | `/Contact` | feedback_form | | | |
| | | | | | |

## Related pages

- [What a small business website needs](what-a-small-business-website-needs.md)
- [Site map and content inventory](../../creators/website-creators/site-map-and-content-inventory.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
