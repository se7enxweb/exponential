---
title: AdminUI and the admin designs
audience: ecosystem, admin, editor, designer
category: ecosystem
order: 15
summary: The administration interfaces of the Exponential ecosystem - admin4, admin4l built from layouts, AdminUI ported from Netgen Admin UI, admin3 and the classic designs - how a user switches between them, and the shared styling rules behind them.
keywords: admin designs, admin4, admin4l, AdminUI, exp_adminui, admin3, classic admin, UI switch, uiswitch/design, styling rules, tokens, dark mode
type: help
---
# AdminUI and the admin designs

The administration of Exponential comes in several designs. They show the same administration with different looks
and layouts, and a user can switch between them.

## Contents

1. [The designs](#the-designs)
2. [Switching designs](#switching-designs)
3. [Shared rules](#shared-rules)
4. [Where they come from](#where-they-come-from)

## The designs

| Design | What it is | Page |
|---|---|---|
| Admin 4 (`admin4`) | "a complete administration design in the look of the setup wizard, with a light and a dark mode" | [The admin4 design](../../../features/6.0/admin4-design.md) |
| Admin 4 (layouts) (`admin4l`) | admin4 "whose page is assembled by Exponential Layouts" | [The admin4l design](../../../features/6.0/admin4l.md) |
| AdminUI (`adminui`) | "the Netgen Admin UI look and layout for the Exponential administration", from the extension `exp_adminui`, as its own siteaccess | [Exponential Admin UI](../../../features/6.0/exp-adminui.md) |
| Admin 3 (`admin3`) | A responsive administration design | [admin3 responsive admin](../../../features/6.0/admin3-responsive-admin.md) |
| Classic (`admin`) and Classic, server-rendered (`admin_classic`) | The classic administration | [Choosing the administration design](../../../features/6.0/ui-switch-design.md) |

## Switching designs

"A signed-in user who holds the policy `uiswitch/design` can choose which administration design they work in [...]
from the right toolbar. The choice changes the interface for that user only, and only for the siteaccess where it
was made" ([Choosing the administration design for your session](../../../features/6.0/ui-switch-design.md)).

## Shared rules

Every admin design follows the same styling rules: no `<style>` block or `style=""` attribute in templates, shared
components and design tokens with dark values, module CSS in `stylesheets/modules/`
([Styling the administration](../../../bc/6.0/admin4-styling.md); designers' pages
[Admin designs overview](../../designers/theme-design/admin-designs-overview.md)).

## Where they come from

- The admin designs are part of the kernel repository (`design/`).
- AdminUI is "A feature complete port of the legendary Netgen AdminUI Administration UI. This solution depends on
  Exponential 6.0.15* or greater" (repository description of `se7enxweb/exp_adminui`, quoted on
  [AdminUI](../../who-is-the-exponential-foundation.md#adminui)), another example of the ecosystem porting work from a
  neighboring PHP project.
- The Symfony platform line has its own admin UI forks ([The Exponential platform ecosystem](../../../history/ecosystem.md)).

## Related pages

- [Products](README.md)
- [Admin tour](../../admin-tour.md)
- [Themes and designs](../packages/themes-and-designs.md)
- [Designers: start here](../../designers/designers-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
