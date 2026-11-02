---
title: "A fast, free brochure site for your business"
audience: local-business, businesses, customer
category: local-business
order: 5
summary: Opening hours, services, prices and contact details on a fast public site with no hosting fee - edited in Exponential, published as a static copy to GitHub Pages, and when not to do it (shops, bookings).
keywords: local business, small business, shop window, brochure site, free website, free hosting, static site, GitHub Pages, gh-pages, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, opening hours, contact
type: help
---
# A fast, free brochure site for your business

Most small business sites change a few times a month: opening hours, a new service, a holiday notice. Such a site does
not need a server running all day. With static publishing you edit it in Exponential and publish a static copy that
GitHub Pages hosts for free, at your own domain.

## What you'll have at the end

- Your site at `https://www.your-business.example/`, with HTTPS, loading fast on every phone.
- No monthly hosting bill for the public site; you pay only for the domain.
- A one-click way back if a change went wrong.

## Good fits

- Opening hours, address, map link and phone number.
- Services and price lists, a team page, photos of your work.
- News and offers, published when you publish them in Exponential, or automatically on a schedule.

## When not to use it

Be honest with yourself here:

- **An online shop** with a basket, checkout and stock. GitHub Pages terms also do not allow running a shop on it.
  Keep the shop on a real server.
- **Bookings and appointments** that need a calendar. Link to a booking service instead.
- **Customer accounts.** A static copy has no login.

A contact form can still work: it can post to your Exponential installation, or show a link to the live page. See
[Search, forms and comments on a static site](../static-publishing/search-forms-and-comments-on-a-static-site.md) and
[GitHub Pages limits and terms](../static-publishing/github-pages-limits-and-terms.md).

## Your own domain

Customers should see your name, not `github.io`. Set the domain in the wizard, add the DNS records your domain
registrar asks for, and GitHub issues the certificate. Step by step:
[Use your own domain](../static-publishing/use-your-own-domain.md).

## Getting found

The build writes a `sitemap.xml` and `robots.txt` and keeps your page titles and descriptions. See
[SEO on a static site](../static-publishing/seo-on-a-static-site.md).

## Make yours now

Follow [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md), then
[use your own domain](../static-publishing/use-your-own-domain.md).
