---
title: Individuals FAQ
audience: individual, website-creator, customer
category: personal-sites
order: 1502
summary: Short, honest answers to the questions people ask before and while running a personal site, portfolio, blog, family site or club site on Exponential alone - cost, hosting, comments, importing, privacy, mail, backups, updates and when to get help.
keywords: FAQ, questions, individual, personal site, blog, portfolio, family site, club, cost, free, hosting, SQLite, comments, WordPress, import, privacy, mail, backups, updates, two-factor, AI
type: help
---
# Individuals FAQ

This page is for individuals who want a quick answer before reading a whole page. Each answer links to the page
with the detail.

## Contents

1. [Cost and hosting](#cost-and-hosting)
2. [Building the site](#building-the-site)
3. [Blogs and portfolios](#blogs-and-portfolios)
4. [Family and club sites](#family-and-club-sites)
5. [Running it](#running-it)

## Cost and hosting

**Does Exponential cost anything?**
No license fee. It is free software under the GNU General Public License, version 2 or later
([Licensing for buyers](../business/licensing-for-buyers.md)). You pay for hosting, a domain name and, if you
want them, outside services such as a mail provider or an AI provider.

**What is the smallest setup that works?**
PHP 7.4 or later, SQLite 3 (no database server) and either a common web server or no web server at all with
Velocity ([Requirements at a glance](../hosting/basics/requirements-at-a-glance.md),
[Running on a small budget](setup/running-on-a-small-budget.md)).

**Can I use cheap shared hosting?**
Often yes, with Apache or LiteSpeed and `.htaccess`, installed through the setup wizard. Velocity usually does not
fit shared hosting because it needs a long-running process
([Shared hosting or a small server](setup/shared-hosting-or-a-small-server.md)).

**Do I need a database server?**
No. SQLite 3 is the default and stores everything in one file. PostgreSQL, MongoDB, MariaDB, Oracle and MySQL are
also supported if your host offers one ([Database support](../database-support.md)).

**Can I try it before paying for anything?**
Yes. Install it on your own computer with `exp:install` and its defaults (SQLite, the default site package)
([Choosing an install method](setup/choosing-an-install-method.md)).

## Building the site

**Do I need to know how to program?**
Not to write and publish. To change how the site looks beyond the theme's options you edit templates and
stylesheets, which is closer to HTML than to programming ([Choosing a site package and theme](setup/choosing-a-site-package-and-theme.md)).

**Which theme should I start with?**
The default installation uses `sevenx_themes_simple`. The older `ezwebin` design is kept for sites built on it
([Site packages and the themes that ship](../creators/website-creators/site-packages-and-shipped-themes.md)).

**Can I lay out pages visually?**
Yes, with Exponential Layouts, the page builder ([Layouts editor](../layouts-editor.md)). Many personal sites never
need it; folders and articles are enough.

**Can I register my domain name through Exponential?**
No. Domains are bought from a registrar and pointed at your server. Exponential only needs to be told its address
([Domain names and HTTPS](setup/domain-names-and-https.md)).

## Blogs and portfolios

**Does a blog have comments?**
The shipped *Blog post* class has an "enable comments" checkbox, and comments are *Comment* objects stored under
the post. Who may write them is a permission you set. There is no separate `ezcomments` extension in this
distribution ([Comments on your blog](blogs/comments-on-your-blog.md)).

**Can I import my WordPress blog?**
Not with one click. There is no ready importer; the documented path is an export, a small conversion script and an
import tool ([Moving from another blog platform](blogs/moving-from-another-blog-platform.md)).

**Does it make RSS feeds?**
Yes, RSS 2.0, Atom and podcast feeds are built in under **Setup > RSS**
([RSS feeds for your blog](blogs/rss-feeds-for-your-blog.md)).

**Can I crop and rotate photos in the browser?**
Yes, with the `ezie` image editor inside the edit form ([Editing images with ezie](portfolios/editing-images-with-ezie.md)).

**Can I upload many photos at once?**
Yes, with the multi-upload page of `ezmultiupload` ([Uploading many images](portfolios/uploading-many-images.md)).

## Family and club sites

**Can I make a site only my family can see?**
Yes. Either one private area (a section that anonymous visitors may not read) or the whole site behind a login
([Private areas with roles and sections](family-sites/private-areas-with-roles-and-sections.md)).

**Does the birthday extension send birthday reminders?**
No. It adds a field that stores a date of birth. Reminders would need custom work
([Birthdays and family dates](family-sites/birthdays-and-family-dates.md)).

**Can club members sign up themselves?**
Yes, with the registration page and activation by mail ([Members and registration](club-sites/members-and-registration.md)).

**Can I send a club newsletter?**
Yes, with `cjw_newsletter`. It needs a cron job and outgoing mail that works ([Club newsletters](club-sites/club-newsletters.md)).

**Can members pay dues or buy event tickets?**
There is no ticketing or membership-fee module. The built-in shop can sell items; anything more needs custom work
or an outside service ([What exists and what does not](what-exists-and-what-does-not.md)).

## Running it

**How do I back up?**
The database and the uploaded files, together, kept somewhere else ([Backups for one person](running-it/backups-for-one-person.md)).

**How often must I update?**
Watch for releases and apply security fixes promptly; a monthly look is a good habit
([Updates for one person](running-it/updates-for-one-person.md)).

**Is two-step sign-in available?**
Yes, with authenticator apps or a code by mail ([Two-factor sign-in](running-it/two-factor-sign-in.md)).

**Will the AI assistant write my blog for me?**
It writes drafts, never publishes, and needs a provider key or a local model that you set up
([The writing assistant](running-it/writing-assistant-expai.md)).

**When should I pay someone?**
When a task touches the server, mail delivery, a migration or custom code you cannot test
([When to hire help](running-it/when-to-hire-help.md)).

## Related pages

- [Individuals: start here](individuals-start-here.md)
- [Individuals glossary](individuals-glossary.md)
- [What exists and what does not](what-exists-and-what-does-not.md)
- [Website creator FAQ](../creators/website-creators/website-creator-faq.md)
- [Customers FAQ](../customers/customers-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
