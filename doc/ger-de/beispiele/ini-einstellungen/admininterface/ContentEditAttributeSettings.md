---
title: admininterface.ini [ContentEditAttributeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentEditAttributeSettings] in admininterface.ini: DefaultKind, KindName, KindIcon, DatatypeKind, CompactDatatypes, FieldNavigationFrom"
keywords: Einstellung, admininterface.ini, ContentEditAttributeSettings, INI, setting, DefaultKind, KindName, KindIcon, DatatypeKind, CompactDatatypes, FieldNavigationFrom
type: generated
generated: exp:help:examples
---
# admininterface.ini [ContentEditAttributeSettings]

Der Block `[ContentEditAttributeSettings]` von `settings/admininterface.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

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

Standard: `other`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
DefaultKind=other
*/ ?>
```

## KindName

Standard: ein Hash mit 28: `text=Text`, `richtext=Rich text`, `number=Number`, `switch=Yes/no`, `choice=Choice`, `date=Date`, `time=Time`, `link=Link`, `email=E-mail`, `relation=Relation`, `image=Image`, `file=File` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# ein leeres "KindName[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
# ... 8 weitere in der ausgelieferten Datei
*/ ?>
```

## KindIcon

Standard: ein Hash mit 28: `text=input-cursor-text`, `richtext=text-paragraph`, `number=123`, `switch=toggles`, `choice=ui-radios`, `date=calendar3`, `time=clock`, `link=link-45deg`, `email=envelope`, `relation=diagram-3`, `image=images`, `file=file-earmark` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# ein leeres "KindIcon[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
# ... 8 weitere in der ausgelieferten Datei
*/ ?>
```

## DatatypeKind

> The datatypes of the kernel and of the extensions composer.json requires  

Standard: ein Hash mit 54: `ezstring=text`, `eztext=text`, `ezidentifier=text`, `ezisbn=text`, `ezxmltext=richtext`, `ezpage=richtext`, `ezinteger=number`, `ezfloat=number`, `ezboolean=switch`, `ezselection=choice`, `sckenhancedselection=choice`, `ezenum=choice` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# ein leeres "DatatypeKind[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
# ... 34 weitere in der ausgelieferten Datei
*/ ?>
```

## CompactDatatypes

> Short inputs: two of them next to each other share a row (a wide window, not  
> a translation, where the original stands beside each field)  

Standard: eine Liste mit 15: `ezboolean`, `ezinteger`, `ezfloat`, `ezdate`, `ezdatetime`, `eztime`, `ezbirthday`, `ezemail`, `ezisbn`, `ezidentifier`, `ezcountry`, `ezselection` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
# ein leeres "CompactDatatypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditAttributeSettings]
FieldNavigationFrom=6
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
