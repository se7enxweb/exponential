---
title: "With RAD: a design extension"
audience: developer, designer, app-creator
category: extension-development
order: 60
summary: The Design extension wizard writes a theme skeleton; then point a siteaccess at the new design and adjust two generated settings, from the wizard's real output.
keywords: RAD, design extension wizard, setup/designextension, theme, design, pagelayout, page_head, override, stylesheet, javascript, SiteDesign, AdditionalSiteDesignList, FrontendCSSFileList
type: help
---
# With RAD: a design extension

Tutorial 6. The **Design extension** wizard (`/setup/designextension`) writes the skeleton of a theme: a pagelayout split
into parts, a stylesheet and a script registered in `design.ini`, an images folder, and an example override. Designers
continue in [Part 6](../../part-6-theme-developers/README.md). The output below was produced by the wizard's generator
(`expDesignExtensionWizard::files()`); every PHP file passed `php -l`.

## 1. The form

| Field | Value |
|---|---|
| Extension name | `mysite_design` |
| Title | `My site design` |
| Base design | `base` |
| What goes in it | Page layout, Page parts, Stylesheets, JavaScript, Images directory, Template overrides, ezinfo.php (the defaults); optional: Print stylesheet, Siteaccess settings, Template operators |

## 2. The files

```text
.gitignore
LICENSE
README.md
composer.json
design/mysite_design/images/.gitkeep
design/mysite_design/javascript/site.js
design/mysite_design/override/templates/full/article.tpl
design/mysite_design/stylesheets/site.css
design/mysite_design/templates/page_footer.tpl
design/mysite_design/templates/page_head.tpl
design/mysite_design/templates/page_header.tpl
design/mysite_design/templates/pagelayout.tpl
extension.xml
ezinfo.php
settings/design.ini.append.php
settings/override.ini.append.php
```

The design is named after the extension, `mysite_design`. The pagelayout is short and readable:

```text
<!DOCTYPE html>
<html lang="{ezini( 'RegionalSettings', 'ContentObjectLocale' )|extract_left( 2 )}">
<head>
{include uri='design:page_head.tpl'}
</head>
<body class="mysite_design {$module_result.node_id|wash}">
{include uri='design:page_header.tpl'}
<main id="content" class="mysite_design-content">
{$module_result.content}
</main>
{include uri='design:page_footer.tpl'}
{ezscript_load( array( 'ezjsc::jquery',
                       ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
</body>
</html>
```

`page_head.tpl` builds the title from the path and loads the stylesheets with `ezcss_load`. The example override in
`settings/override.ini.append.php` gives articles their own full view:

```ini
[full_article_mysite_design]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

## 3. Point a siteaccess at the design

`DesignExtensions[]=mysite_design` (in the generated `design.ini.append.php`) makes the extension's design folders
searchable, but a design is only used by a siteaccess that names it. The generated README says the design is found
"without anything else being set"; in fact you still have to set it, for example:

```bash
./console exp:ini set site.ini/DesignSettings/SiteDesign mysite_design siteaccess:mysite
./console exp:ini clear 'site.ini/DesignSettings/AdditionalSiteDesignList[]' siteaccess:mysite
./console exp:ini add 'site.ini/DesignSettings/AdditionalSiteDesignList[]' base siteaccess:mysite
```

or tick **Siteaccess settings** in the wizard and fill in the siteaccess, so the extension carries
`settings/siteaccess/<name>/site.ini.append.php` itself. Check the result with the design chain script of
[The design chain](../../part-2-builder/the-design-chain.md).

## 4. Prefer the frontend lists

The generated `design.ini.append.php` registers the files in `CSSFileList[]` and `JavaScriptList[]`, which `design.ini`
marks as deprecated in favor of `FrontendCSSFileList[]` and `FrontendJavaScriptList[]`. Change the two lines:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=site.css

[JavaScriptSettings]
FrontendJavaScriptList[]=site.js
```

The generated pagelayout already loads both lists.

## 5. Switch it on

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_design global
php bin/php/ezcache.php --clear-all
```

Open the site: your frame, your stylesheet, articles with your full view. The generated `site.js` shows the
Content Security Policy-friendly pattern: a script file that enhances the page and is not needed for it to work.

## Related pages

- Previous: [With RAD: a workflow event](rad-workflow-event.md)
- Next: [With RAD: a settings-only extension](rad-settings-extension.md)
- [A theme with the RAD wizard](../../part-6-theme-developers/a-theme-with-the-rad-wizard.md)
- [A design inside an extension](../design-in-an-extension.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
