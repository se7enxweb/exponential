---
title: "Checklist: the theme extension is ready"
audience: designer
category: theme-extensions
order: 450
featured: true
summary: The checklist to run before a theme extension leaves your hands - structure, metadata, settings, license, Composer, packages, design quality and a clean repository - each item with the command or page that checks it.
keywords: checklist, ready, theme extension, release readiness, metadata, license, composer validate, xmllint, php -l, quality
type: help
---
# Checklist: the theme extension is ready

This page is for designers about to release or hand over a theme. Copy the list into your release issue and tick it.

## Structure and metadata

- [ ] `extension/<name>/` uses a valid name ([Naming](naming-your-theme-extension.md)).
- [ ] `ezinfo.php` passes `php -l` and shows name, version, copyright, license, website.
- [ ] `extension.xml` passes `xmllint --noout` and agrees with `ezinfo.php`.
- [ ] Every version field shows the release number ([Keeping every version field in sync](keeping-version-fields-in-sync.md)).
- [ ] `LICENSE` holds the official text; third-party notices sit next to their assets.
- [ ] `README.md`, and `INSTALL.md` if installing takes more than one step; `CHANGELOG.md` has this release.

## Settings

- [ ] `settings/design.ini.append.php` registers the design (`DesignExtensions[]`).
- [ ] The theme does not set `SiteDesign` globally ([The design settings a theme ships](design-settings-a-theme-ships.md)).
- [ ] No password, key or token in any settings file; no `settings/override/` in the theme.
- [ ] Every own setting is commented.

## Packaging

- [ ] `composer validate` passes; `extra.ezpublish-legacy-extension-name` names the folder.
- [ ] A test install with Composer lands in the right folder and renders.
- [ ] Bundled `.ezpkg` files validate and install on a test site.

## Design quality

- [ ] Accessibility checklist passed ([Checklist: accessibility](../theme-design/checklist-accessibility.md)).
- [ ] No inline CSS or JavaScript; the CSP checklist passed ([Checklist: CSP](../theme-design/checklist-csp.md)).
- [ ] Light and dark mode, 200% zoom and a phone width checked ([Checklist: dark mode](../theme-design/checklist-dark-mode.md)).
- [ ] Templates escape output ([Checklist: code review for templates](../theme-design/checklist-code-review-for-templates.md)).

## Repository

- [ ] `git status --short` is empty; nothing ignored has slipped in ([A .gitignore for themes](gitignore-for-themes.md)).
- [ ] No customer names or personal data in demo data, screenshots or text.

When every box is ticked, continue with [Checklist: before you publish](../distribution/distribution-checklist.md).

## Related pages

- [Troubleshooting theme extensions](troubleshooting-theme-extensions.md)
- [Checklist: theme launch](../theme-design/checklist-theme-launch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
