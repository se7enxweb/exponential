---
title: What you can build with Exponential
audience: website-creator, project-manager
category: website-creation
order: 30
summary: The kinds of websites Exponential is built for, the parts that ship with it for each kind, and the questions that tell you whether a site idea fits before you start.
keywords: website, what can I build, corporate site, magazine, intranet, shop, multisite, multilingual, landing pages, newsletter, forms, member area, website creator
type: help
---
# What you can build with Exponential

This page is for website creators who want to know, before they start, which kinds of sites Exponential handles out of
the box and which parts of the product each kind uses. It answers: what is in the box, what a typical site of each
kind is made of, and which questions tell you the idea fits.

## Contents

1. [The short answer](#the-short-answer)
2. [Kinds of sites and the parts they use](#kinds-of-sites-and-the-parts-they-use)
3. [What every site gets](#what-every-site-gets)
4. [What you add with extensions](#what-you-add-with-extensions)
5. [Does my idea fit?](#does-my-idea-fit)

## The short answer

Exponential is an open source content management system and application framework. Its central idea is a content
model you define yourself: kinds of content (content classes such as Article or Product) are lists of typed fields,
and every piece of content is an object of such a class, kept with its versions and translations in a content tree
([1.1 What Exponential is](../../../install/01-introduction.md#11-what-exponential-is)). News publishing, e-commerce,
forums, user management, roles and policies, multi-language content and a template language are built in.

So anything that is "pages of structured content, shown to visitors through templates" is a natural fit: from a
five-page company site to a site network in many languages.

## Kinds of sites and the parts they use

| Kind of site | What it is made of in Exponential | Start reading |
|---|---|---|
| Company or organization site | Folders and articles in the content tree, a design, menus, a contact form | [Planning your website](planning-your-website.md), [Building a contact form](building-a-contact-form.md) |
| Magazine, news or blog | Article and blog classes, view modes (full, line), RSS feeds, tags, search | [Designing your content model](designing-your-content-model.md), [RSS feeds for your site](rss-feeds-for-your-site.md), [Tagging content with eztags](tagging-with-eztags.md) |
| Campaign and landing pages | The layouts editor: layouts with zones and blocks, rules that give pages a layout | [Landing pages with the layouts editor](landing-pages-with-layouts.md) |
| Several brands or regions | Several siteaccesses in one installation, each with its own host, design and languages | [Setting up several sites in one installation](multisite-setup.md) |
| Multilingual site | Content languages, translations per object, one siteaccess per language | [Building a multilingual site](multilingual-sites.md) |
| Members area or intranet | User registration, roles limited by section, a login requirement per siteaccess | [Member registration and members-only areas](member-registration-and-areas.md) |
| Online shop | Product classes with a price datatype, basket, checkout, orders, VAT, payment gateways | [Running a shop: overview](shop-overview.md) |
| Newsletter-driven site | `cjw_newsletter`: lists, subscriptions, editions, sending | [Newsletters with cjw_newsletter](newsletters-with-cjw-newsletter.md) |
| Site with an application inside | Custom modules and views in an extension, next to the content | [Application creators: start here](../app-creators/app-creators-start-here.md) |

## What every site gets

Whatever you build, these come with the kernel and are ready on day one:

- **The administration interface** for editors and administrators, with a content tree, a media library, users and
  roles ([The admin tour](../../admin-tour.md)).
- **Versions and drafts** for every object, and a trash you can restore from
  ([Publishing and versions](../../publishing-and-versions.md)).
- **Roles and policies**: who may read, create, edit or publish what, limited by section, subtree, class or language
  ([Roles and policies](../../../guides/roles-and-policies.md)).
- **URL aliases**: readable addresses made from the names of the content
  ([URL aliases and nice URLs](url-aliases-and-nice-urls.md)).
- **Caches** at several levels, so pages are fast without extra work
  ([Performance and caching for websites](performance-and-caching.md)).
- **Search** built in, with Solr as an option for large sites ([Site search](site-search.md)).
- **A Content Security Policy** for the administration and the tools to give your site one
  ([Content Security Policy](../../../features/6.0/content-security-policy.md)).
- **This help**, inside the administration, for you and your editors.

## What you add with extensions

Extensions are Composer packages that add features ([Extensions](../../../guides/extensions.md)). The ones a
website creator meets most often, each documented under [Extensions](../../../features/6.0/extensions/README.md):

| Need | Extension |
|---|---|
| A ready-made design | `sevenx_themes_simple`, `ezwebin`, `ezdemo` |
| Landing pages | the `explayouts*` extensions (the layouts editor), or `ezflow` for classic block pages |
| Tags | `eztags` |
| Newsletter | `cjw_newsletter` |
| XML sitemaps | `bcgooglesitemaps` |
| Page metadata | `xrowmetadata` |
| CAPTCHA on forms | `recaptcha`, `hcaptcha` |
| Solr search | `ezfind` |
| Payment | `ezpaypal`, `ezauthorize` |

## Does my idea fit?

Answer these before you start; each answer points to a page that explains the options.

- [ ] Can the content be described as a handful of kinds (classes) with fields? If yes, the content model fits.
      ([Designing your content model](designing-your-content-model.md))
- [ ] Will people other than you edit it? Then plan roles and an editor handover
      ([Handing the site over to editors](handing-over-to-editors.md)).
- [ ] How many languages, how many sites? ([Building a multilingual site](multilingual-sites.md))
- [ ] Do visitors log in, buy or subscribe? ([Member registration](member-registration-and-areas.md),
      [Running a shop](shop-overview.md), [Newsletters](newsletters-with-cjw-newsletter.md))
- [ ] Does it need logic no extension offers? Then an application creator writes a module
      ([Exponential as an application platform](../app-creators/exponential-as-an-application-platform.md)).
- [ ] Where will it run? ([Hosting requirements for a website](hosting-for-a-website.md))

> **Note**
> When the answer to a question is "we do not know yet", that is a planning task, not a blocker. The
> [planning page](planning-your-website.md) turns these questions into a plan.

## Related pages

- [Website creators: start here](website-creators-start-here.md)
- [Planning your website](planning-your-website.md)
- [What Exponential is: the product family](../../leadership/what-exponential-is.md)
- [Who is 7x?](../../who-is-7x.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
