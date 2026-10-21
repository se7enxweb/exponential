---
title: Local SEO basics
audience: local-business, businesses, customer, editor
category: business-solutions
order: 21
summary: What search engines need from the website of a local business - name, address and phone the same everywhere, the town and trade in titles and text, one page per service, a contact page, fast pages on phones - with the Exponential feature behind each step and a checklist.
keywords: local SEO, local search, name address phone, NAP, title, description, service pages, town, location page, sitemap, mobile, page speed, checklist
type: help
---
# Local SEO basics

This page is for the owner or editor of a local business website who wants the site to show up when people nearby
search for what the business does. It is a short list of habits. None of them needs programming once the site is set
up.

## Contents

1. [Same name, address and phone everywhere](#same-name-address-and-phone-everywhere)
2. [Say what you do and where](#say-what-you-do-and-where)
3. [One page per service](#one-page-per-service)
4. [A complete contact and location page](#a-complete-contact-and-location-page)
5. [Technical basics your builder sets up once](#technical-basics-your-builder-sets-up-once)
6. [Habits that keep it working](#habits-that-keep-it-working)
7. [Checklist](#checklist)

## Same name, address and phone everywhere

Search engines compare what your website says with what map platforms, directories and your social profiles say.
Write the business name, street address and phone number **exactly the same way** everywhere: same abbreviations, same
phone format. On the website, keep them in one content object shown in the footer and on the contact page
([Planning your pages](../getting-started/planning-your-pages.md#write-each-fact-once)), so there is only one place to
change.

## Say what you do and where

- The **page title** of the front page names the trade and the town: "Bakery in Riverton - Example Bakery", not only
  "Home". Titles come from the page's name, or from the `xrowmetadata` field when it is filled
  ([Titles and descriptions with xrowmetadata](titles-and-descriptions-with-xrowmetadata.md)).
- The **first paragraph** of the front page says the same in a full sentence.
- If you serve several towns (a plumber, a mobile hairdresser), list them in text on a "Areas we serve" page. Do not
  create one near-identical page per town: that looks like spam to search engines.

## One page per service

A page called "Boiler repair" with a clear description, prices or a price range, photos and a contact link is found
more often than one long "Services" page. In Exponential each service is an article in a Services folder, and its
name becomes the address (`/Services/Boiler-repair`) automatically
([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).

## A complete contact and location page

| Item | How |
|---|---|
| Address as text | in the shared contact object |
| Map | [Showing your location on a map](../maps-and-hours/showing-your-location-on-a-map.md) |
| Opening hours, including holiday hours | [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md) |
| Phone as a dialable link | a link with `tel:` in the rich text editor, or in the template |
| How to get there: parking, public transport, accessibility of the entrance | text |
| Contact form | [A contact form for your business](../contact-and-forms/a-contact-form-for-your-business.md) |

## Technical basics your builder sets up once

| Basic | Exponential feature | Read |
|---|---|---|
| Readable addresses | URL aliases, built in | [Nice URLs and moved pages](nice-urls-and-moved-pages.md) |
| Title and description per page | `xrowmetadata`, or fields of your own | [Page metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md) |
| A sitemap | `bcgooglesitemaps` cron job | [Sitemaps and robots.txt](sitemaps-and-robots-txt.md) |
| `robots.txt` with the sitemap address | shipped at the web root | [robots.txt for your site](../../creators/website-creators/robots-txt.md) |
| HTTPS and one host name | web server setup | [Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md) |
| Works on phones | a responsive design; the default public design is responsive | [Mobile visitors](../marketing/mobile-visitors.md) |
| Fast pages | caching, Velocity | [Performance and caching](../../creators/website-creators/performance-and-caching.md) |
| Structured data | partly; see the page | [Structured data: what exists](structured-data-what-exists.md) |

## Habits that keep it working

- Update holiday hours **before** the holiday, on the site and on the platforms.
- Add a news item or special now and then; a site that changes is a site that is alive.
- Give every photo a short alternative text that describes it ("Fresh rye bread on the counter"); it helps visitors
  with screen readers and search engines ([Alternative text for images](../../government/accessibility/alternative-text-for-images.md)).
- Use link texts that say where they lead ("Opening hours", not "click here").
- Look at what visitors search for on your own site ([Site search and what visitors look for](../../customers/day-to-day/site-search-and-what-visitors-look-for.md)).

## Checklist

- [ ] Name, address and phone written the same way on the site and on every platform.
- [ ] Front page title names trade and town.
- [ ] One page per main service, each with its own title and description.
- [ ] Contact page with address, map, hours, phone link, directions and a form.
- [ ] Sitemap generated by cron and registered in `robots.txt`.
- [ ] HTTPS everywhere, one host name.
- [ ] Site tested on a phone.
- [ ] Holiday hours planned for the year ([Holiday and seasonal hours](../maps-and-hours/holiday-and-seasonal-hours.md)).

## Related pages

- [SEO basics for Exponential sites](../../creators/website-creators/seo-basics.md)
- [SEO checks](../../customers/day-to-day/seo-checks.md)
- [SEO monthly checklist](../../customers/day-to-day/seo-monthly-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
