---
title: Individuals glossary
audience: individual, website-creator, customer
category: website-creation
order: 1503
summary: The words you meet when you run a personal site, portfolio, blog, family site or club site on Exponential, each explained in one or two plain sentences with a link to the page that explains it in full.
keywords: glossary, words, terms, individual, class, object, node, section, role, siteaccess, site package, theme, image variation, alias, RSS export, SQLite, Velocity, cron, DNS, HTTPS, two-factor
type: help
---
# Individuals glossary

This page is for individuals who meet a word in this help and want it explained in plain language. The words are
in alphabetical order; each links to a page with more.

## Contents

1. [A to F](#a-to-f)
2. [G to R](#g-to-r)
3. [S to Z](#s-to-z)

## A to F

| Word | Meaning | More |
|---|---|---|
| **Administration interface** | The part of the site where you sign in to create content and change settings, usually at its own address. | [Admin tour](../admin-tour.md) |
| **Anonymous** | Any visitor who is not signed in. The *Anonymous* role decides what they may see. | [Private areas](family-sites/private-areas-with-roles-and-sections.md) |
| **Attribute** | One field of a content class, such as the title or the image. | [Choosing datatypes](../creators/website-creators/choosing-datatypes.md) |
| **Backup** | A copy of the database and the uploaded files, kept somewhere else, that you have tested you can restore. | [Backups for one person](running-it/backups-for-one-person.md) |
| **Cache** | Pages and settings the site has prepared in advance so it can answer quickly. Clearing it is safe. | [Performance and caching](../creators/website-creators/performance-and-caching.md) |
| **Class (content class)** | The description of a kind of content: a *Blog post*, an *Image*, an *Event*. | [Content classes for websites](../creators/website-creators/content-classes-for-websites.md) |
| **Comment** | On the shipped classes, a small content object (subject, author, message) stored under a blog post. | [Comments on your blog](blogs/comments-on-your-blog.md) |
| **Cron job** | A command the server runs on a schedule. Newsletters, scheduled publishing and feed imports need one. | [Cronjobs](../../guides/cronjobs.md) |
| **Datatype** | The kind of value a field holds: text, image, date, a date of birth (`ezbirthday`). | [Choosing datatypes](../creators/website-creators/choosing-datatypes.md) |
| **DNS** | The internet's address book. You set it at your registrar so your domain name points at your server. | [Domain names and HTTPS](setup/domain-names-and-https.md) |
| **Draft** | A version you are still working on. Nobody else sees it until you publish. | [Publishing and versions](../publishing-and-versions.md) |
| **Extension** | An add-on in the `extension/` folder that adds features, designs or both. | [Extensions](../../guides/extensions.md) |
| **Feed (RSS, Atom)** | A machine-readable list of your newest items that readers follow in a feed reader. | [RSS feeds for your blog](blogs/rss-feeds-for-your-blog.md) |

## G to R

| Word | Meaning | More |
|---|---|---|
| **Gallery** | A shipped class that holds images below it and shows them as a gallery. | [Building an image gallery](portfolios/building-an-image-gallery.md) |
| **HTTPS** | Encrypted connections, the lock in the address bar. Needs a certificate for your domain. | [Domain names and HTTPS](setup/domain-names-and-https.md) |
| **Image variation (alias)** | A resized copy of an uploaded image made for one purpose, such as a thumbnail. | [Image variations for portfolios](portfolios/image-variations-for-portfolios.md) |
| **Information collector** | Fields that visitors fill in, such as a contact form or a poll; answers are stored and can be mailed. | [Forms with the information collector](../creators/website-creators/forms-with-the-information-collector.md) |
| **Maintenance mode** | A switch that shows visitors a short message while you work on the site. | [Maintenance mode](../creators/website-creators/maintenance-mode.md) |
| **Node** | The place of a content object in the content tree. One object can appear in several places. | [Structuring the content tree](../creators/website-creators/structuring-the-content-tree.md) |
| **Object (content object)** | One piece of content: one blog post, one photograph. | [Editing content](../editing-content.md) |
| **Pagelayout** | The template that wraps every page: header, menu, footer. | [The pagelayout](../creators/website-creators/the-pagelayout.md) |
| **Policy** | One permission inside a role, such as "read content in section Members". | [Roles and policies](../../guides/roles-and-policies.md) |
| **Registrar** | The company you rent a domain name from. Outside Exponential. | [Domain names and HTTPS](setup/domain-names-and-https.md) |
| **Role** | A named set of policies given to users or groups, such as *Member* or *Editor*. | [Roles and policies](../../guides/roles-and-policies.md) |
| **RSS export** | A feed your site publishes, set up under **Setup > RSS**. | [RSS feeds](../../guides/rss-feeds.md) |

## S to Z

| Word | Meaning | More |
|---|---|---|
| **Section** | A label on a part of the content tree that permissions can refer to, the usual way to make an area private. | [Sections](../../guides/sections.md) |
| **Site package** | The starting point of an installation: classes, a design, content, roles and settings. | [Choosing a site package and theme](setup/choosing-a-site-package-and-theme.md) |
| **Siteaccess** | One way into the installation, such as the public site, the administration or a members-only site. | [Going live: hosts, HTTPS and siteaccesses](../creators/website-creators/going-live-hosts-https.md) |
| **SQLite** | A database stored in one file, the default of Exponential. No server to run. | [SQLite for hosts](../hosting/basics/sqlite-for-hosts.md) |
| **Tag** | A word attached to content to group it by topic: the keyword field of the shipped classes, or the `eztags` extension for a managed list. | [Tags and categories](blogs/tags-and-categories.md) |
| **Template** | A file that decides how content is shown as HTML. | [Template overrides](../creators/website-creators/template-overrides.md) |
| **Theme** | The design part of a site package, kept as an extension. | [Choosing a design or theme](../creators/website-creators/choosing-a-design-or-theme.md) |
| **Two-factor sign-in** | Signing in with a password plus a code from an app or a mail. | [Two-factor sign-in](running-it/two-factor-sign-in.md) |
| **URL alias** | The readable address of a page, made from its name. | [URL aliases and nice URLs](../creators/website-creators/url-aliases-and-nice-urls.md) |
| **Velocity** | Exponential's own web server, which serves the site without Apache or nginx. | [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md) |
| **Writing assistant** | Exponential Intelligence (`expai`), which writes drafts for you to check. | [The writing assistant](running-it/writing-assistant-expai.md) |

For the words of the whole help see the [project glossary](../../glossary.md) and the
[customers glossary](../customers/customers-glossary.md).

## Related pages

- [Individuals: start here](individuals-start-here.md)
- [Individuals FAQ](individuals-faq.md)
- [Customers glossary](../customers/customers-glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
