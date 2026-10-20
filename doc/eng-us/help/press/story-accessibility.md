---
title: "Story: accessibility"
audience: press
category: press-media
order: 63
summary: A feature story about the accessibility work in the Exponential administration - WCAG rules checked by tests, keyboard use, skip links and landmarks, dark mode and forced colors, alternative text for images - with printable facts, sources and the conformance claims you must not make.
keywords: story, accessibility, WCAG, admin4, keyboard, screen readers, contrast, dark mode, forced colors, alternative text, press
type: help
---
# Story: accessibility

This page is for journalists who cover digital accessibility, and for creators with disabilities who review software.
Accessibility claims are easy to overstate; the documented work is in the **administration** of Exponential and its
help, not in every public site. This page tells you what is documented and how to say it.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Testing it yourself](#testing-it-yourself)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

In October 2026 the new administration design of Exponential, admin4, went through an accessibility series: landmarks
and names, keyboard use of tabs and the content tree, a name for every form field, contrast of the colors, focus
indicators and the modes a browser asks for ([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#14-october-2026-the-admin4-accessibility-series-merged-and-refined)).
"The admin4 design follows WCAG rules that tests check: a contrast test of the color tokens in light and dark mode and
target sizes of at least 24 by 24 pixels" ([Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility)).
The rules bind contributors too: no inline styles, shared components, color tokens with dark values, and a test that
enforces it ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).

## What is new, and when

All in the 6.0.15 development line, which is not released.

| What | When | Source |
|---|---|---|
| Landmarks and names in admin4 | 14 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#14-october-2026-landmarks-and-names-in-admin4) |
| The content tree is a tree for screen readers and keyboards | 14 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#14-october-2026-the-content-tree-is-a-tree-for-screen-readers-and-keyboards) |
| Focus indicators in admin4 | 14 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#14-october-2026-admin4-focus-indicators) |
| The remaining serious accessibility findings of the admin pages | 17 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-the-remaining-serious-accessibility-findings-of-the-admin-pages) |
| The layout editor page and its zoom | 17 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-the-layout-editor-page-can-be-zoomed) |

## Facts you can print

| Fact | Source |
|---|---|
| Targets are at least 24 by 24 pixels (WCAG 2.5.8), and every form field has a name. | [Talking points: accessibility](../advocates/talking-points-accessibility.md#the-points) |
| A skip link is the first Tab stop, with landmarks for header, menus, breadcrumb and main content. | [The admin4 design, Landmarks and names](../../features/6.0/admin4-design.md#landmarks-and-names) |
| The administration and the help can be used with the keyboard alone. | [Keyboard shortcuts](../keyboard-shortcuts.md) |
| Light and dark themes, Windows high contrast (forced colors) and reduced motion are supported. | [Talking points: accessibility](../advocates/talking-points-accessibility.md#the-points) |
| On the dashboard, a change is shown as a number, an arrow and words, never by color alone. | [The admin4 design, What you get](../../features/6.0/admin4-design.md#what-you-get) |
| Image fields can require alternative text, per class; it is off until switched on. | [Talking points: accessibility](../advocates/talking-points-accessibility.md#the-points) |
| Exponential Intelligence can suggest alternative text and report images that lack it. | [expai, For editors](../../features/6.0/extensions/expai.md#for-editors) |
| Right-to-left languages turn the administration around. | [Right to left](../../features/6.0/right-to-left.md) |

## What not to claim

- "Exponential is WCAG compliant" or "fully accessible". The sources describe tests and features of the
  administration, not a conformance claim for every page or site.
- "Sites built with Exponential are accessible." Each site's own templates and content decide that.
- "Screen readers work perfectly." Say what is built and invite testing.
- The sources contain no accessibility statement for the product; do not say there is one.

## Testing it yourself

On an installation you control: press `Tab` once on any admin page (the skip link appears), press the sun button
(dark mode), use the arrow keys in the content tree, open the help with "?" and press `/`
([Talking points: accessibility](../advocates/talking-points-accessibility.md#show-it-in-one-minute)). Reviewers who use
assistive technology are the best testers; report problems as bugs in the public tracker.

## Angles and headline ideas

```text
"Accessibility as a test that fails: how Exponential guards its admin"
"No state by color alone: inside a CMS administration redesign"
"Contributors must follow the same rules: accessibility written into CONTRIBUTING.md"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| The accessibility work and its tests | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| An accessibility bug | the public tracker | <https://github.com/se7enxweb/exponential/issues> |

## Related pages

- [Talking points: accessibility](../advocates/talking-points-accessibility.md)
- [Story: admin4, the administration design](story-admin4.md)
- [Story: languages and right to left](story-languages-and-right-to-left.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
