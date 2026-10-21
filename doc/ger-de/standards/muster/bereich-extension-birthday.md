---
title: Muster in extension/birthday
audience: developer
category: standards
order: 405
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/birthday mit den Entwurfsmustern.
keywords: extension/birthday, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/birthday

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/birthday` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `birthdayInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/birthday/ezinfo.php:6` | Die Informationen der Extension für die Seite „Über“ und die Upgrade-Prüfungen. |
| `eZBirthday` | class | – | `extension/birthday/datatypes/ezbirthday/ezbirthday.php:3` | – |
| `eZBirthdayCsvHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/birthday/classes/parsers/ezbirthdaycsvhandler.php:29` | CSV-Export-Handler für den Datentyp ezbirthday. |
| `eZBirthdayType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/birthday/datatypes/ezbirthday/ezbirthdaytype.php:3` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezbirthday] HandlerClass` | `eZBirthdayCsvHandler` | `extension/birthday/settings/csv.ini.append.php:19` |
