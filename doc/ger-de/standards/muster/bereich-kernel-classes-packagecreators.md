---
title: Muster in kernel/classes/packagecreators
audience: developer
category: standards
order: 499
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/packagecreators mit den Entwurfsmustern.
keywords: kernel/classes/packagecreators, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/packagecreators

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/packagecreators` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZContentClassPackageCreator` | class extends eZPackageCreationHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/packagecreators/ezcontentclass/ezcontentclasspackagecreator.php:19` | – |
| `eZContentObjectPackageCreator` | class extends eZPackageCreationHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/packagecreators/ezcontentobject/ezcontentobjectpackagecreator.php:18` | – |
| `eZExtensionPackageCreator` | class extends eZPackageCreationHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php:19` | – |
| `eZStylePackageCreator` | class extends eZPackageCreationHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/packagecreators/ezstyle/ezstylepackagecreator.php:21` | – |
