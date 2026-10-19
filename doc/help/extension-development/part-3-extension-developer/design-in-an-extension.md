---
title: A design inside an extension
audience: developer, app-creator, designer
category: extension-development
order: 220
summary: Templates, overrides, stylesheets, scripts and images an extension carries: which design folder to use, how to make them overridable, and how they reach the page.
keywords: design, design extension, DesignExtensions, standard design, admin design, admin4, templates, override templates, module templates, datatype templates, overridable, images
type: help
---
# A design inside an extension

Part 3, chapter 22. Almost every extension has templates: the pages of its module, the views of its datatype, the
admin screens. They live in a `design/` folder of the extension, found through the design chain. This chapter is for
extension authors; building a whole **theme** is [Part 6](../part-6-theme-developers/README.md).

## Register the design folder

`extension/mysite_hello/settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

Every `extension/mysite_hello/design/<design>/` is then part of the design chain for siteaccesses that use `<design>`
([The design chain](../part-2-builder/the-design-chain.md)).

## Which design folder

| Templates for | Put them in | Why |
|---|---|---|
| your module's public pages | `design/standard/templates/<module>/` | `standard` is at the end of every chain: they work under any site design, and a site design can override them |
| your datatype | `design/standard/templates/content/datatype/{edit,view}/` and `class/datatype/{edit,view}/` | the same |
| your module's admin pages | `design/admin/templates/<module>/` and `design/admin4/templates/<module>/` | the admin designs; admin4 is the modern administration |
| overrides you ship as defaults | `design/standard/override/templates/` | used by override rules in your settings |

Do not put module or datatype templates into a site design (`design/mysite/`): they would disappear for every other
site design.

## Make them overridable

A site that installs your extension will want to change your HTML without editing your files. Help it:

- Keep templates small and split them into includes (`{include uri='design:hello/parts/item.tpl' item=$item}`); a site
  can override one part instead of the whole page.
- Name every template by what it is, under a folder named after your module.
- Put no logic into templates that a site would have to copy when it overrides them; move it to operators or the view.
- Document the templates and their variables in your README.

## Stylesheets, scripts, images

They go in `design/<design>/stylesheets/`, `javascript/` and `images/` and are loaded through the design's settings and
the ezjscore packer; see [CSS and JavaScript in an extension](css-and-javascript-in-an-extension.md). Templates never
carry `<style>`, `style=""`, inline `<script>` or `on...=` attributes: the administration enforces a Content Security
Policy and refuses them ([Content Security Policy for extension authors](content-security-policy-for-extensions.md)).

## New template files and caches

A **new** template file is only found after the template override and design caches are cleared:

```bash
php bin/php/ezcache.php --clear-id=template-override,design_base
```

Without it a page can render with an empty content area. `./console exp:velocity deploy` includes these clears.

## Check it

Turn on `ShowTemplatePathComments` and open your page: the comments must name your extension's files. Then try a site
override of one of them to see that it is overridable.

## RAD

**Design extension** (`/setup/designextension`) writes a design with templates, stylesheets and an override set; the
module wizards write their templates into `standard` and the admin designs.

## Related pages

- Previous: [CLI scripts and console commands](cli-scripts-and-console-commands.md)
- Next: [CSS and JavaScript in an extension](css-and-javascript-in-an-extension.md)
- [Admin pages in the admin4 style](admin-pages-in-admin4-style.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
