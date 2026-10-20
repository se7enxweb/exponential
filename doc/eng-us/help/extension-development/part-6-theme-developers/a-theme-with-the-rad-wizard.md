---
title: A theme with the RAD wizard
audience: designer, developer
category: extension-development
order: 30
summary: Start a theme with the Design extension wizard of Setup > RAD and grow its skeleton into a real theme - siteaccess, frontend lists, base design, page parts, view templates - with three corrections.
keywords: RAD, design extension wizard, theme, /setup/designextension, skeleton, pagelayout, siteaccess, FrontendCSSFileList, base design
type: help
---
# A theme with the RAD wizard

Part 6, chapter 3. The quickest way to start a theme is the **Design extension** wizard in the administration
(**Setup > RAD**, address `/setup/designextension`). It writes a complete, working skeleton in a minute. This chapter
uses it as a starting point and then shows how to grow the skeleton into a theme. The wizard itself, field by field,
is the tutorial [With RAD: a design extension](../part-3-extension-developer/tutorials/rad-design-extension.md);
the flow all wizards share is in [The RAD tools tour](../part-3-extension-developer/tutorials/the-rad-tools-tour.md).

## 1. Fill in the wizard

| Field | For a theme |
|---|---|
| Extension name | the theme's machine name, lower case: `mytheme` |
| Title | the readable name: `My theme` |
| Base design | the design your theme falls back to for every template it does not have: usually `base` or `standard` |
| What goes in it | keep the defaults (page layout, page parts, stylesheets, JavaScript, images, template overrides, ezinfo.php); tick **Siteaccess settings** and enter your public siteaccess |
| License | the license you will publish under; GPL-2.0-or-later matches the kernel |

Look at the preview of every file, then **Create in extension/** (or **Download as zip** if the web server may not
write there).

## 2. Three corrections

The generated files work, but three things are worth changing at once. All three were found while writing this book
by generating a design extension and reading every file:

1. **The siteaccess must name the design.** The generated README says the design is found without further settings.
   It is registered (`DesignExtensions[]`), but a siteaccess uses it only when its `site.ini [DesignSettings]` names
   it. Ticking **Siteaccess settings** writes that file for you; otherwise set it yourself
   ([Themes and siteaccesses](themes-and-siteaccesses.md)).
2. **Use the frontend lists.** The generated `design.ini.append.php` registers the files in `CSSFileList[]` and
   `JavaScriptList[]`, which the kernel marks as deprecated. Change them to `FrontendCSSFileList[]` and
   `FrontendJavaScriptList[]`; the generated pagelayout loads both.
3. **Activate the extension.** The wizard tells you the line; until you add it, nothing happens.

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mytheme global
php bin/php/ezcache.php --clear-all
```

## 3. From skeleton to theme

The skeleton has a pagelayout, a header, a footer, one stylesheet, one script and one override (articles in full
view). A theme needs more. Work in this order, checking the site after each step:

| Step | Files | Help |
|---|---|---|
| The frame | `templates/pagelayout.tpl`, `page_head.tpl`, `page_header.tpl`, `page_footer.tpl` | [Templates for builders](../part-2-builder/templates-for-builders.md) |
| Navigation | a menu template in `templates/parts/`, included from the header | [Fetch for builders](../part-2-builder/fetch-for-builders.md) |
| Full views of your classes | `override/templates/full/<class>.tpl` plus a rule each | [Template overrides](../part-2-builder/template-overrides-basics.md) |
| Lists | `override/templates/line/<class>.tpl` | [View modes for builders](../part-2-builder/view-modes-for-builders.md) |
| Embedded objects | `override/templates/embed/<class>.tpl` | the same |
| Forms and user pages | copies of the kernel's `user/` and `content/` templates you want to change | [The design chain](../part-2-builder/the-design-chain.md) |
| Styles | `stylesheets/site.css`, split into files when it grows | [Theme assets, the packer and CSP](theme-assets-the-packer-and-csp.md) |

Copy a kernel template into your design only when you change it. Every copied template is one you have to compare
with the kernel's after each upgrade.

## 4. Commit as you go

Put the theme into Git right after the wizard wrote it, before your first change. Then each step above is a commit,
and you can always see what the wizard wrote and what you changed.

```bash
cd extension/mytheme
git init
git add -A
git commit -m "Added: The theme skeleton written by the design extension wizard"
```

## What can go wrong

| Symptom | Cause | Fix |
|---|---|---|
| The site looks unchanged | the siteaccess does not name the design, or the extension is not active | chapter [Themes and siteaccesses](themes-and-siteaccesses.md) |
| A new template is not used | the template override cache | `php bin/php/ezcache.php --clear-id=template,template-override` |
| The stylesheet is missing | it is in a deprecated list the pagelayout does not read, or the packer cache is old | check the list names; clear the caches |
| The page is empty below the header | an error in a template | turn on the debug output and template debugging ([Debugging templates](../part-2-builder/debugging-templates.md)) |

## Related pages

- Previous: [The anatomy of a design extension](design-extension-anatomy.md)
- Next: [A theme by hand](a-theme-by-hand.md)
- [With RAD: a design extension](../part-3-extension-developer/tutorials/rad-design-extension.md)
- [RAD tools](../../../features/6.0/rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
