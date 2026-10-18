---
title: "Estimating theme work"
audience: designer, project-manager
category: theme-sales
order: 90
summary: Break theme work into pieces you can estimate - pagelayout, view templates, overrides, styles, accessibility, dark mode, testing - keep your own records of actual time, and add a margin for review and the unknown.
keywords: estimate, effort, breakdown, work breakdown, templates, testing, accessibility, time tracking, margin
type: help
---
# Estimating theme work

This page is for designers who need to say how long a theme takes. No figures are given here: your own records are
the only reliable source. The page shows how to build them.

## Break the work down

| Piece | Includes |
|---|---|
| Setup | Theme extension skeleton, settings, test siteaccess ([The design extension wizard](../extensions/design-extension-wizard.md)) |
| Pagelayout | Head, header, navigation, footer |
| View templates | Full, line, embed views per content class |
| Overrides | Rules for classes, sections, nodes |
| Styles | Tokens, components, responsive layout, print |
| Dark mode and right-to-left | If in scope |
| Accessibility | Contrast, keyboard, landmarks, forms; an axe run per page |
| Content Security Policy | No inline scripts or styles |
| Testing | Widths, zoom, browsers, light and dark |
| Documentation and handover | README, editor guide, session |
| Review rounds | Changes after the client's feedback |

## Step by step

1. List the pieces for the project, with the number of templates per piece.
2. For each, take your recorded time from earlier projects; where you have none, estimate and mark it as a guess.
3. Add review rounds and a margin for the unknown, and say both openly.
4. After the project, record the actual time per piece. Next time your estimate is better.

## Common underestimates

- Testing at several widths, in dark mode and at 200% zoom.
- Edge cases of content: long titles, missing images, empty fields.
- Upgrading the theme for the next Exponential release ([Following Exponential releases](../lifecycle/following-exponential-releases.md)).

## Related pages

- [Pricing a custom theme project](pricing-a-custom-theme.md)
- [Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
