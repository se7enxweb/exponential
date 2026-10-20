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

> Wie content/edit (auch beim Übersetzen), Mehrfachbearbeitung und user/register  
> ein Attribut in admin4 zeichnen (design:content/edit\_attribute.tpl), nach seinem  
> Datentyp: Jedes Attribut ist eine Karte, deren Ecke die Art der Eingabe nennt (ein  
> Symbol und ein Wort), und ein kurzes teilt seine Zeile mit einem kurzen Nachbarn.  
>
> Ein Datentyp einer Erweiterung schließt sich über die eigene  
> settings/admininterface.ini.append.php der Erweiterung an: Ordnen Sie ihn unten einer Art zu oder einer eigenen  
> Art (KindName und KindIcon), und nennen Sie ihn in CompactDatatypes, wenn er eine  
> kurze Eingabe ist. Ein Datentyp, den niemand zuordnet, erhält DefaultKind.  
>
> KindIcon ist ein Name aus Bootstrap Icons, gezeichnet als .a4-bi-<name>: components.css  
> von admin4 definiert die Klassen, die es mitliefert; eine Erweiterung, die ein anderes  
> Symbol nennt, bringt die Klasse mit. KindName wird im Kontext  
> design/admin/content/edit\_attribute übersetzt.  

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

> Die Datentypen des Kernels und der Erweiterungen, die composer.json verlangt  

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

> Kurze Eingaben: zwei davon nebeneinander teilen sich eine Zeile (in einem breiten Fenster, nicht  
> beim Übersetzen, wo das Original neben jedem Feld steht)  

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

> Die Liste der Felder über dem Formular, jedes ein Link auf seine Karte: angezeigt ab  
> so vielen Attributen (0: nie)  

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
