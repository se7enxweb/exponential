---
title: Publishing a theme or design as a partner
audience: partner
category: partnership
order: 430
summary: How creative and IT partners publish a theme or design for Exponential - packaging it as an extension, design rules, accessibility, languages and right to left, assets and their licenses, screenshots, release and maintenance.
keywords: theme, design, publish, package, extension, templates, stylesheets, accessibility, right to left, dark mode, assets, fonts, images, license, screenshots
type: help
---
# Publishing a theme or design as a partner

This page is for designers and front-end developers at partner companies. In Exponential a theme or design is
shipped as an extension that contains a design directory (templates, stylesheets, images) and its settings, so the
release steps of [Publishing an extension as a partner](publishing-an-extension.md) apply. This page adds what is
special about designs.

## Before you package it

- [ ] Remove the customer's brand: logo, colors, fonts, images, texts.
- [ ] Replace them with neutral defaults that are easy to change in settings or stylesheets.
- [ ] Get written permission if the design was paid customer work.

## Design quality checklist

- [ ] Works with the content model it expects, and degrades gracefully when fields are empty
- [ ] Works with long titles and other languages; right to left tested if you claim support
      ([Languages](../languages.md))
- [ ] Keyboard navigation and visible focus; text contrast at least 4.5:1
- [ ] Responsive from phone width up; no horizontal scrolling
- [ ] Works with the layouts editor's blocks if it targets layouts ([The layouts editor](../layouts-editor.md))
- [ ] If it touches the administration: follows `doc/eng-us/bc/6.0/admin4-styling.md` (no inline CSS, tokens with dark
      values)

## Assets and their licenses

Every file in a theme has a license. List them in the README:

| Asset | Check |
|---|---|
| Fonts | The font license allows redistribution |
| Icons | The icon set's license allows redistribution |
| Images | Your own, or a license that allows redistribution; no stock photos you may only use on one site |
| Third-party CSS/JS | License compatible with your theme's license |

## Screenshots and demo

- Two or three screenshots with neutral demo content, light and dark if supported.
- A demo installation only if you can keep it current.

## Release and maintenance

- Version and release as for any extension ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).
- Re-test after each Exponential release, especially template and layout changes.
- Keep a changelog designers can follow.

## In the program

A published design is evidence for the Design profile certification and can be the basis of a tutorial or case study
([Certifications](certifications.md), [Earning points through community work](earning-points-through-community-work.md)).

## Related pages

- [Creative partners](creative-partners.md)
- [Extension and theme partners](extension-and-theme-partners.md)
- [Publishing an extension as a partner](publishing-an-extension.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
