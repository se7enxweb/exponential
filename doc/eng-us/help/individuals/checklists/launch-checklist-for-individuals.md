---
title: Launch checklist for individuals
audience: individual, website-creator, customer
category: personal-sites
order: 1652
summary: The checklist one person works through on the day an Exponential personal site, portfolio, blog, family site or club site goes live - accounts and security, content, address and HTTPS, forms and mail, permissions, search engines, backups and the first week after launch.
keywords: launch, go live, checklist, launch day, accounts, password, two-factor, HTTPS, domain, forms, mail, permissions, private area, search engines, sitemap, backup, individual
type: help
---
# Launch checklist for individuals

This page is for individuals about to make their site public. Work through it top to bottom; every line links to the
page that explains it. The builders' version with more technical detail is the
[launch checklist for website creators](../../creators/website-creators/launch-checklist.md).

## Contents

1. [Before moving to the host](#before-moving-to-the-host)
2. [Accounts and security](#accounts-and-security)
3. [Address and HTTPS](#address-and-https)
4. [Content](#content)
5. [Forms and mail](#forms-and-mail)
6. [Permissions and privacy](#permissions-and-privacy)
7. [Search engines and feeds](#search-engines-and-feeds)
8. [Backups](#backups)
9. [The first week](#the-first-week)

## Before moving to the host

- [ ] Real site built on the `_clean` package, or demo content removed ([Choosing a site package and theme](../setup/choosing-a-site-package-and-theme.md)).
- [ ] Host checked: PHP version, extensions, cron, mail, HTTPS ([Shared hosting or a small server](../setup/shared-hosting-or-a-small-server.md)).
- [ ] A full backup of the local site ([Backups for one person](../running-it/backups-for-one-person.md)).

## Accounts and security

- [ ] Default administrator password changed ([Change the admin password](../../change-the-admin-password.md)).
- [ ] Two-step sign-in on your administrator account ([Two-factor sign-in](../running-it/two-factor-sign-in.md)).
- [ ] A deputy or emergency administrator account exists.
- [ ] Test accounts removed or disabled.
- [ ] Debug output off on the live site ([Spam and security basics](../running-it/spam-and-security-basics.md)).

## Address and HTTPS

- [ ] Domain points to the server; `www` and the bare name behave as decided ([Domain names and HTTPS](../setup/domain-names-and-https.md)).
- [ ] Exponential's site URL and host settings use the real address ([Going live](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] HTTPS on every page; `http://` redirects.
- [ ] The administration reachable at its address, over HTTPS.

## Content

- [ ] Home, About, Contact and privacy notice published.
- [ ] Every page has a clear title and intro ([Writing for your own site](../personal-sites/writing-for-your-own-site.md)).
- [ ] Every image has alternative text.
- [ ] Menu complete; no links to the demo or to your local address ([Link and content checks](../../creators/website-creators/link-and-content-checks.md)).
- [ ] Checked on a phone.

## Forms and mail

- [ ] Contact form sends to the right address; the mail arrives ([About page and contact form](../personal-sites/about-page-and-contact-form.md)).
- [ ] Registration and password reset mails arrive, if used ([Members and registration](../club-sites/members-and-registration.md)).
- [ ] Form tokens on; CAPTCHA on public forms and comments ([Spam and security basics](../running-it/spam-and-security-basics.md)).
- [ ] Cron runs, if you use newsletters or scheduled publishing ([Cronjobs](../../../guides/cronjobs.md)).

## Permissions and privacy

- [ ] *Anonymous* role allows only what you want; comment policy decided ([Comments on your blog](../blogs/comments-on-your-blog.md)).
- [ ] Private area tested as a stranger ([Private areas with roles and sections](../family-sites/private-areas-with-roles-and-sections.md#testing-as-a-stranger)).
- [ ] Family sites: the [family privacy checklist](family-privacy-checklist.md) done.

## Search engines and feeds

- [ ] `robots.txt` allows what should be found ([robots.txt](../../creators/website-creators/robots-txt.md)).
- [ ] Sitemap, if you want one ([Sitemaps](../../creators/website-creators/sitemaps.md)).
- [ ] RSS export active and linked, for a blog ([RSS feeds for your blog](../blogs/rss-feeds-for-your-blog.md)).
- [ ] No private content in any feed.

## Backups

- [ ] Automatic backups running on the host.
- [ ] A first copy downloaded and stored away from the server.
- [ ] Restore tested once, or a date set for it.

## The first week

- [ ] Look at the site every day; read form submissions and comments.
- [ ] Check the error log once ([Logs for owners](../../customers/healthy/logs-for-owners.md)).
- [ ] Tell people: friends, family, club members.
- [ ] Put the [monthly upkeep checklist](monthly-upkeep-checklist.md) in your calendar.

## Related pages

- [Checklists for individuals](README.md)
- [Launch checklist (website creators)](../../creators/website-creators/launch-checklist.md)
- [A personal site in a weekend](../personal-sites/a-personal-site-in-a-weekend.md)
- [First 30 days as owner](../../customers/first-30-days-as-owner.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
