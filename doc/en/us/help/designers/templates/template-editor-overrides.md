---
title: The template editor
audience: designer, admin
category: templates
order: 760
summary: Working with templates and overrides in the administration: Design > Template Editor (visual/templatelist), creating an override with conditions, the order list and drag and drop, editing a template file in the browser, the permission it needs, and when to prefer files in a theme extension.
keywords: template editor, visual/templatelist, design tab, create override, edit template in browser, override priority, drag and drop overrides, visual module, templateview
type: help
---
# The template editor

This page is for designers who manage overrides in the administration rather than in INI files. The full description
is the feature page [Template editor: create, order and edit overrides](../../../features/6.0/template-editor-overrides.md).

## Contents

1. [Where it is](#where-it-is)
2. [What it does](#what-it-does)
3. [Editor or files?](#editor-or-files)

## Where it is

**Design** tab, left menu **Template Editor** (`/visual/templatelist`, `settings/menu.ini [Leftmenu_design]`
`Links[template_editor]`). A user needs the `visual/templatelist` policy.

## What it does

- Lists the templates of a siteaccess and, per template, its overrides in evaluation order.
- Creates an override: pick the template, the conditions (class, node, section ...), the new file name; a new
  override is written with `Priority=0` so it takes effect at once.
- Reorders overrides by drag and drop and saves `Priority` values (10, 20, 30 ...) for that template's rules.
- Edits template files in the browser.

## Editor or files?

| | Template editor | Files in a theme extension |
|---|---|---|
| Quick fix on one site | good | slower |
| Version control, review, deploy to several sites | no | yes |
| Survives a reinstall or a move to another server | only with the site's settings | yes, with the extension |

For a theme you maintain, keep overrides and templates in your extension's files ([Override basics](override-ini-basics.md))
and use the editor to look at the order and to experiment on a test siteaccess.

## Related pages

- [Override ordering](override-ordering.md)
- [Override basics](override-ini-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
