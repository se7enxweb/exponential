---
title: Promoting your store
audience: businesses, sales-marketing, editor
category: business-solutions
order: 190
summary: Bringing customers to an Exponential store - search engine basics, sitemaps and metadata, social sharing, feeds, newsletters with consent, campaign landing pages, ratings and measuring what works.
keywords: promote store, marketing, SEO, sitemaps, metadata, Open Graph, social sharing, RSS, newsletter, landing pages, campaigns, analytics, star ratings
type: help
---
# Promoting your store

This page is for owners and marketers who want more visitors and more orders. It lists the promotion tools an
Exponential store has, from search engines to newsletters, and points to the pages that set them up. It does not
promise results: what works depends on your products and market, so measure.

## Contents

1. [Start with the basics](#start-with-the-basics)
2. [Search engines](#search-engines)
3. [Social sharing](#social-sharing)
4. [Feeds](#feeds)
5. [Newsletters to customers](#newsletters-to-customers)
6. [Campaign landing pages](#campaign-landing-pages)
7. [Trust on the product page](#trust-on-the-product-page)
8. [Measuring what works](#measuring-what-works)
9. [A promotion calendar](#a-promotion-calendar)

## Start with the basics

Before any campaign, make sure the store is easy to buy from: clear product pages, honest shipping terms, a short
checkout ([Basket and checkout flow](basket-and-checkout-flow.md)). Promotion multiplies what is there; it does not fix
a confusing checkout.

## Search engines

What Exponential gives you, and the checklist, are in [SEO basics for Exponential sites](../../creators/website-creators/seo-basics.md).
The points that matter most for a store:

| Point | Why | Read |
|---|---|---|
| Readable addresses | Product names in the URL | [URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md) |
| One address per page | Products in several categories must not compete with themselves | [SEO basics](../../creators/website-creators/seo-basics.md#one-address-per-page) |
| Titles and descriptions per product | What the search result shows | [Page metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md) |
| XML sitemaps | New products found quickly | [XML sitemaps](../../creators/website-creators/sitemaps.md) |
| robots.txt | Keep basket, checkout and account pages out of search | [robots.txt](../../creators/website-creators/robots-txt.md) |
| Redirects for removed products | Old links still lead somewhere | [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md) |

On alpha, the product class carries an `xrowmetadata` field for metadata
([xrowmetadata](../../../features/6.0/extensions/xrowmetadata.md)); sitemaps come from `bcgooglesitemaps` or
`xrowmetadata` ([XML sitemaps](../../creators/website-creators/sitemaps.md#choose-an-extension)).

## Social sharing

When a customer shares a product, the preview shows a title, text and image. Open Graph data and sharing images are set
up as described in [Page metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md#open-graph-and-sharing-images).
Use a product image that works when cropped to a wide format.

## Feeds

RSS feeds can carry new products or offers to readers, partner sites and apps
([RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)). For selling through partners
and networks, see [Selling through networks and partners](../mobile-and-headless/selling-through-networks-and-partners.md)
and [Feeds and syndication](../mobile-and-headless/feeds-and-syndication.md).

## Newsletters to customers

Existing customers are the cheapest to reach again. Exponential's newsletter extension `cjw_newsletter` handles lists,
subscriptions, editions and bounces ([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md),
[Newsletters](../newsletters/README.md)).

> **Warning**
> Buying from you is not consent to marketing mail. Order mail is essential mail; offers are optional mail that needs
> the customer's opt-in ([Mail preferences](../../../features/6.0/mail-preferences.md),
> [Opt-in and consent](../newsletters/opt-in-and-consent.md)). This is not legal advice; check the rules that apply to
> you with your lawyer.

Offer the newsletter at checkout or on the receipt page as an unticked choice, and on the product pages.

## Campaign landing pages

A sale, a season or a new collection deserves its own page. Layouts let editors build such pages from zones and blocks,
with drafts and a preview before publishing
([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md),
[Campaigns and landing pages](../nonprofits/campaigns-and-landing-pages.md)). Put the campaign's products on the page
as extra locations or related objects, so prices stay in one place
([Store search and navigation](store-search-and-navigation.md#one-product-in-several-places)).

Pair a campaign with a discount rule when it is meant for a customer group ([Discounts and wishlists](discounts-and-wishlists.md)).

## Trust on the product page

- **Ratings**: `ezstarrating` lets customers rate products from 1 to 5 stars, one vote per user per session, and
  templates can sort by rating ([ezstarrating](../../../features/6.0/extensions/ezstarrating.md)).
- **Specifications**: a clear table answers questions before they are asked.
- **Your terms**: shipping, returns and contact on every product page.

Never publish invented reviews or ratings; show only what customers gave.

## Measuring what works

| Question | Where to look |
|---|---|
| Did orders and revenue rise during the campaign? | Store dashboard, revenue per day ([Store dashboard and reports](store-dashboard-and-reports.md)) |
| Which products sold? | Best sellers and **Store > Statistics** |
| Where did visitors come from? | An analytics service ([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)) |
| What did visitors search for? | Search statistics ([Site search](../../creators/website-creators/site-search.md)) |
| Did more baskets get abandoned? | Dashboard, baskets abandoned |

Analytics services need consent in many countries and must be allowed by your Content Security Policy; both are
covered on the analytics page.

## A promotion calendar

- [ ] Monthly: one newsletter edition with new and best-selling products.
- [ ] Per season: a landing page, prepared two weeks ahead as a draft.
- [ ] Before a peak: performance check ([Store performance at peak times](store-performance-at-peak-times.md)).
- [ ] After each campaign: compare dashboard figures with the weeks before.
- [ ] Yearly: review categories, tags and search terms without results.

## Related pages

- [SEO basics for Exponential sites](../../creators/website-creators/seo-basics.md)
- [Case studies and content marketing](../../business/case-studies-and-content-marketing.md)
- [Newsletter planning](../newsletters/newsletter-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
