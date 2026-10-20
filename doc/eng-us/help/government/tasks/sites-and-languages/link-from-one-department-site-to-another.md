---
title: Link from one department site to another
audience: government, admin, website-creator
category: public-sector
order: 170
summary: Task for administrators: make links from one site to content of another site in the same installation point to the right host, with the ezurlmultisite settings, and check them.
keywords: cross-site links, ezurlmultisite, SiteAccessList, RootNodeList, PathPrefix, multisite links
type: help
---
# Link from one department site to another

This task is for administrators. Background: [Links between department sites](../../building/multisite/links-between-department-sites.md).

## Before you start

The sites use `PathPrefix` (each siteaccess hides its subtree's path) and `ezurlmultisite` is installed (the root
`composer.json` requires it).

## Steps

1. Create `settings/override/ezurlmultisite.ini.append.php`:

   ```ini
   <?php /*
   [MultisiteSettings]
   SiteAccessList[]
   SiteAccessList[]=site
   SiteAccessList[]=dept_example
   RootNodeList[site]=<node id>
   RootNodeList[dept_example]=<node id>
   */ ?>
   ```

2. Clear the INI and template caches; with Velocity also its response cache.

Without the file, the public siteaccesses with a `PathPrefix` take part with their `RootNode` or `IndexPage`.

## Check

On the main site, search for or list a page of the department site; the link must point to `dept.example.gov` and open
the page.

## Problems

| Problem | Fix |
|---|---|
| Links still answer 404 | the siteaccess is missing in `SiteAccessList[]`, or a cache |
| Administration links changed | they should not: administration siteaccesses never take part |

## Related pages

- [Links to content of another site](../../../../features/6.0/multisite-cross-site-links.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
