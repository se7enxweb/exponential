---
title: site.ini [URLTranslator]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [URLTranslator] in site.ini: Translation, TranslatableSystemUrls, LoadOnPartialAliasPath, WordSeparator, TransformationGroup, UrlAliasNameLimit, DecodeOnce, TrailingCleanup ..."
keywords: Einstellung, site.ini, URLTranslator, INI, setting, Translation, TranslatableSystemUrls, LoadOnPartialAliasPath, WordSeparator, TransformationGroup, UrlAliasNameLimit, DecodeOnce, TrailingCleanup, WildcardTranslation, MaximumWildcardIterations, NodeTranslation, FilterClasses
type: generated
generated: exp:help:examples
---
# site.ini [URLTranslator]

Der Block `[URLTranslator]` von `settings/site.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Translation

> Controls whether the url translation is enabled or not.  
> With url translation enabled you can enter the path of content object  
> in the url like my/nice/path and get the content object.  
> Also the url translation allows for custom translations of urls  
> Use either enabled or disabled  

Standard: `enabled`.

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

Standard: `enabled`.

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

Standard: `/`.

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

Standard: `dash`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
WordSeparator=dash
*/ ?>
```

## TransformationGroup

Standard: `urlalias`.

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

Standard: `255`.

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

Standard: `disabled`.

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

Standard: `disabled`.

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

Standard: `enabled`.

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

Standard: `20`.

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

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
NodeTranslation=enabled
*/ ?>
```

## FilterClasses

> List of classes you want to call as an eZURLALiasFilter  

Standard: eine Liste mit 1: `eZURLAliasFilterAppendNodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
# ein leeres "FilterClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FilterClasses[]
FilterClasses[]=eZURLAliasFilterAppendNodeID
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
