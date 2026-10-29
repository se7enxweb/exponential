---
title: Creative partners
audience: partner
category: partnership
order: 370
summary: For creative partners - design studios, web and communication agencies - what the profile means, what to know about Exponential's designs, layouts and editor experience, how to work with developers, and how creative partners contribute.
keywords: creative partner, design, UX, agency, studio, communication, layouts, templates, accessibility, dark mode, editor experience, themes
type: help
---
# Creative partners

This page is for partners with the Creative profile. The program describes them as partners who "Bring their design
and communication skills to Exponential projects, often with a focus on communication strategy (e.g., web design
companies, web agencies, communication and advertisement agencies)" (<https://exponential.earth/partner/program>,
read 18 October 2026).

## What creative partners should know about Exponential

| Topic | Why it matters to designers | Read |
|---|---|---|
| Content model | Designs follow the structure of content, not the other way round | [Content modeling workshops](../leadership/content-modeling-workshops.md) |
| Layouts | Editors arrange blocks in zones; designs must work in many arrangements | [The layouts editor](../layouts-editor.md) |
| Languages and right to left | 359 locale files, 43 of them right to left (`doc/eng-us/languages.md`) | [Languages](../languages.md) |
| Media | Image variations and uploads | [Images and media](../media-and-uploads.md) |
| Editor experience | The people who use the administration every day | [Editing content](../editing-content.md), [Admin tour](../admin-tour.md) |
| Accessibility | The admin4 design follows WCAG rules checked by tests | `doc/eng-us/bc/6.0/admin4-styling.md` |

## Designing for the administration

If your work touches the administration designs, follow the project's rules (CONTRIBUTING.md and
`doc/eng-us/bc/6.0/admin4-styling.md`): no `<style>` blocks or `style=""` attributes in templates, shared `.a4-*`
components, `--a4-*` tokens with dark values, module CSS in `stylesheets/modules/`. These rules exist because the
administration enforces a Content Security Policy and supports light and dark modes.

## Working with developers

- Hand over designs with states: empty, long text, other languages, right to left, dark mode, high zoom.
- Agree the block types early; each new block is development work.
- Use real content in prototypes; invented "lorem ipsum" hides problems the content model will show.

## Services creative partners offer

- Visual identity and design systems for sites built on Exponential.
- Editor experience reviews: is the administration easy for this customer's editors?
- Content strategy and campaigns ([Co-marketing with 7x and the project](co-marketing.md)).
- Themes and designs published for others ([Publishing a theme or design](publishing-a-theme.md)).

## In the program

- From Silver: the Design profile certification is part of the requirement ([Certifications](certifications.md)).
- Case studies and articles earn points ([The points system](the-points-system.md)).

## Related pages

- [Extension and theme partners](extension-and-theme-partners.md)
- [Publishing a theme or design as a partner](publishing-a-theme.md)
- [Partner profiles](partner-profiles.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
