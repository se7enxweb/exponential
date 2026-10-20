---
title: site.ini [TemplateSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TemplateSettings] in site.ini: AutoloadPathList, ExtensionAutoloadPath, Debug, ShowXHTMLCode, ShowMethodDebug, ShowUsedTemplates, ShowTemplatePathComments, NodeTreeCaching ..."
keywords: Einstellung, site.ini, TemplateSettings, INI, setting, AutoloadPathList, ExtensionAutoloadPath, Debug, ShowXHTMLCode, ShowMethodDebug, ShowUsedTemplates, ShowTemplatePathComments, NodeTreeCaching, TemplateCompile, ShareCompiledTemplates, SharedCompiledTemplatesDir, TemplateOptimization, UseFormatting, TemplateCache, DelayedCacheBlockCleanup, TemplateCompression, DevelopmentMode, CompileComments, CompileAccumulators, CompileTimingPoints, CompileResourceFallback, CompileNodePlacements, CompileExecution, CompileAlwaysGenerate, CompileIncludeNodeTree
type: generated
generated: exp:help:examples
---
# site.ini [TemplateSettings]

Der Block `[TemplateSettings]` von `settings/site.ini`, mit 25 Einstellung(en).

> AllowedDeletionDirs\[\]=/var/share/somedir  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AutoloadPathList

> A list of directories to look for eztemplateautoload.php files  
> These files will be used to automatically load in template functions and operators  
> You can new ones if you create custom template code.  

Standard: eine Liste mit 4: `lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`, `kernel/private/eztemplate/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "AutoloadPathList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AutoloadPathList[]
AutoloadPathList[]=lib/eztemplate/classes/
AutoloadPathList[]=kernel/common/
AutoloadPathList[]=lib/ezpdf/classes/
AutoloadPathList[]=kernel/private/eztemplate/
*/ ?>
```

## ExtensionAutoloadPath

> A list of extensions which have template autoloads.  
> Only specify the extension name, not the path.  
> The extension must contain a subdirectory called autoloads.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "ExtensionAutoloadPath[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionAutoloadPath[]
*/ ?>
```

## Debug

> Enabled to see which template files are loaded or disabled to supress debug  
> Warning: Will add debug xhtml comments to your source code, including mails!  
> Note: No debug on templates starting with <!DOCTYPE to not trigger quirks mode!  

Standard: `disabled`.

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

Standard: `enabled`.

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

Standard: `disabled`.

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

Standard: `disabled`.

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

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=disabled
*/ ?>
```

## NodeTreeCaching

> Determines whether the internal node tree should be cached, by enabling this the loading  
> and parsing of templates is significantly reduced.  

Standard: `disabled`.

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

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCompile=enabled
*/ ?>
```

## ShareCompiledTemplates

> If you share compiled templates with other site accesses  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShareCompiledTemplates=disabled
*/ ?>
```

## SharedCompiledTemplatesDir

> Where to store shared compiled templates  

Standard: leer.

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

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateOptimization=enabled
*/ ?>
```

## UseFormatting

> Controls whether to include formatting in compiled templates or not.  

Standard: `disabled`.

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

Standard: `enabled`.

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

Standard: `disabled`.

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

Standard: `disabled`.

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

Standard: `disabled`.

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

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileComments=disabled
*/ ?>
```

## CompileAccumulators

> Whether to include debug accumulators.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileAccumulators=disabled
*/ ?>
```

## CompileTimingPoints

> Whether to include debug timing points.  

Standard: `disabled`.

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

Standard: `disabled`.

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

Standard: `enabled`.

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

Standard: `enabled`.

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

Standard: `disabled`.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "CompileIncludeNodeTree[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CompileIncludeNodeTree[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
