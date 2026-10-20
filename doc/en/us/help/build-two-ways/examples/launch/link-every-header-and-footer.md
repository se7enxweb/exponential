---
title: "Check that every layout links the header and footer"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1205
summary: Before launch, each page layout's header and footer zones must link to Header / Footer; exp:layouts list shows it.
keywords: zone linking, header, footer, launch check, exp:layouts list, Links to
type: help
---
# Check that every layout links the header and footer

No page type launches without the site chrome.

*Life cycle: Launch, example 6 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. In each layout: linking button (bottom left) > **Link zone** on HEADER > *Header / Footer* > **Select zone** on its HEADER; the same for FOOTER.

## On the command line

1. `./console exp:layouts list` - the column *Links to* shows `fd0aa7af77a85a03bf1080018f5db1dc` (Header / Footer) for each page layout.

## Which to choose

The console check covers all layouts in one line each.

## Check

- Every non-shared layout of the site lists Header / Footer under *Links to*.

## Related pages

- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Build a campaign page, start to finish](../../build-a-campaign-page.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
