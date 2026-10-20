---
title: "Third-party fonts, icons and libraries"
audience: designer
category: theme-distribution
order: 280
summary: Check the license of every font, icon set and JavaScript or CSS library a theme ships, one by one - what to look for, where the notices go, and why self-hosting is preferred over CDNs. Not legal advice.
keywords: third-party, fonts, icons, libraries, jQuery, license compatibility, OFL, MIT, notices, CDN, self-hosting, NOTICE
type: help
---
# Third-party fonts, icons and libraries

This page is for designers whose theme contains work by others. You may only ship it if its license allows that, and
you must follow its conditions. Check each item one by one; a general rule never replaces reading the license. This
is not legal advice.

## What to check for each item

| Question | Why |
|---|---|
| May it be redistributed? | Some fonts may be used on your site but not shipped to others |
| Is it compatible with your code license (for a GPL theme: GPL-compatible)? | Combined distribution must obey both |
| Which notice must travel with it? | Most licenses require their text and copyright line |
| May it be changed (subset a font, recolor an icon)? | Some licenses forbid changes or require a renamed font |

## Typical cases

| Item | What you usually find | What to do |
|---|---|---|
| Open-source fonts | Often the SIL Open Font License | Ship the license file next to the font files |
| Icon sets | Licenses vary: MIT, CC BY, or the vendor's own terms | Read the set's license; keep its notice |
| JavaScript libraries | Often MIT or BSD | Keep the header comment and the license file |
| jQuery | Exponential already ships it through ezjscore (jQuery 4) | Use the shipped one instead of a second copy |

## Self-host instead of loading from a CDN

Load fonts, icons and scripts from the theme's own design folder. A CDN adds a third party to every visit (a privacy
question for visitors) and needs extra entries in a Content Security Policy; the Exponential administration's policy
allows only `'self'` by default. See [External resources and CDNs](../theme-design/external-resources-and-cdns.md).

## Step by step

1. List every file you did not create yourself: `fonts/`, `images/`, `javascript/`, `stylesheets/`.
2. For each, record name, version, source URL, license, copyright holder in a `THIRD-PARTY.md` (or `CREDITS.md`).
3. Put each license text next to its files (`design/mytheme/fonts/LICENSE-OFL.txt`).
4. Remove anything you cannot license clearly.
5. Repeat this check whenever you update a library ([Keeping dependencies up to date](../lifecycle/dependency-updates.md)).

## Related pages

- [Creative Commons for images](creative-commons-for-images.md)
- [Fonts in a design](../theme-design/fonts-in-a-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
