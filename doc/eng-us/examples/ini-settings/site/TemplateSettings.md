---
title: site.ini [TemplateSettings]
audience: developer, admin
category: examples
summary: "The settings of [TemplateSettings] in site.ini: AutoloadPathList, ExtensionAutoloadPath, Debug, ShowXHTMLCode, ShowMethodDebug, ShowUsedTemplates, ShowTemplatePathComments, TemplatesWithoutDebugComments ..."
keywords: site.ini, TemplateSettings, INI, setting, AutoloadPathList, ExtensionAutoloadPath, Debug, ShowXHTMLCode, ShowMethodDebug, ShowUsedTemplates, ShowTemplatePathComments, TemplatesWithoutDebugComments, NodeTreeCaching, TemplateCompile, ShareCompiledTemplates, SharedCompiledTemplatesDir, TemplateOptimization, UseFormatting, TemplateCache, DelayedCacheBlockCleanup, TemplateCompression, DevelopmentMode, CompileComments, CompileAccumulators, CompileTimingPoints, CompileResourceFallback, CompileNodePlacements, CompileExecution, CompileAlwaysGenerate, CompileIncludeNodeTree
type: generated
generated: exp:help:examples
---
# site.ini [TemplateSettings]

The block `[TemplateSettings]` of `settings/site.ini`, with 26 setting(s).

> AllowedDeletionDirs\[\]=/var/share/somedir  

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## AutoloadPathList

> A list of directories to look for eztemplateautoload.php files  
> These files will be used to automatically load in template functions and operators  
> You can new ones if you create custom template code.  

Default: a list of 6: `lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`, `lib/smarty/classes/`, `lib/twig/classes/`, `kernel/private/eztemplate/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# an empty "AutoloadPathList[]" first replaces the shipped list; without it the items are added
AutoloadPathList[]
AutoloadPathList[]=lib/eztemplate/classes/
AutoloadPathList[]=kernel/common/
AutoloadPathList[]=lib/ezpdf/classes/
AutoloadPathList[]=lib/smarty/classes/
AutoloadPathList[]=lib/twig/classes/
AutoloadPathList[]=kernel/private/eztemplate/
*/ ?>
```

## ExtensionAutoloadPath

> A list of extensions which have template autoloads.  
> Only specify the extension name, not the path.  
> The extension must contain a subdirectory called autoloads.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# an empty "ExtensionAutoloadPath[]" first replaces the shipped list; without it the items are added
ExtensionAutoloadPath[]
*/ ?>
```

## Debug

> Enabled to see which template files are loaded or disabled to supress debug  
> Warning: Will add debug xhtml comments to your source code, including mails!  
> Note: No debug on templates starting with <!DOCTYPE to not trigger quirks mode!  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
Debug=disabled
*/ ?>
```

## ShowXHTMLCode

> If enabled will add code to display the template name in the browser  
> If Debug is disabled then nothing happens  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowXHTMLCode=enabled
*/ ?>
```

## ShowMethodDebug

> Whether to show debug of functions and operators when  
> processing nodes.  
> This only meant for kernel developers to check which  
> operators and functions are called.  
> Note: Will only work when template compiler is off  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowMethodDebug=disabled
*/ ?>
```

## ShowUsedTemplates

> If enabled will add a table with templates used to render a page.  
> DebugOutput should be enabled too.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowUsedTemplates=disabled
*/ ?>
```

## ShowTemplatePathComments

> If enabled, wraps every rendered template in HTML comments naming its path,  
> e.g. <!-- START design/media/templates/pagelayout/footer.tpl --> ... <!-- STOP ... -->  
> This mirrors the Symfony/Twig debug output and makes layout debugging and block  
> tracking possible in the legacy pipeline. Works with the template compiler on or off.  
> Development only: it injects markup into every template's output, so it must stay  
> disabled in production and on any siteaccess serving JSON, XML or RSS.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=disabled
*/ ?>
```

## TemplatesWithoutDebugComments

> Templates that Debug and ShowTemplatePathComments never wrap in comments, by their path below templates/  
> (a file of that path in any design or extension), like  
> <h1 class="title"{include uri='design:parts/exp\_content\_direction.tpl' ...}>, which prints into a start tag.  
> Most templates need no entry: the comments are taken out of every rendered output where they would break it  
> (inside a tag or an attribute value, inside <script>, <style>, <title> and <textarea>, and everywhere in output  
> that is not HTML: XML, JSON, plain text), and a set-block that captures a value rather than HTML (a name, a URL,  
> attributes, nothing) gets no comments. List a template whose output reaches the page some other way, e.g. through  
> an operator that changes it before it is printed.  

