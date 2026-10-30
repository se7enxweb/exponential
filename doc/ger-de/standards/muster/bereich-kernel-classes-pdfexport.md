---
title: Muster in kernel/classes/pdfexport
audience: developer
category: standards
order: 510
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/pdfexport mit den Entwurfsmustern.
keywords: kernel/classes/pdfexport, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/pdfexport

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/pdfexport` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expPDFExportFile` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/pdfexport/exppdfexportfile.php:19` | Die Datei eines PDF-Exports, die einmal erzeugt wird: ihr Name, wo sie aufbewahrt wird, was über sie bekannt ist, ihr Versand und ihr Entfernen. |
| `expPDFExportForm` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/pdfexport/exppdfexportform.php:19` | Liest und prüft das Formular von pdf/edit. |
| `expPDFExportGenerator` | class | [Builder](builder.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Erzeugt das PDF eines Exports: in seine gespeicherte Datei oder in eine temporäre Datei, die dann gesendet und entfernt wird. |
| `expPDFExportInfo` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/pdfexport/exppdfexportinfo.php:19` | Was die Seiten des PDF-Exports (pdf/list und ihre Löschbestätigung) über einen Export über seine Zeile hinaus sagen: wie er erzeugt wird, ob sein Quellknoten noc… |
