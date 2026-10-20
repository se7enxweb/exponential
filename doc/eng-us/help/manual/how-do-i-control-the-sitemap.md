---
title: How do I get a page into or out of the sitemap?
audience: editor, admin
category: manual
order: 385
summary: Published pages that visitors may read go into the XML sitemap at the next sitemap cronjob; untick Use in sitemap in the page's meta data to leave one out, or hide it; administrators set the classes and subtrees.
keywords: sitemap, XML sitemap, bcgooglesitemaps, use in sitemap, priority, change frequency, search engines, robots.txt
type: help
---
# How do I get a page into or out of the sitemap?

The XML sitemap tells search engines which pages exist. It is written by a cronjob, from the published pages that
visitors may read.

## Who can do this

Editors who may edit the page decide for one page. Administrators decide which classes and parts of the tree go in
(the settings of bcgooglesitemaps and xrowmetadata).

## Steps

1. **To add a page:** publish it in a part of the tree that visitors may read. It is in the sitemap after the next
   sitemap cronjob, usually overnight.
2. **To leave a page out:** edit it, open the meta data field and untick **Use in sitemap**. Click **Send for
   publishing**.
3. **To weight a page:** in the same field, set **Priority** and **Change frequence**.
4. **To leave out a whole part of the tree or a class:** ask an administrator to exclude it in the sitemap settings.

## What can go wrong

- **A new page is not in the sitemap.** The cronjob has not run yet, or the class is excluded.
- **A page you left out still appears in search results.** The sitemap only invites search engines. To keep a page out
  of results, hide it or ask an administrator about `noindex`.

## Related pages

- [How do I set the SEO title and description of a page?](how-do-i-set-the-seo-title-and-description.md)
- [bcgooglesitemaps](../../features/6.0/extensions/bcgooglesitemaps.md)
- [Sitemaps](../creators/website-creators/sitemaps.md)
- [robots.txt](../creators/website-creators/robots-txt.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
