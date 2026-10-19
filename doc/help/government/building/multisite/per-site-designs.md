---
title: Per-site designs
audience: government, website-creator, designer
category: public-sector
order: 40
summary: How each department site of a multisite Exponential installation gets its own look while sharing a common base: SiteDesign and the design fallback chain, a shared government base design, per-department overrides, and keeping accessibility consistent.
keywords: design, SiteDesign, AdditionalSiteDesignList, fallback chain, design extension, branding, department branding, template override, shared header, accessibility
type: help
---
# Per-site designs

This page is for site builders and designers. Each siteaccess names its design in `[DesignSettings] SiteDesign`, with
`AdditionalSiteDesignList[]` as fallbacks; templates are found by walking that chain
([Designs and the fallback chain](../../../creators/website-creators/designs-and-the-fallback-chain.md)).

## Contents

1. [A shared base, a thin layer per department](#a-shared-base-a-thin-layer-per-department)
2. [Settings](#settings)
3. [What a department layer usually changes](#what-a-department-layer-usually-changes)
4. [Consistency and accessibility](#consistency-and-accessibility)

## A shared base, a thin layer per department

Public bodies often have one identity with department variations. Build one base design that holds the pagelayout,
the components and the accessibility work, and a thin design per department that overrides only what differs.

## Settings

```ini
# settings/siteaccess/dept_example/site.ini.append.php
[DesignSettings]
SiteDesign=dept_example
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=gov_base
AdditionalSiteDesignList[]=simple
```

Keep each design in an extension of its own, so it is versioned and deployed with Git
([Template overrides](../../../creators/website-creators/template-overrides.md)).

## What a department layer usually changes

- The logo and the name in the header (often content: a shared header can be kept as content,
  [Shared header and footer](../../../creators/website-creators/shared-header-and-footer.md)).
- An accent color, through the design's stylesheet tokens.
- The footer contacts.
- Templates for a class only that department uses.

## Consistency and accessibility

- Keep navigation, search and the language switcher in the same place on every site.
- Accessibility work done in the base design applies to every department; overrides must not undo it (contrast of
  accent colors, focus styles). Test each department site ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)).
- The public designs that ship are not audited for accessibility ([Accessibility standards](../../../../standards/accessibility.md)).

## Related pages

- [Branding your site](../../../creators/website-creators/branding-your-site.md)
- [Choosing a design or theme](../../../creators/website-creators/choosing-a-design-or-theme.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
