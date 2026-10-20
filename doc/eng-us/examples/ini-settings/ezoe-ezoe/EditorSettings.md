---
title: ezoe.ini [EditorSettings]
audience: developer, admin
category: examples
summary: "The settings of [EditorSettings] in ezoe.ini (ezoe): EditorEngine, Engines, UploadExtensionCheck, UploadFromUrl, UploadFromUrlMaxSize, UploadFromUrlTimeout, UploadFileExtensions, Plugins ..."
keywords: ezoe.ini, EditorSettings, INI, setting, EditorEngine, Engines, UploadExtensionCheck, UploadFromUrl, UploadFromUrlMaxSize, UploadFromUrlTimeout, UploadFileExtensions, Plugins, CustomTagControls, Skin, CompatibilityMode, ValidateEmbedObjects, TagPathOpenDialog, BrowseImageAlias, CustomAttributeStyleMap, XmlTagNameAlias
type: generated
generated: exp:help:examples
---
# ezoe.ini [EditorSettings]

The block `[EditorSettings]` of `extension/ezoe/settings/ezoe.ini` (extension ezoe), with 16 setting(s).

Never edit `extension/ezoe/settings/ezoe.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/ezoe.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in an extension (read when the extension is active).

## EditorEngine

> Editor engine used for ezxmltext attributes  
>  tinymce3 = eZ Online Editor on TinyMCE 3.5 (default)  
>  tinymce8 = prototype on TinyMCE 8, supports formatting, lists, links, anchors,  
>             tables, embed/embed-inline, custom tags and literal  
> Can be overridden per user with the preference ezoe\_engine:  
>  /user/preferences/set/ezoe\_engine/tinymce8  

Default: `tinymce3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
EditorEngine=tinymce3
*/ ?>
```

## Engines

> Show a button below the editor to switch between tinymce3 and tinymce8 (saved as user preference),  
> useful while both editors run in parallel. enabled / disabled; off when not set.  
> Not set here on purpose: this file is read at the extension level, after every siteaccess setting and every  
> extension activated for one siteaccess only (ActiveAccessExtensions\[\]), so a value here would override theirs.  
> EngineSwitch=disabled  
> Force one engine for every editor of a siteaccess, whatever their preference ezoe\_engine says (the engine switch is  
> then hidden and the engine page shows the choice as fixed). Set it per siteaccess, e.g. ForceEngine=tiptap. An engine  
> that cannot be used (missing files, inactive extension) is ignored, so editors fall back to the usual choice.  
> Not set here on purpose, for the same reason as EditorEngine and EngineSwitch.  
> ForceEngine=  
> The editor engines: Engines\[<identifier>\]=<class implementing expOEEditorEngine>. An extension adds an  
> engine with one class and one line in its own ezoe.ini.append.php, EditorEngine / the user preference then  
> name it. An entry whose class is missing or is not an engine is ignored (and shown in setup/rad).  
> Guide: doc/en/us/bc/6.0/ezoe-tinymce8.md  
> No empty Engines\[\] before the list: read at the extension level, it would remove the engines of an extension  
> activated for one siteaccess only (ActiveAccessExtensions\[\]) and of siteaccess settings.  

Default: a hash of 2: `tinymce3=expOETinyMCE3Engine`, `tinymce8=expOETinyMCE8Engine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# an empty "Engines[]" first replaces the shipped list; without it the items are added
Engines[]
Engines[tinymce3]=expOETinyMCE3Engine
Engines[tinymce8]=expOETinyMCE8Engine
*/ ?>
```

## UploadExtensionCheck

> Server-side check of the uploaded file's type against UploadFileExtensions\[\] (ezoe/upload):  
>  engine   = when the user's engine is not tinymce3 (default; the TinyMCE 3 dialog never limited types)  
>  always   = every upload of the online editor  
>  disabled = none  
> In every mode a name with an executable type anywhere in it (php, phtml, phar, sh, cgi, .htaccess, ...) is refused.  

Default: `engine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadExtensionCheck=engine
*/ ?>
```

## UploadFromUrl

> Upload from a URL on the Upload tab of the embed dialog (both editors): the server fetches a file from  
> an http(s) address and creates the object like a browser upload. enabled / disabled  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadFromUrl=enabled
*/ ?>
```

## UploadFromUrlMaxSize

> Largest file fetched from a URL (K, M, G). Only this applies, PHP's upload\_max\_filesize does not (a fetch  
> is not a browser upload); the body is streamed to a temporary file, never held in memory  

Default: `145M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadFromUrlMaxSize=145M
*/ ?>
```

## UploadFromUrlTimeout

> Total time in seconds a fetch may take (the connection must be made within 5 seconds)  

Default: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadFromUrlTimeout=300
*/ ?>
```

## UploadFileExtensions

> File extensions the upload tab of the TinyMCE 8 embed dialog accepts, the class of the new  
> object is chosen by upload.ini like in the TinyMCE 3 upload dialog  

Default: a list of 30: `jpg`, `jpeg`, `png`, `gif`, `webp`, `svg`, `bmp`, `tif`, `tiff`, `pdf`, `doc`, `docx` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# an empty "UploadFileExtensions[]" first replaces the shipped list; without it the items are added
UploadFileExtensions[]
UploadFileExtensions[]=jpg
UploadFileExtensions[]=jpeg
UploadFileExtensions[]=png
UploadFileExtensions[]=gif
UploadFileExtensions[]=webp
UploadFileExtensions[]=svg
UploadFileExtensions[]=bmp
UploadFileExtensions[]=tif
UploadFileExtensions[]=tiff
UploadFileExtensions[]=pdf
UploadFileExtensions[]=doc
UploadFileExtensions[]=docx
UploadFileExtensions[]=xls
UploadFileExtensions[]=xlsx
UploadFileExtensions[]=ppt
UploadFileExtensions[]=pptx
UploadFileExtensions[]=odt
UploadFileExtensions[]=ods
UploadFileExtensions[]=odp
UploadFileExtensions[]=rtf
# ... 10 more in the shipped file
*/ ?>
```

