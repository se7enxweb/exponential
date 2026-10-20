---
title: "Checklist: theme launch"
audience: designer, project-manager
category: theme-design
order: 860
summary: Everything to check before a new or changed design goes live: structure, settings, templates, assets, accessibility, dark mode, right to left, security policy, performance, caches and the switch-over itself.
keywords: checklist, launch, go live, theme launch, release design, pre launch, design review, quality gate, before going live
type: help
---
# Checklist: theme launch

This page is for designers and project managers before a design goes live. Each line links to the page that explains
it. Copy it into your ticket and tick it off.

## Contents

1. [Structure and settings](#structure-and-settings)
2. [Templates](#templates)
3. [Assets](#assets)
4. [Quality](#quality)
5. [The switch-over](#the-switch-over)

## Structure and settings

- [ ] The design lives in its own extension, registered with `DesignExtensions[]` ([Design extensions](design-extensions.md)).
- [ ] `ezinfo.php` and `extension.xml` carry the same version, license and name ([ezinfo.php](../extensions/ezinfo-php.md)).
- [ ] The siteaccess's design chain is intentional and short ([The design cascade](the-design-cascade.md)).
- [ ] Override rules have prefixed, unique section names and, where order matters, a `Priority`.
- [ ] No node ids in settings or overrides that differ between installations; remote ids or classes instead.
- [ ] No secrets in the extension (keys, passwords, hostnames of databases).

## Templates

- [ ] `./console exp:eztemplatecheck <design>/templates/` reports every file valid ([Debugging a design](debugging-a-design.md)).
- [ ] Every printed value is washed ([Wash and escaping](../templates/wash-and-escaping.md)).
- [ ] Every interface text goes through `i18n` with the theme's context.
- [ ] Every list fetch has a limit; no fetch inside a loop.
- [ ] Cache-blocks have the right keys; nothing user-specific in view-cached templates ([Caching for designers](caching-for-designers.md)).
- [ ] No `<style>`, `style=""`, inline scripts or handler attributes ([Checklist: CSP](checklist-csp.md)).

## Assets

- [ ] One `ezcss_load`, scripts at the end of the body or deferred ([Loading stylesheets](loading-stylesheets.md)).
- [ ] Fonts self-hosted with licenses shipped ([Fonts in a design](fonts-in-a-design.md)).
- [ ] Favicons and touch icons ([Favicons and touch icons](favicons-and-touch-icons.md)).
- [ ] Image aliases defined for the sizes shown ([Image aliases](image-aliases.md)).
- [ ] No third-party request before consent ([External resources and CDNs](external-resources-and-cdns.md)).

## Quality

- [ ] [Checklist: accessibility](checklist-accessibility.md) passed.
- [ ] [Checklist: dark mode](checklist-dark-mode.md) passed (if the design has one).
- [ ] [Checklist: right to left](checklist-right-to-left.md) passed (if the site has such languages).
- [ ] [Checklist: performance](checklist-performance.md) passed.
- [ ] Every page type checked at 1440 px, 960 px with scale 2, and 375 px.
- [ ] Print preview of an article is readable.
- [ ] Error pages (not found, no access) and the search page are styled.

## The switch-over

- [ ] Tested on a preview siteaccess against real content ([Previewing a design on a siteaccess](previewing-a-design-on-a-siteaccess.md)).
- [ ] Development settings off on the live siteaccess (`DevelopmentMode`, `ShowTemplatePathComments`, debug output).
- [ ] Caches cleared after the switch, packer included ([The packer cache and deploys](packer-cache-and-deploy.md)).
- [ ] The most visited pages opened signed out right after the switch.
- [ ] A way back: the old `SiteDesign` values written down.

## Related pages

- [Checklist: code review for templates](checklist-code-review-for-templates.md)
- [Checklist: the theme extension is ready](../extensions/checklist-theme-extension-ready.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
