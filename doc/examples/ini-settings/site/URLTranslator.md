---
title: site.ini [URLTranslator]
audience: developer, admin
category: examples
summary: "The settings of [URLTranslator] in site.ini: Translation, TranslatableSystemUrls, LoadOnPartialAliasPath, WordSeparator, TransformationGroup, UrlAliasNameLimit, DecodeOnce, TrailingCleanup ..."
keywords: site.ini, URLTranslator, INI, setting, Translation, TranslatableSystemUrls, LoadOnPartialAliasPath, WordSeparator, TransformationGroup, UrlAliasNameLimit, DecodeOnce, TrailingCleanup, WildcardTranslation, MaximumWildcardIterations, NodeTranslation, FilterClasses
type: generated
generated: exp:help:examples
---
# site.ini [URLTranslator]

The block `[URLTranslator]` of `settings/site.ini`, with 12 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## Translation

> Controls whether the url translation is enabled or not.  
> With url translation enabled you can enter the path of content object  
> in the url like my/nice/path and get the content object.  
> Also the url translation allows for custom translations of urls  
> Use either enabled or disabled  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
Translation=enabled
*/ ?>
```

## TranslatableSystemUrls

> Controls if custom url translations should be disabled for system urls where  
> first part of url is defined in module.ini Modulelist, eg. 'content'  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
TranslatableSystemUrls=enabled
*/ ?>
```

## LoadOnPartialAliasPath

> Path to load when accessing a partial alias  
> For example, when there is a redirect from /path/one/two but the user accesses /path/one  
> By default this loads the homepage, but you can set it to load a 404 page for example  

Default: `/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
LoadOnPartialAliasPath=/
*/ ?>
```

## WordSeparator

> Type of word separator for url aliases, can be one of:  
> dash - Use a dash  
> underscore - Use an underscore  
> space - Use a space  
> Any other value returns in a dash  

Default: `dash`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
WordSeparator=dash
*/ ?>
```

## TransformationGroup

Default: `urlalias`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
TransformationGroup=urlalias
*/ ?>
```

## UrlAliasNameLimit

> Uncomment this to get the new-style url aliases with Unicode support  
> TransformationGroup=urlalias\_iri  
> Uncomment this to get the old-style url aliases  
> TransformationGroup=urlalias\_compat  
> Length limit on urlAliasName, the limit pr path item in the url.  
> Set to 0 to disable.  

Default: `255`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
UrlAliasNameLimit=255
*/ ?>
```

## DecodeOnce

> Decode the request URL once (enabled) instead of twice (disabled, the shipped value).  
> The address is URL decoded when the request starts, and eZURI decodes it again, so an  
> encoded "+" in a path, as in /(foo)/bar%2Bbar, reaches a view as "bar bar" instead of  
> "bar+bar". With enabled it arrives as "bar+bar", and an eZURI made from a string takes  
> that string as decoded text (PHP code with an encoded URL passes $decode = true to  
> new eZURI()). Addresses with an encoded "%" or "+" in a path element or a view  
> parameter then reach the view differently, so check them before you switch it on.  
> Set it in settings/override/site.ini.append.php: the address is read before the  
> siteaccess is known.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
DecodeOnce=disabled
*/ ?>
```

## TrailingCleanup

> In the IRI transformation (TransformationGroup=urlalias\_iri), also treat the double  
> quote as a reserved character (enabled), so it becomes a word separator and is  
> stripped at the beginning and end of an alias like "!", dots and separators already  
> are. disabled (the shipped value) keeps today's aliases. Existing aliases stay as  
> they are until their object is published again.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
TrailingCleanup=disabled
*/ ?>
```

## WildcardTranslation

> Controls whether wildcard translation is to be used or not  
> If enabled wilcard translations will be used if a normal  
> translation is not available and it is also used to remember  
> old sub trees. It's recommended to have this enabled  
> unless you have problems with it.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
WildcardTranslation=enabled
*/ ?>
```

## MaximumWildcardIterations

> Controls how many times the wildcard matches can iterate.  
> The wildcard matcher system will iterate if the translated  
> url is not a complete url, this allows urls lookup to be  
> recursive and is required for proper subtree history.  

Default: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
MaximumWildcardIterations=20
*/ ?>
```

## NodeTranslation

> OBSOLETE: Use Translation setting instead  
> Enable this if you want to percieve the url as a path element  
> for the node tree. By entering the path in the url and ending it  
> with a .html you will get the correct node.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
NodeTranslation=enabled
*/ ?>
```

## FilterClasses

> List of classes you want to call as an eZURLALiasFilter  

Default: a list of 1: `eZURLAliasFilterAppendNodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
# an empty "FilterClasses[]" first replaces the shipped list; without it the items are added
FilterClasses[]
FilterClasses[]=eZURLAliasFilterAppendNodeID
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
