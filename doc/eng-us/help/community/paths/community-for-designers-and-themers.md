---
title: The community for designers and themers
audience: community, designer
category: community
order: 66
summary: What the Exponential Community offers designers and theme authors, and how they contribute - the designers' sections, the administration's styling rules, accessibility and dark mode, sharing themes, and design feedback on the administration.
keywords: designers, themes, themers, design, administration styling, tokens, components, accessibility, dark mode, theme extensions, contribution
type: help
---
# The community for designers and themers

Designers shape how every Exponential site and the administration look and feel. The help has a whole section for
them; this page shows where community work fits in.

## Contents

1. [Where designers learn](#where-designers-learn)
2. [Contributing to the administration's design](#contributing-to-the-administrations-design)
3. [Sharing themes](#sharing-themes)
4. [Design feedback](#design-feedback)

## Where designers learn

| Resource | Page |
|---|---|
| The designers' section | [Designers: start here](../../designers/designers-start-here.md) |
| Theme design: tokens, dark mode, accessibility, right-to-left, checklists | [Theme design](../../designers/theme-design/design-tokens.md) and the pages next to it |
| Theme extensions, distribution, selling and the life cycle of a theme | [Theme extensions](../../designers/extensions/theme-extensions-start-here.md), [Distribution](../../designers/distribution/distribution-start-here.md), [Selling](../../designers/sales/selling-start-here.md), [Lifecycle](../../designers/lifecycle/lifecycle-start-here.md) |
| Courses for theme designers | [Trainers: start here](../../trainers/trainers-start-here.md) |

## Contributing to the administration's design

Templates and stylesheets of the administration designs follow strict rules (`CONTRIBUTING.md`, "Making Changes";
[Styling the administration](../../../bc/6.0/admin4-styling.md)):

- no `<style>` block and no `style=""` attribute in a template;
- the shared components and the design tokens, with a value for dark mode for every new token;
- module-specific CSS only in `stylesheets/modules/`;
- checked in light and dark mode.

A test, `tests/tests/design/AdminStylingRulesTest.php`, checks the rules. See
[Admin styling rules test](../../designers/theme-design/admin-styling-rules-test.md) and
[Admin4 components](../../designers/theme-design/admin4-components.md).

## Sharing themes

A theme is usually shared as a design extension in its own repository and Composer package
([Sharing extensions and themes](../contributing/sharing-extensions-and-themes.md),
[Design extensions](../../designers/theme-design/design-extensions.md)). Before you share it, run the
[theme launch checklist](../../designers/theme-design/checklist-theme-launch.md) and the
[accessibility checklist](../../designers/theme-design/checklist-accessibility.md).

## Design feedback

Designers notice problems others miss: contrast, focus states, spacing at high zoom, dark mode. File them with a
screenshot, the design name, the browser and the zoom level ([Filing a bug](../contributing/filing-a-bug.md)).
Contrast and accessibility problems affect real users; describe who is affected.

## Related pages

- [Paths through the community](README.md)
- [Designers: start here](../../designers/designers-start-here.md)
- [Accessible slides and visuals](../../advocates/accessible-slides-and-visuals.md)
- [Advocating for accessibility](../../advocates/advocating-for-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
