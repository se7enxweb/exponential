---
title: Muster in kernel/classes/packageinstallers
audience: developer
category: standards
order: 508
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/packageinstallers mit den Entwurfsmustern.
keywords: kernel/classes/packageinstallers, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/packageinstallers

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/packageinstallers` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZContentObjectPackageInstaller` | class extends eZPackageInstallationHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/packageinstallers/ezcontentobject/ezcontentobjectpackageinstaller.php:18` | – |
| `eZInstallScriptPackageInstaller` | class extends eZPackageInstallationHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/packageinstallers/ezinstallscript/ezinstallscriptpackageinstaller.php:17` | – |
