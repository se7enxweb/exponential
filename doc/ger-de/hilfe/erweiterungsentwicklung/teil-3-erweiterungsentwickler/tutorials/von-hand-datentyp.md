---
title: "Von Hand: ein Datentyp"
audience: developer, app-creator
category: extension-development
order: 150
summary: Den Bewertungs-Datentyp von Hand hinzufügen - die vier Dateien und zwei Einstellungen in der Reihenfolge des Aufbaus, eine Prüfung nach jedem Schritt und ein Testplan im Klasseneditor.
keywords: Tutorial, von Hand, Datentyp, Bewertung, Bearbeitungs-Template, Ansichts-Template, Klasseneditor, Testplan, tutorial, by hand, datatype, rating, eZDataType, content.ini, AvailableDataTypes, ExtensionDirectories, edit template, view template, class editor, test plan
type: help
---
# Von Hand: ein Datentyp

Tutorial 14. Sie fügen `mysite_hello` ein Bewertungsfeld (1 bis 5) hinzu. Die Klasse ist dieselbe, die in
[Speicherung, Validierung und Templates von Datentypen](../datentyp-speicherung-validierung-und-templates.md)
Zeile für Zeile erklärt wird; dieses Tutorial ist die Reihenfolge des Aufbaus, mit einer Prüfung nach jedem Schritt. Die
Klasse hat `php -l` bestanden, wurde mit `eZDataType::create()` erzeugt und mit geladenem Kernel auf einem Attribut im
Speicher ausgeführt; die Templates wurden von der Template-Engine gerendert.

## 1. Die Dateien

```text
extension/mysite_hello/
    datatypes/mysiterating/mysiteratingtype.php
    design/standard/templates/content/datatype/edit/mysiterating.tpl
    design/standard/templates/content/datatype/view/mysiterating.tpl
    settings/content.ini.append.php
    settings/design.ini.append.php        (already there from tutorial 12)
```

## 2. Die Klasse

```bash
mkdir -p extension/mysite_hello/datatypes/mysiterating
```

Kopieren Sie die vollständige Klasse `mysiteRatingType` aus
[Speicherung, Validierung und Templates von Datentypen](../../../../../eng-us/help/extension-development/part-3-extension-developer/datatype-storage-validation-and-templates.md#the-class)
nach `datatypes/mysiterating/mysiteratingtype.php`. Der Dateiname, der Ordner und die Zeichenkette `mysiterating`
müssen übereinstimmen: Der Kernel bindet `datatypes/<type>/<type>type.php` ein.

Prüfen:

```bash
php -l extension/mysite_hello/datatypes/mysiterating/mysiteratingtype.php
```

## 3. Die Registrierung

`extension/mysite_hello/settings/content.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[DataTypeSettings]
ExtensionDirectories[]=mysite_hello
AvailableDataTypes[]=mysiterating

*/ ?>
```

Prüfen, nachdem Sie den INI-Cache geleert haben:

```bash
php bin/php/ezcache.php --clear-tag=ini
./console exp:ini where content.ini/DataTypeSettings/AvailableDataTypes
```

Ihre Datei steht in der Kette, und `mysiterating` steht in der Liste.

## 4. Die Templates

Kopieren Sie die Bearbeitungs- und Ansichts-Templates aus demselben Kapitel nach
`design/standard/templates/content/datatype/edit/mysiterating.tpl` und `.../view/mysiterating.tpl`, dann:

```bash
php bin/php/ezcache.php --clear-id=template-override,design_base
```

## 5. Verwenden

1. **Setup > Klassen**: Öffnen Sie eine Testklasse, fügen Sie ein Attribut „Rating (1 to 5)“ mit dem Bezeichner
   `rating` hinzu und speichern Sie die Klasse.
2. Legen Sie ein Objekt dieser Klasse an.

## 6. Testplan

| Eingabe | Erwartet |
|---|---|
| nichts, Attribut nicht erforderlich | gespeichert, in der Ansicht wird nichts angezeigt |
| nichts, Attribut erforderlich | „Choose a rating.“ |
| `0`, `6`, `abc` | „A rating is a number from 1 to 5.“ |
| `4` | gespeichert; die Ansicht zeigt `4 / 5` |
| eine neue Version des Objekts | der Entwurf beginnt mit dem alten Wert (`initializeObjectAttribute()`) |
| eine nach dem Attribut sortierte Liste | nach Bewertung geordnet (`sortKey()`, `sortKeyType()`) |
| eine Suche nach `4` | das Objekt wird gefunden, sofern die Suchmaschine es indiziert (`metaData()`) |

## 7. Committen

```bash
git -C extension/mysite_hello add datatypes design settings
git -C extension/mysite_hello commit -m "Added: The datatype mysiterating, a rating from 1 to 5"
```

## Verwandte Seiten

- Zurück: [Von Hand: ein Modul, das durch eine Richtlinie geschützt ist](von-hand-modul-mit-richtlinie.md)
- Weiter: [Von Hand: ein Workflow-Ereignis](von-hand-workflow-ereignis.md)
- [Datentypen](../datentypen.md), [Mit RAD: ein Datentyp](rad-datentyp.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
