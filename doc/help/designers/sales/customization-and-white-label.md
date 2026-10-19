---
title: "Customization and white-label work"
audience: designer, sales-marketing
category: theme-sales
order: 160
summary: Adapt an existing theme to a client's brand, or build themes that agencies resell under their own name - how to keep customizations upgradeable, what may and may not be removed, and how to keep credits honest.
keywords: customization, white label, rebrand, agency, client branding, child theme, override, upgradeable, credits
type: help
---
# Customization and white-label work

This page is for designers who adapt themes for clients or for agencies who resell them.

## Keep customizations upgradeable

[The design cascade](../theme-design/the-design-cascade.md) lets a small extension override a base theme without editing it
(The design cascade):

1. Leave the base theme untouched.
2. Create a client extension with only the changed templates, stylesheets and settings.
3. Declare in its `extension.xml` that it extends the base theme, so it loads first
   ([Declaring dependencies](../extensions/dependencies-in-extension-xml.md)).
4. Update the base theme with Composer; the client extension keeps its changes.

## White-label: what may be changed

| May be changed | Must stay |
|---|---|
| The visible theme name in the client's site | Copyright and license notices in the files and `LICENSE` |
| Colors, logos, typography | The license of GPL code (the reseller passes on the same rights) |
| Product name in the reseller's offer | Third-party notices of fonts, icons, libraries |

Removing the author's notices is not "white-labeling"; it breaks the license
([Credits and attribution](../lifecycle/credits-and-attribution.md)).

## Step by step for an agency deal

1. Agree in writing who supports the end client and how bugs reach you.
2. Agree how the reseller names the theme ([Names and trademarks](names-and-trademarks.md)).
3. Deliver through a channel the agency controls ([Delivering updates](delivering-updates-to-customers.md)).
4. Keep a changelog the agency can forward.

## Related pages

- [Working with agencies and partners](working-with-agencies-and-partners.md)
- [Rebranding a fork, fairly](../lifecycle/rebranding-a-fork.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