Default: a list of 1: `parts/exp_content_direction.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# an empty "TemplatesWithoutDebugComments[]" first replaces the shipped list; without it the items are added
TemplatesWithoutDebugComments[]
TemplatesWithoutDebugComments[]=parts/exp_content_direction.tpl
*/ ?>
```

## NodeTreeCaching

> Determines whether the internal node tree should be cached, by enabling this the loading  
> and parsing of templates is significantly reduced.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
NodeTreeCaching=disabled
*/ ?>
```

## TemplateCompile

> Determines whether the templates should be compiled to PHP code, by enabling this the loading  
> and parsing of templates is omitted and template processing is significantly reduced.  
> Note: The first time the templates are compiled it will take a long time, use the  
>       bin/php/eztc.php script to prepare all your templates.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCompile=enabled
*/ ?>
```

## ShareCompiledTemplates

> If you share compiled templates with other site accesses  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShareCompiledTemplates=disabled
*/ ?>
```

## SharedCompiledTemplatesDir

> Where to store shared compiled templates  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
SharedCompiledTemplatesDir=
*/ ?>
```

## TemplateOptimization

> Controls whether further optimizations should be performed on compiled  
> templates  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateOptimization=enabled
*/ ?>
```

## UseFormatting

> Controls whether to include formatting in compiled templates or not.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
UseFormatting=disabled
*/ ?>
```

## TemplateCache

> Controls all template base caching mechanisms, if disabled they will never be  
> used.  
> The elements currently controlled by this is:  
> - cache-block  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCache=enabled
*/ ?>
```

## DelayedCacheBlockCleanup

> Controls whether expired cache-blocks with 'subtree\_expiry' parameter will  
> be removed from disk immediately or not.  
> If enabled it should increase performance of large sites but you should remove  
> expired caches manually or using cronjob 'cronjobs/subtreeexpirycleanup.php'.  
> Since 4.6, this cronjob is not activated by default.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
DelayedCacheBlockCleanup=disabled
*/ ?>
```

## TemplateCompression

> Controls whether compiled templates should be stored gzip compressed on disk.  
> This drastically reduces disk usage.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCompression=disabled
*/ ?>
```

## DevelopmentMode

> Controls if development is enabled or not.  
> When enabled the system will perform more checks like modification time on  
> compiled vs source file and will reduce need for clearing template compiled  
> files.  
> Note: Live sites should not have this enabled since it increases file access  
>       and can be slower.  
> Note: When switching this setting the template compiled files must be cleared.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
DevelopmentMode=disabled
*/ ?>
```

## CompileComments

> NOTE:  
> The following settings are for template compilation development only  
> and are not useful for normal sites.  
>
> Whether to include comments inside cache files or not.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileComments=disabled
*/ ?>
```

## CompileAccumulators

> Whether to include debug accumulators.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileAccumulators=disabled
*/ ?>
```

## CompileTimingPoints

> Whether to include debug timing points.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileTimingPoints=disabled
*/ ?>
```

## CompileResourceFallback

> Whether to fallback code for resource handling  
> Should not be needed unless one has to debug the compilation process  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileResourceFallback=disabled
*/ ?>
```

## CompileNodePlacements

> If disabled the template compilation will not include placement information for nodes  
> The placement information is used to display line and column numbers for warnings and errors.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileNodePlacements=enabled
*/ ?>
```

## CompileExecution

> If disabled the template compilation files will only be generated but  
> not executed, this means that the normal template process is used for the result.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileExecution=enabled
*/ ?>
```

## CompileAlwaysGenerate

> If enabled the templates are always compiled even if a  
> sufficient cache already exists.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileAlwaysGenerate=disabled
*/ ?>
```

## CompileIncludeNodeTree

> An array with node trees to include in the compiled template file.  
> Including them will increase the size of the cache files and  
> is only useful for template compilation development.  
> Possible entries are:  
> transformed - The tree after node transformation has been done  
> static      - The tree after static extraction has been done  
> combined    - The tree after static element combines has been done  
> original    - The originally parsed tree  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# an empty "CompileIncludeNodeTree[]" first replaces the shipped list; without it the items are added
CompileIncludeNodeTree[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
