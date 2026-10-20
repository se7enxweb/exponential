---
title: "A performance audit for every release"
audience: designer, developer
category: theme-lifecycle
order: 170
summary: Check that a release did not make the theme slower - page weight, number of requests, image sizes, layout shift and largest contentful paint on the page list - and compare with the previous release.
keywords: performance audit, page weight, requests, images, CLS, LCP, Lighthouse, packer, release routine
type: help
---
# A performance audit for every release

This page is for designers maintaining a theme. Performance degrades release by release unless measured. How to build
a fast theme is in [Theme performance](../theme-design/theme-performance.md) and
[Measuring CLS and LCP](../theme-design/measuring-cls-and-lcp.md).

## What to measure

| Measure | How | Watch for |
|---|---|---|
| Page weight | Browser developer tools, network panel | Growth without a reason |
| Requests | The same | New fonts, scripts, images |
| Image sizes | The same | Images larger than displayed |
| Layout shift (CLS) | A lab tool such as Lighthouse | Images without dimensions, late fonts |
| Largest contentful paint (LCP) | The same | Big hero images, render-blocking files |

Measure on the test installation with the packer active, so stylesheets and scripts are bundled as on a live site.

## Step by step

1. Measure the front page, an article and a list with the previous release; write the numbers down.
2. Update and measure the same pages the same way.
3. Compare. Explain or fix every increase.
4. Note notable improvements in the changelog.

## Related pages

- [Image performance](../theme-design/image-performance.md)
- [Benchmarking](../../../guides/benchmarking.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
