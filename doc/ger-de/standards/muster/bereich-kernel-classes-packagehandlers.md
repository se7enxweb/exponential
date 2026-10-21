---
title: Muster in kernel/classes/packagehandlers
audience: developer
category: standards
order: 500
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/packagehandlers mit den Entwurfsmustern.
keywords: kernel/classes/packagehandlers, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/packagehandlers

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/packagehandlers` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZContentClassPackageHandler` | class extends eZPackageHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php:18` | – |
| `eZContentObjectPackageHandler` | class extends eZPackageHandler | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php:18` | – |
| `eZDBPackageHandler` | class extends eZPackageHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/ezdb/ezdbpackagehandler.php:18` | – |
| `eZExtensionPackageHandler` | class extends eZPackageHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php:18` | – |
| `eZFilePackageHandler` | class extends eZPackageHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php:18` | – |
| `eZINIAddonPackageHandler` | class extends eZPackageHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/eziniaddon/eziniaddonpackagehandler.php:18` | – |
| `eZInstallScriptPackageHandler` | class extends eZPackageHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php:19` | – |
