---
title: "Check every page type by its markup"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1210
summary: Layouts and templates leave markers in the HTML (zone sections, ngl-block classes, menu item IDs) that a script can check before launch.
keywords: launch check, markup, curl, ngl-block, zone, smoke test
type: help
---
# Check every page type by its markup

A one-minute check that each page type renders its layout.

*Life cycle: Launch, example 11 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. In the editor, compare the blocks of the layout with what the page shows.

## On the command line

1. For each page type, request one page and look for its markers:

```bash
curl -s https://www.example.com/fitness | grep -o 'zone zone-[a-z_]*' | sort -u
curl -s https://www.example.com/fitness | grep -c 'ngl-block '
curl -s https://www.example.com/ | grep -o 'menu-item-main_menu-location-id-[0-9]*'
```

## Which to choose

The script runs after every deploy; the editor comparison when something is missing.

## Check

- Every page type has `zone-header`, `zone-main` and `zone-footer` sections and a plausible block count.

## Related pages

- [Why doesn't my change appear?](../../why-doesnt-my-change-appear.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
