---
title: "Set the social media links"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1208
summary: The footer's social icons come from the fields facebook, twitter, instagram and linkedin of the site info object; each icon shows only when its field has content.
keywords: social links, facebook, twitter, instagram, linkedin, site info, footer
type: help
---
# Set the social media links

The footer links to the site's real profiles.

*Life cycle: Launch, example 9 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. In **Content structure**, edit the site info object (*Fit & Healthy site info*, class Site info) and fill the profile addresses; publish.

## With templates

1. `pagelayout/footer.tpl` prints an icon for each of the four fields that has content; another network needs a field and a template change.

## Which to choose

Content for the addresses; template only for a new network.

## Check

- Each icon links to the right profile; empty fields show no icon.

## Related pages

- [How the footer is built](../../how-the-footer-is-built.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
