---
title: Muster in kernel/private/classes/runnable
audience: developer
category: standards
order: 522
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/runnable mit den Entwurfsmustern.
keywords: kernel/private/classes/runnable, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/runnable

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/runnable` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Runnable\Command` | abstract class extends Runnable | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/classes/runnable/command.php:36` | Ein CLI-Befehl. |
| `Exponential\Runnable\CronjobPart` | abstract class extends Runnable | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/classes/runnable/cronjobpart.php:21` | Ein Cronjob-Teil. |
| `Exponential\Runnable\ModuleView` | abstract class extends Runnable | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/classes/runnable/moduleview.php:23` | Eine Modulansicht. |
| `Exponential\Runnable\Runnable` | abstract class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/classes/runnable/runnable.php:30` | Die Basis von allem, was Exponential aus einer Datei ausführt: CLI-Befehle (bin/), Cronjob-Teile (cronjobs/) und Modulansichten (kernel//.php, extension//module… |
