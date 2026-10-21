---
title: Muster in kernel/private/classes/asynchronouspublisheroutput
audience: developer
category: standards
order: 515
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/asynchronouspublisheroutput mit den Entwurfsmustern.
keywords: kernel/private/classes/asynchronouspublisheroutput, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/asynchronouspublisheroutput

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/asynchronouspublisheroutput` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezpAsynchronousPublisherCliOutput` | class implements ezpAsynchronousPublisherOutput | – | `kernel/private/classes/asynchronouspublisheroutput/cli.php:16` | Behandelt die Ausgabe der asynchronen Veröffentlichung auf der CLI |
| `ezpAsynchronousPublisherLogOutput` | class implements ezpAsynchronousPublisherOutput | – | `kernel/private/classes/asynchronouspublisheroutput/log.php:16` | Behandelt die Ausgabe der asynchronen Veröffentlichung nach var/log/async.log |
