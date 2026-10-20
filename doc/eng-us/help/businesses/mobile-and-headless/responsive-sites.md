---
title: Responsive sites
audience: businesses, website-creator, sales-marketing
category: business-solutions
order: 20
summary: What makes an Exponential site work well on phones and tablets - responsive designs, image sizes and formats, fast pages, accessible forms, and a test plan a business can run itself.
keywords: responsive, mobile website, phones, tablets, responsive design, srcset, image aliases, WebP, performance, accessibility, zoom, sevenx_themes_simple, business
type: help
---
# Responsive sites

This page is for owners and marketing leads who want their website and store to work well on phones and tablets. It
explains which parts Exponential provides, which parts belong to your design, and how to test a site yourself before
launch. It is the first step of any mobile plan: an app does not replace a website that works on phones
([Mobile strategy](mobile-strategy.md)).

## Contents

1. [What "responsive" means for your business](#what-responsive-means-for-your-business)
2. [The design decides](#the-design-decides)
3. [Images that fit small screens](#images-that-fit-small-screens)
4. [Fast pages on mobile connections](#fast-pages-on-mobile-connections)
5. [Forms, checkout and accessibility](#forms-checkout-and-accessibility)
6. [A test plan you can run yourself](#a-test-plan-you-can-run-yourself)

## What "responsive" means for your business

A responsive site rearranges itself for the screen: one column on a phone, more on a desktop, menus that open by
touch, text that stays readable without zooming. For a business, it means customers can find a product, read the
details and complete a purchase or a form on whatever device they hold.

## The design decides

In Exponential, how pages look is decided by the design (templates and stylesheets), not by the content. Content
stays the same; the design adapts it to the screen.

| Design | Responsive? | Source |
|---|---|---|
| `sevenx_themes_simple`, the default public design | Described as "the light, responsive public design of the default installation", with the stylesheet `main.css` and "its responsive companion `main.res.css`" | [sevenx_themes_simple](../../../features/6.0/extensions/sevenx_themes_simple.md) |
| Other shipped themes | See their own pages | [Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md#the-themes-that-ship) |
| Your own design | Whatever your designers build | [Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md#your-own-design) |

When you commission a design, write "works on phones from 320 px wide, without horizontal scrolling, and at 200%
zoom" into the brief. The accessibility guidance for site builders asks for exactly that: "layouts that work at 200%
zoom and on narrow screens, no horizontal scrolling"
([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls)).

## Images that fit small screens

Exponential stores the uploaded image once and makes **image aliases**: variations in defined sizes. A design can
offer two or three aliases of the same image with `srcset`, so a phone downloads a small file and a large screen a
large one ([Responsive images](../../creators/website-creators/images-and-image-variations.md#responsive-images)).

| Setting (shipped) | Meaning |
|---|---|
| Editors may upload | JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG |
| Pages may show | JPEG, PNG, WebP, GIF |
| SVG | Sanitized on upload and never scaled |

Sources: [Images and image variations](../../creators/website-creators/images-and-image-variations.md#converters-formats-and-quality)
and [SVG and image formats](../../creators/website-creators/svg-and-image-formats.md).

> **Tip**
> Ask your designers which aliases the design defines, and ask editors to upload images large enough for the largest
> alias. Small uploads look blurred on large screens; the aliases cannot add detail.

## Fast pages on mobile connections

Mobile visitors often have slower connections. Exponential has several cache layers, and the website creators' guide
lists "the levers, in order of effect" ([Performance and caching for websites](../../creators/website-creators/performance-and-caching.md)).
Relevant for phones:

- Packed stylesheets and scripts through ezjscore, so a page loads fewer files
  ([Stylesheets and scripts for your site](../../creators/website-creators/stylesheets-and-scripts.md)).
- HTTP caching and CDNs for anonymous visitors
  ([HTTP caching, proxies and CDNs](../../creators/website-creators/http-caching-and-cdns.md)).
- Under Exponential Velocity, a response cache and an optional browser navigation cache for repeat visits
  ([Offline and repeat visits](offline-and-repeat-visits.md)).

## Forms, checkout and accessibility

Forms are where mobile visitors give up. The site builders' accessibility guidance for forms helps on phones too:

- every input has a visible label;
- required fields are marked in text;
- errors are shown next to the field and summarized at the top;
- CAPTCHAs have an accessible alternative (both shipped CAPTCHA services offer one).

Source: [Accessibility for site builders, Forms](../../creators/website-creators/accessibility-for-site-builders.md#forms).
For the store's checkout, see the [basket and checkout flow](../stores/basket-and-checkout-flow.md).

## A test plan you can run yourself

Use a real phone, not only a narrow desktop window.

- [ ] The start page loads and is readable without zooming.
- [ ] The menu opens and closes by touch; every main section is reachable.
- [ ] Search works and its results are readable.
- [ ] A product or article page shows images at a sensible size, without horizontal scrolling.
- [ ] A form (contact, sign-up) can be filled, sent, and its errors are understandable.
- [ ] For a store: add to basket, change quantity, check out, receive the confirmation mail.
- [ ] Phone numbers and addresses can be tapped.
- [ ] The site works at 200% text zoom on a desktop.
- [ ] Each language and each brand passes the same test
      ([Multilingual checklist](../multilingual/multilingual-checklist.md),
      [Multi-brand checklist](../multisite/multi-brand-checklist.md)).

## Related pages

- [Mobile strategy](mobile-strategy.md)
- [Mobile checklist](mobile-checklist.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Performance and caching for websites](../../creators/website-creators/performance-and-caching.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
