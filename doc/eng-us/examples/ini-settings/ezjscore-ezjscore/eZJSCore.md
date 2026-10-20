---
title: ezjscore.ini [eZJSCore]
audience: developer, admin
category: examples
summary: "The settings of [eZJSCore] in ezjscore.ini (ezjscore): JavaScriptOptimizer, CssOptimizer, LoadFromCDN, PreferredLibrary, ExternalScripts, LocalScripts"
keywords: ezjscore.ini, eZJSCore, INI, setting, JavaScriptOptimizer, CssOptimizer, LoadFromCDN, PreferredLibrary, ExternalScripts, LocalScripts
type: generated
generated: exp:help:examples
---
# ezjscore.ini [eZJSCore]

The block `[eZJSCore]` of `extension/ezjscore/settings/ezjscore.ini` (extension ezjscore), with 6 setting(s).

Never edit `extension/ezjscore/settings/ezjscore.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/ezjscore.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in an extension (read when the extension is active).

## JavaScriptOptimizer

> Disables/enables js / css packer (for debugging Apache rewrite rules)  
> Normally controlled by \[TemplateSettings\]DevelopmentMode for convenience,  
> but can also be specifically controlled by this setting if set.  
> Force packer level by setting integer from 0 to 3 instead of \[dis|en\]abled  
> Packer=disabled  

Default: a list of 1: `ezjscJavascriptOptimizer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# an empty "JavaScriptOptimizer[]" first replaces the shipped list; without it the items are added
JavaScriptOptimizer[]
JavaScriptOptimizer[]=ezjscJavascriptOptimizer
*/ ?>
```

## CssOptimizer

Default: a list of 1: `ezjscCssOptimizer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# an empty "CssOptimizer[]" first replaces the shipped list; without it the items are added
CssOptimizer[]
CssOptimizer[]=ezjscCssOptimizer
*/ ?>
```

## LoadFromCDN

> # CDN Script settings (jQuery integration)  
> The following settings in this block are specific to ezjscore's  
> jQuery integration and used by the ezjsc::jquery calls.  
> You only need to change them if you:  
> - Need to load these scripts locally (intranet / not rely on internet connection)  
> - Want to add support for another js library / framework  
> - Want to update the library it self  
> You don't have to set up your js scripts with this, it's enough to just refer  
> to them directly using {ezscript\[\_require\]('somescript.js')} or  
> {ezscript\_require(array('ezjsc::jquery', 'somescript\_that\_uses\_jquery.js'))}  
> enable / disable loading the jQuery files from external servers  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
LoadFromCDN=disabled
*/ ?>
```

## PreferredLibrary

> Preferred Library (jquery)  
> This is a preference hint to extensions that use ezjscore  
> so they can optionally select to support both and select by this setting  

Default: `jquery`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
PreferredLibrary=jquery
*/ ?>
```

## ExternalScripts

> List of external JavaScript jQuery libraries with their CDN URL  
> If it starts with :// http or https will be selected based on if page is served as https or not  
> Security hardening (H-06, CWE-829/319): use https for external scripts so the  
> admin/frontend JavaScript cannot be tampered with on the network path. Prefer  
> leaving LoadFromCDN=disabled (local scripts) or adding Subresource Integrity.  
> jQuery: the same releases from the CDN as the local files below (the CDN entries used to name  
> jQuery 1.10.2 and jQuery UI 1.10.3, older than the local copies).  

Default: a hash of 3: `jquery=https://code.jquery.com/jquery-4.0.0.min.js`, `jqueryMigrate=https://code.jquery.com/jquery-migrate-4.0.2.min.js`, `jqueryUI=https://code.jquery.com/ui/1.14.2/jquery-ui.min.js`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# an empty "ExternalScripts[]" first replaces the shipped list; without it the items are added
ExternalScripts[]
ExternalScripts[jquery]=https://code.jquery.com/jquery-4.0.0.min.js
ExternalScripts[jqueryMigrate]=https://code.jquery.com/jquery-migrate-4.0.2.min.js
ExternalScripts[jqueryUI]=https://code.jquery.com/ui/1.14.2/jquery-ui.min.js
*/ ?>
```

## LocalScripts

> List of local JavaScript jQuery libraries, optionally start with "/" to signal that its in  
> root of design folder and not in /javascript sub folder  
> ezjsc::jquery: jQuery 4.0.0, followed by jQuery Migrate 4.0.2, which restores the APIs jQuery 4 removed so older  
> code keeps working. This build is quiet; to see in the browser console every old call it restores, use the  
> reporting build, jquery-migrate-4.0.2.js. Leave jqueryMigrate empty to load jQuery 4 alone. Only these releases  
> are shipped: the older jQuery 3.7.1, Migrate 3.4.1 and jQuery UI 1.10.3 files are removed.  
> ezjsc::jqueryUI: jQuery UI 1.14.2, which supports jQuery 4  

Default: a hash of 3: `jquery=jquery-4.0.0.min.js`, `jqueryMigrate=jquery-migrate-4.0.2.min.js`, `jqueryUI=jquery-ui-1.14.2.min.js`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# an empty "LocalScripts[]" first replaces the shipped list; without it the items are added
LocalScripts[]
LocalScripts[jquery]=jquery-4.0.0.min.js
LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.min.js
LocalScripts[jqueryUI]=jquery-ui-1.14.2.min.js
*/ ?>
```

## Related

- [All blocks of ezjscore.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `extension/ezjscore/settings/ezjscore.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
