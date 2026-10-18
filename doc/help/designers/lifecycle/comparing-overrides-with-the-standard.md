---
title: "Comparing your overrides with the standard templates"
audience: designer, developer
category: theme-lifecycle
order: 110
summary: Find out whether the standard templates you copied into your theme changed upstream - list your copies, diff them against the new release, and merge fixes (especially escaping and accessibility fixes) into your versions.
keywords: overrides, template drift, diff, standard templates, design/standard, upstream changes, merge, escaping fixes
type: help
---
# Comparing your overrides with the standard templates

This page is for designers whose theme contains copies of standard templates (from `design/standard` or another
theme) that it changes. When the original is fixed upstream, your copy keeps the old version until you merge the fix.

## Step by step

1. **List your copies**: every template in your design that has a counterpart in `design/standard/templates/` (or in
   the base theme):

   ```bash
   cd extension/mytheme/design/mytheme/templates
   find . -name '*.tpl' | while read f; do [ -f "../../../../../design/standard/templates/$f" ] && echo "$f"; done
   ```

2. **See what changed upstream** between the old and the new release, for each file:

   ```bash
   git -C /path/to/exponential log --oneline v6.0.13..v6.0.14 -- design/standard/templates/node/view/full.tpl
   ```

3. **Read each change** and decide whether your copy needs it. Fixes for escaping (`|wash`), accessibility and
   markup that the kernel now expects are usually needed.
4. **Merge** the needed changes by hand into your copy, keeping your own changes.
5. **Test** the pages that use the template.

## Keep the list short

The fewer templates you copy, the less there is to compare. Prefer overriding one template for one class over
copying general ones, and use the design cascade instead of copies where you can
(The design cascade).

## Related pages

- [Upgrade testing, step by step](upgrade-test-step-by-step.md)
- Override basics
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
