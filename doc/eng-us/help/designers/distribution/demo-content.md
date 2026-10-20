---
title: "Demo content for a theme"
audience: designer
category: theme-distribution
order: 340
summary: Demo content that shows a theme at its best and at its limits - which pages to create, realistic but invented text, licensed images, long titles and empty fields - and how to ship it as a package or seed data.
keywords: demo content, sample content, lorem ipsum, test content, edge cases, content package, seed data, demo pages
type: help
---
# Demo content for a theme

This page is for designers who fill a demo site or ship sample content with a theme. Good demo content sells the
theme and finds its bugs.

## What to create

| Content | Why |
|---|---|
| A front page with every block type the theme styles | Shows the range |
| Articles with short and very long titles, with and without images | Finds wrapping and cropping bugs |
| A list with more items than one page | Shows paging |
| An event, a blog post, a form (if the theme styles them) | Shows the class templates |
| Pages in a second language, and one right-to-left language if supported | Proves translations and direction |
| Empty optional fields | Shows the template copes with missing data |

## Text and images

- Write realistic but invented text; never copy a real customer's site. Exponential can generate placeholder text in
  a test language ([Test language and lorem ipsum](../../../guides/test-language-lorem-ipsum.md)).
- Use only images you may redistribute ([Creative Commons for images](creative-commons-for-images.md)).
- No real people, addresses or email addresses.

## Shipping it

| Way | Page |
|---|---|
| A content object package (`.ezpkg`) of the demo subtree | [The package creator wizard](../extensions/package-creator-wizard-step-by-step.md), [Exporting with ezpm](../extensions/exporting-with-ezpm.md) |
| Images and files in `share/` with install steps | [The share folder](../extensions/share-folder-assets.md) |
| Only on your demo site | [Running a demo site](demo-sites.md) |

## Step by step

1. Build the content on a development installation in its own subtree.
2. Review it with the table above.
3. Export the subtree: `php ezpm.php add mytheme_demo ezcontentobject <node id>` after `create`, then `export`.
4. Install it on a fresh test site ([Installing your package on a test site](../extensions/installing-your-package-on-a-test-site.md)).

## Site packages: contribute them

If your theme comes with a complete site package (the kind the setup wizard installs a new site from), 7x and the
Exponential Foundation strongly encourage you to submit it by pull request to
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth),
the repository of the package server the setup wizard uses. Then every new installation can start from it. How:
[Contribute your site package](../../site-packages/contribute-your-site-package.md).

## Related pages

- [Screenshots for a theme](screenshots-for-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
