---
title: Give a department its own design
audience: government, website-creator, designer
category: public-sector
order: 150
summary: Task for site builders: create a thin design layer for one department site that overrides the shared base design only where needed, set it in the siteaccess, and test accessibility.
keywords: department design, SiteDesign, AdditionalSiteDesignList, design extension, fallback chain, template override, branding
type: help
---
# Give a department its own design

This task is for site builders. Background: [Per-site designs](../../building/multisite/per-site-designs.md) and
[Designs and the fallback chain](../../../creators/website-creators/designs-and-the-fallback-chain.md).

## Before you start

A shared base design exists (for example `gov_base`) in its own extension.

## Steps

1. Create an extension `site_dept_example` with `design/dept_example/` and register it as a design extension.
2. Put only what differs into it: the logo, an accent color in the stylesheet, a footer template.
3. In the department's siteaccess:

   ```ini
   [DesignSettings]
   SiteDesign=dept_example
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=gov_base
   AdditionalSiteDesignList[]=simple
   ```

4. Clear the INI, template and template-override caches (`php bin/php/ezcache.php --clear-tag=ini,template`), and
   the content cache if pages look stale.

## Check

- The department site shows its logo and color; every other page part comes from the base design.
- Contrast of the accent color, focus styles and keyboard navigation still pass your checks
  ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)).

## Problems

| Problem | Fix |
|---|---|
| A new template is not used | clear the template-override cache; check the file path matches the design name |
| Other sites changed too | the change was made in the base design instead of the department layer |

## Related pages

- [Template overrides](../../../creators/website-creators/template-overrides.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
