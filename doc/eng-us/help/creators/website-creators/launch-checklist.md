---
title: The launch checklist
audience: website-creator, project-manager
category: website-creation
order: 660
summary: Everything to check before a site goes live - content, design, accessibility, SEO, forms and mail, security, performance, operations and the go-live day itself - each item with the page that explains it.
keywords: launch, go live, checklist, launch checklist, pre-launch, go-live day, DNS, HTTPS, backups, cronjobs, security, SEO, accessibility, website creator
type: help
---
# The launch checklist

This page is for website creators in the last weeks before launch. Work through it with the people responsible; each
item names the page that explains it. Project managers find the acceptance side in
[Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md).

## Content

- [ ] Every page of the site map exists and is published; placeholder text is gone.
- [ ] Menus, footers and shared layouts are final ([Menus and navigation](menus-and-navigation.md)).
- [ ] Images have alternative texts; downloads show format and size.
- [ ] Legal pages (imprint, privacy, cookies) are published and linked.
- [ ] Test content and test users are removed or unpublished.

## Design and accessibility

- [ ] Every page type tested in the browsers and devices your visitors use.
- [ ] Accessibility tested ([Testing your site's accessibility](testing-accessibility.md)).
- [ ] Error pages (not found, access denied) are designed and return the right status.
- [ ] Print view and right-to-left siteaccesses checked, if any.

## SEO

- [ ] Titles and descriptions on every page type ([Page metadata](metadata-and-social-sharing.md)).
- [ ] `robots.txt` adapted, sitemap generated and listed ([robots.txt](robots-txt.md), [XML sitemaps](sitemaps.md)).
- [ ] Redirects from the old site tested ([Redirects and moved content](redirects-and-moved-content.md)).
- [ ] A staging host is not reachable for search engines.

## Forms, mail and features

- [ ] Real mail transport set, sender domain set up; a password reset mail arrives
      ([10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).
- [ ] Every form tested logged out; submissions arrive; spam protection on.
- [ ] Newsletter rehearsed with the file transport before switching to real mail ([Sending newsletters](sending-newsletters.md)).
- [ ] Shop: a full test order with real payment in test mode ([Basket, checkout and orders](basket-checkout-and-orders.md)).
- [ ] Search finds the main pages ([Site search](site-search.md)).

## Security

- [ ] The administrator password is changed and personal ([Default administrator password](../../default-administrator-password.md)).
- [ ] Hardening done ([Security hardening](../../security-hardening.md)).
- [ ] The Anonymous role only grants what visitors need ([Visitor permissions](visitor-permissions.md)).
- [ ] `[HTMLForms] Secret` set; debug output off on public siteaccesses.
- [ ] HTTPS on every host, HTTP redirects to HTTPS ([Going live](going-live-hosts-https.md)).
- [ ] The Content Security Policy at least in report-only on the site ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Performance

- [ ] Caches on; static cache off; Velocity response cache on if Velocity serves the site
      ([Performance and caching](performance-and-caching.md)).
- [ ] A benchmark baseline saved.
- [ ] Image aliases sized for the design.

## Operations

- [ ] Cron runs `frequent`, the default part, `infrequent` and `cache_cleanup` ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] A backup was taken and restored on another machine ([Backups and restores](backups-and-restores.md)).
- [ ] Logs rotate; someone reads the error log ([Log viewer](../../../features/6.0/log-viewer.md)).
- [ ] Who to call when the site is down is written down.
- [ ] Editors trained ([Handing the site over to editors](handing-over-to-editors.md)).

## Go-live day

1. Freeze content changes on the old site.
2. Take a backup of the new site.
3. Switch DNS or the proxy to the new site; keep the old one running until DNS has spread.
4. Check the front page, a deep page, a form and the administration on the public addresses.
5. Clear the content and response caches once the new addresses answer.
6. Submit the sitemap to the search engines.
7. Watch the error log and the 404s for the first days.

## Related pages

- [Going live: hosts, HTTPS and siteaccesses](going-live-hosts-https.md)
- [Project templates](../../leadership/project-templates.md)
- [A checklist for the first week](../../../install/10-after-installing.md#1015-a-checklist-for-the-first-week)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
