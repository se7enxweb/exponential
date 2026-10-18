---
title: admininterface.ini [ContentEditAttributeSettings]
audience: developer, admin
category: examples
summary: "The settings of [ContentEditAttributeSettings] in admininterface.ini: DefaultKind, KindName, KindIcon, DatatypeKind, CompactDatatypes, FieldNavigationFrom"
keywords: admininterface.ini, ContentEditAttributeSettings, INI, setting, DefaultKind, KindName, KindIcon, DatatypeKind, CompactDatatypes, FieldNavigationFrom
type: generated
generated: exp:help:examples
---
# admininterface.ini [ContentEditAttributeSettings]

The block `[ContentEditAttributeSettings]` of `settings/admininterface.ini`, with 6 setting(s).

Never edit `settings/admininterface.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/admininterface.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in an extension (read when the extension is active).

## DefaultKind

> How content/edit (also while translating), multi-edit and user/register  
> draw an attribute in admin4 (design:content/edit\_attribute.tpl), by its  
> datatype: each attribute is a card whose corner names the kind of input (an  
> icon and a word), and a short one shares its row with a short neighbour.  
>
> A datatype of an extension joins in with the extension's own  
> settings/admininterface.ini.append.php: map it to a kind below or to a kind  
> of its own (KindName and KindIcon), and name it in CompactDatatypes if it is  
> a short input. A datatype nobody maps takes DefaultKind.  
>
> KindIcon is a Bootstrap Icons name, drawn as .a4-bi-<name>: components.css  
> of admin4 defines the classes it ships; an extension that names another  
> icon brings the class with it. KindName is translated in the context  
> design/admin/content/edit\_attribute.  

Default: `other`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
DefaultKind=other
*/ ?>
```

## KindName

Default: a hash of 28: `text=Text`, `richtext=Rich text`, `number=Number`, `switch=Yes/no`, `choice=Choice`, `date=Date`, `time=Time`, `link=Link`, `email=E-mail`, `relation=Relation`, `image=Image`, `file=File` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# an empty "KindName[]" first replaces the shipped list; without it the items are added
KindName[]
KindName[text]=Text
KindName[richtext]=Rich text
KindName[number]=Number
KindName[switch]=Yes/no
KindName[choice]=Choice
KindName[date]=Date
KindName[time]=Time
KindName[link]=Link
KindName[email]=E-mail
KindName[relation]=Relation
KindName[image]=Image
KindName[file]=File
KindName[media]=Media
KindName[tags]=Tags
KindName[people]=People
KindName[account]=User account
KindName[location]=Location
KindName[rating]=Rating
KindName[metadata]=Metadata
KindName[shop]=Shop
# ... 8 more in the shipped file
*/ ?>
```

## KindIcon

Default: a hash of 28: `text=input-cursor-text`, `richtext=text-paragraph`, `number=123`, `switch=toggles`, `choice=ui-radios`, `date=calendar3`, `time=clock`, `link=link-45deg`, `email=envelope`, `relation=diagram-3`, `image=images`, `file=file-earmark` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# an empty "KindIcon[]" first replaces the shipped list; without it the items are added
KindIcon[]
KindIcon[text]=input-cursor-text
KindIcon[richtext]=text-paragraph
KindIcon[number]=123
KindIcon[switch]=toggles
KindIcon[choice]=ui-radios
KindIcon[date]=calendar3
KindIcon[time]=clock
KindIcon[link]=link-45deg
KindIcon[email]=envelope
KindIcon[relation]=diagram-3
KindIcon[image]=images
KindIcon[file]=file-earmark
KindIcon[media]=film
KindIcon[tags]=tags
KindIcon[people]=people
KindIcon[account]=person-lock
KindIcon[location]=geo-alt
KindIcon[rating]=star
KindIcon[metadata]=info-circle
KindIcon[shop]=tag
# ... 8 more in the shipped file
*/ ?>
```

## DatatypeKind

> The datatypes of the kernel and of the extensions composer.json requires  

Default: a hash of 54: `ezstring=text`, `eztext=text`, `ezidentifier=text`, `ezisbn=text`, `ezxmltext=richtext`, `ezpage=richtext`, `ezinteger=number`, `ezfloat=number`, `ezboolean=switch`, `ezselection=choice`, `sckenhancedselection=choice`, `ezenum=choice` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# an empty "DatatypeKind[]" first replaces the shipped list; without it the items are added
DatatypeKind[]
DatatypeKind[ezstring]=text
DatatypeKind[eztext]=text
DatatypeKind[ezidentifier]=text
DatatypeKind[ezisbn]=text
DatatypeKind[ezxmltext]=richtext
DatatypeKind[ezpage]=richtext
DatatypeKind[ezinteger]=number
DatatypeKind[ezfloat]=number
DatatypeKind[ezboolean]=switch
DatatypeKind[ezselection]=choice
DatatypeKind[sckenhancedselection]=choice
DatatypeKind[ezenum]=choice
DatatypeKind[ezcountry]=choice
DatatypeKind[ngclasslist]=choice
DatatypeKind[ezdate]=date
DatatypeKind[ezdatetime]=date
DatatypeKind[ezbirthday]=date
DatatypeKind[eztime]=time
DatatypeKind[ezurl]=link
DatatypeKind[ngenhancedlink]=link
# ... 34 more in the shipped file
*/ ?>
```

## CompactDatatypes

> Short inputs: two of them next to each other share a row (a wide window, not  
> a translation, where the original stands beside each field)  

Default: a list of 15: `ezboolean`, `ezinteger`, `ezfloat`, `ezdate`, `ezdatetime`, `eztime`, `ezbirthday`, `ezemail`, `ezisbn`, `ezidentifier`, `ezcountry`, `ezselection` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# an empty "CompactDatatypes[]" first replaces the shipped list; without it the items are added
CompactDatatypes[]
CompactDatatypes[]=ezboolean
CompactDatatypes[]=ezinteger
CompactDatatypes[]=ezfloat
CompactDatatypes[]=ezdate
CompactDatatypes[]=ezdatetime
CompactDatatypes[]=eztime
CompactDatatypes[]=ezbirthday
CompactDatatypes[]=ezemail
CompactDatatypes[]=ezisbn
CompactDatatypes[]=ezidentifier
CompactDatatypes[]=ezcountry
CompactDatatypes[]=ezselection
CompactDatatypes[]=sckenhancedselection
CompactDatatypes[]=ezprice
CompactDatatypes[]=ezsrrating
*/ ?>
```

## FieldNavigationFrom

> The list of the fields above the form, each a link to its card: shown from  
> this many attributes on (0: never)  

Default: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
FieldNavigationFrom=6
*/ ?>
```

## Related

- [All blocks of admininterface.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/admininterface.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
