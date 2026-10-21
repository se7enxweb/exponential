---
title: Ratings and reviews
audience: local-business, businesses, customer
category: local-business
order: 54
summary: What Exponential offers a local business for customer opinions - ezstarrating star ratings (1 to 5 stars per object, one vote per session, no written text), comments with the shipped comment class, and why written reviews usually live on outside platforms - with moderation, honesty rules and a checklist.
keywords: reviews, ratings, star rating, ezstarrating, comments, testimonials, moderation, outside platforms, trust, local business
type: help
---
# Ratings and reviews

This page is for local businesses that want to show what customers think. It separates three things that are often
mixed up: **star ratings**, **comments** and **written reviews**.

## Contents

1. [What exists](#what-exists)
2. [Star ratings with ezstarrating](#star-ratings-with-ezstarrating)
3. [Comments](#comments)
4. [Written reviews and testimonials](#written-reviews-and-testimonials)
5. [Being honest about opinions](#being-honest-about-opinions)
6. [Checklist](#checklist)

## What exists

| Thing | In Exponential | Source |
|---|---|---|
| Star ratings, 1 to 5, on any content | yes, `ezstarrating` (in the standard `composer.json`) | [ezstarrating](../../../features/6.0/extensions/ezstarrating.md) |
| Comments under a page | yes, a `comment` class from the `ezwebin` site package, with a template in `ezwebin` | [ezwebin site package](../../../features/6.0/extensions/ezwebin-ezpackage.md) |
| Written reviews with a star rating each, a reviewer name and a reply | no review module found | searched in `composer.json` and `extension/`, 20 October 2026 |
| Importing reviews from map or review platforms | no | - |

## Star ratings with ezstarrating

What it does, from the extension's README and [ezstarrating](../../../features/6.0/extensions/ezstarrating.md):

- adds a **Star Rating** field type; add it to a class (product, service, recipe) and visitors rate objects of that
  class from 1 to 5 stars;
- each user can vote **once per session**;
- ratings are stored and averaged per object; templates show the average, and lists can be sorted by rating;
- voting needs a permission (`ezjscore` / `call` with the limitation `ezstarrating_rate`); the shipped site package
  gives it to anonymous visitors.

What it does not do: no text, no reviewer name, no moderation queue, no protection against someone voting again in a
new session. For a local business, star ratings work best on products or dishes, as a light signal. They are not a
substitute for reviews.

## Comments

The `comment` class lets visitors write under an article or a product. The `ezwebin` site package gives anonymous
visitors permission to create comments in the Standard section. If you allow comments:

- protect the comment class with a CAPTCHA ([Stopping spam](stopping-spam.md));
- decide whether comments appear immediately or after approval (an approval workflow, set up by your builder:
  [Approvals and review](../../customers/day-to-day/approvals-and-review.md));
- read them regularly; an unanswered complaint under your own page is worse than none.

Many local businesses switch comments off and send people to the contact form instead. Removing the permission is a
small change for your builder ([Visitor permissions](../../creators/website-creators/visitor-permissions.md)).

## Written reviews and testimonials

Most customers write reviews on map, search and review platforms, not on the business's own site
([Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)). On your site you
can:

- publish **testimonials**: short quotes from customers who agreed in writing to be quoted, with only the name they
  allow (often first name and town);
- link to your profiles on review platforms ("Read our reviews");
- embed a widget from a review platform, if it offers one; that is an outside script (privacy note, Content Security
  Policy).

## Being honest about opinions

- Do not invent reviews or ratings, and do not write them yourself under a customer's name. Many countries forbid it.
- Do not remove critical comments only because they are critical; remove spam, abuse and personal data.
- Do not copy reviews from platforms onto your site without asking the authors.
- Mark testimonials you selected as selected.

## Checklist

- [ ] Decided: star ratings yes or no, and on which classes.
- [ ] Comments allowed or switched off; if allowed, with CAPTCHA and a moderation routine.
- [ ] Testimonials only with written permission.
- [ ] Links to review profiles where customers actually write.
- [ ] Someone reads and answers reviews weekly.

## Related pages

- [Contact and forms](README.md)
- [Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)
- [Promoting your store](../../businesses/stores/promoting-your-store.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