## Plugins

> TinyMCE plugins you want to use  
> plugins not mentioned here aren't tested and therefore not supported together with Online Editor  
> Plugins\[\]=insertdatetime  
> Plugins\[\]=visualchars  
> Support for button to insert nonbreaking white space  
> Plugins\[\]=nonbreaking  
> Spellchecker Requirements:  
> http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker  
> Plugins\[\]=spellchecker  
> Plugins\[\]=searchreplace  
> eZ variant of emotions plugin  
> Plugins\[\]=ezemotions  
> Provides two buttons for text direction: ltr and rtl  
> Plugins\[\]=directionality  
> autolink plugin  
> list plugin  
> Support CTRL+Enter to exit a custom tag  

Default: a list of 7: `eztable`, `paste`, `fullscreen`, `inlinepopups`, `autolink`, `lists`, `breakoutspace`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# an empty "Plugins[]" first replaces the shipped list; without it the items are added
Plugins[]
Plugins[]=eztable
Plugins[]=paste
Plugins[]=fullscreen
Plugins[]=inlinepopups
Plugins[]=autolink
Plugins[]=lists
Plugins[]=breakoutspace
*/ ?>
```

## CustomTagControls

> Custom tag controls (TinyMCE 3, TinyMCE 8 and Tiptap): a focused custom tag gets buttons to move it up or  
> down, to edit and to remove it (Alt+Shift+Up / Down / E / Delete; Backspace removes an empty block custom tag),  
> and every block custom tag shows its name as a label. enabled / disabled. New installations write  
> CustomTagControls=enabled; an upgraded site keeps disabled until it is switched on here or in an override.  
> TinyMCE 3 loads the plugin exp\_customtag\_controls for it, so it needs no entry in Plugins\[\].  
> (From LoveStack, commit 63334f1681, with thanks to Mugo Web.)  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
CustomTagControls=disabled
*/ ?>
```

## Skin

> Skin for the editor, 'default' and 'o2k7' is included as standard  

Default: `o2k7`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
Skin=o2k7
*/ ?>
```

## CompatibilityMode

> Included skin variants in the o2k7 skin is: 'silver' and 'black'  
> SkinVariant=  
> This option specifies the default writing direction. Some languages (Like Hebrew, Arabic, Urdu...)  
> write from right to left instead of left to right. The default value of this option is "ltr" but if  
> you want to use from right to left mode specify "rtl" instead.  
> Directionality=ltr  
> Lets you control alignment of toolbar buttons \[left|right\]  
> ToolbarAlign=left  
> Enables compatibility mode by making editor behave more like OE 4.x  
> - uses attachment icon for embed relations, makes it easier to place embed objects inside text  
>   but looses preview functionality. (name of embed object is shown if you hover the icon though)  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
CompatibilityMode=disabled
*/ ?>
```

## ValidateEmbedObjects

> Validates embed objects and gives a warning if it's in trash  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
ValidateEmbedObjects=disabled
*/ ?>
```

## TagPathOpenDialog

> Makes it possible to get default TinyMCE behavior when clicking tag name by disabling  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
TagPathOpenDialog=enabled
*/ ?>
```

## BrowseImageAlias

> Image alias to use for the preview while browsing  

Default: `small`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
BrowseImageAlias=small
*/ ?>
```

## CustomAttributeStyleMap

> Map custom attribute names to style values so they are previewed in the editor.  
> Supported styles are limited to the following types:  
>     border\* (so border or border-color will work)  
>     margin\*  
>     padding\*  
>     width  
>     height  

Default: a hash of 8: `width=width`, `height=height`, `margin=margin`, `padding=padding`, `border=border`, `border_color=border-color`, `border_size=border-width`, `border_style=border-style`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# an empty "CustomAttributeStyleMap[]" first replaces the shipped list; without it the items are added
CustomAttributeStyleMap[]
CustomAttributeStyleMap[width]=width
CustomAttributeStyleMap[height]=height
CustomAttributeStyleMap[margin]=margin
CustomAttributeStyleMap[padding]=padding
CustomAttributeStyleMap[border]=border
CustomAttributeStyleMap[border_color]=border-color
CustomAttributeStyleMap[border_size]=border-width
CustomAttributeStyleMap[border_style]=border-style
*/ ?>
```

## XmlTagNameAlias

> Friendly name for tags, is shown in status bar and dialogs  

Default: a hash of 6: `th=table header`, `td=table cell`, `tr=table row`, `ul=unordered list`, `ol=ordered list`, `li=list item`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# an empty "XmlTagNameAlias[]" first replaces the shipped list; without it the items are added
XmlTagNameAlias[]
XmlTagNameAlias[th]=table header
XmlTagNameAlias[td]=table cell
XmlTagNameAlias[tr]=table row
XmlTagNameAlias[ul]=unordered list
XmlTagNameAlias[ol]=ordered list
XmlTagNameAlias[li]=list item
*/ ?>
```

## Related

- [All blocks of ezoe.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `extension/ezoe/settings/ezoe.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
