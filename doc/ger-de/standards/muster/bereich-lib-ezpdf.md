---
title: Muster in lib/ezpdf
audience: developer
category: standards
order: 549
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezpdf mit den Entwurfsmustern.
keywords: lib/ezpdf, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezpdf

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezpdf` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Cezpdf` | class extends Cpdf | – | `lib/ezpdf/classes/class.ezpdf.php:18` | – |
| `Cpdf` | class | – | `lib/ezpdf/classes/class.pdf.php:18` | – |
| `eZPDF` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/ezpdf/classes/ezpdf.php:22` | – |
| `eZPDFTable` | class extends Cezpdf | – | `lib/ezpdf/classes/class.ezpdftable.php:18` | – |
| `eZPDFTrueTypeFont` | class | – | `lib/ezpdf/classes/eztruetypefont.php:24` | Liest eine TrueType-Schrift gut genug, um sie als zusammengesetzte Schrift in ein PDF einzubetten: die Zuordnung von Zeichen zu Glyphen, die Vorschubbreiten, den Deskriptor, den das PD… |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| pdf | `eZPDF` | `lib/ezpdf/classes/eztemplateautoload.php:14` |
