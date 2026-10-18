---
title: "A deprecation policy"
audience: designer, developer
category: theme-lifecycle
order: 220
summary: Decide and publish how your theme retires settings, templates, view modes and operators - announce, keep working, warn, remove in a later step - so users have time to adapt.
keywords: deprecation, deprecation policy, retire, announce, removal, settings, templates, operators, notice period
type: help
---
# A deprecation policy

This page is for designers whose theme has users who built on its settings, templates or operators. Removing them
without notice breaks those users' sites.

## A policy in four steps

1. **Announce**: the changelog says "Deprecated: <thing>, use <replacement>; it will be removed in <version or date>".
2. **Keep it working**: the old thing still works, ideally through the new one.
3. **Warn**: where possible, log a notice when the old thing is used (for example `eZDebug::writeWarning()` in an
   operator), so developers see it in the debug output.
4. **Remove** in the announced release, with a `Removed:` entry and the upgrade step.

The product does the same: `settings/design.ini` keeps `CSSFileList[]` and `JavaScriptList[]` working while marking them
"DEPRECATED in favor of [Frontend|Backend]CSSFileList[]" and "...JavaScriptList[]".

## Step by step: write the policy

1. Choose the notice period: a number of releases or a time span you can keep.
2. Write the four steps with your period into the README or `doc/deprecation.md`.
3. List current deprecations there, each with its replacement and planned removal.

## Related pages

- [Deprecating a setting or a template](deprecating-a-setting-or-template.md)
- [Breaking changes](breaking-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
